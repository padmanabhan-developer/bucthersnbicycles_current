<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PayPalPayment
 *
 * @author ipm
 */
class PayPalPayment 
{
	/**
	 *
	 * @var array
	 */
	protected $sdkConfig;
	
	/**
	 *
	 * @var string client id from PayPal
	 */
	protected $clientId;
	
	/**
	 *
	 * @var string client secret from PayPal
	 */
	protected $clientSecret;
	
	/**
	 *
	 * @var \PayPal\Auth\OAuthTokenCredential
	 */
	protected $cred;
	
	/**
	 *
	 * @var \PayPal\Api\Payment
	 */
	protected $payment;
	
	/**
	 *
	 * @var PayPal\Rest\ApiContext
	 */
	protected $apicontext;
	
	/**
	 *
	 * @var \PayPal\Api\Payer 
	 */
	protected $payer;
	
	/**
	 *
	 * @var PayPal\Api\Amount 
	 */
	protected $amount;
	
	
	protected $redirectUrls;
	protected $transaction;
			
	function __construct(array $config=null) 
	{
		$this->setConfig($config);
		$this->getCredential();
		$this->setApiContext();
		$this->setPayer();
	}
	
	protected function setConfig($config)
	{
		if (count($config) == 0)
			return;
		
		else
		{
			$this->sdkConfig = array('mode' => $config['mode']);
			$this->clientId = $config['clientId'];
			$this->clientSecret = $config['clientSecret'];
		}
	}
	
	protected function getCredential()
	{
		$this->cred = new \PayPal\Auth\OAuthTokenCredential($this->clientId, $this->clientSecret);
	
		try {
			$this->cred->getAccessToken($this->sdkConfig);
		} catch (Exception $ex) {
			bbpp_display_error_json($ex->getMessage());
		}
	}
	
	protected function setApiContext()
	{
		$this->apicontext = new PayPal\Rest\ApiContext($this->cred);
		$this->apicontext->setConfig($this->sdkConfig);
	}
	
	protected function setPayer()
	{
		$this->payer = new \PayPal\Api\Payer();
		$this->payer->setPaymentMethod('paypal');
	}
	
	protected function setTransaction($descriptions)
	{
		$this->transaction = new PayPal\Api\Transaction();
		$this->transaction->setDescription($descriptions);
		$this->transaction->setAmount($this->amount);
	}
	
	protected function getApprovalLink(array $links)
	{
		foreach ($links as $l)
		{
			if ($l->rel == 'approval_url')
				return $l->href;
		}

		return false;
	}
	
	public function setRedirectUrls($cancelUrl, $returnUrl)
	{
		$this->redirectUrls = new \PayPal\Api\RedirectUrls();
		$this->redirectUrls->setReturn_url($returnUrl);
		$this->redirectUrls->setCancel_url($cancelUrl);
	}

	public function setAmount($currency, $amount, $descriptions)
	{
		$this->amount = new PayPal\Api\Amount();
		$this->amount->setCurrency($currency);
		$this->amount->setTotal($amount);
		$this->setTransaction($descriptions);
	}
	
	public function createPayment()
	{
		if(!empty($this->redirectUrls))
		{
			$this->payment = new \PayPal\Api\Payment();
			$this->payment->setIntent("sale");
			$this->payment->setPayer($this->payer);
			$this->payment->setRedirect_urls($this->redirectUrls);
			$this->payment->setTransactions(array($this->transaction));
		}
		else
		{
			return array('error' => 'redirect urls not set');
		}

		try {
			$this->payment->create($this->apicontext);
		} catch (Exception $ex) {
//			return array('error' => $ex->getMessage());
			return serialize($ex);
		}
		
		$result = array(
			'success' => 1,
			'id' => $this->payment->getId(),
			'approval_url' => $this->getApprovalLink($this->payment->getLinks())
		);

		return $result;
	}

	public function redirectToPayPal($url)
	{
		header("Location: ". $url);
		exit;
	}
	
//	public function setPaymentId($payment_id) 
//	{
//		$this->payment = new PayPal\Api\Payment();
//		$this->payment->setId($payment_id);
//	}
	
	public function executePayment($payment_id, $payer_id)
	{
		$this->payment = new PayPal\Api\Payment();
		$this->payment->setId($payment_id);
		
		$execution = new \PayPal\Api\PaymentExecution();
		$execution->setPayerId($payer_id);
		
		return $this->payment->execute($execution, $this->apicontext);
	}
	
	public function getCreateTime()
	{
		return $this->payment->getCreateTime();
	}
	
//	public function executePayment($payment_id, $payer_id)
//	{
//		$apicontext = new \PayPal\Rest\ApiContext($this->cred, 'Request' . time());
//		$apicontext->setConfig($this->sdkConfig);
//		
//		$this->payment = new \PayPal\Api\Payment();
//		$execution = new \PayPal\Api\PaymentExecution();
//		$execution->setPayerId($payer_id);
//		
//		$this->payment->setId($payment_id);
//		$this->payment->execute($execution, $apicontext);
//		
//		$test = $this->payment;
//	}
}
