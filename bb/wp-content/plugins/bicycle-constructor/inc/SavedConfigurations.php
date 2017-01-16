<?php

require_once 'GoodsConstructor.php';
require_once 'rain.tpl.class.php';

/**
* Saved Configurations
*/
class SavedConfigurations extends GoodsConstructor
{
	protected 
			/**
			 * @var array of configurations
			 */
			$confs,
			/**
			 * @var RainTPL object
			 */
			$tpl;
	
	private $userConfigurations;
			
	function __construct(wpdb $wpdb, RainTPL $tpl)
	{
		parent::__construct($wpdb);
		$this->tpl = $tpl;
	}

	/**
	 * fetch configurations from database
	 * 
	 * @param int $userId
	 * @return stdClass object database query result
	 */
	protected function fetchUserConfigurations($userId, $confId=0)
	{
		$query = "SELECT h.conf_id, "
						. "h.conf_date AS conf_date, "
						. "i.path AS img_path, "
						. "h.main_part_id, "
						. "b.external_part_id "
				. " FROM " . Tables::USER_CONF_HEAD . " AS h"
				. " LEFT JOIN " . Tables::USER_CONF_BODY . " AS b ON (h.conf_id = b.conf_id)"
				. " LEFT JOIN " . Tables::GC_IMAGES . " AS i ON (i.img_id = h.img_id)"
				. " WHERE h.user_id = " . $userId;
		
		if ($confId != 0)
			$query .= ' AND h.conf_id = ' . $confId;
		
		$res = $this->db->get_results($query);
		
		return $res;
	}
	
	/**
	 * returns array of user configurations
	 * 
	 * @param int $userId
	 * @return array user configurations
	 */
	public function getUserConfigurations($userId, $confId=0)
	{
		$data = $this->fetchUserConfigurations($userId, $confId);
		
		$confs = array();
		foreach ($data as $d)
		{
			$c = array(
				'conf_id' => $d->conf_id,
				'img_url' => is_null($d->img_path) ? NOIMAGE_URL : $d->img_path,
				'conf_date' => $d->conf_date
			);
			if (!in_array($c, $confs))
				array_push($confs, $c);
		}
		
		$res = array();
		foreach ($confs as $c) 
		{
			$e = array();
			$m = 0;
			foreach ($data as $d) 
			{
				if ($c['conf_id'] == $d->conf_id)
				{
					if (!in_array($d->external_part_id, $e))
						array_push($e, $d->external_part_id);
					
					$m = $d->main_part_id;
				}
			}
			
			array_push($res, array(
					'conf' => $c,
					'main_part' => $m,
					'external_parts' => $e
				)
			);
		}
		
		$this->confs = $res;
		return $res;
	}
	
	/**
	 * get string name of main part by it's id
	 * 
	 * @param int $mainPartId
	 * @return mixed string if found false if not
	 */
	private function getMainPartName($mainPartId)
	{
		$this->loadBase();
		
		foreach ($this->baseDetails as $b)
		{
			if ($b->main_part_id == $mainPartId)
				return $b->name;
		}
		
		return false;
	}
	
	/**
	 * required parts names string
	 * 
	 * @param array $parts ids of parts
	 * @return string parts names
	 */
	private function getRequiredPartsNames(array $parts)
	{
		$this->loadRequired();
		$names = '';
		
		foreach ($parts as $id) 
		{
			foreach ($this->requiredDetails as $part)
			{
				if($part->external_part_id == $id)
				{
					if ($part->type_id == 1)
						$names .= $part->name . ' Frame, ';
					else if ($part->type_id == 2)
						$names .= $part->name . ' Box';
				}
			}
		}
		
		return $names;
	}
	
	/**
	 * external parts name 
	 * 
	 * @param array $parts ids of parts
	 * @return string external parts names
	 */
	private function getExternalPartsNames(array $parts)
	{
		$this->loadExternal();
		$names = array();
		
		foreach ($parts as $part_id)
		{
			foreach ($this->externalDetails as $e)
			{
				if ($e->external_part_id == $part_id)
				{
					array_push($names, $e->name);
				}
			}
		}
		
		return implode(', ', $names);
	}
	
	/**
	 * Format mysql timestamp as custom string
	 * 
	 * @param string $datetime MySQL timestamp
	 * @return string custom formatted datetime
	 */
	private function strDateTimeFormat($datetime)
	{
		$date = date('F d, Y', strtotime($datetime));
		$time = date('h:i A', strtotime($datetime));
		
		return $date . ' around ' . $time;
	}
	
	/**
	 * get image html from conf array
	 * 
	 * @param array $conf configuration
	 * @return string img html element
	 */
	private function getImageHtml(array $conf)
	{
		return '<img src="'.$conf['img_url'].'" alt="configuration image">';
	}

