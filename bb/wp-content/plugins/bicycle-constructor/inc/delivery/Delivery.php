<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('Dump'))
	require_once realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../data/Dump.php');

if(!class_exists('Tables'))
	require_once realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../Tables.php');

/**
 * Description of Delivery
 *
 * @author ipm
 */
class Delivery extends Dump
{
	private $countries;
	
    private $regions;
    
	private $services;
	
	private $delivery_cost;

	public function __construct(\wpdb $db)
	{
		parent::__construct($db);
	}
	
	private function fetchCountries()
	{
		if(count($this->countries) == 0)
			$this->countries = $this->fetchFromTable(Tables::GC_COUNTRIES);
	}
	
	public function getCountries()
	{
		$this->fetchCountries();
		
		return $this->countries;
	}
    
    private function fetchRegions($country=null, $output=ARRAY_A)
    {
        if(is_null($country)) 
        {
            $this->regions = $this->fetchFromTable(Tables::GC_REGIONS);
        }
        else
        {
            $query = "select * from ".Tables::GC_REGIONS." r 
                inner join ".Tables::GC_COUNTRIES." c on (c.country_id = r.country_id)
                where c.country_name = '".$country."'";
        
            $this->regions = $this->db->get_results($query, $output);
        }
    }
    
    public function getRegions($country=null)
    {
        $this->fetchRegions($country);
        
        return $this->regions;
    }
	
	private function fetchServices($region=null, $output=ARRAY_A)
	{   
        if(is_null($region)) 
        {
            $this->services = $this->fetchFromTable(Tables::GC_DELIVERY_SERVICES);
            return;
        }
        else
        {
            $query = "select ds.* 
                    from ".Tables::GC_DELIVERY_COSTS." dc
                    inner join ".Tables::GC_DELIVERY_SERVICES." ds on (dc.delivery_service_id = ds.ds_id)
                    inner join ".Tables::GC_REGIONS." r on (dc.region_id = r.id)
                    where r.region_name = '".$region."'";
            
            $this->services = $this->db->get_results($query, $output);
        }
	}
	
	public function getServices($region=null, $output=ARRAY_A)
	{
		$this->fetchServices($region, $output);
		
		return $this->services;
	}
	
	/**
	 * @param string $where
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 */
	private function fetchDeliveryCost($region='', $service='', $output=ARRAY_A)
	{
		$where = '';
		if($region != '')
			$where .= " where `r`.`region_name` = '" . $region . "'";
		if($region != '' && $service != '')
			$where .= " and `s`.`delivery_service_name` = '" . $service . "'";
		else if ($region == '' && $service != '')
			$where .= " where `s`.`delivery_service_name` = '" . $service . "'";
		
		if(count($this->delivery_cost) == 0)
		{
			$query = "select 
						`dc`.`id`,
						`c`.`country_name`,
                        `r`.`region_name`,
						`s`.`delivery_service_name`,
						`dc`.`delivery_cost`,
						`dc`.`currency` 
					from 
						`".Tables::GC_DELIVERY_COSTS."` as dc
                    inner join `".Tables::GC_REGIONS."` as r
                        on (`r`.`id` = `dc`.`region_id`) 
					inner join `".Tables::GC_COUNTRIES."` as c 
						on (`c`.`country_id` = `r`.`country_id`)
					inner join `".Tables::GC_DELIVERY_SERVICES."` as s 
						on (`s`.`ds_id` = `dc`.`delivery_service_id`)" . $where . " order by `c`.`country_name`";
			
			$this->delivery_cost = $this->db->get_results($query, $output);
		}
	}
	
	/**
	 * 
	 * @param string $region Optional
	 * @param string $service Optional
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 * @return mixed array or object as specified by $output arg
	 */
	public function getDeliveryCost($region='', $service='', $output=ARRAY_A)
	{
		$this->fetchDeliveryCost($region, $service, $output);
		
		return $this->delivery_cost;
	}
    
    /**
     * @param array $_insert
     * @param string $key
     * @param string $table 
     * @return int
     */
    private function insertArray(array $_insert, $key, $table)
    {
        $insert = array();
        foreach ($_insert as $ins)
        {
            if(!in_array($ins[$key], $insert))
                array_push ($insert, array($key => $ins[$key]));
        }
        
        return $this->insertIntoTable($table, $insert);
    }
    
    /**
     * 
     * @param array $costs
     * @param array $data
     * @param string $nameKey
     * @param string $idKey
     * @return array
     */
    private function getIds(array $costs, array $data, $nameKey, $idKey)
    {
        $ids = array();
        foreach ($costs as $cost)
        {
            foreach($data as $d)
            {
                if($cost[$nameKey] == $d[$nameKey])
                    array_push ($ids, $d[$idKey]);
            }
        }
        
        return $ids;
    }

