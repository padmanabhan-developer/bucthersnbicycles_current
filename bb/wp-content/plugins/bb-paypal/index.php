<?php 

/**
 * PayPal
 */
require 'inc/vendor/autoload.php';
require_once 'inc/PayPalPayment.php';

/**
 * include helpers
 */
require_once 'bb-paypal-helpers.php';

if (isset($_GET['success']))
{
	$payment_id = filter_var($_COOKIE['payment'], FILTER_SANITIZE_STRING);
	$payer_id = filter_var($_GET['PayerID'], FILTER_SANITIZE_STRING);
	
	$payment = new PayPalPayment(bbpp_get_options());
	$result = $payment->executePayment($payment_id, $payer_id);
	
	//save orders and decrement in_stock
	$dbh = bbpp_get_dbh_instance();
	$dbh->savePaymentData($result);
	$confid = $dbh->getSoldConfId($payment_id);
	$dbh->updateInStockStatus($confid);
	
	//var_dump($_GET);
	header("Location: /payment-successfull");
}
else
{
	header("Location: /payment-canceled");
	//var_dump($_GET);
}

?>