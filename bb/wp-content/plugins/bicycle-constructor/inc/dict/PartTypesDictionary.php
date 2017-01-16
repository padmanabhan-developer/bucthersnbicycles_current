<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('GoodsDictionary'))
	include_once 'GoodsDictionary.php';

/**
 * Description of PartTypesDictionary
 *
 * @author ipm
 */
class PartTypesDictionary extends GoodsDictionary 
{
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
	
	public function getEmptyPartType()
	{
		$t = new stdClass();
		$t->type_id = '';
		$t->name = '';
		$t->is_required = '';
		$t->order = '';
		
		return $t;
	}

	/**
	 * get html formatting for part type input
	 * @param stdClass $t type
	 * @param int $i type input number on page | type array index
	 * @return string html formatting
	 */
	public function getPartTypeInputHtml(stdClass $t, $i)
	{
		$this->tpl->assign('i', $i);
		$this->tpl->assign('type_id', $t->type_id);
		$this->tpl->assign('type_name', $t->name);
		$this->tpl->assign('type_is_required_checked', ($t->is_required == 1) ? 'checked' : '');
		$this->tpl->assign('type_display_order', $t->order);
		//$this->tpl->assign('part_type_delete_button', )
		
		$html = $this->tpl->draw('admin-part-types-inputs', true);
		
		return $this->trim_all($html);
	}
	
	/**
	 * get html formatting for part types inputs
	 * @return string html formatting
	 */
	public function getPartTypesInputHtml()
	{
		$types = $this->fetchPartTypes();
		$html = '';
		
		for($i=0; $i < count($types); $i++)
		{
			$html .= $this->getPartTypeInputHtml($types[$i], $i);
		}
		
		return $html;
	}
	
	private function checkPartTypeIsUsed($externalPartId)
	{
		if ($externalPartId == '')
			return 0;
		
		$query = $this->db->prepare(
			"SELECT count(1) AS count FROM " . Tables::GC_EXTERNAL_PARTS ." WHERE `type_id` = %d",
			$externalPartId
		);
		
		$count = $this->db->query($query);
		
		return $count;
	}
	
	/**
	 * Save part types to database
	 * @param array $types from $_POST
	 * @return boolean true if succeeded, false if failed
	 */
	public function savePartTypes(array $types)
	{
		$types = json_decode(json_encode($types));
		
		foreach ($types as $t)
		{
			$query = $this->db->prepare(
				"INSERT INTO " .Tables::GC_PART_TYPES." (`type_id`, `name`, `is_required`, `order`)
				VALUES(%d, %s, %d, %d)
				ON DUPLICATE KEY UPDATE
				`type_id` = VALUES(`type_id`), `name` = VALUES(`name`), `is_required` = VALUES(`is_required`), `order` = VALUES(`order`)",
				$t->type_id, $t->name, ($t->type_is_required == 'on') ? 1 : 0, $t->order
			);
			
			$this->db->query($query);
		}
		
		if (!$this->commit()) 
		{
			error_log('Failed to commit part types');
			return false;
		}
		
		return true;
	}
	
}
