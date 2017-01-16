<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('GoodsDictionary'))
	include_once 'GoodsDictionary.php';

/**
 * Description of ExternalPartsDictionary
 *
 * @author ipm
 */
class ExternalPartsDictionary extends GoodsDictionary
{
	/**
	 * external parts from database
	 * @var array stdObject 
	 */
	private $externalParts;
	
	/**
	 * part types from database
	 * @var array stdObject 
	 */
	private $partTypes;
	
	public function __construct(wpdb $wpdb, RainTPL $tpl) 
	{
		parent::__construct($wpdb, $tpl);
	}
	
	/**
	 * fetch part types from database
	 * @return array of stdObject
	 */
	private function fetchPartTypes()
	{
		if(empty($this->partTypes))
		{
			$query = "SELECT * FROM " .Tables::GC_PART_TYPES. " ORDER BY `order`";
			$this->partTypes = $this->db->get_results($query);
		}
				
		return $this->partTypes;
	}
	
	/**
	 * fetch external parts from database
	 * @return array of stdObject
	 */
	private function fetchExternalParts()
	{
		if (empty($this->externalParts))
		{
			$query = "SELECT * FROM ".Tables::GC_EXTERNAL_PARTS." WHERE `type_id` IS NOT NULL ORDER BY `order`";
			$this->externalParts = $this->db->get_results($query);
		}
		
		return $this->externalParts;
	}
	
	private function getPartTypesSelectHtml(stdClass $e)
	{
		$this->fetchPartTypes();
		
		$html = '';
		foreach ($this->partTypes as $t)
		{
			if ($t->type_id == $e->type_id)
				$html .= '<option selected="selected" value="'.$t->type_id.'">'.$t->name.'</option>';
			else
				$html .= '<option value="'.$t->type_id.'">'.$t->name.'</option>';
		}
		
		return $html;
	}
	
	private function getDeleteButtonHtml()
	{
		$html = '';
		return $html;
	}

	/**
	 * get html formatting for external part input
	 * @param stdClass $e external part
	 * @param int $i external part input number on page | external part array index
	 * @return string html
	 */
	public function getExternalPartInputHtml(stdClass $e, $i)
	{
		$this->tpl->assign('i', $i);
		$this->tpl->assign('external_part_id', $e->external_part_id);
		$this->tpl->assign('cost', $e->cost);
		$this->tpl->assign('in_stock', $e->in_stock);
		$this->tpl->assign('name', $e->name);
		$this->tpl->assign('part_types_select', $this->getPartTypesSelectHtml($e));
		$this->tpl->assign('order', $e->order);
		$this->tpl->assign('external_part_descr', $e->descr);
		$this->tpl->assign('delete_btn', $this->getDeleteButtonHtml());
		
		$html = $this->tpl->draw('admin-external-details-inputs', true);
		
		return $this->trim_all($html);
	}
	
	/**
	 * get html formatting for external parts inputs
	 * @return string html
	 */
	public function getExternalPartsInputHtml()
	{
		$this->fetchExternalParts();
		$html = '';
		
		for($i=0; $i<count($this->externalParts); $i++)
		{
			$html .= $this->getExternalPartInputHtml($this->externalParts[$i], $i);
		}
		
		return $html;
	}
	
	public function getEmptyExternal() 
	{
		$e = new stdClass();
		$e->external_part_id = '';
		$e->cost = '';
		$e->in_stock = '';
		$e->name = '';
		$e->type_id = '';
		$e->order = '';
		$e->descr = '';
		
		return $e;
	}
	
	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 * 
	 * @param array $external
	 * @return array
	 */
	private function prepareForMultiple(array $external)
	{
		$external = json_decode(json_encode($external));
		
		$values = array();
		foreach ($external as $e)
		{
			array_push(
				$values,
				$this->db->prepare(
					"(%d, %d, %d, %s, %s, %d, %d)",
					$e->external_part_id, $e->cost, $e->in_stock, $e->name, $e->descr, $e->type_id, $e->order
				)
			);
		}
		
		return $values;
	}

	/**
	 * save to database
	 * @param array $external
	 * @return boolean
	 */
	public function saveExternalParts(array $external)
	{
		$values = $this->prepareForMultiple($external);
		
		$query = "INSERT INTO ".Tables::GC_EXTERNAL_PARTS." (`external_part_id`, `cost`, `in_stock`, `name`, `descr`, `type_id`, `order`)
					VALUES " . implode(',', $values) 
				. " ON DUPLICATE KEY UPDATE
				`external_part_id` = VALUES(`external_part_id`), `cost` = VALUES(`cost`), `in_stock` = VALUES(`in_stock`), `name` = VALUES(`name`), `descr` = VALUES(`descr`), `type_id` = VALUES(`type_id`), `order` = VALUES(`order`)";
		
		$this->db->query($query);
		
		if (!$this->commit()) 
		{
			error_log('Failed to commit part types');
			return false;
		}
		
		return true;
	}
}
