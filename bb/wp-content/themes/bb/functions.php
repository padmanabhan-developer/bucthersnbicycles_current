<?php 

class Description_Walker extends Walker_Nav_Menu{
      function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0){
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
           $class_names = $value = '';
           $classes = empty( $item->classes ) ? array() : (array) $item->classes;
           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';
           $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
           $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
           $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
           $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
           if($item->object == 'page')
           {
                $varpost = get_post($item->object_id);
                if(is_home()){
                  $attributes .= ' href="#' . $varpost->post_name . '"';
                }else{
                  $attributes .= ' href="'.home_url().'#' . $varpost->post_name . '"';
                }
           }
           else
                $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID );
            $item_output .= $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
     }
}

add_theme_support('menus'); 
add_post_type_support( 'post', 'page-attributes' );
add_post_type_support( 'page', 'page-attributes' );

//fight evil closing <p> tags
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'shortcode_unautop' );

/**
 * scripts user side
 */

add_action('wp_enqueue_scripts', 'bb_load_scripts');

function bb_load_scripts() {

  //dropdown menus
  wp_register_script(
    'jquery_dropotron', 
    get_template_directory_uri() . '/js/jquery.dropotron.min.js',
    array("jquery"),
    '1.4',
    false
  );
  wp_enqueue_script('jquery_dropotron');

  //scroll animations
  wp_register_script(
    'jquery_easing', 
    get_template_directory_uri() . '/js/jquery.easing.1.3.js',
    array("jquery"),
    '1.3',
    false
  );
  wp_enqueue_script('jquery_easing');
  
  wp_register_script(
		'bb_init',
		get_template_directory_uri() . '/js/init.js',
		array('jquery')
	);
  
//  if(is_page('index'))
	  wp_enqueue_script('bb_init');
  
//  wp_register_script(
//		'bb_init_shop',
//		get_template_directory_uri() . '/js/init_shop.js',
//		array('jquery')
//	);
//  if(is_page('shop'))
//	  wp_enqueue_script ('bb_init_shop');

  wp_register_script(
    'jquery-ui',
    'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js',
    array("jquery"),
    '1.9.1',
    false
  );
  wp_enqueue_script('jquery-ui');

//   wp_register_script(
//     'jquery_rigthMenu',
//     get_template_directory_uri() . '/js/jquery.rigthMenu.js',
//     array("jquery")
//   );
//   wp_enqueue_script('jquery_rigthMenu');

  //scroll animations
  // wp_register_script(
  //   'slimscroll_js', 
  //   get_template_directory_uri() . '/vendors/jquery.slimscroll.min.js',
  //   array("jquery"),
  //   '1.3',
  //   false
  // );
  // wp_enqueue_script('slimscroll_js');

  //responsive menu
  wp_register_script(
    'jquery_meanmenu',
    get_template_directory_uri() . '/js/jquery.meanmenu.js',
    array("jquery")
  );
  wp_enqueue_script('jquery_meanmenu');

  // wp_register_script(
  //   'jquery_fullPage',
  //   get_template_directory_uri() . '/js/jquery.fullPage.js',
  //   array("jquery")
  // );
  // wp_enqueue_script('jquery_fullPage');

}

/**
 * styles user side
 */

add_action('wp_enqueue_scripts', 'bb_load_styles');

function bb_load_styles()
{
  //main stylesheet
  wp_register_style( 
    'bb_main_styles', 
    get_template_directory_uri() . '/style.css'
  );
  wp_enqueue_style( 'bb_main_styles' );

  //stylesheet for jquery.fullpage
  wp_register_style( 
   'jquery_fullpage_styles', 
    get_template_directory_uri() . '/css/jquery.fullPage.css'
  );
  wp_enqueue_style( 'jquery_fullpage_styles' );

  //stylesheet for jquery.meanmenu
  wp_register_style( 
   'jquery_meanmenu_styles', 
    get_template_directory_uri() . '/css/meanmenu.css'
  );
  wp_enqueue_style( 'jquery_meanmenu_styles' );

  //jqueryui
  wp_register_style(
    'jquery_ui_custom',
    get_template_directory_uri() . '/css/jquery-ui-1.10.4.custom.min.css'
  );
  wp_enqueue_style('jquery_ui_custom');

  //some additional styles
    wp_register_style( 
   'bb_additional_styles', 
    get_template_directory_uri() . '/css/style.css'
  );
  wp_enqueue_style( 'bb_additional_styles' );

  //styles for shop page
  wp_register_style( 
   'bb_shop_styles', 
    get_template_directory_uri() . '/css/shop.css'
  );
  wp_enqueue_style( 'bb_shop_styles' );
}

/**
 * Custom CSS
 */
add_action('wp_head', 'bb_generate_custom_css');

function bb_generate_custom_css()
{	
	$colors = get_option('bb_theme_options_colors');

	if (count($colors) > 0)
	{
		include_once 'libs/customcss.php';
		use_custom_colors($colors);
	}	
}

/**
 * scripts and styles admin side
 */

if(isset($_GET['page']) && $_GET['page'] == 'bb_manage_theme_settings')
{
  add_action('admin_enqueue_scripts', 'bb_enqueue_admin_scripts');
}

function bb_enqueue_admin_scripts($hook_suffix)
{
  wp_register_script('bb_admin', get_template_directory_uri() . '/js/admin.js', array('jquery', 'thickbox', 'media-upload', 'jquery-ui-core', 'jquery-ui-tabs'));
  wp_register_style('bb_admin', get_template_directory_uri() . '/css/admin.css');

  wp_enqueue_script('jquery');

  wp_enqueue_script('thickbox');
  wp_enqueue_style('thickbox');

  wp_enqueue_script('media-upload');

  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-tabs');
  wp_register_style(
    'jquery-ui-smoothness',
    get_template_directory_uri() . '/css/jquery-ui-smoothness.css'
  );
  wp_enqueue_style('jquery-ui-smoothness');

  wp_enqueue_script('bb_admin');
  wp_enqueue_style('bb_admin');
}

/**
 * admin menu
 */

if(is_admin()) 
{
  add_action('admin_menu', 'bb_manage_admin_menu');
}

function bb_manage_admin_menu() 
{
  global $theme_name;

  add_theme_page('Theme Options', 'Theme Options', 'administrator', 'bb_manage_theme_settings', 'bb_manage_theme_settings');
}

function bb_manage_theme_settings()
{
  if(isset($_POST['settings_logo_submit']))
  {
    bb_submit_display_response($_POST['settings_logo_submit'], 'bb_theme_settings_logo');
  }
  else if(isset($_POST['settings_colors_submit']))
  {
    bb_submit_display_response($_POST['settings_colors_submit'], 'bb_theme_settings_colors');
  }
  else if(isset($_POST['settings_menu_items_submit']))
  {
    bb_submit_display_response($_POST['settings_menu_items_submit'], 'bb_theme_settings_menu_items');
  }
  else
  {
    include_once('theme_options_admin_layout.html');
  }
}

function bb_submit_display_response($nonce, $action=-1)
{
    if(!wp_verify_nonce($nonce, $action))
    {
      echo "<h2>Something goes wrong</h2>";
    }
    else
    {
      $options = $_POST;
      foreach ($options as $opt => $value) 
      {
        if(is_array($value))
          update_option($opt, $value);
        else
          update_option($opt, trim($value));
      }

      echo "<h2>Setting has been saved</h2>";
    }
}

function bb_theme_get_menu_anchor($str)
{
	return strtolower(preg_replace('/.,+-\s+/', '_', $str));
}

?>