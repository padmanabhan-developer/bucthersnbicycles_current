<?php
/*
	Plugin Name: B&B Send SMS
	Plugin URI: http://woto-info.com
	Description: Send SMS to number, supplied by user on the page. Message text and SMS-gateway credentials are defined in admin panel.
	Version: 1.0
	Author: Igor Malinovskiy
	Author URI: mailto:psy.ipm@gmail.com
	License: GPLv2 or later
*/

	//scripts
	add_action('wp_enqueue_scripts', 'bbssms_load_scripts');

	function bbssms_load_scripts() 
	{
		wp_enqueue_script('jquery');
	}

	//styles
	add_action('wp_enqueue_scripts', 'bbsms_load_styles');

	function bbsms_load_styles()
	{
		wp_register_style(
			'bbsms_style', 
			plugins_url('css/style.css', __FILE__)
		);
		wp_enqueue_style('bbsms_style');
	}

	//shortcode and frontend
	add_shortcode("bb_send_sms", "bb_shortcode_function");

	function bb_shortcode_function($text)
	{
		$nonce = wp_create_nonce( 'send_sms' );
		ob_start();
		?>
			<div id="sendtophone">
				<div id="sendtophone-left">
					<p class="uppercase">Send this map to my phone, pronto!</p>
					<p>Please always include country code. Example   009912345678</p>
				</div>
				<div id="sendtophone-right">
					<p>
						+
						<input 
							class="wpcf7-text" 
							type="text" 
							id="phonenumber" 
							placeholder="Telephone No."
							style="width:350px;"
						>
					</p>
					<p>
						<input 
							class="wpcf7-submit" 
							type="button" 
							id="sendbtn" 
							value="Send"
							style="width:150px;"
						>
					</p>
				</div>
			</div>
			<p class="uppercase right gmnoprint">No obligations - no charge - of course!</p>
			<div id="sendtophone-response">
			</div>

			<script type="text/javascript">

				var sendQuerySuccess = function(data) {
					if(data && data.hasOwnProperty('successmsgid'))
						showMessage("#sendtophone-response", "The message has been sent successfully!");
					else
						showMessage("#sendtophone-response", data.failed);
				}
				
				function sendAjaxQuery(data, success)
				{
					jQuery.ajax({
						type: "post",
						dataType: "json",
						url: "/wp-admin/admin-ajax.php",
						data: data,
						success: success
					});
				}
				
				function showMessage(selector, message) {
					jQuery(selector).empty();
					jQuery(selector).append('<p>' + message + '</p>');
					jQuery(selector).slideToggle("slow").delay(3000).slideToggle("slow");
				}
				
				function validatePhone(phone) {
					var filter = /^[0-9-+]+$/;
					if (filter.test(phone)) {
						return true;
					}
					else {
						return false;
					}
				}

				jQuery("#sendbtn").click(function () {
					var phone = jQuery("#phonenumber").val();
					if (validatePhone(phone)) {
						var data = 	{
							action: 	'send_sms',
							phone:  	phone,
							_ajax_nonce: '<?php echo $nonce; ?>'
						};
						
						sendAjaxQuery(data, sendQuerySuccess);
					}
					else {
						showMessage("#sendtophone-response", "Phone number is invalid");
					}
				});
			</script>

		<?php
		return ob_get_clean();
	}

	//process ajax on server side
	add_action('wp_ajax_send_sms', 'send_sms_ajax_function');
	add_action('wp_ajax_nopriv_send_sms', 'send_sms_ajax_function');

	function send_sms_ajax_function()
	{
		check_ajax_referer( "send_sms" );

		$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
		$opts = bb_sms_plugin_get_options();

		if(isset($opts) && $phone != "")
			bb_send_sms($opts, $phone);
	}

	//send sms
	function bb_send_sms(array $args, $to)
	{
		require_once 'clickatellsms.php';

		$gw = new ClickatellSms($args['user'], $args['password'], $args['api_id']);
		die($gw->sendMessageWAuth($to, $args['msgtext']));
	}

	//get plugin options
	function bb_sms_plugin_get_options()
	{
		$options = array(
			'msgtext' => get_option('msgtext'),
			'user' => get_option('user'),
			'password' => get_option('password'),
			'api_id' => get_option('api_id')
		);

		return $options;
	}

	//admin menu
	add_action('admin_menu', 'bb_sms_plugin_set_options');
	
	require_once 'bb_sms_admin.php';

?>