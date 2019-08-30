<?php
define( 'PAYSTACK_PATH', dirname( __FILE__ ) );

define( 'PAYSTACK_URL', get_template_directory_uri().'/payment/paystack' );

require_once('debug.php');
function add_paystack($gateways){

	array_push($gateways, $paystack);
	return (object) $gateways;

}
add_filter('list_gateways','add_paystack');
function add_df_paystack_api($args){
	$paystack = array(
		'secret_key' => '',
		'public_key' => '',
		'test_secret_key' => '',
		'test_public_key' => '',
	);
	$args['paystack'] = $paystack;
	return $args;

}
add_filter('df_payments_setting','add_df_paystack_api');

function box_get_paystack($setting = 0){
	$default = array(
        'id' => 'paystack',
       	'live_secret_key' => '',
		'live_public_key' => '',
		'test_secret_key' => 'sk_test_cd0274df50ff951262e0d335ead9d52ab97b4316',
		'test_public_key' => 'pk_test_5b9d8f5c3276aafd8ae3b57bb67dfabb4e2bf841',
        'enabled' => 0,
        'test_mode' => 1,
        'possition' => 88,

    );
    $option = BX_Option::get_instance();
    $payment = $option->get_group_option('payment');
    $paystack = (object) $default;
    if( isset($payment->paystack) )
    	$paystack =  (object) wp_parse_args($payment->paystack, $default);

    global $checkout_mode;
    if( $checkout_mode && ( empty( $paystack->live_secret_key ) || empty( $paystack->live_secret_key ) ) ){
        $paystack->enable = false;
    }
    if( ! $checkout_mode && ( empty( $paystack->live_public_key ) || empty( $paystack->live_public_key ) ) ){
    	$paystack->enable = false;
    }


    return $paystack;
}
require_once( PAYSTACK_PATH .'/class_paystack.php');