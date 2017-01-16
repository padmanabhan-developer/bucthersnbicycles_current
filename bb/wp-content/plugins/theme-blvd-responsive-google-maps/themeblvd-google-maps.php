<?php
/*
Plugin Name: Theme Blvd Responsive Google Maps
Description: Add responsive google maps to your pages and posts via shortcode [tb_google_map].
Version: 1.0.2
Author: Jason Bobich
Author URI: http://jasonbobich.com
License: GPL2
*/

/*
Copyright 2012 JASON BOBICH

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Scripts
 */

function themeblvd_google_map_scripts() {
	// Register 'em
	wp_register_script( 'gmap', plugins_url( 'assets/jquery.gmap.min.js', __FILE__ ), array('jquery'), '3.0' );
    
    // Enque 'em
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'gmap' );
}
add_action( 'wp_enqueue_scripts', 'themeblvd_google_map_scripts' );

/**
 * CSS
 */

function themeblvd_google_map_css() {		
	wp_register_style( 'themeblvd_gmap', plugins_url( 'assets/style.css', __FILE__ ), false, '1.0' );
	wp_enqueue_style( 'themeblvd_gmap' );
}
add_action( 'wp_print_styles', 'themeblvd_google_map_css' );

/**
 * Shortcode - [tb_google_map]
 */

function themeblvd_google_map_shortcode( $atts ) {
	
	extract( shortcode_atts( array(
		'maptype' 	=> 'roadmap',  	// hybrid, satellite, roadmap, terrain
		'zoom'		=> 14,			// 1-19
		'address'	=> '',			// Ex: 6921 Brayton Drive, Anchorage, Alaska
		'html'		=> '',			// Will default to Address if left empty
		'popup'		=> 'true',		// true/false
		'width'		=> '',			// Leave blank for 100%, need to use 'px' or '%'
		'height'	=> '500px'		// Need to use 'px' or '%'
	), $atts ) );
	
	// Map type
	$maptype = strtoupper( $maptype );
	
	// HTML
	if( ! $html )
		$html = $address;
		
	// Width/Height
	$styles = 'height:'.$height;
	if( $width )
		$styles .= ';width:'.$width;
	
	// Unique ID
	$id = rand();
	
	// Start output
	ob_start();
	?>
	<!-- INCLUDE GOOGLE MAP API (can't be enqueue'd by WP) -->	
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	
	<!-- RUN gMap() -->
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#tb_gmap_<?php echo $id; ?>").gMap({
			maptype: "<?php echo $maptype; ?>",
			zoom: <?php echo $zoom; ?>,
			markers: [
				{
					address: "<?php echo $address; ?>",
					popup: <?php echo $popup; ?>,
					html: "<?php echo $html; ?>"
				}
			],
			controls: {
				panControl: true,
				zoomControl: true,
				mapTypeControl: true,
				scaleControl: true,
				streetViewControl: false,
				overviewMapControl: false
			}
		});
	});
	</script>
	<div id="tb_gmap_<?php echo $id; ?>" class="themeblvd-gmap" style="<?php echo $styles; ?>"></div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tb_google_map', 'themeblvd_google_map_shortcode' );