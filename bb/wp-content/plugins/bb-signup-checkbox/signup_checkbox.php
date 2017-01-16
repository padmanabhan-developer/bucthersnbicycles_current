<?php

/*
	Plugin Name: B&B Signup checkbox
	Plugin URI: http://woto-info.com
	Description: Add checkbox to sign up to receive the news
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

add_action('wp_enqueue_scripts', 'bb_signup_checkbox_load_scripts');

function bb_signup_checkbox_load_scripts()
{
	wp_enqueue_script('jquery');

	wp_register_script(
		'bb_signup_checkbox',
		plugins_url('js/main.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script('bb_signup_checkbox');
}

add_action('wp_enqueue_scripts', 'bb_signup_checkbox_load_styles');

function bb_signup_checkbox_load_styles()
{
	wp_register_style(
		'bb_signup_checkbox',
		plugins_url('css/style.css', __FILE__)
	);
	wp_enqueue_style('bb_signup_checkbox');
}

add_shortcode('bb_signup_checkbox', 'bb_signup_checkbox_shortcode');

function bb_signup_checkbox_shortcode($attr, $content)
{
    extract(shortcode_atts(array(
				'id' => 'bb_signup_checkbox',
				'name' => 'bb_signup_checkbox'
			),
			$attr));
	$userId = get_current_user_id();
	$state = ($userId > 0) ? get_user_meta($userId, 'news_subscription', true) : 'false';
	
	$html = '<div class="bb_signup_checkbox">
				<div class="bb_signup_checkbox_input">
					<input class="bb_signup_checkbox_inp" type="checkbox" id="'.$id.'" name="'.$name.'" checked="'.$state.'">
				</div>
				<div class="bb_signup_checkbox_label">
					<label for="'.$id.'">'.$content.'</label>
				</div>
				<div class="bb_signup_checkbox_response" style="display: none;"></div>
			</div>';
	return $html;
}

add_action('wp_ajax_bb_signup_checkbox_changed', 'bb_signup_checkbox_changed');
add_action('wp_ajax_nopriv_bb_signup_checkbox_changed', 'bb_signup_checkbox_changed');

function bb_signup_checkbox_changed() 
{
	$userId = get_current_user_id();
	
	if ($userId == 0)
		die(json_encode (array('error' => 'You are not logged in.', 'user_id' => $userId)));
	
	$state = filter_var($_POST['state'], FILTER_VALIDATE_INT);
	bb_signup_checkbox_save_state($userId, ($state == 1) ? 'true' : 'false');
	
	die(json_encode(array('success' => 1)));
}

function bb_signup_checkbox_save_state($userId, $state)
{
    if (update_user_meta($userId, 'news_subscription', $state))
		die(json_encode(array('state' => $state, 'success' => 1)));
	else 
	{
		error_log ('bb signup checkbox: could not update usermeta');
		die(json_encode(array('state' => $state, 'error' => 1)));
	}
}