	private function getCountryIds(array $costs)
	{
		$this->insertArray($costs, 'country_name', Tables::GC_COUNTRIES);
		$this->fetchCountries();
		
        return $this->getIds($costs, $this->countries, 'country_name', 'country_id');
	}
	
	private function getServicesIds(array $costs)
	{
		$this->insertArray($costs, 'delivery_service_name', Tables::GC_DELIVERY_SERVICES);
		$this->fetchServices();
		
        return $this->getIds($costs, $this->services, 'delivery_service_name', 'ds_id');
	}
    
    private function getRegionsIds(array $costs, array $country_ids)
    {
        $regions = array();
        $i = 0;
        foreach($costs as $cost)
        {
            if(!in_array($cost['region_name'], $regions))
                array_push ($regions, array(
                    'country_id' => $country_ids[$i], 
                    'region_name' => $cost['region_name']
                ));
            $i++;
        }
        
        $this->insertIntoTable(Tables::GC_REGIONS, $regions);
        $this->fetchRegions();
        
        return $this->getIds($costs, $this->regions, 'region_name', 'id');
    }


    public function saveDeliveryCosts(array $costs)
	{
		$country_ids = $this->getCountryIds($costs);
		$services_ids = $this->getServicesIds($costs);
        $regions_ids = $this->getRegionsIds($costs, $country_ids);
		
		$delivery_costs = array();
        $i = 0;
        foreach ($costs as $cost)
        {
            array_push($delivery_costs, array(
                'id' => $cost['id'],
                'region_id' => $regions_ids[$i],
                'delivery_service_id' => $services_ids[$i],
                'delivery_cost' => $cost['delivery_cost'],
                'currency' => $cost['currency']
            ));
            $i++;
        }
		
		return $this->insertIntoTable(Tables::GC_DELIVERY_COSTS, $delivery_costs);
	}
	
	protected function isCountryUsed($name)
	{
        $query = "select * from `".Tables::GC_DELIVERY_COSTS."` dc
                inner join `".Tables::GC_REGIONS."` r on (`r`.`id` = `dc`.`region_id`)
                inner join `".Tables::GC_COUNTRIES."` c on (`r`.`country_id` = `c`.`country_id`)
                where `c`.`country_id` = `r`.`country_id` and `c`.`country_name` = '".$name."';";
		
        $res = $this->db->get_results($query);
        
		if(count($res) > 0)
			return true;
		else
			return false;
	}
	
	public function deleteCountry($name)
	{
		if($this->isCountryUsed($name))
			return false;
		
		$query = "delete from ".Tables::GC_COUNTRIES." where `country_name` = '".$name."'";
		$this->db->query($query);
		
		if($this->db->last_error)
			return false;
		
		return true;
	}
    
    protected function isRegionUsed($name)
    {
        $query = "select * from `".Tables::GC_DELIVERY_COSTS."` dc
                inner join `".Tables::GC_REGIONS."` r on (r.id = dc.region_id)
                where r.region_name = '".$name."';";
        
        $res = $this->db->get_results($query);
        
        return (count($res) > 0) ? true : false;
    }
    
    public function deleteRegion($name)
    {
        if($this->isRegionUsed($name)) return false;
        
        $query = "delete from ".Tables::GC_REGIONS." where `region_name` = '".$name."'";
        $this->db->query($query);
        
        if($this->db->last_error)
			return false;
		
		return true;
    }

    protected function isServiceUsed($name)
	{
		$query = "select `s`.`delivery_service_name` from ".Tables::GC_DELIVERY_COSTS." as dc, ".Tables::GC_DELIVERY_SERVICES." as s
			where `dc`.`delivery_service_id` = `s`.`ds_id` and `s`.`delivery_service_name` = '".$name."'";
		
		$res = $this->db->get_results($query);
		
		return (count($res) > 0) ? true : false;
	}
	
	public function deleteDeliveryService($name)
	{
		if($this->isServiceUsed($name))
			return false;
		
		$query = "delete from ".Tables::GC_DELIVERY_SERVICES." where `delivery_service_name` = '".$name."'";
		$this->db->query($query);
		
		if($this->db->last_error)
			return false;
		
		return true;
	}
	
	public function deleteDeliveryCostsRecord($id)
	{
		$this->db->delete(Tables::GC_DELIVERY_COSTS, array('id' => $id));
		
		if($this->db->last_error)
			return false;

		return true;
	}
}
