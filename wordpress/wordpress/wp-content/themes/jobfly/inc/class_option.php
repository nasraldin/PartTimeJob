<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class BX_Option {

	static $instance;
	static function get_instance(){
		if(null === static::$instance){
			static::$instance = new static();
		}
		return static::$instance;
	}
	function get_option($group, $name){
		$current = get_option($group);
		return $current[$name];
	}
	function get_group_option($group){
		$group_args = $this->get_default();
		return (object)wp_parse_args( get_option($group), $group_args[$group]);
	}
	function get_default_option($group, $section, $key){
			$default = $this->get_default();
			return $default[$group][$section][$key];
	}
	function get_general_default(){
		return array(
				'pending_post' => false,
				'google_analytic' => '',
				'copyright' => '2017 © Boxthemes. All rights reserved. <a href="https://boxthemes.net/terms-and-conditions/" target="_blank">Term of Use</a> and <a href="https://boxthemes.net/terms-and-condition/" target="_blank">Privacy Policy</a>',
				'fb_link' => 'https://fb.com/boxthemes/',
				'gg_link' => 'https://https://plus.google.com/boxthemes/',
				'tw_link' => 'https://twitter.com/',
				'le_link.' => 'https://linkedin.com.com/boxthemes/',
				'checkout_mode' => 0,
				'currency' => array(
					'code' => 'USD',
					'position' => 'left',
					'price_thousand_sep' => ',',
					'price_decimal_sep' => '.',
				),
				'enable_captcha' => 0,
				'static_link' => array (
					'login' => array( 'id' => 0, 'link' =>'' ),
					'signup' => array( 'id' => 0, 'link' =>'' ),
					'signup-employer' => array( 'id' => 0, 'link' =>'' ),
					'signup-jobseeker' => array( 'id' => 0, 'link' =>'' ),
					'verify' => array( 'id' => 0, 'link' =>'' ),
					'profile' => array( 'id' => 0, 'link' =>'' ),
					'messages' => array( 'id' => 0, 'link' =>'' ),
					'my-credit' => array( 'id' => 0, 'link' =>'' ),
					'buy-credit' => array( 'id' => 0, 'link' =>'' ),
					'dashboard' => array( 'id' => 0, 'link' =>'' ),
					'post-project' => array( 'id' => 0, 'link' =>'' ),
					'process-payment' => array( 'id' => 0, 'link' =>'' ),
				),
				'app_api' => $this->get_app_api_default(),
			);

	}
	function get_default($key = ''){
		$default =  array(
			'general'=> $this->get_general_default(),
			'payment' => array(
				'mode' => 0,
				'paypal' => array(
					'email' => '',
					'enable' => 0,
				),
				'stripe' => array(
					'email' => '',
					'enable' => 0,
				),
				'cash' => array(
					'description' => __("<p>Kindly deposite to this bank account:</p><p>Number: XXXXXXXXXX.</p><p>Bank: ANZ Bank.</p><p> Account name: Johnny Cook.</p><p>After get your fund, we will approve your order and you can access your credit.</p",'boxtheme'),
					'enable' => 1,
				),
			),

			'escrow' => array(
				'active' => 'credit',
				'commision' => array(
					'number' => '10',
					'type'   => 'fit',
					'user_pay' => 'fre',
					'system' => 'credit',
				),

			),
			'opt_credit'=>array(
				'number_credit_default' => 10,
			),
			'paypal_adaptive' => array(
				'sandbox_mode' => 1,
				'api_appid_sandbox' => 'APP-80W284485P519543T',
				'api_useremail_sandbox' => 'employer@etteam.com',
				'api_userid_sandbox' => 'employer_api1.etteam.com',
				'app_signarute_sandbox' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31A34rWCcmcj5MTfA8FTdjkQJj-JDg',
				'api_userpassword_sandbox' => '824SVG8UC4VKBHTG',

				'api_appid' => '',
				'api_useremail' => '',
				'api_userid' => '',
				'app_signarute' => '',
				'api_userpassword' => '',

			),
			'box_mail_content' => $this->list_email(),

		);
		if( !empty($key) )
			return $default[$key];
		return $default;
	}
	function get_app_api_default( $key = 0 ){
		$default = array(
			'facebook' => array(
				'app_id' => '',
				'enable' => 0,

			),
			'google' => array(
				'client_id' => '',
				'enable' => 0,
			),
			'gg_captcha' => array(
				'site_key' => '',
				'secret_key' => '',
				'enable' => 0,
			),
		);
		if( $key )
			return $default[$key];
		return $default;
	}
	function list_email(){
		$setting = get_option('box_mail_content', true);
		if( !is_array($setting) )
			$setting = array();
		$defaults = $this->get_default_mails_content();
		return wp_parse_args( $setting, $defaults );
	}
	function get_default_mails_content(){
		return array(
			'new_account_confirm' => array(
				'receiver' => 'register',
				'subject' =>	'Congratulations! You have successfully registered to #blog_name',
				'content' =>	'<p>Hello #user_login,</p><p>Thank you for register.</p><p> To finally activate your account please click the following link <a href="#link"> here</a>.</p><p>If clicking the link doesn\'t work you can copy this link <a href="#link">#link</a> into your web browser window or type it there directly.</p>Regards,'
			),
			'verified_success' => array(
				'receiver' => 'register',
				'subject' =>	'Congratulations!  You have successfully verified your account at #blog_name',
				'content' =>	'<p>Hello #user_login,</p><p>Congratulations!  You have successfully verified your account at <i>#blog_name</i>.</p><p> Here are detail of your account:<br /> Username: <strong>#user_login</strong><br />Email: <strong>#user_email</strong></p>Regards,'
			),
			'new_account_noti' => array(
				'receiver' => 'register',
				'subject' =>	'Has new register in #blog_name site',
				'content' =>	'<p>Hello administrator,</p> <p>This email to let you know that has a new register in <i> #blog_name </i> site.</p><p>Here are the detail:<br />Username: <strong>#user_login</strong>.<br />Email: <strong>#user_email</strong></p>Sincerely,'
			),
			'reset_password' => array(
				'receiver' => 'register',
				'content' =>  '<p>Hi #display_name,</p><p><a href="#home_url">#blog_name</a> has received a request to reset the password for your account. If you did not request to reset your password, please ignore this email.</p>
						<p>Click <a href="#reset_link"> here </a> to reset your password now.</p><p>Regards,</p>',
				'subject' => __('Reset password at #blog_name','boxtheme'),
			),
			'new_job' => array(
				'receiver' => 'admin',
				'subject' =>	'The job %s has been posted',
				'content' =>	'The job %s has been posted'
			),
			'new_bidding' => array(
				'receiver' => 'employer',
				'subject' =>	'Has mew bidding in your project.',
				'content' =>	'Hello #display_name, <p>This email to let you that has new a bidding in your project <i>#project_name</i>.</p> <p>You can click <a href="#project_link">here</a> to check the detail. </p> Regards,',
			),
			'new_converstaion' => array(

				'receiver' => 'freelancer',
				'subject' =>	'Have a new message to you',
				'content' =>	__('Hello #display_name, <p>#employer_name just sent a new message for you in the project: <i>#project_name<i/>. You can click <a href="#project_link">here</a> to check the detail.</p> <p>Regards, </p.','boxtheme'),
			),
			'new_message' => array(
				'receiver' => 'receiver',
				'subject' =>	'Have a new message for you',
				'content' =>	'Hi, Have new message for you.'
			),
			'assign_job' => array(
				'receiver' => 'freelancer',
				'subject' =>	'Your bidding is choosen for project #project_name',
				'content' =>	__('Congart #display_name, <p>Your bidding is choosen in the project: <i>#project_name<i/>. You can click <a href="#project_link">here</a> to check the detail.</p> <p>Regards, </p.','boxtheme'),

			),
			'request_withdrawal' => array(
				'receiver' => 'Admin',
				'subject' =>	'Has a new withdrawal request',
				'content' =>	'<p>Has a new withdrawal in %blog_name and here are the detail of this request:</p><p><label> Amout:</label> #amount<br /><label>Method:</label> #method <br /> <label> Notes:</label> #notes <br /> Method: <strong>#detail</strong></p>, Sincrely,'
			),
		);
	}
	function get_default_mail_content($key){
		return $this->get_default_mails_content()[$key];
	}
	function get_mail_settings($key){
		$list = $this->list_email();
		$setting = $list[$key];
		$defaults = $this->get_default_mail_content($key);
		return (object) wp_parse_args( $setting, $defaults );

	}
	function set_mails($args){
		update_option('box_mail_content', $args);

	}
	function set_option($group, $section, $item, $name, $new_value, $level = 0 ){

		$current = get_option($group, false);

		if ( !is_array($current) )
			$current = array();

		$level = (int) $level;

		if(  $level == 0 ) {
			$current[$name] = $new_value; // copyright, pending_post,
		} else  if( $level == 1 ) {

			$current[$section][$name]= $new_value; // social link, currency

		} else if( $level == 2 ) {
			$cur_item = $current[$section][$item];
			$new_item = wp_parse_args( $cur_item, $this->get_item_default( $group, $section, $item ) );
			$new_item[$name] = $new_value;
			$current[$section][$item]= $new_item;
		}

		return update_option($group, $current);
	}

	function get_item_default($group,$section, $item){

		$default = $this->get_default($group);
		return $default[$section][$item];

	}

	function get_opt_credit_default(){
		$default =$this->get_default_option('opt_credit');
		$setting =  get_option('opt_credit');
		$result = wp_parse_args( $setting, $default );
		return (object)$result;
	}
	function get_general_option($object = true){

		$general = get_option('general', true);
		if( !$object ) return $general;
		return (object) wp_parse_args($general, $this->get_general_default() );
	}
	function get_app_api_option( $general, $object = true ){

		if( isset( $general->app_api ) )
			return  wp_parse_args( $general->app_api, $this->get_app_api_default() );
		return  $this->get_app_api_default();

	}
	function get_currency_option($box_global){
		return  (object) wp_parse_args( $box_global->currency, $this->get_currency_default() );

	}
	function get_currency_default(){
		$default= array(
			'code' => 'USD',
			'position' => 'left',
			'price_thousand_sep' => ',',
			'price_decimal_sep' => '.',
		);
		return $default;
	}
	function get_escrow_setting(){
		$default = $this->get_default('escrow');

		$opt_escrow = get_option('escrow', true);
		if( is_array($opt_escrow) && !empty( $opt_escrow ) )
			$opt_escrow['commision'] = wp_parse_args( $opt_escrow['commision'], $default['commision'] );

		$result =  (object)wp_parse_args( $opt_escrow, $default );

		return $result;
	}
	/**
	 * mailing setting in dashboar and be used in mail content.
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @return  [type] [description]
	 */
	function get_mailing_setting(){

		$default = array(
			'main_bg' => '#33cc66',
			'from_name' => 'BoxThemes Inc',
			'footer_text' => '© 2009-2017. BoxThemes, Inc. USA. All Rights Reserved.',
			'header_image' => get_template_directory_uri().'/img/header-email.png',
			'from_address' => 'admin@boxthemes',
			'emails' => $this->list_email(),
		);
		$setting =  get_option('box_mail');
		$result = wp_parse_args( $setting, $default );
		return (object)$result;
	}

}
function box_get_currency(){
	return BX_Option::get_instance()->get_currency_option();
}

