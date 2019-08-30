<?php

require_once('class_order.php');
require_once('class_box_gateway.php');
require_once('class_paypal.php');
require( BOX_STRIPE_PATH . '/init.php');

require_once( dirname(__FILE__) . '/stripe/class_stripe.php');
require_once('functions.php');
require(dirname(__FILE__) . '/payfast/requires.php');
require(dirname(__FILE__) . '/paystack/requires.php');
require(dirname(__FILE__) . '/mobilpay/requires.php');
require(dirname(__FILE__) . '/pesapal/class_pesapal.php');


require_once('class_cash.php');


new Box_Paypal();
new Box_Stripe();


function create_subcription_draft_order($gateway, $package_id){
	$object_order = new \stdClass;

	switch ($gateway) {
		case 'paypal':
			$object_order = new Box_Paypal();
			break;
		case 'stripe':
			$object_order = new Box_Stripe();
			break;
		default:

			break;
	}

	$url = $object_order->create_subscription_draft_order( $package_id );

	return $url;
}

