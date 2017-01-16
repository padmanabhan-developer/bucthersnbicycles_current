<?php
/* Plugin Name: Bicycle constructor
Description: Contructor for bicycles
Version: 1.0
Author: Nesvit Evgen
License: GPLv2 or later
*/

//styles user side
add_action('wp_enqueue_scripts', 'goods_constructor_load_user_styles');

function goods_constructor_load_user_styles()
{
	wp_register_style(
		'bb_constructor_main', 
		plugins_url('/tpl/css/bb_constructor_main.css', __FILE__)
	);
	wp_enqueue_style('bb_constructor_main');
	
	wp_register_style(
		'bb_welcome_page', 
		plugins_url('/tpl/css/bb_welcome_page.css', __FILE__)
	);
	wp_enqueue_style('bb_welcome_page');
}

//scripts user side
add_action( 'init', 'goods_constructor_register_user_scripts' );

function goods_constructor_register_user_scripts()
{
	wp_enqueue_script('gc_calculator',	plugins_url('/tpl/js/calculator.js', __FILE__), array('jquery'));
	wp_enqueue_script('gc_main',	plugins_url('/tpl/js/main.js', __FILE__), array('jquery','gc_calculator'));
	wp_enqueue_script('gc_configuration_info',	plugins_url('/tpl/js/configuration_info.js', __FILE__), array('jquery','gc_calculator', 'gc_main'));
	//wp_enqueue_script('gc_main', plugins_url('/tpl/js/main.js', __FILE__), array('jquery'));
}

//styles and scripts admin side
if (isset($_GET['page']) && $_GET['page'] == 'goods_constructor_admin')
{
	add_action('admin_print_scripts', 'goods_constructor_register_admin_scripts');
	add_action('admin_print_styles', 'goods_constructor_register_admin_styles');
}

function goods_constructor_register_admin_scripts()
{
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-ui-tabs');
	//wp_enqueue_script('gc_main', plugins_url('/tpl/js/main.js', __FILE__), array('jquery','gc_calculator', 'gc_main'));
	wp_register_script('gc_bicycle_constructor', plugins_url('tpl/js/constructor.js', __FILE__), array('jquery', 'media-upload', 'thickbox'));
	wp_enqueue_script('gc_bicycle_constructor');
	wp_register_script('gc_admin', plugins_url('tpl/js/admin.js', __FILE__), array('jquery', 'media-upload', 'thickbox', 'jquery-ui-core', 'jquery-ui-dialog'));
	wp_enqueue_script('gc_admin');
}

function goods_constructor_register_admin_styles()
{
	wp_enqueue_style('thickbox');
	
	wp_register_style(
		'jquery-ui-smoothness',
		plugins_url('tpl/css/jquery-ui-smoothness.css', __FILE__)
	);
	wp_enqueue_style('jquery-ui-smoothness');
	
	wp_register_style(
		'bc_admin', 
		plugins_url('tpl/css/admin.css', __FILE__)
	);
	wp_enqueue_style('bc_admin');
}

/**
 * Shortcodes
 */
add_shortcode("goods_constructor", "goods_display_constructor_shortcode_function");

function goods_display_constructor_shortcode_function($attr=null, $content=null)
{	
	return get_goods_constructor_instance()->getHtml();
}

add_shortcode("user_configurations", "goods_constructor_display_user_confs");

function goods_constructor_display_user_confs()
{
	$userId = get_current_user_id();
	
	if (!$userId)
		return;
	$saved = get_user_saved_configurations_instance();
	
	return $saved->getUserConfHtml($userId);
}

add_shortcode('bb_shipping_costs', 'goods_constructor_shipping_shortcode');

function goods_constructor_shipping_shortcode()
{
	return get_delivery_costs_instance()->getShippingOptionsShortcodeHtml();
}

/**
 * AJAX
 */
require_once 'goods_constructor_ajax.php';

/**
 * admin menu
 */
add_action('admin_menu', 'goods_constructor_admin_settings');

require_once 'goods_constructor_admin.php';

/**
 * helper functions
 */
require_once 'goods_constructor_helpers.php';

?>