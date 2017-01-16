<?php

/*
	Plugin Name: B&B Social Share dialog
	Plugin URI: http://woto-info.com
	Description: Display social share popup dialog
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/	

/**
 * Scripts
 */
add_action('wp_enqueue_scripts', 'bbss_load_scripts');

function bbss_load_scripts()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	wp_register_script('bb_share_dialog', plugins_url('/js/main.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'));
	wp_enqueue_script('bb_share_dialog');
}

/**
 * Styles
 */
add_action('wp_enqueue_scripts', 'bb_social_share_load_styles');

function bb_social_share_load_styles()
{
	wp_register_style('bb_social_share_style', plugins_url('/css/share.css', __FILE__));
	wp_enqueue_style('bb_social_share_style');
}

/**
 * Shortcode
 */
add_shortcode('bb_social_share_dialog', 'bbss_display_shortcode');

function bbss_display_shortcode()
{
	$html = '<div id="bbshare_popup_dialog" style="display: none;">';
		$html .= '<p>
			<a href="#" class="pop share-square share-square-facebook"></a>
			<a href="#" class="pop share-square share-square-twitter"></a>
			<a href="#" class="pop share-square share-square-googleplus"></a>
			<a href="#" class="pop share-square share-square-stumbleupon"></a>
			<a href="#" class="pop share-square share-square-pinterest"></a>
			</p>';
	$html .= '</div>';
	
	return $html;
}