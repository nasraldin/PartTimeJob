<?php
//quit_job , fre_markascomplete, submit_disputing, review_emp, review_fre, frmadminact
// frmadminact = {ask_frem,ask_emp, choose_fre_win, choose_emp_win}

//admin act.
// normally
class Box_WorkSpace{
	static protected $instance;
	public $project_id;
	public $transaction_id;
	public $bid_winner_id;
	public $winner_id;
	public $owner_id;
	function __construct(  ){ //$project_id, $bid_winner_id

	}
	static function get_Instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function ws_action($args, $act){
		$project_id = $args['project_id'];
		$project  = get_post($project_id);
		$check = $this->check_ws_action($project);
		if( ! is_wp_error($check ) )
			return $this->$act($args);
		return $check;
	}
	function quit_job($args){
		// undepost
		global $user_ID;

		$project_id = $args['project_id'];
		$project = get_post($project_id);
		$check = $this->check_ws_action($project);
		if ( is_wp_error( $check ) ){
			return $check;
		}

		if ( $project->post_status != AWARDED ){
			return new WP_Error( 'status_wrong', __('This job is archived','boxtheme') );
		}
		$employer_id = $project->post_author;

		$bid_id = get_post_meta($project_id, BID_ID_WIN, true);
		$bid_price = (float) get_post_meta($bid_id, BID_PRICE, true);

		$credit = BX_Credit::get_instance();
		$transfered = $credit->undeposit( $employer_id, $bid_price, $freelancer_id );

		if ( is_wp_error($transfered) ){
			return $transfered;
		}
		BX_Order::get_instance()->create_undeposit_order($bid_price, $project);

		$request['ID'] = $project_id;
		//$request['post_status'] = ARCHIVED;
		$request['post_status'] = 'publish';
		$request['meta_input'] = array(
			WINNER_ID => 0,
			BID_ID_WIN => 0,
			'fre_markedascomplete'=> '',
		);
		wp_delete_post( $bid_id, true );
		$res = wp_update_post($request);
		if($res){
			wp_update_post( array('ID' => $bid_id, 'post_status'=> 'publish') );
			return $res;
		}

		return true;
	}
	function fre_markascomplete($args){
		box_log_track('fre_markascomplete');
		global $user_ID;
		$project_id = $args['project_id'];
		$project = get_post($project_id);
		if( $project->post_status == 'awarded' ){
			update_post_meta( $project_id, 'fre_markedascomplete', $args['review_msg'] );
			$this->do_after_fre_markascomplete($project);
		}
		$respond = array(
			'success' => true,
			'msg' => 'done',
		);
		wp_send_json( $respond);

	}
	function do_after_fre_markascomplete($project){
		//global $current_user;
		$current_user = wp_get_current_user();
		$owner_project = get_userdata($project->post_author);
		$owner_email = $owner_project->user_email;
		$subject = __('Your project is complete','boxtheme');
		$content = '<p>Dear #owner_name,</p> <p>Your project has been marked as complete by #fre_name.  Please check the result and approve/review it.</p>
				<p> Job title: #project_name<p> You can check the detail <a href="#project_link">here</a>.</p>Regards,';

		$project_link = get_permalink($project->ID);
		$project_link = add_query_arg('workspace','1', $project_link);
		$content = str_replace("#owner_name", $owner_project->user_login , stripcslashes($content) );
		$content = str_replace("#project_name", $project->post_title, stripcslashes($content) );
		$content = str_replace("#project_link", $project_link, stripcslashes($content));

		$content = apply_filters('project_complete_msg', $content, $project);
		box_mail( $owner_email, $subject, $content );

		do_action('after_fre_delivery', $project);

	}
	/**
	 * award project to a freelancer
	*/
	function award_project( $request ){
		$escrow =  BX_Option::get_instance()->get_escrow_setting();
		$type = $escrow->active;
		$bid_id = $request['bid_id'];
		$project_id = $request['project_id'];
		$freelancer_id = $request['freelancer_id'];
		$project = get_post($project_id);

		$check = $this->check_before_award( $project, $freelancer_id );
		if( is_wp_error( $check ) ){
			return $check;
		}
		$employer_id = $project->post_author;

		$respond  = array(
			'success' => true,
			'type' => $type,
			'msg' => __('Assign job successful.','boxtheme'),
			'url_redirect' => '',
		);
		// check balance and deducts.
		switch ($type) {
			case 'credit':
				$response = BX_Credit::get_instance()->act_award( $bid_id, $freelancer_id,  $project );

				break;
			case 'paypal_adaptive':
				$response = PP_Adaptive::get_instance()->act_award( $bid_id, $freelancer_id,  $project );
				break;
			default:
				# code...
				break;
		}

		if( is_wp_error( $response ) ){
			$respond['success'] = false;
			$respond['msg'] = $response->get_error_message();
		} else if( isset($response['url_redirect']) ){
			$respond['url_redirect'] = $response['url_redirect'];
		}

		wp_send_json( $respond);

	}
	function act_review( $request, $action ){
		$escrow =  BX_Option::get_instance()->get_escrow_setting();
		$type = $escrow->active;

		if ($action == 'review_fre') {
			switch ($type) {
				case 'credit':
					$response = BX_Credit::get_instance()->emp_mark_as_complete($request);

					break;
				case 'paypal_adaptive':
					$response = PP_Adaptive::get_instance()->emp_mark_as_complete($request);
					break;

				default:
					# code...
					break;
			}

			$respond = array('success' => true, 'msg'=> 'DONE');
			if( ! is_wp_error( $response ) ){
				if( isset($respond['respond']))
					$respond['url_redirect'] = $response['url_redirect'];

				$project = get_post($request['project_id']);
				$this->do_after_emp_markascomplete($project);

			} else {
				$respond['success'] = false;
				$respond['msg'] = $response->get_error_message();
			}
			wp_send_json( $respond);

		} else if($action == 'review_emp'){
			// action of freelancer.
			global $current_user;
			$project_id = $request['project_id'];
			$review_msg = $request[REVIEW_MSG];
			$rating_score = (int) $request[RATING_SCORE];
			$check = $this->check_before_fre_review($request);
			if($rating_score > 5){
				$rating_score = 5;
			}
			if($rating_score < 1){
				$rating_score = 1;
			}

			if ( is_wp_error($check) ){
				return $check;
			}


			$time = current_time('mysql');
			$project = get_post($project_id);
			$current_user = wp_get_current_user();

			$data = array(
				'comment_author' => $current_user->user_login,
				'comment_author_email' => $current_user->user_email,
			    'comment_post_ID' => $project_id,
			    'comment_content' => $review_msg,
			    'comment_type' => 'fre_review',
			    'user_id' => $project->post_author,
			    'comment_date' => $time,
			    'comment_approved' => 1,
			);

			$cmn_id = wp_insert_comment($data);
			if( !is_wp_error( $cmn_id ) ){
				add_comment_meta( $cmn_id, RATING_SCORE, $rating_score);
				update_post_meta( $project_id, 'is_fre_review', 1);
				$rating_score = count_rating( $project->post_author );
				update_user_meta( $project->post_author,RATING_SCORE,$rating_score );

			}
			$respond = array(
				'success' => true,
				'msg' => 'DONE'
			);
			wp_send_json( $respond);
		}
	}
	function do_after_emp_markascomplete($project){
		//global $current_user;
		$current_user = wp_get_current_user();
		$winner_id =  get_post_meta($project->ID, WINNER_ID, true);
		$winner_data  = get_userdata($winner_id);
		$fre_email = $winner_data->user_email;
		$subject = __('Your task is complete','boxtheme');
		$content = '<p>Congrat #fre_name,</p> <p>Your task is approved.</p>
				<p> Project title: #project_name<p> You can check the detail <a href="#project_link">here</a>.</p>Regards,';

		$project_link = get_permalink($project->ID);
		$project_link = add_query_arg('workspace','1', $project_link);
		$content = str_replace("#fre_name", $winner_data->user_login , stripcslashes($content) );
		$content = str_replace("#project_name", $project->post_title, stripcslashes($content) );
		$content = str_replace("#project_link", $project_link, stripcslashes($content));

		$content = apply_filters('emp_approve_msg', $content, $project);
		box_mail( $fre_email, $subject, $content );

		do_action('after_employer_review', $winner_id, $project);

	}
	/**
	 * admin send a message to employer or freelancer and choose the account winner in dispute case.
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $args [description]
	 * @return  [type]       [description]
	 */
	function admin_act($args){

		$act = isset($args['act']) ? $args['act'] : 0;
		$emp_id = isset($args['emp_id']) ? $args['emp_id'] : 0;
		$fre_id = isset($args['fre_id']) ? $args['fre_id'] : 0;
		$project_id = isset($args['project_id']) ? $args['project_id'] : 0;

		$response = array('success' => true,'msg' => 'done');

		if( ! current_user_can( 'manage_options' ) ){
			wp_die('Die');
		}
		switch ($act) {
			case 'ask_fre':
				$this->insert_disputing_msg($fre_id, $args);
				break;
			case 'ask_emp':
				$this->insert_disputing_msg($emp_id, $args);
				break;
			case 'choose_fre_win':
				// transfre the credit to freelancer
				//increase_credit_available
				BX_Credit::get_instance()->act_refund('freelancer',$project_id);
				// end transfer.
				break;

			case 'choose_emp_win':

				BX_Credit::get_instance()->act_refund('employer',$project_id);
				//end transfer.
				break;

			default:
				$response['success'] = false;
				$response['msg'] = __('Please select 1 option.','boxtheme');
				break;
		}
		wp_send_json( $response );
	}
	function check_ws_action($project){
		global $user_ID;
		$winner_id 	= get_post_meta($project->ID, WINNER_ID, true);
		if( current_user_can( 'manage_options' ) )
			return true;
		if( $project->post_author == $user_ID || ($winner_id && $winner_id == $user_ID) )
			return true;

		return new WP_Error( 'unallow', __( "You are not allowed to perform this action.", "boxtheme" ) );

	}
	function insert_disputing_msg($receive_id, $args ){
		$cvs_id = isset($args['cvs_id']) ? $args['cvs_id'] : 0;
		$args = array(
				'msg_content' => $args['msg_content'],
				'receiver_id' => $receive_id,
				'msg_is_read' => 0,
				'msg_type' => 'disputing',
				);

			$msg_dispute = BX_Message::get_instance($cvs_id)->insert($args);
	}
	function submit_disputing($args){
		global $user_ID;
		$project_id = $args['project_id'];
		$project = get_post($project_id);

		if( $project->post_status == 'awarded' ){

			$winner_id 	= get_post_meta($project->ID, WINNER_ID, true);
			wp_update_post( array('ID' => $project_id,'post_status' => 'disputing') );

			$cvs_id = is_sent_msg($project_id, $winner_id);
			$args = array(
				'sender_id' => 0,
				'msg_content' => $args['msg_content'],
				'receiver_id' => $project->post_author,
				'msg_is_read' => 0,
				'msg_type' => 'disputing',
				);
			if( $user_ID == $project->post_author){
				$args['receiver_id'] = $winner_id;
			}
			update_post_meta( $project->ID,'user_send_dispute', $user_ID );
			$msg_dispute = BX_Message::get_instance($cvs_id)->insert($args);
		}
		$respond = array(
			'success' => true,
			'msg' => 'done',
		);
		wp_send_json( $respond);
	}
	/**
	 * check the condition and make sure it fit with the flow of system.
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   object $project
	 * @param   int $freelancer_id
	 * @return  bool  true or false
	 */
	function check_before_award( $project, $freelancer_id ){


		global $user_ID;

		if( (int)$project->post_author !== $user_ID ){
			return new WP_Error( 'empty', __( "You are not author of this project.", "boxtheme" ) );
		}
		if( $project->post_status != 'publish'){
			return new WP_Error( 'empty', __( "This job is not available.", "boxtheme" ) );
		}
		if( empty( $freelancer_id ) ){
			return new WP_Error( 'empty', __( "Please choose a freelancer.", "boxtheme" ) );
		}

		return true;
	}
	function check_before_emp_review($request){
		$rating_score = $request[RATING_SCORE];
		if( empty($rating_score) ){
			return new WP_Error( 'empty', __( "You have to set score", "boxtheme" ) );
		}
		$project_id = $request['project_id'];
		$project = get_post($project_id);

		if($project->post_status != AWARDED){
			return new WP_Error( 'empty', __( "This project is not available", "boxtheme" ) );
		}
		global $user_ID;

		if( (int) $project->post_author != $user_ID){
			return new WP_Error( 'empty', __( "You are not author of this project.", "boxtheme" ) );
		}
		return true;
	}
	function check_before_fre_review($request){
		$rating_score = $request[RATING_SCORE];
		if( empty($rating_score) ){
			return new WP_Error( 'empty', __( "You have to set score", "boxtheme" ) );
		}
		$project_id = $request['project_id'];
		$is_fre_review = get_post_meta($project_id, 'is_fre_review', true);

		if( $is_fre_review ){
			return new WP_Error( 'empty', __( "You revieded this project.", "boxtheme" ) );
		}
		$winner_id  = get_post_meta($project_id,WINNER_ID, true);
		global $user_ID;

		if( (int) $winner_id != $user_ID){
			return new WP_Error( 'empty', __( "You are not winner.", "boxtheme" ) );
		}
		return true;
	}
}