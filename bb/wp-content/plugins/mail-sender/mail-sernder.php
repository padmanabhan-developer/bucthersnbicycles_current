<?php
/* Plugin Name: B&B Email sender
Description: Send emails to subscribers
Version: 1.0
Author: Nesvit Evgen
License: GPLv2 or later
*/
/*
add_action('admin_menu', 'create_send_mail_panel');

//styles and scripts admin side
if (isset($_GET['page']) && $_GET['page'] == 'email-send-panel')
{
	add_action('admin_print_scripts', 'mail_sender_register_admin_scripts');
}

function mail_sender_register_admin_scripts()
{
	wp_register_script('mail_sender', plugins_url('js/mail-sender.js', __FILE__), array('jquery',));
	wp_enqueue_script('mail_sender');
}

function create_send_mail_panel() {
    add_menu_page('menu page', 'Send email', 'manage_options', 'email-send-panel', 'custom_panel');
}


function custom_panel(){
	echo '<div class="wrap">
			<div id="icon-options-general" class="icon32">
			<br>
			</div>
			<h2>Send email to subscribers</h2></div>
			<h3>Theme<h3>
			
			<input id=\'theme\' style="width:400px;">
			
			<br />
			<br />
			
			<div>';
				
	wp_editor('', 'email_text');
				
	echo '<button id=\'send_mail_btn\'>Send</button></div>';
}*/

function send_email_to_subscribers()
{
	
	global $wpdb;
	
	$result = $wpdb->get_results('select u.user_email from wp_users u, wp_usermeta m
						where u.ID = m.user_id
						and m.meta_key = \'news_subscription\'
						and m.meta_value = \'true\'', ARRAY_A);
	
	if(count($result) == 0)
		return;
	
	require __DIR__ . '/PHPMailer-master/PHPMailerAutoload.php';
	
	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	//Set who the message is to be sent from
	//$mail->setFrom('from@example.com', 'First Last');
	//Set an alternative reply-to address
	foreach ($result as $email)
	{
		$mail->AddBCC($email['user_email']);
	}
	
	//Set who the message is to be sent to
	//$mail->addAddress('whoto@example.com', 'John Doe');
	//Set the subject line
	$mail->Subject = $_POST['post_title'];
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$body = stripslashes($_POST['post_content']);
	$mail->isHTML(TRUE);
	$mail->Body = $body;
	
	$mail->send();
}

//custom post type
add_action('init', 'bbimg_custom_post_subscriber_email');

function bbimg_custom_post_subscriber_email()
{
	$labels = array(
		'name'				 =>		__('Sendmails' , 'post type general name'),
		'singular_name'		 =>		__('Mail', 'post type singular name'),
		'add_new'			 =>		__('Send new', 'subscriber_email'),
		'add_new_item'		 =>		__('New email', 'subscriber_email'),
		'edit_item' 		 => 	__('Edit email', 'subscriber_email'),
		'new_item' 			 =>		__('New email', 'subscriber_email'),
		'view_item' 		 => 	__('View email', 'subscriber_email'),
		'search_items' 		 => 	__('Search email', 'subscriber_email'),
		'not_found' 		 => 	__('No email', 'subscriber_email'),
		'not_found_in_trash' => 	__('No email found in Trash', 'subscriber_email'),
		'parent_item_colon'  => 	'',
		'menu_name' 		 => 	__('Emails', 'subscriber_email')
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Subsribers emails',
		'public' => false,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => false,
		'capability_type' => 'page',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 21,
		'supports' => array('title', 'editor')
	);

	register_post_type('subscriber_email', $args);
}

add_action( 'publish_subscriber_email', 'publish_subscriber_email');

function publish_subscriber_email($post_id)
{
	send_email_to_subscribers();
}