<?php

/*
	helper functions for bb paypal plugin
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

function bbpp_display_error_json($text)
{
	goods_constructor_display_json(array('error' => $text));
}

function bbpp_display_success_json(array $data = null)
{
	if(!$data)
		$data = array();
	
	$data['success'] = 1;
	
	bbpp_display_json($data);
}

function bbpp_display_json(array $data = null)
{
	if(!$data)
		die();
	
	$json = json_encode($data);
	
	die(stripslashes($json));
}

/**
 * get wordpress database handle instance
 * 
 * @global wpdb $wpdb wordpress database handle
 * @return \DBHandle instance
 */
function bbpp_get_dbh_instance()
{
	if (!defined('SHORTINIT'))
		define( 'SHORTINIT', true );
	
	require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
	require_once 'inc/DBHandle.php';
	
	global $wpdb;
	
	return new DBHandle($wpdb);
}

/**
 * get paypal rest api sdk options set in admin panel
 * 
 * @return array options
 */
function bbpp_get_options()
{
	$dbh = bbpp_get_dbh_instance();
	
	return array(
		'mode' => $dbh->getOptionValue('ppsdkmode'),
		'clientId' => $dbh->getOptionValue('ppclientId'),
		'clientSecret' => $dbh->getOptionValue('ppclientsecret')
	);
}

function bbpp_get_raintpl_instance()
{
	include_once 'inc/rain.tpl.class.php';
	
	raintpl::configure('tpl_dir', dirname(__FILE__) . '/tpl/');
	raintpl::configure('cache_dir', dirname(__FILE__) . '/tmp/' );
	raintpl::configure( 'path_replace', false );
	
	return new RainTPL();
}