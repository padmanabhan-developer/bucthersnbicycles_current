<?php 

/*
	Plugin Name: B&B PayPal payments
	Plugin URI: http://woto-info.com
	Description: Accept payments via PayPal
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/	

/**
 * PayPal
 */
require 'inc/vendor/autoload.php';

/**
 * helpers
 */
require_once 'bb-paypal-helpers.php';

/**
 * scripts
 */
add_action('wp_enqueue_scripts', 'bbpp_load_scripts');

function bbpp_load_scripts()
{
	wp_enqueue_script('jquery');
	
	wp_register_script(
		'bbpp_main',
		plugins_url('tpl/js/bbpp_main.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script('bbpp_main');
}

/**
 * styles
 */
add_action('wp_print_styles', 'bbpp_load_styles');

function bbpp_load_styles() 
{
	wp_register_style(
		'canvas_loader', 
		plugins_url('tpl/css/canvas_loader.css', __FILE__)
	);
	wp_enqueue_style('canvas_loader');
}

/**
 * scripts admin side
 */
if (isset($_GET['page']) && $_GET['page'] == 'bb_paypal_settings')
{
	add_action('admin_print_scripts', 'bbpp_register_admin_scripts');
	add_action('admin_print_scripts', 'bbpp_load_scripts');
	add_action('admin_print_styles', 'bbpp_register_admin_styles');
}

function bbpp_register_admin_scripts()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-accordion');
}

/**
 * styles admin side
 */
function bbpp_register_admin_styles()
{
	wp_register_style(
		'jquery-ui-smoothness',
		plugins_url('tpl/css/jquery-ui-smoothness.css', __FILE__)
	);
	wp_enqueue_style('jquery-ui-smoothness');
}

/**
 * shortcodes
 */
add_shortcode('bb_paypal', 'bbpp_display_shortcode');

function bbpp_display_shortcode($attr=null, $content=null)
{
	extract(shortcode_atts(array(
		'value' => 'Proceed to checkout',
		'id' => '1',
		'class' => ''
	), $attr));
	$html = '<input type=button class="bbpaypal-btn '.$class.'" id="bb-paypal-btn'.$id.'" value="'.$value.'">';
	
	return $html;
}

/**
 * AJAX
 */
add_action('wp_ajax_proceed_to_checkout', 'bbpp_proceed_to_checkout');

require_once 'inc/PayPalPayment.php';

function bbpp_proceed_to_checkout()
{
	$opt = bbpp_get_options();
	$conf_data = filter_var_array($_POST);
	
	$p = new PayPalPayment($opt);
	$descr = $conf_data['conf_name'] . '; Total Cost: ' . $conf_data['amount'] . ' ' . $conf_data['currency'] . '; Saved on ' . $conf_data['conf_saved_date'];
	$p->setAmount(
		$conf_data['currency'], 
		number_format($conf_data['amount'], 2), 
		substr($descr, 0, 126)
	);
	$baseUrl = plugins_url('index.php', __FILE__);
	$p->setRedirectUrls( $baseUrl . '?cancel=true', $baseUrl . '?success=true' );
	$data = $p->createPayment();
	
	if(isset($data['success'])) 
	{
		$conf_data['order_created_time'] = $p->getCreateTime();
		bbpp_get_dbh_instance()->saveOrderData(array_merge_recursive($data, $conf_data));
		
		$data['conf_id'] = $conf_data['user_conf_id'];
		bbpp_display_success_json ($data);
	}
	else
		bbpp_display_error_json ("Cannot complete the payment, please try again later");
}

/**
 * admin page
 */
require_once 'bbpp_admin.php';

?>