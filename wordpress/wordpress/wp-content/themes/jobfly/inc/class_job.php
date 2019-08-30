<?php
if ( ! defined( 'ABSPATH' ) ) exit;

Class BX_Project extends BX_Post{
	static protected $instance;
	function __construct(){
		$this->post_type = JOB;
		add_action( 'after_insert_'.$this->post_type, 'do_after_insert');
	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function get_meta_fields(){
		return array( 'min_salary','max_salary','benefit','address', BOX_VIEWS);
	}
	function get_taxonomy_fields(){
		//return array('project_cat', 'skill');
		return array('location', 'skill');
	}

	function get_hierarchica_taxonomy(){
		return array('location');
	}
	function get_nonhierarchica_taxonomy(){
		return array('skill');
	}

	function insert($args){
		global $user_ID;
		$args['author'] 		= $user_ID;
		$args['post_status'] 	= 'publish';
		$args['post_type'] 		= $this->post_type;
		$args['meta_input'] 	= array();
		$metas 			= $this->get_meta_fields();
		$taxonomies 	= $this->get_taxonomy_fields();

		foreach ($metas as $key) {
			if ( !empty ( $args[$key] )  ){
				$args['meta_input'][$key] = $args[$key];
			}

		}
		$nonhierarchica = $this->get_nonhierarchica_taxonomy();
		$hierachice 	= $this->get_hierarchica_taxonomy();
		foreach ($taxonomies as $tax) {
			if ( !empty ( $args[$tax] )  ){
				$args['tax_input'][$tax] = $args[$tax];
			}
		}
		foreach ( $hierachice as $tax ) {
			if ( !empty ( $args['tax_input'][$tax] )  ){
				$args['tax_input'][$tax] = array_map('intval', $args['tax_input'][$tax]); // auto insert tax if admin post project. #111
			}
		}

		//https://developer.wordpress.org/reference/functions/wp_insert_post/
		$check = $this->check_before_insert( $args );
		if ( ! is_wp_error( $check ) ){
			$project_id = wp_insert_post($args);
			if ( !is_wp_error( $project_id ) ){
				if( isset($args['attach_ids']) ){
					foreach ($args['attach_ids'] as $attach_id) {
						wp_update_post( array(
							'ID' => $attach_id,
							'post_parent' => $project_id
							)
						);
					}
				}
				if( !current_user_can( 'manage_option' ) ){
					$this->update_post_taxonomies($project_id, $args); // #222 - back up for #111 when employer post project.
				}

				$count_posted = (int) get_user_meta( $user_ID,'project_posted', true ) + 1;
				update_user_meta( $user_ID, 'project_posted', $count_posted);
				return $project_id;

			}
			return new WP_Error( 'insert_fail',  $project_id->get_error_message() );

		} else {

			return new WP_Error( 'insert_fail',$check->get_error_message()  );
		}

		return $id;
	}

	/**
	 * [convert description]
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @return  [type] [description]
	 */
	function convert($post){

		$result = parent::convert($post);
		$profile_id =get_user_meta($post->post_author,'profile_id', true);
		global $currency_sign;
		$total_spent = (float) get_user_meta( $post->post_author, TOTAL_SPENT_TEXT, true);


		$result->total_spent_txt = sprintf( __( 'Spent %s','boxtheme'),box_get_price_format($total_spent) );
		$result->budget_txt = sprintf( __( 'Budget: %s','boxtheme'), box_get_price_format($result->_budget) );

		$result->location_txt =  get_user_meta( $post->post_author, 'location_txt', true); // country of emplo

		$not_set = __('Not set','boxtheme');
		$result->country = $not_set;
		$result->time_txt = bx_show_time($result);
		$result->short_des = wp_trim_words( $result->post_content, 62);

		if( $profile_id ){

			$pcountry = get_the_terms( $profile_id, LOCATION_CAT );

			if( !empty ( $pcountry ) ) {
				$result->location_txt =  $pcountry[0]->name;
			}

		}

		return $result;
	}

	function check_before_post($args){

		if( !is_user_logged_in() ){
			return new WP_Error( 'not_logged', __( "Please log in your account again.", "boxtheme" ) );
		}
		return true;
	}
	function call_action( $request, $action ){
		$escrow =  BX_Option::get_instance()->get_escrow_setting();
		$type = $escrow->active;

		if( $action == 'award' ){ // accept, hire assign task
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


			//wp_update
			// update post status and  freelancer of this project
		} else if($action == 'review_fre'){
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

			$respond = array('success' => true,'msg'=>'DONE');
			if( is_wp_error( $response ) ){
				$respond['success'] = false;
				$respond['msg'] = $response->get_error_message();
			} else if( isset($response['url_redirect']) ){
				$respond['url_redirect'] = $response['url_redirect'];
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
	function workspace_action($args, $act){
		$project_id = $args['project_id'];
		$project  = get_post($project_id);
		$check = $this->check_workspace_action($project);
		if( !is_wp_error($check ) )
			return $this->$act($args);
		return $check;
	}
	function quit_job($args){
		// undepost
		global $user_ID;

		$project_id = $args['project_id'];
		$project = get_post($project_id);
		$check = $this->check_workspace_action($project);
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

		global $user_ID;
		$project_id = $args['project_id'];
		$project = get_post($project_id);
		if( $project->post_status == 'awarded' ){
			update_post_meta( $project_id, 'fre_markedascomplete', $args['review_msg'] );
		}
		$respond = array(
			'success' => true,
			'msg' => 'done',
		);
		wp_send_json( $respond);

	}

	/**
	 * admin send a message to employer or freelancer and choose the account winner in dispute case.
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $args [description]
	 * @return  [type]       [description]
	 */
	function frmadminact($args){

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
				update_post_meta( $project_id,'choose_dispute_winner', $fre_id);
				update_post_meta( $project_id,'choose_dispute_msg', $args['msg_content']);
				wp_update_post( array( 'ID'=> $project_id, 'post_status' => 'resolved') );
				break;

			case 'choose_emp_win':
				update_post_meta($project_id,'choose_dispute_winner', $fre_id);
				update_post_meta($project_id,'choose_dispute_msg', $args['msg_content']);
				wp_update_post( array( 'ID' => $project_id, 'post_status' => 'resolved'));
				break;

			default:
				$response['success'] = false;
				$response['msg'] = __('Please select 1 option.','boxtheme');
				break;
		}
		wp_send_json( $response );
	}
	function insert_disputing_msg($receive_id, $args ){
		$cvs_id = isset($args['cvs_id']) ? $args['cvs_id'] : 0;
		$args = array(
				'msg_content' => $args['msg_content'],
				'msg_link' => get_permalink($project_id),
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
				'msg_link' => get_permalink($project_id),
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
	function check_workspace_action($project){
		global $user_ID;
		$winner_id 	= get_post_meta($project->ID, WINNER_ID, true);
		if( current_user_can( 'manage_options' ) )
			return true;
		if( $project->post_author == $user_ID || ($winner_id && $winner_id == $user_ID) )
			return true;

		return new WP_Error( 'unallow', __( "You are not allowed to perform this action.", "boxtheme" ) );

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
	function mark_as_premium_post($order){
		$pack_id = get_post_meta( $order->ID, 'pack_id', true);
		$priority = get_post_meta( $pack_id, 'priority', true );
		$project_id = get_post_meta( $order->ID, 'pay_premium_post', true  );
		update_post_meta( $project_id, 'priority', $priority);

	}

}