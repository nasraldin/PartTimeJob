<?php



function box_get_payfast(){
	$default = array(
        'id'   => 'payfast',
        'live_merchant_id' => '',
        'live_merchant_key' => '',
        'test_merchant_id' => '',
        'test_merchant_key' => '',
        'enabled' => 0,
        'test_mode' => 1,
        'possition' => 11,

    );
    $option = BX_Option::get_instance();
    $payment = $option->get_group_option('payment');

    $payfast = (object) $default;
    if( isset($payment->payfast) )
    	$payfast =  (object) wp_parse_args($payment->payfast, $default);



    return $payfast;
}

function add_payfast($gateways){

	array_push($gateways, $payfast);
	return (object) $gateways;

}
add_filter('list_gateways','add_payfast');
function add_df_payfast_api($args){
	$payfat_init = array(
		'live_merchant_id' => '',
		'live_merchant_key' => '',
		'test_merchant_id' => '',
		'test_merchant_key' => '',
	);
	$args['payfast'] = $payfat_init;
	return $args;

}
add_filter('df_payments_setting','add_df_payfast_api');

function require_payfast_files() {

    require_once( dirname( __FILE__ )  .'/class_payfast.php');


}
add_action( 'after_setup_theme', 'require_payfast_files', 99 );