	/**
	 * get conf name from array
	 * 
	 * @param array $c conf array
	 * @return string configuration name
	 */
	private function getConfigurationName(array $c)
	{
		$names = array();
		
		array_push($names, $this->getMainPartName($c['main_part']));
		$req = $this->getRequiredPartsNames($c['external_parts']);
		if ($req != '')
			array_push($names, $req);
		$ext = $this->getExternalPartsNames($c['external_parts']);
		if ($ext != '')
			array_push ($names, $ext);
		
		return implode(', ', $names);
	}
	
	/**
	 * 
	 * @param array $parts parts ids
	 * @param stdObject $details $this->requiredDetails or $this->externalDetails
	 * @return int sum cost of required parts
	 */
	private function calculatePartsCost(array $parts, $details)
	{
		$this->loadRequired();
		$this->loadExternal();
		
		$cost = 0;
		foreach ($parts as $part_id) 
		{
			foreach ($details as $d)
			{
				if ($d->external_part_id == $part_id)
				{
					$cost += $d->cost;
				}
			}
		}
		
		return $cost;
	}
	
	private function checkInStockStatus($confid)
	{
		$query = "SELECT m.in_stock, e.in_stock FROM gc_user_conf_head AS h
			INNER JOIN gc_user_conf_body AS b
			ON (b.conf_id = h.conf_id)
			INNER JOIN gc_external_parts AS e
			ON (e.external_part_id = b.external_part_id)
			INNER JOIN gc_main_parts AS m 
			ON (m.main_part_id = h.main_part_id)
			WHERE h.conf_id = ".$confid."
			AND m.in_stock > 0 AND e.in_stock > 0";
		
		$result = $this->db->get_results($query);
		if (count($result) == 0)
			return 'data-in-stock="0"';
		else
			return '';
	}
	
	/**
	 * config head html
	 * 
	 * @param array $confs
	 * @return string html
	 */
	private function getConfHeadHtml(array $confs)
	{
		$html = '';
		
		foreach ($confs as $c) 
		{
			$confid = $c['conf']['conf_id'];
			$html .= '<div id="gc_conf_'.$confid.'" class="gc-conf-head">';
			$html .= '<div class="gc-conf-name" '.$this->checkInStockStatus($confid).'>'
						. '<div><input type="radio" class="gc-conf green-radio_btn" name="gc-input" value="conf_id_'.$confid.'"></div>'
						. '<div><p class="gc-conf-name conf-name-p">' 
							.  $this->getConfigurationName($c)
						. '</p>'
						. '<p class="gc-head-saved conf-name-p">Saved on ' 
							. $this->strDateTimeFormat($c['conf']['conf_date']) 
						. '</p></div>'
					. '</div>'
					. '<div class="headinp-btn"><input type="button" value="Show" id="bc_btn_'.$confid.'" class="wpcf7-submit bc_btn bc_btn_show"></div>'
				. '</div>';
		}
		
		$html .= '<input type="button" id="delete_selected" value="Delete selected" class="wpcf7-submit bc_btn">';
		
		return $html;
	}
	
	/**
	 * main part description html
	 * 
	 * @param array $conf
	 * @return string html
	 */
	private function getMainPartDescrHtml(array $conf)
	{
		$this->loadBase();

		$html = '';
		
		$this->tpl->assign('main_part_name', $this->getMainPartName($conf['main_part']));
		
		foreach ($this->baseDetails as $d)
		{
			if ($d->main_part_id == $conf['main_part'])
			{
				$html .= $d->descr;
				$cost = $d->cost + $this->calculatePartsCost($conf['external_parts'], $this->requiredDetails);
				$html .= '<span class="gccost" data-gc-cost="'.$cost.'"></span>';
			}
		}
		
		$this->tpl->assign('main_part_descr', $html);
		
		return $html;
	}
	
	public function getExternalPartsHtml(array $parts) 
	{
		$this->loadExternal();
		
		$html = '';
		
		foreach ($parts as $part_id)
		{
			foreach ($this->externalDetails as $e)
			{
				if ($part_id == $e->external_part_id)
				{
					$html .= '<div class="expart"><span class="gcname left">'.$e->name.'</span>'
							. '<span class="gccost right" data-gc-cost="'.$e->cost.'">'.$e->cost.'</span></div>';
				}
			}
		}
		
		return $html;
	}

	public function getUserConfHtml($userId)
	{
		if (count($this->confs) == 0)
			$this->getUserConfigurations($userId);
		
		if (count($this->confs) == 0)
			return 'You have no configurations yet';

		$c = $this->confs[0];
		
		$this->tpl->assign('conf_head', $this->getConfHeadHtml($this->confs));
		$this->tpl->assign('conf_img', $c['conf']['img_url']);
		$this->tpl->assign('conf_name', $this->getConfigurationName($c));
		$this->tpl->assign('conf_saved_date', $this->strDateTimeFormat($c['conf']['conf_date']));
		$this->getMainPartDescrHtml($c);
		$this->tpl->assign('external_parts_descr', $this->getExternalPartsHtml($c['external_parts']));
		
		$html = $this->tpl->draw( 'layout', $return_string = true );
		return $html;
	}
	
