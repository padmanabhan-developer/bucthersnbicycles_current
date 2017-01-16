<?php

/**
  	Plugin Name: B&B Vimeo Gallery
	Plugin URI: http://woto-info.com
	Description: Get videos from Vimeo channel and display gallery
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
 */

/**
 * Shortcodes
 */
add_shortcode('bb_vimeo_gallery', 'bb_vimeo_gallery_shortcode');

function bb_vimeo_gallery_shortcode($attr, $content)
{
	$tpl = bb_vimeo_get_raintpl_instance();
	bb_vimeo_assign_videos($tpl);
	$tpl->assign('bb_vimeo_other_videos', bb_vimeo_gallery_get_other_videos_html());
	return $tpl->draw('client', true);
}

/**
 * Scripts
 */
add_action('wp_enqueue_scripts', 'bb_vimeo_gallery_load_scripts');

function bb_vimeo_gallery_load_scripts()
{
	wp_enqueue_script('jquery');
	
	wp_register_script(
		'responsive_video_embed',
		plugins_url('tpl/js/jquery.fitvids.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script('responsive_video_embed');
	
    wp_register_script(
		'bbvideo_gallery',
		plugins_url('tpl/js/video_gallery.js', __FILE__), 
		array('jquery')
	);
	wp_enqueue_script('bbvideo_gallery');
}

/**
 * Styles
 */
add_action('wp_enqueue_scripts', 'bb_vimeo_gallery_load_styles');

function bb_vimeo_gallery_load_styles()
{
	wp_register_style('bb_vimeo_gallery_styles', plugins_url('tpl/css/style.css', __FILE__));
	wp_enqueue_style('bb_vimeo_gallery_styles');
}

/**
 * scripts and styles admin side
 */
if (isset($_GET['page']) && $_GET['page'] == 'bb_vimeo_gallery_settings')
{
	add_action('admin_print_scripts', 'bb_vimeo_gallery_load_admin_scripts');
}

function bb_vimeo_gallery_load_admin_scripts()
{
	wp_register_script(
		'bb_vimeo_admin',
		plugins_url('tpl/js/bbvg_admin.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script('bb_vimeo_admin');
}

/**
 * Helpers
 */
function bb_vimeo_get_raintpl_instance()
{
	if (!class_exists('RainTPL'))
		include_once 'inc/rain.tpl.class.php';
	
	raintpl::configure('tpl_dir', dirname(__FILE__) . '/tpl/');
	raintpl::configure('cache_dir', dirname(__FILE__) . '/tmp/' );
	raintpl::configure( 'path_replace', false );
	
	return new RainTPL();
}

/**
 * Admin menu
 */
add_action('admin_menu', 'bb_vimeo_plugin_set_options');

function bb_vimeo_plugin_set_options()
{
	add_options_page(
		'B&B Vimeo Gallery', 
		'B&B Vimeo Gallery', 
		'manage_options', 
		'bb_vimeo_gallery_settings', 
		'bb_vimeo_gallery_settings_page'
	);
}

function bb_vimeo_gallery_settings_page()
{
	if(isset($_POST['settings_submit']))
	{
		if(!wp_verify_nonce($_POST['settings_submit'], 'bb_vimeo_settings'))
		{
			echo "<h2>Something goes wrong</h2>";
		}
		else
		{
			if(isset($_POST['bb_vimeo_other_videos']))
			{
				update_option('bb_vimeo_other_videos', json_encode($_POST['bb_vimeo_other_videos']));
				unset($_POST['bb_vimeo_other_videos']);
			}
			
			foreach ($_POST as $opt => $value) 
			{
				update_option($opt, trim($value));
			}
			
			echo "<h2>Settings has been saved</h2>";
		}
	}
	else
	{
		$tpl = bb_vimeo_get_raintpl_instance();
		bb_vimeo_assign_admin_options($tpl);
		bb_vimeo_assign_videos($tpl);
		$tpl->draw('admin');
	}
}

function bb_vimeo_assign_admin_options(RainTPL $tpl)
{
	$tpl->assign('nonce_field', wp_nonce_field('bb_vimeo_settings', 'settings_submit'));
	bb_vimeo_gallery_assign_other_videos($tpl);
}

function bb_vimeo_assign_videos($tpl)
{
	$tpl->assign('bb_vimeo_primary_video', get_option('bb_vimeo_primary_video'));
	$tpl->assign('bb_vimeo_primary_title', get_option('bb_vimeo_primary_title'));
	$tpl->assign('bb_vimeo_primary_descr', get_option('bb_vimeo_primary_descr'));
	$tpl->assign('bb_other_videos_caption', get_option('bb_other_videos_caption'));
}

function bb_vimeo_gallery_get_other_videos()
{
	return json_decode(get_option('bb_vimeo_other_videos'), true);
}

function bb_vimeo_gallery_get_other_videos_html()
{
	$videos = bb_vimeo_gallery_get_other_videos();
	$html = '';
	
	if (count($videos) < 3)
		return $html;
	
	foreach ($videos as $v) 
	{
		$img = bb_vimeo_gallery_get_video_thumb($v['id']);
		
		$html .= '<div class="other_video">
					<figure>
						<img src='.$img.' data-video-id="'.$v['id'].'"/>
                        <div class="play-overlay">
                            <div class="play-icon">
                              <svg viewBox="0 0 20 20" preserveAspectRatio="xMidYMid">
                                <polygon class="fill" points="1,0 20,10 1,20"></polygon>
                              </svg>
                            </div>
                          </div>
					</figure>
					<div class="other_video_headline" id="'.$v['id'].'_headline">'.$v['title'].'</div>
				</div>';
	}
	return $html;
}

function bb_vimeo_gallery_get_video_thumb($id)
{	
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://vimeo.com/api/v2/video/'.$id.'.json',
	));
	
	$resp = curl_exec($ch);
	curl_close($ch);
	
	$res = json_decode($resp, true);
	
	return $res[0]['thumbnail_large'];
}

function bb_vimeo_gallery_assign_other_videos(RainTPL $tpl)
{
	$videos = bb_vimeo_gallery_get_other_videos();
	$html = '';
	
	if (count($videos) == 0) 
	{
		$tpl->assign('other_videos_input', $html);
		return;
	}
		
	for($i=0; $i<count($videos); $i++)
	{
		$html .= '<div class="other_video_inp" id="other_video_inp_'.$i.'">'
				. '<label for="bb_vimeo_other_videos['.$i.'][id]">ID:</label>'
				. '<input type="text" name="bb_vimeo_other_videos['.$i.'][id]" value="'.$videos[$i]['id'].'">'
				. '<label for="bb_vimeo_other_videos['.$i.'][title]">Headline:</label>'
				. '<input type="text" name="bb_vimeo_other_videos['.$i.'][title]" value="'.$videos[$i]['title'].'">'
                . '<input type="button" class="remove_other_video_inp" value="Remove" data-inp-id="'.$i.'">'
				. '</div>';
	}
	
	$tpl->assign('other_videos_input', $html);
}