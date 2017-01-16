<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tables
{
	const WP_OPTIONS = 'wp_options';
	const PP_ORDERS = 'bbpp_orders';
	const PP_PAYMENTS = 'bbpp_payments';
	
	const GC_USER_CONF_HEAD = 'gc_user_conf_head';
	const GC_USER_CONF_BODY = 'gc_user_conf_body';
	const GC_EXTERNAL = 'gc_external_parts';
	const GC_MAIN_PARTS = 'gc_main_parts';
}

/**
 * Description of DBHandle
 *
 * @author ipm
 */
class DBHandle
{
	/**
	 *
	 * @var wpdb
	 */
	private $db;
	
	public function __construct(wpdb $wpdb)
	{
		$this->db = $wpdb;
	}
	
	/**
	 * get wordpress option value
	 * 
	 * @param string $optionName
	 * @return string
	 */
	public function getOptionValue($optionName)
	{	
		$query = $this->db->prepare('SELECT * FROM ' . Tables::WP_OPTIONS .' AS o'
			. ' WHERE o.option_name = %s', $optionName);
		
		$result = $this->db->get_results($query);
		
		if (count($result) == 0)
			return false;
		
		return $result[0]->option_value;
	}
	
	/**
	 * save payment details to database
	 * 
	 * @param PayPal\Api\Payment $paymentData
	 * @return mixed insert ID if succeeded or false if failed
	 */
	public function savePaymentData($paymentData)
	{	
		$this->db->insert(
			Tables::PP_PAYMENTS,
			array(
				'payment_id' => $paymentData->id,
				'create_time' => $paymentData->create_time,
				'state' => $paymentData->state,
				'total_price' => $paymentData->transactions[0]->amount->total,
				'currency' => $paymentData->transactions[0]->amount->currency,
				'payment_details' => $paymentData->transactions[0]->description
			)
		);
		
		if(!$this->db->insert_id)
			return false;
		
		return $this->db->insert_id;
	}
	
	/**
	 * fetch order details from database
	 * 
	 * @return stdObject payments data
	 */
	public function getPaymentsData($skip=0, $limit=100)
	{
		$query = 'SELECT * FROM ' . Tables::PP_PAYMENTS . ' LIMIT ' . $skip . ', ' . $limit;
		
		return $this->db->get_results($query);
	}

	/**
	 * Save order to database
	 * @param array $orderData
	 * @return mixed insert ID if succeeded or false if failed
	 */
	public function saveOrderData(array $orderData)
	{
		$this->db->insert(
			Tables::PP_ORDERS,
			array(
				'payment_id' => $orderData['id'],
				'order_created_time' => $orderData['order_created_time'],
				'user_configuration_id' => $orderData['user_conf_id'],
				'configuration_name' => $orderData['conf_name']
			)
		);
		
		if (!$this->db->insert_id)
			return false;
		
		return $this->db->insert_id;
	}
	
	/**
	 * retrieve main part id from database
	 * @param int $confId user configuration id
	 * @return mixed main part ID if succeeded or false if failed
	 */
	protected function fetchMainPartByConfId($confId)
	{
		$mainpart = $this->db->get_results("SELECT main_part_id FROM "
				.Tables::GC_USER_CONF_HEAD ." WHERE conf_id = " . $confId);
		
		if (count($mainpart) == 0)
			return false;
		else
			return $mainpart[0]->main_part_id;
	}
	
	/**
	 * retrieve external parts ids from database
	 * @param int $confId user configuration id
	 * @return array external parts ids
	 */
	protected function fetchExternalPartsByConfId($confId)
	{
		$external= $this->db->get_results("SELECT external_part_id FROM "
				.Tables::GC_USER_CONF_BODY ." WHERE conf_id = " . $confId);
		
		$parts = array();
		foreach ($external as $e)
		{
			array_push($parts, $e->external_part_id);
		}
		
		return $parts;
	}

	/**
	 * Update in stock status
	 */
	public function updateInStockStatus($confId)
	{
		$mainpart = $this->fetchMainPartByConfId($confId);
		$external = $this->fetchExternalPartsByConfId($confId);
			
		$this->db->query(
			"UPDATE ".Tables::GC_EXTERNAL." "
				. "SET in_stock = in_stock-1 "
				. "WHERE external_part_id in(".implode(',', $external).")"
		);
		
		$this->db->query(
				"UPDATE " . Tables::GC_MAIN_PARTS . " "
				. "SET in_stock = in_stock-1 "
				. "WHERE main_part_id = " . $mainpart
		);
	}
	
	/**
	 * retrieve sold configuration from database
	 * @param string $payment_id
	 * @return mixed conf ID if succeeded or false if failed
	 */
	public function getSoldConfId($payment_id)
	{
		$conf_id = $this->db->get_results(
			"SELECT user_configuration_id FROM bb_outs.bbpp_orders as o
			INNER JOIN bb_outs.bbpp_payments AS p ON (p.payment_id = o.payment_id)
			WHERE o.payment_id = '".$payment_id."';"
		);
		
		if (count($conf_id) == 0)
			return false;
		else 
			return $conf_id[0]->user_configuration_id;
	}
}