	public function getUserConfById($userId, $confId)
	{
		$this->getUserConfigurations($userId, $confId);
		
		if (count($this->confs) == 0)
			return false;
		
		$c = $this->confs[0];
		
		$data = array(
			'conf_id' => $c['conf']['conf_id'],
			'conf_img'	=> $c['conf']['img_url'],
			'conf_name' => $this->getConfigurationName($c),
			'main_part_name' => $this->getMainPartName($c['main_part']),
			'conf_saved_date' => $this->strDateTimeFormat($c['conf']['conf_date']),
			'main_part_descr' => htmlspecialchars($this->getMainPartDescrHtml($c)),
			'external_parts_descr' => htmlspecialchars($this->getExternalPartsHtml($c['external_parts']))
		);
		
		return $data;
	}
	
	/**
	 * 
	 * @param const string $table table name
	 * @param int $id configuration id
	 * @return boolean true if success, false if failed
	 */
	protected function deleteUserConf($table, $id)
	{
		$query = $this->db->prepare(
			"DELETE FROM " . $table . " WHERE conf_id = %d",
			$id
		);
		
		$this->db->query($query);
		
		if(!$this->commit())
			return false;
		
		return true;
	}
	
	/**
	 * delete user saved configuration from database
	 * 
	 * @param int $confId database conf_id
	 * @return boolean true if success, false if failed
	 */
	public function deleteUserConfigurationById($confId)
	{
		if ($this->deleteUserConf(Tables::USER_CONF_BODY, $confId) && 
			$this->deleteUserConf(Tables::USER_CONF_HEAD, $confId))
		return true;
		
		else return false;
	}
	
	/**
	 * 
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 * @return mixed as specified in $output
	 */
	private function getUsersWithConfsEmailsDistinct($output=OBJECT)
	{
		$query = "SELECT distinct `u`.`user_email` 
				FROM ".Tables::USER_CONF_HEAD." as h
				inner join ".$this->db->users." as u on (`h`.`user_id` = `u`.`ID`)
				order by `u`.`user_email`;";
		
		$emails = $this->db->get_results($query, $output);
		
		return $emails;
	}
	
	/**
	 * get select options for user emails
	 * @return string html formatted string with select options
	 */
	public function getUsersWithConfsEmailsHtml()
	{
		$emails = $this->getUsersWithConfsEmailsDistinct();
		
		$html = '';
		foreach($emails as $e)
		{
			$html .= '<option>' . $e->user_email;
		}
		
		return $html;
	}
	
	/**
	 * 
	 * @param string $email
	 * @return array of user configuration ids
	 */
	public function getUserConfigurationsByEmail($email)
	{
		$query = "select h.conf_id, u.ID as user_id
				from ".Tables::USER_CONF_HEAD." as h
				inner join ".$this->db->users." as u on (`u`.`ID` = `h`.`user_id`)
				where `u`.`user_email` = '".$email."'";
		
		$res = $this->db->get_results($query);
		
		if(count($res) == 0)
			return false;
		
		$confs = array();
		foreach ($res as $r)
		{
			array_push ($confs, $this->getUserConfById($r->user_id, $r->conf_id));
		}
		
		$this->userConfigurations = array(
			'user' => array(
				'email' => $email,
				'user_id' => $res[0]->user_id
			), 
			'confs' => $confs
		);
		
		return $this->userConfigurations;
	}
	
	public function getUserConfigurationSelectOptionsHtml($email)
	{	
		$confs = $this->getUserConfigurationsByEmail($email);
		
		$html = '';
		foreach($confs['confs'] as $c)
		{
			$html .= '<option data-conf-id="'.$c['conf_id'].'">'.$c['conf_name'];
		}
		return $html;
	}
	
	public function getUserConfigurationHtml($constructorShortcode)
	{
		$emails = $this->getUsersWithConfsEmailsDistinct();
		$this->tpl->assign('user_emails_options', $this->getUsersWithConfsEmailsHtml());
		$this->tpl->assign('user_configuration_options', $this->getUserConfigurationSelectOptionsHtml($emails[0]->user_email));
		$this->tpl->assign('conf_id', $this->userConfigurations['confs'][0]['conf_id']);
		$this->tpl->assign('constructor_shortcode', $constructorShortcode);
		
		$html = $this->tpl->draw('admin-users-configurations', true);
		
		return $html;
	}
}