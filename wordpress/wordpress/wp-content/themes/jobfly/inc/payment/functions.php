<?php
// [mc_gross] => 1.00
// [invoice] => 165
// [protection_eligibility] => Eligible
// [address_status] => confirmed
// [payer_id] => QEA3LL7Q594UW
// [address_street] => 1 Main St
// [payment_date] => 19:19:05 Mar 25, 2017 PDT
// [payment_status] => Completed
// [charset] => windows-1252
// [address_zip] => 95131
// [first_name] => Test
// [mc_fee] => 0.33
// [address_country_code] => US
// [address_name] => Test Buyer
// [notify_version] => 3.8
// [custom] =>
// [payer_status] => verified
// [business] => testing@etteam.com
// [address_country] => United States
// [address_city] => San Jose
// [quantity] => 1
// [verify_sign] => A9LC3Qajo-H2V8mPq4eIktgPvG2RAbopMlMU1hbOUPQ.PP5rdfCFoSyD
// [payer_email] => contact-buyer@gmail.com
// [txn_id] => 6YW58077JP1695900
// [payment_type] => instant
// [last_name] => Buyer
// [address_state] => CA
// [receiver_email] => testing@etteam.com
// [payment_fee] => 0.33
// [receiver_id] => 2RA8H9SXQKVQ2
// [txn_type] => web_accept
// [item_name] => abc act
// [mc_currency] => USD
// [item_number] => 123
// [residence_country] => US
// [test_ipn] => 1
// [transaction_subject] =>
// [payment_gross] => 1.00
// [ipn_track_id] => 85674fa6407ce

/**
 * run after the veryfied success
 * @author boxtheme
 * @version 1.0
 * @param   arrray $post  $_POSt send respond form APN of paypal
 * @return  [type]
 */
function bx_process_payment($order_id) {


	$payment_gross = $_POST['payment_gross'];
	$order = BX_Order::get_instance();
	$order_record = $order->get_order($order_id);

	if ( $order_record->post_status == 'publish' ){
		return 0;// this order is approved, exit the verify step.
	}

	if ( (float) $order_record->amout != (float)$payment_gross ) {
		box_log('not_equal');
		return new WP_Error( 'not_equal', __( "The order is not equal", "boxtheme" ) );
	}
	// only update status of order

	$order->approve($order_id);
	$order_type = $order_record->order_type;

	box_log('Order type '.$order_type);

	$payer = get_user_by('email',$order_record->payer_email);
	$payer_id = $payer->ID;

	switch ($order_type) {
		case 'buy_credit':
			//add credit to payer id
			$return = BX_Credit::get_instance()->process_verified_order($payer_id,(float) $order_record->amout);
			break;
		case 'premium_post':
			$return = BX_Project::get_instance()->mark_as_premium_post($order_record);

		default:
			# code...
			break;
	}

}