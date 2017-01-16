<?php 

/*
	Plugin Name: B&B delete users
	Plugin URI: http://woto-info.com
	Description: Add button to delete user account, delete user on click
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

add_action('wp_enqueue_scripts', 'bbdelete_user_load_scripts');

function bbdelete_user_load_scripts() 
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	
	wp_register_script(
		'delusr_main',
		plugins_url('js/main.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script('delusr_main');
}

add_action('wp_enqueue_scripts', 'bbdelete_user_load_styles');

function bbdelete_user_load_styles()
{
	wp_register_style(
		'delete_user_main',
		plugins_url('css/delete_user.css', __FILE__)
	);
	wp_enqueue_style('delete_user_main');
}

add_shortcode('bb_delete_user_button', 'bbdelete_user_shortcode');

function bbdelete_user_shortcode($attr, $content)
{
	extract(shortcode_atts(array(
		'class' => '', //default value if not extracted
		'value' => 'Delete Account',
		'confirmation' => 'Are you sure you want to delete your account?'
	), $attr));

	$html = '<input type="button" id="delete_user_account_btn" value="'.$value.'" class="'.$class.'">';
	$html .= '<div id="delete_user_message" class="delete_user_message" style="display: none;"></div>';
	$html .= '<div id="delete_confirm"><p>'.$confirmation.'</p>';
	$html .= '<div class="du-ui-buttons"><input type="button" value="Yes" id="deleteUserConfirmed">'
		. '<input type="button" value="No" id="deleteUserCancel"></div></div>';
	return $html;
}

add_action('wp_ajax_bbdelete_user_ajax', 'bbdelete_user_ajax');
add_action('wp_ajax_nopriv_bbdelete_user_ajax', 'bbdelete_user_ajax');

function bbdelete_user_ajax()
{
	$userId = get_current_user_id();
	
	if ($userId && $userId != 1) {
		wp_logout();
		if(wp_delete_user($userId)) {
			header("Location: /");
			die();
		}
	}
	
	die(json_encode (array('error' => 'cannot delete current user')));
}

?>