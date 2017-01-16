<?php

/**
 * Helper functions for Bicycle constructor plugin
 */

/**
 * 
 * @global type $wpdb
 * @return \GoodsConstructor
 */
function get_goods_constructor_instance()
{
	include_once 'inc/GoodsConstructor.php';
	
	global $wpdb;
	
	return new GoodsConstructor($wpdb);
}

/**
 * 
 * @global type $wpdb
 * @return \SavedConfigurations
 */
function get_user_saved_configurations_instance()
{
	require_once 'inc/SavedConfigurations.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new SavedConfigurations($wpdb, $tpl);
}

/**
 * 
 * @return \RainTPL
 */
function goods_constructor_get_tpl_instance()
{
	include_once 'inc/rain.tpl.class.php';
	
	raintpl::configure('tpl_dir', dirname(__FILE__) . '/tpl/');
	raintpl::configure('cache_dir', dirname(__FILE__) . '/tmp/' );
	raintpl::configure( 'path_replace', false );

	//initialize a Rain TPL object
	return new RainTPL;
}

/**
 * 
 * @global type $wpdb
 * @return \GoodsDictionary
 */
function get_goods_dictionary_instance()
{
	require_once 'inc/dict/GoodsDictionary.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new GoodsDictionary($wpdb, $tpl);
}

/**
 * 
 * @global type $wpdb
 * @return \BasicModelsDictionary
 */
function get_basic_model_dictionary_instance()
{
	require_once 'inc/dict/BasicModelsDictionary.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new BasicModelsDictionary($wpdb, $tpl);
}

/**
 * 
 * @global type $wpdb
 * @return \PartTypesDictionary
 */
function get_part_types_dictionary_instance()
{
	require_once 'inc/dict/PartTypesDictionary.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new PartTypesDictionary($wpdb, $tpl);
}

/**
 * 
 * @global type $wpdb
 * @return \ExternalPartsDictionary
 */
function get_external_details_dictionary_instance()
{
	require_once 'inc/dict/ExternalPartsDictionary.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new ExternalPartsDictionary($wpdb, $tpl);
}

/**
 * 
 * @global type $wpdb
 * @return \UpgradesPartsDictionary
 */
function get_upgrades_parts_dictionary_instance()
{
	require_once 'inc/dict/UpgradesPartsDictionary.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new UpgradesPartsDictionary($wpdb, $tpl);
}

/**
 * 
 * @global type $wpdb
 * @return \DumpCsv
 */
function get_dump_to_csv_instance()
{
	require_once 'inc/data/DumpCsv.php';
	
	global $wpdb;
	
	return new DumpCsv($wpdb);
}

/**
 * 
 * @global type $wpdb
 * @return \DeliveryView
 */
function get_delivery_costs_instance()
{
	if(!class_exists('DeliveryView'))
		require_once 'inc/delivery/DeliveryView.php';
	
	global $wpdb;
	$tpl = goods_constructor_get_tpl_instance();
	
	return new DeliveryView($wpdb, $tpl);
}

/**
 * display admin POST result message
 * @param boolean $result
 */
function goods_constructor_display_message($result)
{
	if($result)
		echo '<h2>Changes has been saved</h2>';
	else
		echo '<h2>Failed</h2>';
}

function goods_constructor_display_error_json($text)
{
	goods_constructor_display_json(array('error' => $text));
}

function goods_constructor_display_success_json(array $data = null)
{
	if(!$data)
		$data = array();
	
	$data['success'] = 1;
	
	goods_constructor_display_json($data);
}

function goods_constructor_display_json(array $data = null)
{
	if(!$data)
		die();
	
	$json = json_encode($data);
	
	die(stripslashes($json));
}

/**
 * 
 * @return int
 */
function goods_constructor_get_user_id_or_send_error()
{
	$userID = get_current_user_id();
	
	if(!$userID)
		goods_constructor_display_error_json ('You are not logged in');
	
	return $userID;
}