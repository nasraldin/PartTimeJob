<?php
Class Box_Escrow{

	function __construct(){
		$paypal_adaptive = (OBJECT) BX_Option::get_instance()->get_group_option('paypal_adaptive');

	}
	/**
	 * call this function when employer award job done and paid to system( not release)
	 * for paypal: after pay and the transaction has the "INCOMPLETE" status
	*/

	function send_mail_noti_assign( $project, $freelancer_id, $cvs_status = 'publish'){

		Box_ActMail::get_instance()->assign_job( $freelancer_id, $project );
		if( is_numeric( $project ) ){
			$project = get_post($project);
		}
		$project_id = $project->ID;

		$request = isset( $_REQUEST['request'] ) ? $_REQUEST['request'] : '';
		$cvs_content  = 'New project is assign for you';
		if( $request){
			$cvs_content = isset( $request['cvs_content'])? $request['cvs_content']: '';
		}
		$cvs_id = is_sent_msg( $project_id, $freelancer_id );
		global $user_ID;
		if ( ! $cvs_id ) {
			$args  = array(
				'project_id' => $project_id,
				'receiver_id' => $freelancer_id,
				'cvs_content' => $cvs_content,
				'cvs_status' => $cvs_status,
			);

			BX_Conversations::get_instance()->insert( $args, $assign = 1 );

		} else {
			$msg_arg = array(
				'msg_content' 	=> $cvs_content,
				'cvs_id' 		=> $cvs_id,
				'receiver_id'=> $freelancer_id,
				'sender_id' => $project->post_author,
				'msg_type' => 'message',
			);
			$msg_id =  BX_Message::get_instance($cvs_id)->insert($msg_arg); // msg_id\
			$employer = get_userdata( $project->post_author );

		}
	}
	function mark_as_complete( $project_id, $request){
		global $current_user;
		$bid_win_id = get_post_meta($project_id, BID_ID_WIN, true);
		$review_msg = $request[REVIEW_MSG];
		$rating_score = (int) $request[RATING_SCORE];
		if($rating_score > 5){
			$rating_score = 5;
		}
		if( $rating_score < 1 ) {
			$rating_score = 1;
		}

		$winner_id 	= get_post_meta($project_id, WINNER_ID, true); // freelancer_id

		$bid_price 	= (float) get_post_meta($bid_win_id, BID_PRICE, true);

		$commision_fee = get_commision_fee($bid_price); // web owner will get this amount.

		$emp_pay = $bid_price;
		$amount_fre_receive = $bid_price - $commision_fee;

		$project_worked = (int) get_user_meta( $winner_id, PROJECTS_WORKED, true) + 1;
		$earned = (float) get_user_meta( $winner_id,EARNED, true) + $amount_fre_receive;


		update_user_meta( $winner_id, PROJECTS_WORKED , $project_worked );
		update_user_meta( $winner_id, EARNED , $earned);

		$bid_args = array(
			'ID' 	=> $bid_win_id,
			'post_status' => COMPLETE,

		);
		$bid = wp_update_post( $bid_args);

		$current_user = wp_get_current_user();
		$time = current_time('mysql');
		$data = array(
		    'comment_post_ID' => $bid_win_id,
		    'comment_author' => $current_user->user_login,
		    'comment_author_email' => $current_user->user_email,
		    'comment_content' => $review_msg,
		    'comment_type' => 'emp_review',
		    'comment_approved' => 1,
		    'user_id' => $winner_id,
		    'comment_date' => $time,
		);

		$cmn_id = wp_insert_comment($data);
		if( !is_wp_error($cmn_id)){
			add_comment_meta($cmn_id, RATING_SCORE, $rating_score);
			$rating_score = count_rating($winner_id);
			update_user_meta($winner_id,RATING_SCORE,$rating_score);
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

}