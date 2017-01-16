<?php
/*
Plugin Name: Print Button Shortcode
Plugin URI: http://MyWebsiteAdvisor.com
Description: Shortcode to add a Print Button that prints a specified HTML element.
Version: 1.0.1
Author: MyWebsiteAdvisor
Author URI: http://MyWebsiteAdvisor.com
*/

/*
Print Button Shortcode (Wordpress Plugin)
Copyright (C) 2011 MyWebsiteAdvisor
Contact us at http://MyWebsiteAdvisor.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

//register styles
add_action('wp_enqueue_scripts', 'sc_print_button_load_styles');

function sc_print_button_load_styles() 
{
	wp_register_style(
		'cs_print_button_main_style',
		plugins_url('css/print.css', __FILE__)
	);
	wp_enqueue_style('cs_print_button_main_style');
}

//tell wordpress to register the children_index shortcode
add_shortcode("print-button", "sc_show_print_button");

function sc_show_print_button($atts, $content = null){
		
	$target_element = $atts['target'];
	
	if($target_element == ''){$target_element = "document.body";}
	
	$output = "<div id='print-button' style='background-image: url(".plugins_url('images/print-ico.png', __FILE__).");'></div>
	
	<script type='text/javascript'>

	jQuery(document).ready(function($) {
		$('#print-button').click(function() {
			window.location.href = '#map';
            addNoPrintClass();
			window.print();
            removeNoPrintClass();
		});
        
        function addNoPrintClass() {
            $('.section').addClass('gmnoprint');
            $('#header').addClass('gmnoprint');
            $('#site-pagination').addClass('gmnoprint');
            $('#map').removeClass('gmnoprint');
        }
        
        function removeNoPrintClass() {
            $('.section').removeClass('gmnoprint');
        }
	});"

."	</script>";
	

	return  $output;
}


?>