<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Class BX_Credit extends Box_Escrow {

	static private $instance;
	public $meta_available;
	public $meta_pending;
	private $mode;
	protected $transaction;
	function __construct(){
		//add_action('init',array($this,'register_postype') );

		global $checkout_mode; // 0 = sandbox, 1 == real
		$this->mode = (int) $checkout_mode;
		$this->transaction = Box_Transaction::get_instance();


		$this->meta_total = '_credit_total';
		$this->meta_pending = '_credit_pending';
		$this->meta_available = '_credit_available';

	}

	function get_meta_by_mode($mode){

		// $meta = array(
		// 	'meta_total' => '_sandbox_credit_total',
		// 	'meta_pending' => '_sandbox_credit_pending',
		// 	'meta_available' => '_sandbox_credit_available',
		// );

		//if( $mode == 1 ){
			//real mode
			$meta['meta_total'] = '_credit_total';
			$meta['meta_pending'] = '_credit_pending';
			$meta['meta_available'] = '_credit_available';
		//}
		return (object) $meta;
	}

	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	/**
	 * ONLY deduct credit number in ballance of Employer account.
	 * @param int $employer_id
	 * @param int $bidding  bidding id
	*/
	function deposit(  $bid_price, $pay_info, $project , $freelance_id) {

		$employer_id = $project->post_author;

		$balance = $this->get_ballance( $employer_id );

     	$emp_pay = $pay_info->emp_pay;

      	$fre_receive = $pay_info->fre_receive;

      	$new_available = $balance->available - $emp_pay;

		if( $new_available < 0 ){
			return new WP_Error( 'not_enough', __( "Your credit is not enough to perform this transaction.", "boxtheme" ) );
		}

		$args = array(
			'total' => $emp_pay,
			'emp_pay' => $emp_pay,
			'payer_id' => $employer_id,
			'user_pay' => $pay_info->user_pay, // user pay commision fee
			'receiver_id' => $freelance_id,
			'fre_receive' => $fre_receive, //amount money freelancer will got to ballance.
			'commision_fee' => $pay_info->cms_fee,

		);
		$update_balance = false;

		$trans = $this->transaction->create( $args, $project );

		if ( $trans && !is_wp_error( $trans ) ) {
			box_log('Create transaction done.');
			$update_balance = update_user_meta( $employer_id, $this->meta_available, $new_available ); // most improtant action.

			if( $update_balance ){
				$t = update_post_meta( $project->ID, 'transaction_id', $trans->id );

				return $trans;
			} else {
				$trans->delete();
				return false;
				wp_die('Can not update balance of employer');
			}
		} else {
			box_log('create transaction failss.');
		}
		return $update_balance;

	}
	function act_award( $bid_id, $freelancer_id,  $project){
		// assign job to freelancer.
		$bid_price = (float) get_post_meta( $bid_id, BID_PRICE, true );
		$employer_id = $project->post_author;
		$project_id = $project->ID;

		$pay_info = box_get_pay_info( $bid_price );

		$trans = $this->deposit( $bid_price, $pay_info, $project, $freelancer_id); //ONLY deduct credit number in ballance of Employer account and create  1 transaction to save detail of this contractor.
		if ( is_wp_error($trans) ){
			return $trans;
		}

		$request['ID'] = $project_id;
		$request['post_status'] = AWARDED;
		$request['meta_input'] = array(
			WINNER_ID => $freelancer_id,
			BID_ID_WIN => $bid_id,
		);
		$res = wp_update_post( $request );

		if( $res ){

			global $user_ID;


	      	$emp_pay = $pay_info->emp_pay;
			$employer_id = $project->post_author;
			$fre_receive = $pay_info->fre_receive;

			$total_spent = (float) get_user_meta($employer_id, 'total_spent', true) + $emp_pay;

			update_user_meta( $employer_id, 'total_spent', $total_spent );

			$fre_hired = (int) get_user_meta( $employer_id, 'fre_hired', true) + 1;
			update_user_meta( $employer_id, 'fre_hired',  $fre_hired );

			// create coversation
			// update bid status to AWARDED
			wp_update_post( array( 'ID' => $bid_id, 'post_status'=> AWARDED) );

			BX_Order::get_instance()->create_deposit_orders( $emp_pay, $fre_receive, $project, $freelancer_id );

			$this->send_mail_noti_assign( $project_id, $freelancer_id );
			do_action('after_award_job', $freelancer_id, $project);
			return $res;
		} else {
			$this->undeposit( $employer_id, $bid_price, $project_id );
			$trans->delete(); // delete transaction
		}
		return new WP_Error( 'award_fail', __( "Assign job failse", "boxtheme" ) );

	}
	/**
	 * Admin handle  and use to set freelancer or employer is winner.
	*/
	function act_refund($winner_role, $project_id ){

		$bid_id = get_post_meta($project_id, BID_ID_WIN, true);
		$bid_price = (float) get_post_meta( $bid_id, BID_PRICE, true );
		$pay_info = box_get_pay_info( $bid_price );

		$trans_id = get_post_meta( $project_id, 'transaction_id', true );
		$transaction  = Box_Transaction::get_instance()->get_transaction($trans_id);
		if( $winner_role == EMPLOYER ){
			$project = get_post($project_id);
			$winner_id = $project->post_author;
			//BX_Credit::get_instance()->undeposit( $winner_id, $pay_info->fre_receive);
			BX_Credit::get_instance()->undeposit_transaction($transaction); // update credit of employer and del transaction.
			update_post_meta($project_id,'transaction_id',0);


		} else {
			$winner_id 	= get_post_meta($project_id, WINNER_ID, true);
			$this->release_transaction( $transaction );
		}
		$args = $_REQUEST['request'];

		update_post_meta( $project_id,'choose_dispute_winner', $winner_id);
		update_post_meta( $project_id,'choose_dispute_msg', $args['msg_content']);
		wp_update_post( array( 'ID' => $project_id, 'post_status' => 'resolved'));
	}


	function emp_mark_as_complete($request){

			$project_id = $request['project_id'];

			$check = $this->check_before_emp_review($request);

			if ( is_wp_error($check) ){
				return $check;
			}
			$release = 0;

			try {
				//$winner_id 	= get_post_meta($project_id, WINNER_ID, true); // freelancer_id
				$trans_id = get_post_meta( $project_id, 'transaction_id', true );

				if( empty($trans_id)  || ! $trans_id ){
					throw new Exception("Empty transaction");
				}
				if( $trans_id ){
					$release = BX_Credit::get_instance()->release_transaction( $trans_id );
				}

			} catch( Exception  $e ){
				return new WP_Error('empty_transaction',$e->getMessage());
			}

			if ( $release && ! is_wp_error( $release ) ) {
				wp_update_post( array('ID' => $trans_id,'post_status' => 'publish') );
				$request['ID'] = $request['project_id'];
				$request['post_status'] = COMPLETE;
				$project_id = wp_update_post($request);

				if( ! is_wp_error( $project_id ) ){
					$this->mark_as_complete( $project_id, $request );
				}
				$fre_order = get_post_meta( $project_id, 'fre_order_id', true);
				return wp_update_post( array('ID' => $fre_order, 'post_status' =>'publish'));
			}
			return new WP_Error('failse',__('Review fail','boxtheme') );
	}


	/**
	* use this function in disputing case and winner is employer
	*/
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
		// should update order of this deposit // not yet implement.
		return true;

	}
	/**
	 * 1: call this action when employer mark as finish a project
	 * 2: In dipusting and set winner is freelancer.
	 */

	function release_transaction( $transaction ){
		if( is_numeric( $transaction) ){
			$transaction = Box_Transaction::get_instance()->get_transaction($transaction);

		}
		$mode = $transaction->is_realmode; //1 = real , 0, empty ... = sandbox
		$meta = $this->get_meta_by_mode($mode);

		$freelancer_id = $transaction->receiver_id;

		$available = $transaction->fre_receive;

		$current_available = $this->get_credit_available_base_on_meta($freelancer_id, $meta);

		$new_available = $current_available + (float) $available;

		$t =  update_user_meta($freelancer_id, $meta->meta_available, $new_available);
		if($t){
			wp_update_post(array('ID' => $transaction->ID,'post_status' => 'publish') );
		}
		return $t;

	}

	/**
	* use this function in disputing case and winner is employer
	*/
	function undeposit_transaction($transaction){

		$mode = $transaction->is_realmode; //1 = real , 0, empty ... = sandbox
		$meta = $this->get_meta_by_mode($mode);
		$employer_id =$transaction->payer_id;

		$ballance = $this->get_ballancen_base_on_meta($employer_id, $meta);


		$fre_receive = $transaction->fre_receive; // admin earning commision fee (if have). If $transaction->emp_pay -> admin don't earn commsion fee.

		$new_available = $ballance->available + $fre_receive;

		global $wpdb;
		$wpdb->query( $wpdb->prepare(			"
				UPDATE $wpdb->usermeta
				SET  meta_value = %f
				WHERE user_id = %d AND meta_key ='%s' ",
			    $new_available, $employer_id, $meta->meta_available
			)
		);
		wp_delete_post($transaction->ID);
	}
	/**
	 * add more available credit to the account.
	 * @author boxtheme
	 * @version 1.0
	 * @param  int  $user_receiver_id int
	 * @param   float $amount
	 * @return  void
	 */
	function process_verified_deposit_order( $order ){

		$metas = $this->get_meta_base_on_order($order);
		$payer_id = $order->payer_id;

		$current_available = (float) get_user_meta($payer_id, $metas->meta_available, true) ;
		$amount = (float)$order->amount;

		$new_available = $current_available + $amount;

		$return = update_user_meta($payer_id, $this->meta_available, $new_available);


		if($return){
			box_track($payer_id .' - verify deposit order DONE.');
		} else {
			box_track($payer_id .' - verify deposit order FAIL');
		}
	}

	function get_meta_base_on_order($order){

		$meta =  (object) array('meta_pending'=>'_credit_pending','meta_total'=>'_credit_total','meta_available' =>'_credit_available' );
		return $meta;
	}

	function get_ballance($user_id) {
		return (object) array(
			'pending' => $this->get_credit_pending($user_id),
			'available' => $this->get_credit_available($user_id)
		);
	}
	function get_ballancen_base_on_meta($user_id,$meta) {
		return (object) array(
			'pending' => (float) get_user_meta($user_id, $meta->meta_pending, true),
			'available' =>(float) get_user_meta($user_id, $meta->meta_available, true)
		);
	}
	function get_credit_available($user_id){

		return (float) get_user_meta($user_id, $this->meta_available, true) ;
	}
	function get_credit_available_base_on_meta($user_id,$meta){

		return (float) get_user_meta($user_id, $meta->meta_available, true) ;
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

	function approve_withdrawal_credit($withdrawal){

		$available = $withdrawal->amount;
		$receiver_id =  $withdrawal->post_author;
		$mode = $withdrawal->is_realmode;

		$meta = $this->get_meta_by_mode($mode);

		$current_available = (float) get_user_meta($withdrawal->post_author, $meta->meta_available, true);

		$new_available = $current_available + (float) $available;

		return update_user_meta($withdrawal->post_author, $meta->meta_available, $new_available);


	}

	function approve_deposit_credit($order){
		$mode = $order->is_realmode;
		$meta = $this->get_meta_by_mode($mode);
		$available = (float) $order->amount;

		$current_available = (float) get_user_meta($order->post_author, $meta->meta_available, true);

		$new_available = $current_available + (float) $available;

		$check =  update_user_meta($order->post_author, $meta->meta_available, $new_available);

		if($check){
			$this->subtract_credit_pending_by_meta($order->post_author, $order->amount, $meta);
		}
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
	function subtract_credit_pending_by_meta($user_id, $available, $meta){


		$current_available =  (float) get_user_meta($user_id, $meta->meta_pending, true);
		$new_available = $current_available - (float)$available;
		if( $new_available >= 0){
			return update_user_meta( $user_id, $meta->meta_pending, $new_available);
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

		$amount = (float) $request['withdraw_amount'];
		$method =  $request['withdraw_method'];
		$notes =  $request['withdraw_note'];
		$payment_method = $this->get_withdraw_info();

		$ballance = $this->get_ballance($user_ID);

		$method_detail = array('paypay' => '', 'bank_account' => array( 'account_name' => '', 'bank_name' => '', 'account_number' => '' ) );

		if( empty( $payment_method->$method ) ){
			return new WP_Error( 'unset_method', __( "Please set your payment method to withdraw", "boxtheme" ) );
		}

		if( $amount <  10  )
			return new WP_Error( 'inlimitted', __( "Your amount must bigger than 10$", "boxtheme" ) );

		if( $ballance->available < $amount ){
			return new WP_Error( 'not_enough', sprintf(__( "You only can withdraw less than %s.", "boxtheme" ), box_get_price( $ballance->available) ) );
		}
		if( BOX_VERIFICATION ){
			global $user_ID;
			$profile_id =(int) get_user_meta( $user_ID, 'profile_id', true);

			$is_reviewed = (int) get_post_meta( $profile_id, 'is_reviewed', true );

			if( ! $is_reviewed && bx_get_user_role($user_ID) == FREELANCER ) {
				return new WP_Error('verification_error',__('Your account is not reviewed and you can not withdraw now.','boxtheme') );
			}

		}
		$this->subtract_credit_available( $user_ID, $amount ); //deducte in available credit of this user.


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
		$content = str_replace('#amount', $amount, stripcslashes($mail->content) );
		$content = str_replace('#blog_name', get_bloginfo('name') , $content);
		$content = str_replace('#method', $method, $content);
		$content = str_replace('#notes', $notes, $content);
		$content = str_replace('#detail', $method_text, $content);

		$args_wdt = array(
			'post_title' => sprintf( __('%s sent a withdrawal request with amout  %f ','boxtheme'), $curren_user->user_login, $amount ),
			'amount' => $amount,
			'order_type' => 'withdraw' ,
			'post_content' => $content,
		);

		$withdrawal_id = Box_Withdrawal::get_instance()->save_withdrawal( $args_wdt );


		$content = str_replace('#link_request', get_edit_post_link($withdrawal_id), $content);


		$to = get_option('admin_email', true);
		$result = box_mail( $to, $subject, $content ); // mail to admin.
		if( $result ){
			//Withdrawal request received
			$mail = BX_Option::get_instance()->get_mail_settings('withdrawal_request_received');
			$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes($mail->subject) );

			$content = str_replace('#amount', $amount, stripcslashes($mail->content) );
			$content = str_replace('#display_name', $curren_user->display_name, $content);
			$content = str_replace('#method', $method, $content);
			$content = str_replace('#home_url', home_url(), $content);
			$content = str_replace('#blog_name', get_bloginfo('name'), $content );

			$content = str_replace('#notes', $notes, $content);
			$content = str_replace('#detail', $method_text, $content);

			box_mail( $curren_user->user_email, $subject, $content ); // mail to freelancer.
		}
		return true;
	}

	function approve_deposit_act($order_id){
		try{
			$order = BX_Order::get_instance()->get_order($order_id);

			if( $order && $order->post_status == 'pending' ){
				$order_access = BX_Order::get_instance()->approve($order_id);

				if( ! $order_access ){
					throw new Exception("Some error message", 101);
				}

				//$this->subtract_credit_pending($order->post_author, $order->amount);
				//$this->increase_credit_available( $order->amount, $order->post_author);
				$this->approve_deposit_credit( $order );
				do_action('after_approve_cash_deposit', $order);
			}

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
	function approve_withdraw_act($withdrawal_id){

		try{


			$withdrawal = Box_Withdrawal::get_instance()->get_withdrawal( $withdrawal_id );

			if($withdrawal->post_status == 'pending'){
				$result = Box_Withdrawal::get_instance()->approve_withdrawal_status( $withdrawal_id ); // just approve  record post onnly.

				if( ! $result ){
					throw new Exception("Some error message", 101);
				}
				// no need this step. because data is query from db.
				//$this->approve_withdrawal_credit( $withdrawal);
			}

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
	function perform_after_deposit(  $bid_id, $bid_price, $freelancer_id,  $project){
		//update bid status
		//update user meta
		if( is_numeric( $project ) ){
			$project = get_post($project);
		}
		$project_id = $project->ID;
		$pay_info = box_get_pay_info( $bid_price );

      	$emp_pay = $pay_info->emp_pay;

		$employer_id = $project->post_author;

		$total_spent = (float) get_user_meta($employer_id, 'total_spent', true) + $emp_pay;
		update_user_meta( $employer_id, 'total_spent', $total_spent );

		$fre_hired = (int) get_user_meta( $employer_id, 'fre_hired', true) + 1;
		update_user_meta( $employer_id, 'fre_hired',  $fre_hired );

		$request['ID'] = $project_id;

		$request['post_status'] = AWARDED;
		$request['meta_input'] = array(
			WINNER_ID => $freelancer_id,
			BID_ID_WIN => $bid_id,
			'tem' => '123',
		);
		$res = wp_update_post( $request );



		if( $res ){

			global $user_ID;
			// create coversation
			// update bid status to AWARDED
			wp_update_post( array( 'ID' => $bid_id, 'post_status'=> AWARDED) );

			$this->send_mail_noti_assign( $project_id, $freelancer_id );

			return $res;
		}
		return new WP_Error( 'award_fail', __( "Depossit has something wrong", "boxtheme" ) );
	}

}