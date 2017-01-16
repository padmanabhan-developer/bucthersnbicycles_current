<?php

if(!defined('NOIMAGE_URL'))
	define ('NOIMAGE_URL', plugins_url('../tpl/img/No_Image_Wide.svg', __FILE__));

if(!class_exists('Tables'))
	include_once dirname(__FILE__) . '/Tables.php';

/**
 * Description of GoodsConstructor
 *
 * @author di_djon
 */
class GoodsConstructor
{
	
	protected 
			/**
			 * @var wpdb
			 */
			$db,
			/**
			 * @var array
			 */
			$baseDetails,
			/**
			 * @var array 
			 */
			$requiredDetails,
			/**
			 * @var array
			 */
			$externalDetails;
	
	public function __construct(wpdb $db)
	{
		$this->db = $db;
	}

	protected function loadBase()
	{
		if (count($this->baseDetails) == 0) 
			$this->baseDetails = $result = $this->db->get_results('select * from gc_main_parts m order by m.order');
	}
	
	protected function loadRequired()
	{
		if (count($this->requiredDetails) == 0)
		$this->requiredDetails = $result = $this->db->get_results('select p.*,t.name as type_name from gc_external_parts p, gc_part_types t
			where p.type_id = t.type_id
			and t.is_required = 1 order by t.`order`, p.type_id, p.`order`');
	}
	
	protected function loadExternal()
	{
		$query = 'SELECT p.external_part_id AS external_part_id,'
				. ' p.*, '
				. 'i.path AS img_path '
				. 'FROM ' . Tables::GC_EXTERNAL_PARTS . ' AS p '
				. 'LEFT JOIN ' . Tables::GC_EXTERNAL_PARTS_IMAGES . ' AS pi ON (pi.external_part_id = p.external_part_id) '
				. 'LEFT JOIN ' . Tables::GC_IMAGES . ' AS i ON (i.img_id = pi.img_id) '
				. 'WHERE p.type_id IS NULL '
				. 'ORDER BY p.`order`';
		
		if (count($this->externalDetails) == 0)
			$this->externalDetails = $result = $this->db->get_results($query);
	}
	
	public function getHtml()
	{		
		$this->loadBase();
		$this->loadRequired();
		$this->loadExternal();

		$image_url = $this->getStartImgUrl();
		
		$html = '<div class="bc_body">';
			$html .= '<div class="bc_parts">';
				$html .= '<div class="bc_base_conf">Step 1: Select your basic model:' . $this->base2html() . '</div>';
				$html .= '<div class="bc_required_parts">' . $this->required2html() . '</div>';
			$html .= '</div>';
			$html .= '<div class="bc_featured_image">' . $this->imgHtml($image_url['img_url']) . '<div class="constr-share-wrapper"><div class="share-black"></div></div></div>';
		$html .= '</div>';
		$html .= '<div class="bc_external_parts"><p>Step 4: Add upgrades:</p><div class="bc_ext">' . $this->external2html() . '</div></div>';
		$html .= '<div class="bc_ajax_response"></div>';

		return $html;
	}
	
	protected function getStartImgUrl()
	{
		$mainPart = count($this->baseDetails) > 0 ? $this->baseDetails[0]->main_part_id : null;
		$requiredDetails = array();
		
		$type = 0;
		if(count($requiredDetails) > 0)
			$type = $this->requiredDetails[0]->type_id;
		
		foreach ($this->requiredDetails as $value)
		{
			$requiredDetails[] = $value->external_part_id;
		}
		
		return $this->getImageUrl(array('main_part' => $mainPart, 'required_details' => $requiredDetails), NOIMAGE_URL);
	}

	protected function base2html($selectedId = null)
	{
		$html = '<ul>';
		
		if(count($this->baseDetails) == 0)
			return $html;
		
		if(is_null($selectedId))
			$selectedId = $this->baseDetails[0]->main_part_id;

		foreach ($this->baseDetails as $item)
		{
			$html .= "<li class='detail_main_part' data-in-stock=".$item->in_stock."><input type='radio' name='main_part' class='detail main_part' value=\"$item->main_part_id\" data-main-part-name=\"$item->name\" data-cost=\"$item->cost\" " 
					. ($selectedId == $item->main_part_id ? 'checked' : '') . "><label for='main_part'>$item->name</label></li>";
		}

		$html .= '</ul>';
		
		return $html;
	}
	
	protected function required2html($selected = null)
	{
		if(count($this->requiredDetails) == 0)
			return '';
		
		$type		= 0;
		$html		= '';
		$selection	= is_null($selected) ? array() : $selected;		
		
		$items = $this->getRequiredItems();
		$i = 0;
		
		foreach ($items as $id => $item) 
		{
			if ($type != $id && count($item) > 0)
			{
				$html .= $item[$i]->type_name;
				
				if(is_null($selected))
					$selection = array($item[$i]->external_part_id);
				
				$type = $item[$i]->type_id;
			}
			$i++;
			
			$html .= $this->getRequiredItemsHtml($item, $selection);
		}
				
		return $html;
	}
	
	/**
	 * Display helper function
	 * 
	 * @return array required items array arranged by type as key
	 */
	protected function getRequiredItems() 
	{
		$types = array();
		foreach($this->requiredDetails as $item)
		{
			if (!in_array($item->type_id, $types))
				array_push ($types, $item->type_id);
		}
		
		$item_types = array();
		foreach ($types as $id)
		{	
			$tmp = array();
			foreach ($this->requiredDetails as $item)
			{
				if ($id == $item->type_id)
				{
					array_push($tmp, $item);
				}
			}
			
			$item_types[$id] = $tmp;
		}
		
		return $item_types;
	}
	
	/**
	 * Returns html formatted string with each item
	 * 
	 * @param array $items
	 * @param array $selection
	 * @return string html formatted string
	 */
	protected function getRequiredItemsHtml(array $items, array $selection) 
	{
		$html = '<ul>';
		
		foreach ($items as $item)
		{
			$html .= "<li class='detail_required' data-in-stock='".$item->in_stock."'>"
						. "<input "
							. "type='radio' "
							. "name='required_$item->type_id' "
							. "class='detail required_detail' "
							. "value=\"$item->external_part_id\" "
							. "data-cost=\"$item->cost\" " 
							. (in_array($item->external_part_id, $selection) ? 'checked' : '') . ""
						. ">"
							. "<label for='required_$item->type_id'>$item->name</label>"
					. "</li>";
		}
		
		$html .= '</ul>';
		
		return $html;
	}

	protected function external2html($selected = null)
	{
		$html = '';
		
		if (is_null($selected))
			$selected = array();
		
		foreach ($this->externalDetails as $item)
		{
			$html .= '<div class="bc-external-item" data-in-stock="'.$item->in_stock.'">';
				$html .= '<div class="bc-external-item-image"><img src="'. $item->img_path .'" alt="'. $item->name .'"></div>';
				$html .= '<div><div class="bc-external-item-name">'. $item->name .'</div>';
				$html .= '<div class="bc-external-item-price">add <span class="bc-item-price">'. $item->cost .'</span></div>';
				$html .= "<input type='checkbox' "
						. "name='external_$item->external_part_id' "
						. "class='detail external_detail customCheckbox' "
						. "value='$item->external_part_id' "
						. "data-cost=\"$item->cost\" "
						. "data-external-item-name=\"$item->name\" " 
						. (in_array($item->external_part_id, $selected) ? 'checked' : '') . "></div>";
				$html .= '</div>';
		}
		
		return $html;
	}
	
	/**
	 * get image URL by configuration parameters
	 * 
	 * @param array $params
	 * @param string $noimageUrl default image to show if not found
	 * @return array
	 */
	public function getImageUrl($params, $noimageUrl = NOIMAGE_URL)
	{
		if(empty($params['main_part']))
			return $noimageUrl;
		
		if(empty($params['required_details']) || !is_array($params['required_details']))
			return $noimageUrl;
		
		$result = $this->getConfigurationID($params);
		
		if(count($result) == 0)
			return array('img_url' => $noimageUrl, 'conf_id' => 0);
		
		return array('img_url' => $result[0]->img_path, 'conf_id' => $result[0]->conf_id);
	}
	
	public function getMainPartByName($name)
	{
		if(count($this->baseDetails) == 0)
			$this->loadBase ();
		
		$mainparts = array();
		foreach ($this->baseDetails as $d)
		{
			if ($d->name == $name)
				array_push ($mainparts, $d);
		}
		
		return $mainparts;
	}
	
	public function getExternalParts()
	{
		if (count($this->externalDetails) == 0)
			$this->loadExternal();
		
		return $this->externalDetails;
	}
	
	/**
	 * get confiruration ID by parameters
	 * 
	 * @param array $params 
	 * @return array of configurations or empty
	 */
	public function getConfigurationID($params)
	{
		if (empty($params['required_details']) || 
				!is_array($params['required_details']) || 
				empty($params['main_part']))
		{
			return array();
		}
			
		$main_part			= intval($params['main_part']);
		$required_detail	= $params['required_details'];
		$externalDetails	= empty($params['external_details']) || !is_array($params['external_details']) ? array() : $params['external_details'];
		
		$partsCount = count($required_detail) + count($externalDetails);
		
		array_walk($required_detail, function(&$n){ 
			$n = intval($n);
		});
		
		array_walk($externalDetails, function(&$n){
			$n = intval($n);
		});
		
		$details = array_merge_recursive($required_detail, $externalDetails);
		
		$query = "SELECT h.conf_id, i.path AS img_path "
				. " FROM " . Tables::GC_CONF_HEAD . " AS h "
				. " INNER JOIN " . Tables::GC_CONF_BODY . " AS b ON (b.conf_id = h.conf_id) "
				. " INNER JOIN " . Tables::GC_IMAGES . " AS i ON (i.img_id = h.img_id) "
				. " WHERE h.main_part_id = " . $main_part
				. " AND h.parts_count = " . $partsCount
				. " AND b.external_part_id IN(" . implode(',', $details) . ") "
				. " GROUP BY h.conf_id, i.path "
				. " HAVING count(1) = " . $partsCount;
				
		$result = $this->db->get_results($query);
		
		return $result;
	}
	
	protected function imgHtml($src)
	{
		return "<img class='constructor_img' src='$src' alt=''/>";
	}
	
	/**
	 * 
	 * @param type $confID
	 * @param type $userID
	 * @return array Configuration data
	 */
	public function getUserCofigurationByID($confID)
	{
		$headQuery	= $this->db->prepare('select h.conf_id, h.user_id, h.main_part_id, h.parts_count, h.conf_date, i.path as img_path
										from gc_user_conf_head h 
										left join gc_images i on (h.img_id = i.img_id)
										where h.conf_id =  %d', $confID);
		$head		= $this->db->get_results($headQuery);
		
		if(count($head) == 0)
			return array();
		
		$bodyQuery	= $this->db->prepare('select b.*, if(p.type_id is null, 0, 1) is_required, p.type_id
						from gc_user_conf_body b, gc_external_parts p
						where p.external_part_id = b.external_part_id
						and b.conf_id = %d', $confID);
		
		$body		= $this->db->get_results($bodyQuery);
		
		return array('head' => $head[0], 'body' => $body);
	}
	
	/**
	 * 
	 * @param int $userID
	 * @param int $mainPart
	 * @param array $reqDetails
	 * @param array $extDetails
	 * @param string $imageUrl relative image url of configuration
	 */
	public function saveUserConfiguration($userID, $mainPart, array $reqDetails, array $extDetails, $imageUrl="", $datetime)
	{
		if(!$this->transactionStart())
		{
			error_log ('Transaction start failed');
			return false;
		}
		
		$confHead = array(
			'main_part_id' => $mainPart,
			'user_id' => $userID,
			'conf_date' => $datetime
		);
		
		if ($imageUrl != "")
			$confHead['img_id'] = $this->saveConfigurationImage($imageUrl);
		
		$this->db->insert(Tables::USER_CONF_HEAD, $confHead);
		
		$confID = $this->db->insert_id;
		
		if(!$confID)
		{
			error_log('failed to insert user configuration header');
			return false;
		}
		
		$details = array_merge($reqDetails, $extDetails);
		
		foreach ($details as $value)
		{
			$result = $this->db->insert(Tables::USER_CONF_BODY, array(
				'conf_id' => $confID,
				'external_part_id' => $value));
			
			if(!$result)
			{
				error_log('failed to insert user configuration body');
				return false;
			}
		}
		
		if(!$this->commit())
		{
			error_log('Failed to commit');
			return false;
		}
		
		return $confID;
	}
	
	/**
	 * Save configuration on the admin side
	 * 
	 * @param type $imageId id of the image in database
	 * @param type $mainpart array of main parts
	 * @param array $requiredParts required parts
	 * @param array $externalParts external parts
	 * @return bool true if succeeded else false
	 */
	public function saveConfiguration($imageId, $mainpart, array $requiredParts, array $externalParts)
	{
		if (!$this->transactionStart())
		{
			error_log('Transaction start failed');
			return false;
		}
		
		$params = array(
			'main_part' => $mainpart,
			'required_details' => $requiredParts,
			'external_details' => $externalParts
		);
		$res = $this->getConfigurationID($params);

		if (count($res) == 0)
		{
			$details = array_merge($requiredParts, $externalParts);
			
			$this->db->insert(Tables::GC_CONF_HEAD, array(
					'main_part_id' => $mainpart,
					'img_id' => $imageId,
					'parts_count' => count($details)
				)
			);

			$confID = $this->db->insert_id;

			if(!$confID)
			{
				error_log('Failed to insert admin-side configuration header');
				return false;
			}

			foreach ($details as $value) 
			{
				$result = $this->db->insert(Tables::GC_CONF_BODY, array(
					'conf_id' => $confID,
					'external_part_id' => $value
					)
				);

				if (!$result)
				{
					error_log('Failed to insert admin-side configuration body');
					return false;
				}
			}

			if (!$this->commit())
			{
				error_log('Failed to commit admin-side configuration body');
				return false;
			}
		}
		else return false;

		return true;
	}
	
	/**
	 * insert image into database and return its id
	 * 
	 * @param type $imageUrl absolute image url
	 * @return mixed false in case of error, image id if succeeded 
	 */
	public function saveConfigurationImage($imageUrl)
	{
		if (!$this->transactionStart())
		{
			error_log('Save Image transaction start failed');
			return false;
		}
		
		$res = $this->getImageId($this->getRelativeImageUrl($imageUrl));
		
		if (count($res) == 0)
		{
			$data = array('path' => $this->getRelativeImageUrl($imageUrl));
			$insert = $this->db->replace(Tables::GC_IMAGES, $data);
			$img_id = $this->db->insert_id;

			if (!$img_id)
			{
				error_log('failed to insert featured image');
				return false;
			}
			else
			{
				if (!$this->commit())
				{
					error_log('Failed to commit featured image');
					return false;
				}
				return $img_id;
			}
		}
		else
		{
			return $res[0]->img_id;
		}
	}
	
	
	/**
	 * get image by its relative url
	 * 
	 * @param string $imgRelUrl relative image url
	 * @return array
	 */
	protected function getImageId($imgRelUrl)
	{
		$query = "SELECT * FROM gc_images WHERE path = '$imgRelUrl'";
		
		$res = $this->db->get_results($query);
		
		return $res;
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

	protected function transactionStart()
	{
		if(mysql_query("SET AUTOCOMMIT=0")=== FALSE)
			return false;
		
		if(mysql_query("START TRANSACTION") === false)
			return false;
		
		return TRUE;
	}
	
	protected function commit()
	{
		if(mysql_query("COMMIT") === false)
			return false;
			
		return true;
	}
	
	/**
	 * 
	 * @param int $userID
	 * @param int $mainPart
	 * @param array $requiredDetails
	 * @param array $externalDetails
	 */
	public function getUserConfHeadByParts($userID, $mainPart, array $requiredDetails, array $externalDetails)
	{
		$externalDetails = empty($externalDetails) ? array() : $externalDetails;
		
		$partsCount = count($requiredDetails) + count($externalDetails);
		
		array_walk($requiredDetails, function(&$n){ 
			$n = intval($n);
		});
		
		array_walk($externalDetails, function(&$n){
			$n = intval($n);
		});
		
		$details = array_merge_recursive($requiredDetails, $externalDetails);
		
		$query = "SELECT h.conf_id"
				. " FROM " . Tables::USER_CONF_HEAD . " AS h"
				. " INNER JOIN " . Tables::USER_CONF_BODY . " AS b ON(b.conf_id = h.conf_id)"
				. " WHERE h.user_id = " . $userID
				. " AND h.main_part_id = " . $mainPart
				. " AND b.external_part_id IN(" . implode(',', $details) . ")"
				. " GROUP BY h.conf_id"
				. " HAVING count(1) = " . $partsCount;
		
		$result = $this->db->get_results($query);
		
		return $result;
	}	
}