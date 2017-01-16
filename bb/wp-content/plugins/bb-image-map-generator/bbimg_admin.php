<?php 
	
/*
	Admin panel settings page for B&B Image Map Generator plugin

	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

	function bbimg_plugin_set_options()
	{
		bbimg_load_scripts();

		bbimg_load_styles();
	}

	// Add theme support for featured images if not already present
	// http://wordpress.stackexchange.com/questions/23839/using-add-theme-support-inside-a-plugin
	add_action( 'after_setup_theme', 'bbimg_addFeaturedImageSupport');
	
	function bbimg_addFeaturedImageSupport() {
		$supportedTypes = get_theme_support( 'post-thumbnails' );
		if( $supportedTypes === false ) {
			add_theme_support( 'post-thumbnails', array( 'imagemap' ) );      
			add_image_size('featured_preview', 100, 55, true);
		} elseif( is_array( $supportedTypes ) ) {
			$supportedTypes[0][] = 'imagemap';
			add_theme_support( 'post-thumbnails', $supportedTypes[0] );
			add_image_size('featured_preview', 100, 55, true);
		}
	}
	
	// Add column in admin list view to show featured image
	// http://wp.tutsplus.com/tutorials/creative-coding/add-a-custom-column-in-posts-and-custom-post-types-admin-screen/
	function bbimg_get_featured_image($post_ID) {  
		$post_thumbnail_id = get_post_thumbnail_id($post_ID);  
		if ($post_thumbnail_id) {  
			$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');  
			return $post_thumbnail_img[0];  
		}  
	}
	function bbimg_columns_head($defaults) {  
		$defaults['featured_image'] = 'Featured Image';  
		$defaults['shortcode'] = 'Shortcode';
		return $defaults;  
	}  
	function bbimg_columns_content($column_name, $post_ID) {  
		if ($column_name == 'featured_image') {  
			  
		}  

		switch ($column_name) {
			case 'featured_image':
				$post_featured_image = bbimg_get_featured_image($post_ID);  
				if ($post_featured_image) {  
					echo '<a href="'.get_edit_post_link($post_ID).'"><img src="' . $post_featured_image . '" /></a>';  
				}
				break;
			
			case 'shortcode': 
				echo "[bb_imagemap id='$post_ID' /]";
				break;

			default:
				# code...
				break;
		}
	}
	add_filter('manage_imagemap_posts_columns', 'bbimg_columns_head');  
	add_action('manage_imagemap_posts_custom_column', 'bbimg_columns_content', 10, 2);
	
	//add metaboxes
	add_action('add_meta_boxes', 'bbimg_post_meta_boxes_setup');

	//metabox setup function
	function bbimg_post_meta_boxes_setup()
	{
		//show featured image
		add_meta_box(
			'bbimg-featured-image',				//Unique ID
			__('Featured Image', 'imagemap'),	//Title
			'bbimg_featured_image_meta_box',	//Callback
			'imagemap',							//Post type
			'normal',							//Context
			'default'							//Priority
		);

		//show map shapes
		add_meta_box(
			'bbimg-map-shapes-metabox',			//Unique ID
			__('Map Shapes', 'imagemap'),		//Title
			'bbimg_map_shapes_meta_box',		//Callback
			'imagemap',							//Post type
			'normal',							//Context
			'default'							//Priority
		);
	}

	function bbimg_featured_image_meta_box($object, $box)
	{
		$img_url = wp_get_attachment_image_src(
			get_post_thumbnail_id($object->ID),
			"large"
		);

		$coords = bbimg_get_coords($object->ID);
		
		echo bbimg_display_image($img_url);

		echo bbimg_display_map($coords);
	}

	function bbimg_display_image($img_url)
	{
		$html = '<div class="map-image-holder">'
			  . '<img class="bb_im_featured" src="'.$img_url[0].'" alt="imagemap"/>'
			. '</div>';

		return $html;
	}
	
	function bbimg_decode_coords($coords)
	{
		if (!isset($coords))
			$coords = array();

		$decoded = array();
		foreach ($coords as $c) 
		{
			array_push($decoded, 
				array(
					'x' => $c['x'], 
					'y' => $c['y'], 
					'text' => stripslashes(
						base64_decode($c['text'])
					)
				)
			);
		}

		return $decoded;
	}

	function bbimg_display_map($coords) 
	{
		$shapes = bbimg_decode_coords($coords);

		$html = '<script type="text/javascript">'
					. 'var iconSet = ' . json_encode($shapes) . ';'
					. '	function showModal(selector) {
							jQuery( selector ).dialog({
								height: 760,
								width: 670,
								modal: true
							});
							return false;
						}' 
				. '</script>';

		return $html;
	}
	
	function bbimg_display_tech_details($name)
	{
		$html = '<div class="about-text-wrapper">'
				. '<div class="section-text descr-right">'
					. '<p style="font-size: 15pt; font-weight: bold;">'
						// . 'SEE THE COMPLETE TECHNICAL SPECIFICATION HERE '
						. '<a href="#" class="display_tech" data-name="'.$name.'">'
							// . '<img style="height: 25px; margin: 0 0 -5px 10px;" src="'.plugins_url('images/icon.png', __FILE__).'">'
						. 'SEE THE COMPLETE TECHNICAL SPECIFICATION HERE '
						. '</a>'
					. '</p>'
				. '</div>'
				. '</div>';
		
		$html .= bbimg_display_tech_dialog();

		return $html;
	}
	
	function bbimg_display_tech_dialog()
	{
		$html = '<div class="gc-conf-detail dialog tech-dialog" style="display:none;">
					<div class="gc-standard left"> 
						<p class="gc-headconf-name tech-dialog-head"> Standard on <span class="techdialog-mpname"></span></p> 
						<div class="gc-main-descr techdialog-mpdescr">
						</div>
					</div>
					<div class="gc-added right">
						<p class="gc-headconf-name tech-dialog-head">Extra:</p>
						<div class="external_parts_descr techdialog-epdescr">
						</div>
					</div>
				</div>';
		
		return $html;
	}

	function bbimg_map_shapes_meta_box($object, $box) 
	{
		$nonce = wp_create_nonce('add_shape');

		//get meta as assoc array
		$coords = bbimg_get_coords($object->ID);

		?>
			<form action="" method="post">
				<div class="map-info-holder">
					<h4>Map info</h4>
        			<div class="map-debug-holder"></div>
        			<div class="map-points-holder">
						<?php 

							if(count($coords) > 0) 
							{
								for ($i=0; $i < count($coords); $i++) { 
									$text = stripslashes(htmlspecialchars(base64_decode($coords[$i]['text'])));
									
									$html = '<div><input type="text" name="iconCoords[' .$i. '][x]" value="' .$coords[$i]['x']. '"/>'
							                    . '<input type="text" name="iconCoords[' .$i. '][y]" value="' .$coords[$i]['y']. '"/>'
							                    . '<input type="text" name="iconCoords[' .$i. '][text]" value="'.$text.'"/>'
							                    . '<input type="button" data-icon-id="' .$i. '" value="-"/>'
							                . '</div>';

									echo $html;
								}
							}
						?>
					</div>
				</div>
				<?php wp_nonce_field('bb_img_position', 'imagemap_submit') ?>
			</form>
		<?php
	}

	//fetch map shapes from database
	function bbimg_get_coords($post_id)
	{
		return json_decode(get_post_meta($post_id, 'coords',true), true);
	}

	add_action('save_post', 'bbimg_save_post_meta');

	//save coords to database as json string
	function bbimg_save_post_meta($post_id)
	{
		if (isset($_POST['imagemap_submit']))
		{
			if(!wp_verify_nonce($_POST['imagemap_submit'], 'bb_img_position'))
			{
				echo "<h2>Something goes wrong</h2>";
			}
			else
			{
				log_it($_POST);
				$coords = array();

				if (isset($_POST['iconCoords']))
				{
					$post_filtered = filter_var_array($_POST['iconCoords']);

					foreach ($post_filtered as $c) 
					{
						array_push($coords, array('x' => $c['x'], 'y' => $c['y'], 'text' => base64_encode($c['text'])));
					}
				}

				update_post_meta($post_id, 'coords', json_encode($coords));
			}
		}
	}
?>