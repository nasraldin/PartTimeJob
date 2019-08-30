<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Class BX_Credit {

	static private $instance;
	public $meta_available;
	public $meta_pending;
	private $mode;
	function __construct(){
		global $checkout_mode; // 0 = sandbox, 1 == real
		$this->mode = $checkout_mode;
		if( $checkout_mode === 1 ) {
			$this->meta_total = '_credit_total';
			$this->meta_pending = '_credit_pending';
			$this->meta_available = '_credit_available';
		} else {
			$this->meta_total = '_sandbox_credit_total';
			$this->meta_pending = '_sandbox_credit_pending';
			$this->meta_available = '_sandbox_credit_available';
		}
	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	/**
	 * Tranfer credit in employer account to freelancer account with status pending.
	 * @param int $employer_id
	 * @param int $bidding  bidding id
	*/
	function deposit(  $bid_price, $project, $freelancer_id ) {

		$employer_id = $project->post_author;

		$ballance = $this->get_ballance( $employer_id );

		$pay_info = box_get_pay_info( $bid_price );

      	$emp_pay = $pay_info->emp_pay;

      	$fre_receive = $pay_info->fre_receive;

		if( $ballance->available < $emp_pay ){
			return new WP_Error( 'not_enough', __( "Your credit are not enough to perform this transaction.", "boxtheme" ) );
		}
		$new_available = $ballance->available - $emp_pay;
		global $wpdb;
		$ok = $wpdb->query( $wpdb->prepare("
				UPDATE $wpdb->usermeta
				SET  meta_value = %f
				WHERE user_id = %d AND meta_key ='%s' ",
			    $new_available, $employer_id, $this->meta_available
			)
		);
		if( $ok ){
			$total_spent = (float) get_user_meta($employer_id, 'total_spent', true) + $emp_pay;
			update_user_meta( $employer_id, 'total_spent', $total_spent );
			BX_Order::get_instance()->create_deposit_orders( $emp_pay, $fre_receive, $project, $freelancer_id );
		}
		return $ok;

	}
	function undeposit( $employer_id, $bid_price, $project_id = 0 ) {

		$ballance = $this->get_ballance($employer_id);

		$pay_info = box_get_pay_info($bid_price);

		$emp_pay = $pay_info->emp_pay;

		$new_available = $ballance->available + $emp_pay;

		global $wpdb;
		$wpdb->query( $wpdb->prepare(			"
				UPDATE $wpdb->usermeta
				SET  meta_value = %f
				WHERE user_id = %d AND meta_key ='%s' ",
			    $new_available, $employer_id, $this->meta_available
			)
		);
		// should update order of this deposit // not implement.
		return true;

	}

	// call this action when employer mark as finish a project.
	function release( $freelancer_id, $amout){
		return $this->increase_credit_available( $amout, $freelancer_id );
	}

	/**
	 * add more available credit to the account.
	 * @author boxtheme
	 * @version 1.0
	 * @param  int  $user_receiver_id int
	 * @param   float $amout
	 * @return  void
	 */
	function process_verified_order( $user_receice_id, $amout ){
		$return =  $this->increase_credit_available($amout, $user_receice_id);
		// box_log('User Receiver ID Input:'.$user_receice_id);
		// box_log('Amout order:'.$amout);
		if($return){
			box_log('Process verified order : OK');
		} else {
			box_log('Process verified order : Fail');
		}
	}
	function get_ballance($user_id) {
		return (object) array(
			'pending' => $this->get_credit_pending($user_id),
			'available' => $this->get_credit_available($user_id)
		);
	}
	function get_credit_available($user_id){

		return (float) get_user_meta($user_id, $this->meta_available, true) ;
	}
	function increase_credit_available($available, $user_id =0 ){

		if( ! $user_id ){
			global $user_ID;
			$user_id = $user_ID;
		}


		$current_available = $this->get_credit_available($user_id);
		$new_available = $current_available + (float) $available;

		return update_user_meta($user_id, $this->meta_available, $new_available);
	}
	function increase_credit_pending( $user_id, $available ){
		$new_pending = $this->get_credit_pending($user_id) + (float)$available;
		return update_user_meta($user_id, $this->meta_pending, $new_pending);
	}
	function approve_credit_pending($user_id, $value){
		$this->subtract_credit_pending($user_id,$value);
		$this->increase_credit_available( $value, $user_id);
	}
	//deduct
	function subtract_credit_available($user_id, $value){
		$current = $this->get_credit_available($user_id);
		$new_available = $this->get_credit_available($user_id) - (float)$value;

		if( $new_available >= 0 )
			return update_user_meta($user_id, $this->meta_available, $new_available);

		return false;
	}

	function get_credit_pending($user_id){
		return (float) get_user_meta($user_id, $this->meta_pending, true);
	}

	/**
	 * [subtract_credit_pending description]
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $user_id   [description]
	 * @param   [type] $available [description]
	 * @return  [type]            [description]
	 */
	function subtract_credit_pending($user_id, $available){

		$new_available = $this->get_credit_pending($user_id) - (float)$available;
		if( $new_available >= 0){
			return update_user_meta( $user_id, $this->meta_pending, $new_available);
		}
		return 0;
	}

	/**
	 * admin approve 1 buy_credit order
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $order_id [description]
	 * @return  [type]           [description]
	 */
	function request_withdraw( $request){ //widthraw_request

		global $user_ID;
		$amout = (float) $request['withdraw_amout'];
		$method =  $request['withdraw_method'];
		$notes =  $request['withdraw_note'];
		$payment_method = $this->get_withdraw_info();

		$ballance = $this->get_ballance($user_ID);

		$method_detail = array('paypay' => '', 'bank_account' => array( 'account_name' => '', 'bank_name' => '', 'account_number' => '' ) );

		if( empty( $payment_method->$method ) ){
			return new WP_Error( 'unset_method', __( "Please set your payment method to withdraw", "boxtheme" ) );
		}



		if( $amout < 10 )
			return new WP_Error( 'inlimitted', __( "Your amout must bigger than 10$", "boxtheme" ) );

		if( $ballance->available < $amout ){
			return new WP_Error( 'not_enough', sprintf(__( "You only can withdraw less than %s.", "boxtheme" ), box_get_price( $ballance->available) ) );
		}
		$this->subtract_credit_available($user_ID, $amout); //deducte in available credit of this user.
		//create order
		$curren_user = wp_get_current_user();

		$method_text = '';
		if( $method == 'paypal_email'){
			$method_text = '<p> &nbsp; &nbsp; PayPal email: '.$payment_method->paypal_email.'</p>';
		} else {
			// array('account_name' => 'empty', 'account_number' => '', 'bank_name'=>'' );
			$method_detail = (object)$payment_method->$method;
			$method_text = '<p> &nbsp; &nbsp; Bank name: '.$method_detail->bank_name.'</p>';
			$method_text .= '<p> &nbsp; &nbsp; Account name: '.$method_detail->account_name.'</p>';
			$method_text .= '<p> &nbsp; &nbsp; Account number: '.$method_detail->account_number.'</p>';
		}
		$mail = BX_Option::get_instance()->get_mail_settings('request_withdrawal');
		$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes($mail->subject) );
		$content = str_replace('#amount', $amout, $mail->content);
		$content = str_replace('#method', $method, $content);
		$content = str_replace('#notes', $notes, $content);
		$content = str_replace('#detail', $method_text, $content);

		$args_wdt = array(
			'post_title' => sprintf( __('%s send a request withdraw: %f ','boxtheme'), $curren_user->user_login, $amout ),
			'amout' => $amout,
			'order_type' => 'withdraw' ,
			'payment_type' => 'none' ,
			'post_content' => $content,
		);

		$order_id = BX_Order::get_instance()->create_custom_pending_order( $args_wdt );
		$admin_content = $content . '<p> Link to check detail: <a href="'.get_edit_post_link($order_id).'">link</a></p>';

		$to = get_option('admin_email', true);
		box_mail( $to, $subject, $admin_content ); // mail to admin.
		//$subject = __( 'You have just sen a  requested to withdraw.','boxtheme' );
		//box_mail( $curren_user->user_email, $subject, $content ); // mail to freelancer.
		return true;
	}

	function approve_buy_credit($order_id){
		try{
			$order = BX_Order::get_instance()->get_order($order_id);
			$order_access = BX_Order::get_instance()->approve($order_id);

			if( !$order_access ){
				throw new Exception("Some error message", 101);
			}
			//$this->subtract_credit_pending($order->post_author, $order->amout);

			$this->increase_credit_available( $order->amout, $order->post_author);

		} catch(Exception  $e){
			$code = $e->getCode();

			if($code == 101){
				// update order to pending
			}
			if($code == 100){

			}
			return false;
		}
		return true;
	}
	/**
	 * admin approve 1 widthraw
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @return  [type] [description]
	 */
	function approve_withdraw($order_id){

		try{

			$order_access = BX_Order::get_instance()->approve($order_id);

			if( !$order_access ){
				throw new Exception("Some error message", 101);
			}
			$order = BX_Order::get_instance()->get_order($order_id);

			$this->increase_credit_available( $order->amout, $order->post_author);

		} catch(Exception  $e){

			$code = $e->getCode();

			if($code == 101){
				// update order to pending
			}
			if($code == 100){

			}
			return false;
		}
		return true;
	}
	/**
	 *
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $args [description]
	 * @return  [type]       [description]
	 */
	function update_withdraw_info( $args ){

		global $user_ID;
		$withdraw_info = get_user_meta( $user_ID, 'withdraw_info', true );

		if( !is_array($withdraw_info) )
			$withdraw_info = array();

		if( isset($args['paypal_email']) ){
			$withdraw_info['paypal_email'] = $args['paypal_email'];
		} else {
			// update bank infor
			$withdraw_info['bank_account'] = array(
				'account_name' => $args['account_name'],
				'account_number' => $args['account_number'],
				'bank_name' => $args['bank_name'],
				'account_name' => $args['account_name'],
			);
		}
		return update_user_meta( $user_ID, 'withdraw_info', $withdraw_info );

	}
	function get_withdraw_info($user_id = 0){
		if( empty( $user_id )){
			global $user_ID;
			$user_id = $user_ID;
		}
		return (object) get_user_meta( $user_id, 'withdraw_info', true );
	}

}