function get_commision_fee( $total, $setting = false){
	if( ! $setting ){
		$setting = get_commision_setting();
	}
	$number = $setting->number; // fix price
	if( $setting->type == 'percent' ) {
		return ( $number/100 ) * (float) $total;
	}

	return $number;
}
/**
 * get commsion setting in dashboard.
 * This is a cool function
 * @author boxtheme
 * @version 1.0
 * @param   boolean $object return 1 object or 1 array type
 * @return  1 object or 1 array
 */
function get_commision_setting($object = true){

	$escrow = BX_Option::get_instance()->get_escrow_setting();
	$commision = $escrow->commision;
	$commision['number'] = floatval($commision['number']);
	if( $object )
		return (object) $commision;
	return $commision;
}
function box_get_pay_info( $bid_price ){

	$setting = get_commision_setting();
	$cms_fee = get_commision_fee( $bid_price, $setting );

	$emp_pay = $bid_price;

	$fre_receive = $bid_price - $cms_fee;

	$result = array( 'emp_pay' => $emp_pay, 'fre_receive' => box_get_price( max( $fre_receive, 0 ) ), 'cms_fee' => box_get_price($cms_fee), 'user_pay'=> $setting->user_pay);

	if( $setting->user_pay == 'emp') {

		$result['emp_pay'] = box_get_price($bid_price + $cms_fee);
		$result['fre_receive'] = box_get_price($bid_price);

	} else if( $setting->user_pay =='share'){
		$emp_pay = $bid_price + ( $cms_fee/2 ) ; 	$result['emp_pay'] = box_get_price( $emp_pay );

		$fre_receive = $bid_price - ( $cms_fee/2 ); 	$result['fre_receive'] = box_get_price( $fre_receive );
	}
	return (object)$result;
}

?>