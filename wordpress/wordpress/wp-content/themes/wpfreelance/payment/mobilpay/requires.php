<?php

define( 'MOBILPAY_PATH', dirname( __FILE__ ) );

define( 'MOBILPAY_URL', get_template_directory_uri().'/payment/mobilpay' );


function box_get_mobilpay_api($setting = 0){
	$default = array(
		'id' 			=> 'mobilpay',
		'enabled' 		=> 0,
		'account_id' 	=> 'QBW3-5CT3-JJ3M-PDTB-2QJR',
		'test_mode' 	=> 1,
		'possition' 	=> 8,
	);
    $option = BX_Option::get_instance();
    $payment = $option->get_group_option('payment');
    $mobilpay = (object) $default;
    if( isset($payment->mobilpay) )
    	$mobilpay =  (object) wp_parse_args($payment->mobilpay, $default);

    global $checkout_mode;
    if( $checkout_mode && empty( $mobilpay->account_id )  ){
        $mobilpay->enable = false;
    }



    return $mobilpay;
}

function add_df_mobilpay_api($args){
	$default = array(
		'id'			=> 'mobilpay',
		'enabled'		=> 0,
		'account_id' 	=> '',
		'test_mode' 	=> 1,
	);
	$args['mobilpay'] = $default;
	return $args;

}
add_filter('df_payments_setting','add_df_mobilpay_api');

function mobilpay_track($contents,$file=false){
	if(!$file)	$file = MOBILPAY_PATH.'/track.txt';
	file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

	if (is_array($contents))
		$contents = var_export($contents, true);
	else if (is_object($contents))
		$contents = json_encode($contents);

	file_put_contents($file, $contents."\n", FILE_APPEND);
}
require_once( MOBILPAY_PATH .'/class_box_mobil_pay.php');


