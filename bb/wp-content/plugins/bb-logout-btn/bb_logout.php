<?php

/*
	Plugin Name: B&B logout button
	Plugin URI: http://woto-info.com
	Description: Add button to log out current user
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

add_shortcode('bb_logout_btn', 'bb_logout_btn_shortcode');

function bb_logout_btn_shortcode($attr, $content)
{
	extract(shortcode_atts(array(
		'class' => '', //default value if not extracted
		'value' => 'Log out',
	), $attr));
	
	$html = '<input type="button" value="'.$value.'" class="bb_logout_btn '.$class.'">';
	$html .= '<script type="text/javascript">
		jQuery(".bb_logout_btn").click(function() {
			window.location.href = "'.  htmlspecialchars_decode(wp_logout_url(home_url())).'"
		});
		</script>';
	
	return $html;
}