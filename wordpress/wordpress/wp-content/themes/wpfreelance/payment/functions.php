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
// [business] => testing@boxthemes.com
// [address_country] => United States
// [address_city] => San Jose
// [quantity] => 1
// [verify_sign] => A9LC3Qajo-H2V8mPq4eIktgPvG2RAbopMlMU1hbOUPQ.PP5rdfCFoSyD
// [payer_email] => boxtheme-buyer@gmail.com
// [txn_id] => 6YW58077JP1695900
// [order_gateway] => instant
// [last_name] => Buyer
// [address_state] => CA
// [receiver_email] => testing@boxthemes.com
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
 * @author boxthemes
 * @version 1.0
 * @param   arrray $post  $_POSt send respond form APN of paypal
 * @return  [type]
 */
function bx_process_payment($order, $txn_type = '') {

	$payment_gross = isset( $_POST['payment_gross'] ) ? $_POST['payment_gross'] : 0;
	$mc_gross = isset( $_POST['mc_gross'] ) ? $_POST['mc_gross'] : 0;

	if($payment_gross == 0){
		$payment_gross = $mc_gross;
	}

	$boxorder = BX_Order::get_instance();

	if( is_numeric($order) ){
		$order_record = box_get_order($order);
	} else {
		$order_record = $order;
	}

	box_track('order_record->post_status: '.$order_record->post_status);
	if ( $order_record->post_status == 'publish' || $order_record->post_status == 'completed' ){
		box_track('order approved - exit');
		return 0;// this order is approved, exit the verify step.
	}

	$boxorder->approve($order_record->ID);
	$order_type = $order_record->order_type; // premium_post, buy_credit, membership...
	box_track('order_type: '.$order_type);

	switch ($order_type) {
		case 'deposit':
			//add credit to payer id
			$return = BX_Credit::get_instance()->process_verified_deposit_order( $order_record);
			break;
		case 'premium_post':
			$return = mark_as_premium_post($order_record);
			break;
		case 'membership':
		 	$return = box_update_subscription_profile($order_record);
		default:
			# code...
			break;
	}
	box_track('send_mail');
	box_after_approve_payment($order_record);
}
/**
 * do some stuff after approve pament.
 * like: send mail,....
 * $order: object post type
**/
function box_after_approve_payment($order){

	global $user_ID;
	$payer_id = $order->payer_id;
	$current_user = get_userdata($payer_id);
	box_track('payer_id:'.$payer_id );
	$order_type = $order->order_type; // deposit  premium_post membership
	$subject =  __('Thank you for your order','boxtheme');
	$message = sprintf(
		__( 'Hello %s,<p> Thank you for your order.</p>
			<p>Here are detail of your order:</p>
			<p> <strong>Amount:</strong> %s<br />
			<strong>Order Type:</strong> %s<br />
			<strong>Order ID: </strong>%s<br />
			<p>Best Regards,</p><p> %s</p>','boxtheme'),
		$current_user->display_name,
		$order->amount,
		$order_type,
		$order->ID, get_bloginfo('name') );
	box_track('user_email:'.$current_user->user_email );
	box_track('subject: '.$subject);
	box_track('message: '.$message);

	return box_mail( $current_user->user_email, $subject, $message );
}

function deposit_list_payment($service = array()){ ?>
    <div class="payment-box full all-gate">
            <?php
            $gateways = box_get_list_payment();
            foreach ($gateways as $position => $gateway) {
            	do_action('payment_gateway_html_'.$gateway['id']);
            }?>
     </div><?php
}

function box_get_payment($name = ''){

	$gate_ways = BX_Option::get_instance()->get_group_option('payment');
	if( ! empty($name))
	return (object) $gate_ways->$name;
	return $gate_ways;
}