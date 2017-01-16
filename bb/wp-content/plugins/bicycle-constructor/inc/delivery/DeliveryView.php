<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('Delivery'))
	require_once realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../delivery/Delivery.php');

if(!class_exists('RainTPL'))
	require_once realpath(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../rain.tpl.class.php');

/**
 * Description of DeliveryView
 *
 * @author ipm
 */
class DeliveryView extends Delivery
{
	/**
	 * @var RainTPL
	 */
	private $tpl;
	
	public function __construct(\wpdb $db, RainTPL $tpl) 
	{
		parent::__construct($db);
		$this->tpl = $tpl;
	}
    
    private function getOptionsHtml(array $options, $key)
    {
        $html = '';
		foreach($options as $opt)
		{
            if(isset($opt[$key]))
                $html .= '<option>' . $opt[$key];
		}
		
		return $html;
    }
	
	public function getCountriesSelectOptionsHtml()
	{
		$countries = $this->getCountries();
		
        return $this->getOptionsHtml($countries, 'country_name');
	}
	
	public function getDeliveryServicesOptionsHtml($region = null)
	{
		$services = $this->getServices($region);
        
		return $this->getOptionsHtml($services, 'delivery_service_name');
	}
    
    public function getRegionsSelectOptionsHtml($country=null)
    {
        $regions = $this->getRegions($country);
        
        return $this->getOptionsHtml($regions, 'region_name');
    }
	
	public function getDeliveryCostsInputsHtml($cost, $i)
	{
		$this->tpl->assign('i', $i);
		$this->tpl->assign('id', $cost['id']);
		$this->tpl->assign('country', $cost['country_name']);
		$this->tpl->assign('region', $cost['region_name']);
		$this->tpl->assign('service', $cost['delivery_service_name']);
		$this->tpl->assign('cost', $cost['delivery_cost']);
		$this->tpl->assign('currency', $cost['currency']);
		
		$html = $this->tpl->draw('admin-delivery-costs-inputs', true);
		
		return $this->trim_all($html);
	}
	
	public function getEmpty()
	{
		return array(
			'id' => '',
			'country_name' => '',
            'region_name' => '',
			'delivery_service_name' => '',
			'delivery_cost' => '',
			'currency' => ''
		);
	}
	
	/**
	 * 
	 * @return string html formatted string
	 */
	public function getDeliveryCostsInputs()
	{
		$costs = $this->getDeliveryCost();
		
		$html = '';
		for($i=0; $i<count($costs); $i++)
		{
			$html .= $this->getDeliveryCostsInputsHtml($costs[$i], $i);
		}
		
		return $html;
	}
	
	/**
	 * 
	 * @return string html formatted string
	 */
	public function getShippingOptionsShortcodeHtml()
	{
		$countries = $this->getCountries();
		if(count($countries) > 0) 
		{
			$costs = $this->getDeliveryCost();#$countries[0]['country_name']);
		}
        
		$html = '';
		if(count($costs) > 0)
		{
			$this->tpl->assign('shipping_countries_options', $this->getCountriesSelectOptionsHtml());
			$this->tpl->assign('currency', $costs[0]['currency']);
			$this->tpl->assign('price', $costs[0]['delivery_cost']);
			$html .= $this->tpl->draw('shipping-shortcode', true);
		}
		
		return $html;
	}
}
