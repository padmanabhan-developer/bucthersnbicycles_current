<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('GoodsDictionary'))
	include_once 'GoodsDictionary.php';

if(!defined('NOIMAGE_URL'))
	define ('NOIMAGE_URL', plugins_url('../../tpl/img/No_Image_Wide.svg', __FILE__));

/**
 * Description of UpgradesPartsDictionary
 *
 * @author ipm
 */
class UpgradesPartsDictionary extends GoodsDictionary
{
	/**
	 *  upgrade parts from database
	 * @var stdObject 
	 */
	private $upgradesParts;
	
	public function __construct(\wpdb $wpdb, \RainTPL $tpl) 
	{
		parent::__construct($wpdb, $tpl);
	}
	
	/**
	 * fetch upgrade parts from database
	 * @return stdObject
	 */
	private function fetchUpgradeParts()
	{
		if(empty($this->upgradesParts))
		{
			$query = "SELECT p.*, im.path AS img_path, im.img_id
					FROM ".Tables::GC_EXTERNAL_PARTS." AS p
					INNER JOIN ".Tables::GC_EXTERNAL_PARTS_IMAGES." AS i 
					ON (`i`.`external_part_id` = `p`.`external_part_id`)
					INNER JOIN ".Tables::GC_IMAGES." AS im
					ON (`im`.`img_id` = `i`.`img_id`)
					WHERE `p`.`type_id` IS NULL";
		
			$this->upgradesParts = $this->db->get_results($query);
		}
		
		return $this->upgradesParts;
	}
	
	private function assignDeleteBtn()
	{
		return '';
	}
	
	public function getEmptyUpgrade()
	{
		$u = new stdClass();
		$u->img_path = NOIMAGE_URL;
		$u->img_id = '';
		$u->external_part_id = '';
		$u->cost = '';
		$u->in_stock = '';
		$u->name = '';
		$u->order = '';
		$u->descr = '';
		
		return $u;
	}


	/**
	 * get html formatted inputs
	 * 
	 * @param stdClass $u upgrade part
	 * @param int $i input id on page
	 * @return string html
	 */
	public function getUpgradePartInputHtml(stdClass $u, $i)
	{
		$this->tpl->assign('i', $i);
		$this->tpl->assign('upgrades_img_src', $u->img_path);
		$this->tpl->assign('upgrades_img_id', $u->img_id);
		$this->tpl->assign('external_part_id', $u->external_part_id);
		$this->tpl->assign('cost', $u->cost);
		$this->tpl->assign('in_stock', $u->in_stock);
		$this->tpl->assign('name', $u->name);
		$this->tpl->assign('order', $u->order);
		$this->tpl->assign('delete_btn', $this->assignDeleteBtn());
		$this->tpl->assign('upgrades_part_descr', $u->descr);
		
		$html = $this->tpl->draw('admin-upgrades-inputs', true);
		
		return $this->trim_all($html);
	}
	
	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 * 
	 * @param array $upgrades
	 * @return array
	 */
	private function prepareForMultiple(array $upgrades)
	{
		$values = array();
		foreach ($upgrades as $u)
		{
			array_push(
				$values,
				$this->db->prepare(
					"(%d, %d, %d, %s, %s, %d)",
					$u->external_part_id, $u->cost, $u->in_stock, $u->name, $u->descr, $u->order
				)
			);
		}
		
		return $values;
	}
	
	/**
	 * get html formatted inputs
	 * @return string html
	 */
	public function getUpgradePartsInputsHtml()
	{
		$this->fetchUpgradeParts();
		$html = '';
		
		for($i=0; $i < count($this->upgradesParts); $i++)
		{
			$html .= $this->getUpgradePartInputHtml($this->upgradesParts[$i], $i);
		}
		
		return $html;
	}
	
	/**
	 * delete hostname from url
	 * 
	 * @param string $absUrl absolute url with hostname
	 * @return string relative url without hostname
	 */
	protected function getRelativeImageUrl($absUrl)
	{
		if (strpos($absUrl, $_SERVER['HTTP_HOST']))
			return substr($absUrl, strpos($absUrl, $_SERVER['HTTP_HOST']) + strlen($_SERVER['HTTP_HOST']));
		else
			return $absUrl;
	}
	
	private function saveImages($upgrades)
	{
		foreach ($upgrades as $u)
		{
			$imgQuery = $this->db->prepare("SELECT img_id FROM ".Tables::GC_IMAGES." WHERE path = %s", $this->getRelativeImageUrl($u->img_path));
			$img_id = $this->db->get_results($imgQuery);
			
			if (count($img_id) == 0)
			{
				$this->db->replace(Tables::GC_IMAGES, array('path' => $this->getRelativeImageUrl($u->img_path)));
				$imageId = $this->db->insert_id;
				if ($imageId)
					$u->img_id = $imageId;
			}
		}
		
		return $upgrades;
	}
	
	private function saveExternalParts($upgrades)
	{
		$new = array();
		for($i=0; $i<count($upgrades); $i++)
		{
			if ($upgrades[$i]->external_part_id == '') 
			{
				array_push($new, $upgrades[$i]);
				unset($upgrades[$i]);
			}
		}
		
		$query = "INSERT INTO ".Tables::GC_EXTERNAL_PARTS." 
			(`external_part_id`, `cost`, `in_stock`, `name`, `descr`, `order`) 
			VALUES ".implode(',', $this->prepareForMultiple($upgrades))." 
			ON DUPLICATE KEY UPDATE 
			`external_part_id` = VALUES(`external_part_id`), `cost` = VALUES(`cost`), `in_stock` = VALUES(`in_stock`), `name` = VALUES(`name`), `descr` = VALUES(`descr`), `order` = VALUES(`order`)";
		
		$this->db->query($query);
		
		return $new;
	}
	
	private function saveNewExternalParts($new)
	{
		foreach ($new as $n)
		{
			$query = $this->db->replace(Tables::GC_EXTERNAL_PARTS, array(
					'cost' => $n->cost,
					'in_stock' => $n->in_stock,
					'name' => $n->name,
					'descr' => $n->descr,
					'order' => $n->order
				)
			);
			$part_id = $this->db->insert_id;
			if($part_id)
				$n->external_part_id = $part_id;
		}
		
		return $new;
	}

	private function saveExternalPartsImages($upgrades)
	{
		$values = array();
		foreach ($upgrades as $u)
		{
			array_push($values, $this->db->prepare("(%d, %d)", $u->img_id, $u->external_part_id));
		}
		
		if (count($values) > 0)
		{
			$query = "INSERT INTO " .Tables::GC_EXTERNAL_PARTS_IMAGES. "(`img_id`, `external_part_id`) 
					VALUES ".implode(',', $values)." 
					ON DUPLICATE KEY UPDATE 
					`img_id` = VALUES(`img_id`), `external_part_id` = VALUES(`external_part_id`)";
		
			$this->db->query($query);

			if (!$this->commit()) 
			{
				error_log('Failed to commit images');
				return false;
			}
		}
		
		return true;		
	}

	public function saveUpgradeParts(array $upgrades)
	{
		$upgrades = json_decode(json_encode($upgrades));
		$this->saveImages($upgrades);
		
		$new = $this->saveExternalParts($upgrades);
		$this->saveNewExternalParts($new);
		$this->saveExternalPartsImages($new);
		
		if (!$this->commit()) 
		{
			error_log('Failed to commit upgrade parts');
			return false;
		}
		
		return true;
	}
}
