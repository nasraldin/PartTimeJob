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
		return (object)wp_parse_args(  get_option($group),  $group_args[$group]);
	}
	function get_default_option($group, $section, $key){
			$default = $this->get_default();
			return $default[$group][$section][$key];
	}
	function get_general_default(){
		return array(
				'pending_post' => false,
				'professional_default' => __('Graphic Designer Expert','boxtheme'),
				'requires_confirm' => 0,
				'singup_avatar_field' => 0,
				'map_in_archive' => 0,
				'one_column' => 0,
				'number_bid_free' => 15,
				'direct_message' => 0,
				'google_analytic' => '',
				'copyright' => '© 2018  Boxthemes. All Rights Reserved.  Powered by <a target="_blank" href="https://boxthemes.net/themes/freelance-marketplace-theme/">WPFreelance Theme</a>.',
				'fb_link' => 'https://fb.com/boxthemes/',
				'gg_link' => 'https://https://plus.google.com/boxthemes/',
				'tw_link' => 'https://twitter.com/',
				'le_link.' => 'https://linkedin.com.com/boxthemes/',
				'checkout_mode' => 0, // 0: sandbox, 1 -real move
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
				'box_slugs' => $this->get_default_box_slugs(),

			);

	}
	function get_default($key = ''){
		$default =  array(
			'general'=> $this->get_general_default(),
			'payment' => $this->get_default_payment_setting(),
			'escrow' => array(
				'active' => 'credit',
				'commision' => array(
					'number' => '10',
					'type'   => 'fit',
					'user_pay' => 'fre',
					'system' => 'credit',
				),
			),
			'opt_credit'=>array('number_free_credit' => 10,	),
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
	function get_default_payment_setting(){
		return apply_filters('df_payments_setting', array(
				'mode' => 0,
				'paypal' => array(
					'email' => '',
					'enabled' => 0,
				),
				'stripe' => array(
					'live_publishable_key' => '',
					'live_secret_key' => '',
					'test_publishable_key' => '',
					'test_secret_key' => '',
					'enabled' => 0,

				),
				'cash' => array(
					'description' => __("<p>Kindly deposite to this bank account:</p><p>Number: XXXXXXXXXX.</p><p>Bank: ANZ Bank.</p><p> Account name: Johnny Cook.</p><p>After get your fund, we will approve your order and you can access your credit.</p",'boxtheme'),
					'enabled' => 1,
				),
			)
		);
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
				'lang_code' => '',
			),
			'gmap_key' => '',

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
			'new_account' => array(
				'receiver' => 'register',
				'subject' =>	'Congratulations! You have successfully registered on #blog_name',
				'content' =>	'<p>Hello #user_login,</p><p>Thank you for register.</p><p>You can login and access in the site now.</p>Regards,'
			),
			'verified_success' => array(
				'receiver' => 'register',
				'subject' =>	'Congratulations!  You have successfully verified your account at #blog_name',
				'content' =>	'<p>Hello #user_login,</p><p>Congratulations!  You have successfully verified your account at <i>#blog_name</i>.</p><p> Here are detail of your account:<br /> Username: <strong>#user_login</strong><br />Email: <strong>#user_email</strong></p>Regards,'
			),
			'reset_password' => array(
				'receiver' => 'register',
				'content' =>  '<p>Hi #display_name,</p><p><a href="#home_url">#blog_name</a> has received a request to reset the password for your account. If you did not request to reset your password, please ignore this email.</p>
						<p>Click <a href="#reset_link"> here </a> to reset your password now.</p><p>Regards,</p>',
				'subject' => __('Reset password at #blog_name','boxtheme'),
			),

			'new_bidding' => array(
				'receiver' => 'employer',
				'subject' =>	'Has new bidding in your project.',
				'content' =>	"Hello #display_name, <p>This email to let you that there is a new bidding in your project <i>#project_name</i>.</p> <p>You can click <a href='#project_link'>here</a> to check the detail. </p> Regards,",
			),
			'new_converstaion' => array(

				'receiver' => 'freelancer',
				'subject' =>	'Have a new message to you',
				'content' =>	__('Hello #display_name, <p>#employer_name just sent a new message to you in the project: <i>#project_name<i/>. You can click <a href="#inbox_link"> here</a> to check the message.</p> <p>Regards, </p>','boxtheme'),
			),
			'subscriber_skill' => array(

				'receiver' => 'freelancer',
				'subject' =>	__('New job match with your skill','boxtheme'),
				'content' =>	__('Hello , <p>There is a new job match with your skill. This is the detail job:</p>
				<p>Job title: #project_name<br />Author: #author_name. <br />Skills: #skill_list. </p>

				<p> You can check the detail job <a class="link" href="#project_link">here</a>.</p> <p>Regards, </p>','boxtheme'),
			),
			'new_message' => array(
				'receiver' => 'receiver',
				'subject' =>	'Have a new message for you',
				'content' =>	'Hi, Have new message for you.'
			),
			'assign_job' => array(
				'receiver' => 'freelancer',
				'subject' =>	'Your bidding is choosen for project #project_name',
				'content' =>	__('Congart #display_name, <p>Your bidding is choosen in the project: <i>#project_name<i/>. You can click <a href="#project_link">here</a> to check the detail.</p> <p>Regards, </p>','boxtheme'),

			),
			'new_account_noti' => array(
				'receiver' => 'administrator',
				'subject' =>	'Has new register in #blog_name site',
				'content' =>	'<p>Hello administrator,</p> <p>This email to let you know that has a new register in <a href="#home_url"> <i> #blog_name </i></a> site.</p><p>Here are the detail:<br />Username: <strong>#user_login</strong>.<br />Email: <strong>#user_email</strong></p>Regards,'
			),
			'new_job' => array(
				'receiver' => 'administrator',
				'subject' =>	'Has new job  in <a href="#home_url"> #blog_name </> site',
				'content' =>	'<p>Hello administrator,</p> <p>This email to let you know that have a new job in <i> #blog_name </i> site.</p> Sincerely,'
			),
			'request_withdrawal' => array(
				'receiver' => 'administrator',
				'subject' =>	'Has a new withdrawal request',
				'content' =>	'<p>Hello administrator,</p> <p>Has a new withdrawal in <a href="#home_url">#blog_name</a> and here are the detail of this request:</p><p><label> Amount:</label> #amount <br /><label>Method:</label> #method <br /> <label> Notes:</label> #notes <br /> Details: <strong>#detail</strong><br /><a href="#link_request"> Check detail.</a></p><p>Regards,</p>'
			),
			'withdrawal_request_received' => array(
				'label' => 'Withdrawal request received',
				'receiver' => 'sender',
				'subject' =>	'Withdrawal request received',
				'content' =>	'<p>Hello #display_name,</p>
					<p>We received your request to withdraw your balance account revenues from <a href="#home_url">#blog_name</a>. This is the detail:</p>
					<p><label> Amount:</label> #amount <br /><label>Method:</label> #method <br /> <label> Notes:</label> #notes <br /> Details: <strong>#detail</strong></p><p>Regards,</p>'
			),
			// buy credit.
			'cash_order' => array(
				'receiver' => 'Buyer',
				'subject' =>	'Your cash order detail',
				'content' =>	'<p>Hello #display_name,</p>
					<p> Thank you for your cash order and this is the detail</p>

					<p>
						<label> Order ID:</label> #order_id <br /><label>
						<label> Amount:</label> #amount <br /><label>
						<label> Notes:</label> This order only available after admin approved. <br /><br />
						Regards,
					</p>'
			),
			'cash_approve'  => array(
				'receiver' => 'Buyer',
				'subject' =>	'Your cash is approved',
				'content' =>	'<p>Hello #display_name,</p>
					<p>Your cash is approve and you can use your credit is available now. Detail</p>

					<p>
						<label> Order ID:</label> #order_id <br /><label>
						<label> Amount:</label> #amount <br /><label>
						<br /><br />
						Regards,
					</p>'
				),
			'send_job_to_mail' => array(
				'receiver' => 'Administrator(cc:list subscriber)',
				'subject' =>	'Auto',
				'content' =>	'<p>Hello,</p> <p>Has a friend send this job to you via #blog_name site. This is the detail:</p>
				<p> Job title: #project_name<p><p>Message: <i>#message</i></p><p> You can check the detail job <a href="#project_link">here</a>.</p>Regards,'
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
			$current_section = $current[$section];

			if( ! is_array($current_section) ){
				$default = $this->get_item_default( $group, $section, $item );

				$current_section = wp_parse_args( $cur_item, $default );
			}

			$current_section[$name] = $new_value;
			$current[$section] = $current_section;

		} else if( $level == 2 ) {

			$cur_item = $current[$section][$item];

			$new_item = wp_parse_args( $cur_item, $this->get_item_default( $group, $section, $item ) );
			$new_item[$name] = $new_value;
			$current[$section][$item]= $new_item;

		}else if( $level == 3 ) {

			$cur_item = $current[$section][$item]; // [box_slug]['login']['id']
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
		if( ! $object ) return $general;
		return (object) wp_parse_args($general, $this->get_general_default() );
	}
	function get_app_api_option( $general, $object = true ){

		if( isset( $general->app_api ) )
			return  wp_parse_args( $general->app_api, $this->get_app_api_default() );
		return  $this->get_app_api_default();

	}
	function get_box_slugs( $general, $object = true ){

		if( isset( $general->box_slugs ) )
			return  wp_parse_args( $general->box_slugs, $this->get_default_box_slugs() );
		return  $this->get_default_box_slugs();

	}
	function get_default_box_slugs(){
		return array(
            'login' => array('ID'=> '', 'label' => 'Login'),
            'signup' =>  array('ID'=> '', 'label' => 'Signup'),
            'membership-plans' =>  array('ID'=> '', 'label' => 'List MemberShip Plan'),
            'checkout' =>  array('ID'=> '', 'label' => 'Checkout MemberShip'),
            'post-project' => array('ID'=> '', 'label' => 'Post Project'),
            'thankyou' =>array( 'ID'=> '', 'label' => 'Thankyou'),
            'deposit' => array('ID'=> '', 'label' => __('Deposit Credit','boxtheme') ),
            'dashboard' =>array( 'ID'=> '', 'label' => 'Dashboard'),
            'my-credit' => array('ID'=> '', 'label' => 'My Credit'),
            'my-project' =>array('ID'=> '', 'label' => 'My Project'),
            'my-bid' =>array('ID'=> '', 'label' => 'My Bidding'),
            'my-profile' => array('ID'=> '', 'label' => 'My Profile'),
            'inbox' =>array('ID'=> '', 'label' => 'Inbox'),
            'verify' =>array('ID'=> '', 'label' => 'Verify'),
            'tos' =>  array('ID'=> '', 'label' => 'Terms of service'),
            'reset-pass' =>array( 'ID'=> '', 'label' => 'Reset Password'),

        );
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
	function get_plugins_settings(){
		$default = array(
			'acf_pro' => '',

		);
		$setting =  get_option('box_plugins');
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
if( ! function_exists('box_get_pay_info') ){
	function box_get_pay_info( $bid_price ){
		$bid_price = (float) $bid_price;
		$setting = get_commision_setting();
		$cms_fee = get_commision_fee( $bid_price, $setting );

		$emp_pay = $bid_price;

		$fre_receive = $bid_price - $cms_fee;
		$fre_receive = max($fre_receive, 0);

		$result = array( 'emp_pay' => $emp_pay,'emp_pay_price' => box_get_price($emp_pay), 'fre_receive' =>  $fre_receive , 'fre_receive_price'=> box_get_price($fre_receive), 'cms_fee' => box_get_price($cms_fee), 'user_pay'=> $setting->user_pay);

		if( $setting->user_pay == 'fre') { // pay commision

			$result['emp_pay'] = $bid_price;
			$result['emp_pay_price'] = box_get_price($bid_price);
			$fre_receive_case = $bid_price - $cms_fee;
			$result['fre_receive'] =  max($fre_receive_case,0) ;

			// is membership check and no charge commision fee.

			$plan_purchased = is_box_check_plan_available();

			if($plan_purchased){
				$get_number_bid_remain = get_number_bid_remain();
				if($get_number_bid_remain > 0){
					$result['fre_receive'] = $bid_price;
					$result['fre_receive_price'] = box_get_price($result['fre_receive']);
				}
			}

		} else if( $setting->user_pay == 'emp') { // pay commision

			$result['emp_pay'] = $bid_price + $cms_fee;
			$result['emp_pay_price'] = box_get_price($result['emp_pay']);
			$result['fre_receive'] = $bid_price;
			$result['fre_receive_price'] = box_get_price($result['fre_receive']);

		} else if( $setting->user_pay =='share'){
			$emp_pay = $bid_price + ( $cms_fee/2 ) ; 	$result['emp_pay'] = box_get_price( $emp_pay );
			$fre_receive = $bid_price - ( $cms_fee/2 );

			// is membership check and no charge commision fee.
			$plan_purchased = is_box_check_plan_available();

			if( $plan_purchased ){
				$get_number_bid_remain = get_number_bid_remain();


				$zero_commision = (int) get_post_meta($plan_purchased,'zero_commision', true);
				if( $zero_commision ){
					$fre_receive = $bid_price;
				}
			}
			$result['fre_receive'] =  $fre_receive ;
			$result['fre_receive_price'] = box_get_price($result['fre_receive']);
		}

		return (object)$result;
	}
}

?>