<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('GoodsDictionary'))
	include_once 'GoodsDictionary.php';

/**
 * Description of BasicModelsDictionary
 *
 * @author ipm
 */
class BasicModelsDictionary extends GoodsDictionary 
{
	/**
	 * basic models from database
	 * @var array stdObject
	 */
	private $basicModels;
	
	public function __construct(wpdb $wpdb, RainTPL $tpl) 
	{
		parent::__construct($wpdb, $tpl);
	}
	
	/**
	 * fetch models from database
	 * 
	 * @return array of stdObject
	 */
	private function fetchBasicModels()
	{
		$query = 'SELECT * FROM ' . Tables::GC_MAIN_PARTS . ' ORDER BY `order`';
		
		$this->basicModels = $this->db->get_results($query);
		
		return $this->basicModels;
	}
	
	/**
	 * check if any user has model in their configurations
	 * @param int $mainpartid main part id to check
	 * @return int count of user configured models
	 */
	private function checkModelIsConfigured($mainpartid)
	{
		if ($mainpartid == '')
			return 0;
		
		$count = $this->db->get_results(
			"SELECT count(1) as count FROM ".Tables::USER_CONF_HEAD." WHERE main_part_id = " . $mainpartid
		);
		
		return $count[0]->count;
	}
	
	/**
	 * admin cannot delete basic models if users have configurations with that model.
	 * 
	 * @param stdClass $model model
	 * @param int $i model input number on page | models array index
	 * @return string html button code if no user configurations found, or empty string to assign to tpl
	 */
	protected function getDeleteButtonHtml(stdClass $model, $i, $class='delete_btn')
	{
		if($this->checkModelIsConfigured($model->main_part_id) == 0)
			return '<input type="button" value="-" class="'.$class.'" data-input-id="'.$i.'" data-model-id="'.$model->main_part_id.'">';
		else
			return '';
	}
	
	/**
	 * get html formatting for model input
	 * 
	 * @param stdClass $m model
	 * @param int $i model input number on page | models array index
	 * @return string html formatting
	 */
	public function getBasicModelInputHtml(stdClass $m, $i)
	{
		$this->tpl->assign('i', $i);
		$this->tpl->assign('main_part_id', $m->main_part_id);
		$this->tpl->assign('name', $m->name);
		$this->tpl->assign('cost', $m->cost);
		$this->tpl->assign('order', $m->order);
		$this->tpl->assign('in_stock', $m->in_stock);
		$this->tpl->assign('delete_btn', $this->getDeleteButtonHtml($m, $i, 'delete_model_input'));
		$this->tpl->assign('descr', $m->descr);
		
		$html = $this->tpl->draw('admin-basic-model-inputs', true);
		
		return $this->trim_all($html);
	}
	
	/**
	 * get all models from database and display them for editing
	 * 
	 * @return string html formatting
	 */
	public function getBasicModelsInputHtml()
	{
		$models = $this->fetchBasicModels();
		
		$html = '';
		$i = 0;
		foreach ($models as $m)
		{
			$html .= $this->getBasicModelInputHtml($m, $i);
			$i++;
		}
		
		return $html;
	}
	
	/**
	 * to display new input form
	 * @return \stdClass model
	 */
	public function getEmptyModel()
	{
		$m = new stdClass();
		$m->main_part_id = '';
		$m->name = '';
		$m->cost = '';
		$m->order = '';
		$m->in_stock = '';
		$m->descr = '';
		
		return $m;
	}
	
	/**
	 * save models to database
	 * 
	 * @param array $models models
	 * @return boolean true if ok, false if failed
	 */
	public function saveBasicModels(array $models)
	{
		$models = json_decode(json_encode($models));
		foreach ($models as $m)
		{				
			$query = $this->db->prepare(
				"INSERT INTO gc_main_parts(`main_part_id`, `name`, `descr`, `cost`, `in_stock`, `order`)
				VALUES(%d, %s, %s, %d, %d, %d)
				ON DUPLICATE KEY UPDATE
				`main_part_id` = VALUES(`main_part_id`), `name` = VALUES(`name`), `descr` = VALUES(`descr`), `cost` = VALUES(`cost`), `in_stock` = VALUES(`in_stock`), `order` = VALUES(`order`)",
				$m->model_id, $m->name, $m->descr, $m->cost, $m->in_stock, $m->order
			);
			
			$this->db->query($query);
		}
		
		if (!$this->commit()) {
			error_log('Failed to commit basic models');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete basic model from database
	 * @param int $id model id | main part id
	 * @return boolean true if succeeded, false if failed
	 */
	public function removeBasicModel($id)
	{
		if ($this->checkModelIsConfigured($id) != 0)
			return false;
		
		$this->db->delete(Tables::GC_CONF_HEAD, array('main_part_id' => $id));
		$this->db->delete(Tables::GC_MAIN_PARTS, array('main_part_id' => $id));
		
		if (!$this->commit()) {
			error_log('Failed to delete basic model');
			return false;
		}
		
		return true;
	}
}