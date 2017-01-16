<?php 
	
/*
	Plugin Name: B&B Image Map Generator
	Plugin URI:  http://woto-info.com
	Description: Create imagemap with pop-up annotations, editable from admin panel
	Version:     1.0
	Author:      Igor Malinovskiy
	Author URI:  mailto:psy.ipm@gmail.com
	License:     GPLv2
*/

	//scripts
	add_action('wp_enqueue_scripts', 'bbimg_load_scripts');

	function bbimg_load_scripts()
	{
		wp_enqueue_script('jquery');

		//tooltips
		wp_register_script(
			'jquery_tooltipster',
			plugins_url('js/jquery.tooltipster.min.js', __FILE__),
			array("jquery"),
			'1.2.2',
			false
		);
		wp_enqueue_script('jquery_tooltipster');
		
		//imagemap
		wp_register_script(
			'bb_imagemap',
			plugins_url('js/map.image.icon.js', __FILE__),
			array("jquery", "jquery_tooltipster"),
			'1.0',
			false
		);
		wp_enqueue_script('bb_imagemap');		

		if (!is_front_page())
		{
			//imagemap admin init script
			wp_register_script(
				'bb_imagemap_admin_init',
				plugins_url('js/admin_init_script.js', __FILE__),
				array("jquery", "jquery_tooltipster", "bb_imagemap"),
				'1.0',
				false
			);
			wp_enqueue_script('bb_imagemap_admin_init');
		}
		else
		{
			//imagemap admin init script
			wp_register_script(
				'client_init_script',
				plugins_url('js/client_init_script.js', __FILE__),
				array("jquery", "jquery_tooltipster", "bb_imagemap"),
				'1.0',
				false
			);
			wp_enqueue_script('client_init_script');
		}

	}

	//styles
	add_action('wp_enqueue_scripts', 'bbimg_load_styles');

	function bbimg_load_styles()
	{
		//self stylesheet
		wp_register_style( 
			'bb_imagemap_style', 
			plugins_url('css/style.css', __FILE__) 
		);
		wp_enqueue_style( 'bb_imagemap_style' );

		//tooltipster
		wp_register_style( 
			'tooltipster', 
			plugins_url('css/tooltipster.css', __FILE__) 
		);
		wp_enqueue_style( 'tooltipster' );

		//additional stylesheet for tooltips
		wp_register_style( 
			'bb_imagemap_additional_style', 
			plugins_url('css/themes/tooltipster-bb.css', __FILE__)
		);
		wp_enqueue_style( 'bb_imagemap_additional_style' );
	}

	//custom post type
	add_action('init', 'bbimg_custom_post_imagemap');

	function bbimg_custom_post_imagemap()
	{
		$labels = array(
			'name'				 =>		__('ImageMaps' , 'post type general name'),
			'singular_name'		 =>		__('ImageMap', 'post type singular name'),
			'add_new'			 =>		__('Add New', 'imagemap'),
			'add_new_item'		 =>		__('Add New ImageMap', 'imagemap'),
			'edit_item' 		 => 	__('Edit Image', 'imagemap'),
			'new_item' 			 =>		__('New Image', 'imagemap'),
			'view_item' 		 => 	__('View Image', 'imagemap'),
			'search_items' 		 => 	__('Search Images', 'imagemap'),
			'not_found' 		 => 	__('No Image', 'imagemap'),
			'not_found_in_trash' => 	__('No ImageMap Images found in Trash', 'imagemap'),
			'parent_item_colon'  => 	'',
			'menu_name' 		 => 	__('ImageMap', 'imagemap')
		);

			$args = array(
			'labels' => $labels,
			'description' => 'Holds our imagemaps with pop-up annotations',
			'public' => true,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'page',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => 21,
			'supports' => array('title', 'thumbnail', 'page-attributes')
	); 
		
		register_post_type('imagemap', $args);
	}

	//ajax
	add_action('wp_ajax_bbimg_display_map', 'bbimg_ajax_display_map');
	add_action('wp_ajax_nopriv_bbimg_display_map', 'bbimg_ajax_display_map');

	function bbimg_ajax_display_map()
	{
		$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
		
		$img = wp_get_attachment_image_src(
				get_post_thumbnail_id($id),
				'large'
			);

		$args = array(
			'title' => filter_var($_POST['title'], FILTER_SANITIZE_STRING),
			'img' => $img[0],
			'coords' => bbimg_decode_coords(bbimg_get_coords($id))
		);

		die(json_encode($args));
	}

	//add shortcode
	add_shortcode("bb_imagemap", "bbimg_display_map_shortcode_function");

	function bbimg_display_map_shortcode_function($attr, $content)
	{
		extract(shortcode_atts(array(
			'id' => '' //default value if not extracted
		), $attr));

		$img = wp_get_attachment_image_src(
			get_post_thumbnail_id($id),
			"large"
		);

		$html = bbimg_display_image($img);
		$coords = bbimg_get_coords($id);
		$html .= bbimg_display_map($coords);
		
		return $html;
	}

	add_shortcode("bb_imagemaps", "bbimg_display_all_maps_shortcode_function");
	
	function bbimg_display_all_maps_shortcode_function($id = 0)
	{
		$cp = bbimg_get_image_map_posts();

		$html = '<div class="image-map-wrapper">';

		if (count($cp) > 0)
		{
			if ($id == 0)
				$id = $cp[0]['id'];
			$html .= bbimg_display_map_shortcode_function(array('id' => $id), '');
			$html .= bbimg_get_image_map_slider_html($cp);
			
			$html .= '<div class="bbimg-share-wrapper">
					    <div class="share-black">
					</div>
					</div></div>';
			$html .= bbimg_display_tech_details($cp[0]['post_title']);
		}
		else 
		{
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * return html for image map slider
	 * 
	 * @param array $category_posts id => post_id, post_title => post_title
	 * @return string html
	 */
	function bbimg_get_image_map_slider_html(array $category_posts = null)
	{
		if ($category_posts == null)
			$category_posts = bbimg_get_image_map_posts();

		$html = '<ul class="bb_imagemap_slider">';
		
			foreach ($category_posts as $p)
			{
				$html .= '<li class="bb_imagemap_slide">';
					$html .= '<input type="radio" id="map-'.$p['id'].'" name="bb_imagemap_slide" class="bb_imagemap_slide_input" value="'.$p['post_title'].'">';
					$html .= '<span><label for="map-'.$p['id'].'">' . $p['post_title'] . '</label></span>';
				$html .= '</li>';
			}
				
		$html .= '</ul>';
		
		return $html;
	}
	
	/**
	 * get all posts of type imagemap
	 * 
	 * @param string $order ASC or DESC
	 * @return array
	 */
	function bbimg_get_image_map_posts($order="ASC")
	{
		$args = array(
			'post_type' => 'imagemap',
			'orderby' => 'menu_order',
			'order' => $order
		);
		$cp = new WP_Query($args);

		$posts = array();
		foreach ($cp->posts as $p) 
		{
			array_push($posts, array(
					'id' => $p->ID,
					'post_title' => $p->post_title
				)
			);
		}

		return $posts;
	}

	//admin menu
	add_action('admin_menu', 'bbimg_plugin_set_options');

	require_once 'bbimg_admin.php';

	//write debug logs
	if(!function_exists('log_it'))
	{
		function log_it( $message ) 
		{
			if( WP_DEBUG === true )
			{
				if( is_array( $message ) || is_object( $message ) )
				{
	       			error_log( print_r( $message, true ) );
	     		} 
	     		else 
	     		{
	       			error_log( $message );
				}
			}
		}
	}
?>