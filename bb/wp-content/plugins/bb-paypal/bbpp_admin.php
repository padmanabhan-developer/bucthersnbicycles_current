<?php 

/*
	B&B PayPal admin settings page
*/

add_action('admin_menu', 'bbpp_plugin_settings');

//require_once 'inc/rain.tpl.class.php';

function bbpp_plugin_settings()
{
	add_options_page(
		'B&B PayPal',
		'B&B PayPal',
		'manage_options',
		'bb_paypal_settings',
		'bb_paypal_settings_page'
	);
}

function bb_paypal_settings_page()
{
	if (isset($_POST['settings_submit']))
	{
		if(!wp_verify_nonce($_POST['settings_submit'], 'bb_paypal_settings'))
		{
			echo "<h2>Something goes wrong</h2>";
		}
		else
		{
			$options = $_POST;
			foreach ($options as $opt => $value) 
			{
				update_option($opt, trim($value));
			}

			echo "<h2>Setting has been saved</h2>";
		}
	}
	else
	{
		$tpl = bbpp_get_raintpl_instance();
		
		bbpp_assign_settings($tpl);
		bbpp_assign_payments($tpl);
		$tpl->draw('layout');
	}
}

function bbpp_assign_settings(RainTPL $tpl)
{
//	$tpl->assign('ajaxurl', plugins_url('index.php', __FILE__));
	$tpl->assign('ppclientId', get_option('ppclientId'));
	$tpl->assign('ppclientsecret', get_option('ppclientsecret'));
	$tpl->assign('ppsdkmode', get_option('ppsdkmode'));
	$tpl->assign('wpnonce', wp_nonce_field('bb_paypal_settings', 'settings_submit'));
}

function bbpp_assign_payments(RainTPL $tpl)
{
	$payments = bbpp_get_dbh_instance()->getPaymentsData();
	
	$html = '';
	foreach ($payments as $p)
	{
		$html .= '<tr><td>' . $p->id . '</td>';
		$html .= '<td>' . $p->payment_id . '</td>';
		$html .= '<td>' . $p->create_time . '</td>';
		$html .= '<td>' . $p->state . '</td>';
		$html .= '<td>' . $p->total_price . '</td>';
		$html .= '<td>' . $p->currency . '</td>';
		$html .= '<td>' . $p->payment_details . '</td></tr>';
	}
	
	$html .= '';
	
	$tpl->assign('payments', $html);
}



?>