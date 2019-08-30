<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce WC_AJAX.
 *
 * AJAX Event Handler.
 *
 * @class    WC_AJAX
 * @version  2.4.0
 * @package  WooCommerce/Classes
 * @category Class
 * @author   WooThemes
 */
class BX_AJAX {
	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		//add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		//self::add_ajax_events();
		// woocommerce_EVENT => nopriv
		$ajax_events = array(
			'sync_bid' 				=> false,
			'bx_login'         		=> true,
			'bx_signup' 			=> true,
			'apply_coupon'     		=> true,
			'fb_signout' 			=> false,
			'sync_project' 		 	=> false,
			'sync_profile' 			=> true,
			'bj_plupload_action' 	=> true,
			'sync_message' 			=> false,
			'sync_conversations'	=> false,
			//'sync_account' 			=> true,
			'update_avatar'			=> false,
			'award_project'			=> false,
			'workspace_act'		=> false,
			'act_review' 			=> false,
			'sync_attachment'      	=> false,
			'upload_file' 			=> false,
			'sync_search' 			=> true,
			'box_checkout'            => false, // deposit credit
			'box_membership_checkout'  => false,
			'box_upload_file' 		=> false,
			'sync_msg' 				=> false,
			'sync_portfolio'		=> false,
			'custom_avatar' 		=> false,
			'social_signup' 		=> true,

			'request_withdraw' => false,
			'update_withdraw_info' => false,
			'send_new_confirm_email' => false,
			'generate_price' => false,
			'sync_notify' => true,
			'bx_resetpass' => true,
			'send_job_to_email' => true,
			'toggleActivateRole' => false,
			'send_direct_message' => false,
			'invite_bid' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {

			add_action( 'wp_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				// user no logged
				add_action( 'wp_ajax_nopriv_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Get WC Ajax Endpoint.
	 * @param  string $request Optional
	 * @return string
	 */
	// public static function get_endpoint( $request = '' ) {
	// 	return esc_url_raw( add_query_arg( 'wc-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart' ) ) ) );
	// }

	/**
	 * Set WC AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['wc-ajax'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			if ( ! defined( 'WC_DOING_AJAX' ) ) {
				define( 'WC_DOING_AJAX', true );
			}
			// Turn off display_errors during AJAX events to prevent malformed JSON
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 );
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for WC Ajax Requests
	 * @since 2.5.0
	 */
	private static function wc_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_wc_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['wc-ajax'] ) ) {
			$wp_query->set( 'wc-ajax', sanitize_text_field( $_GET['wc-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'wc-ajax' ) ) {
			//self::wc_ajax_headers();
			do_action( 'wc_ajax_' . sanitize_text_field( $action ) );
			//die();
		}
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {

	}
	/**
	 * ajax login after submit modal login form
	 * @since   1.0
	 * @author wa-login.php
	 * @return json
	 */
	public static function bx_login(){

		$request 	= $_REQUEST;
		$info 		= $request['request'];

		// $captcha = isset($request['captcha']) ? $request['captcha'] : '';
		// $response = box_get_response( $captcha );

		// if ( is_wp_error($response) ) {
		// 	$response= array( 'success'=> false,
		// 		'msg' => __('Invalid captcha','boxtheme')
		// 	);
		// 	wp_send_json( $response );
		// }

		/*
		 * check security
		 */
		if( ! wp_verify_nonce( $info['nonce_login_field'], 'bx_login' ) ) {
			wp_send_json( array( 'success' => false, 'msg'=> _e('The nonce field is incorrect','boxtheme') ) ) ;
	    }
	    $response = bx_signon($info);
	    wp_send_json( $response );

	}
	public static function bx_signup(){

		$response = array(
				'success' 	=> false,
				'msg' 		=> __('Has something wrong', 'boxtheme'),
			);
		$request 	= $_REQUEST['request'];

		$captcha = isset($request['g-recaptcha-response']) ? $request['g-recaptcha-response'] : '';
		$rescap = box_get_response( $captcha );

		if ( is_wp_error($rescap) ) {
			$response= array( 'success'=> false,
				'msg' => __('Invalid captcha','boxtheme')
			);
			wp_send_json( $response );
		}

		//signup_nonce_check($request); // auto die if uncorrect nonce fields
		$user 		= new BX_User();
		$employer_type = isset($request[EMPLOYER_TYPE]) ? $request[EMPLOYER_TYPE] : INDIVIDUAL;

		if ( empty($request['user_pass']) )
			$request['user_pass'] = wp_generate_password( 12, true );


		$user_id = $user->sync( $request, 'insert');
		$is_membership = false;
		if( isset($request['is_membership']) )
			$is_membership = true;

		$response['nextstep'] = $is_membership;


		if ( ! is_wp_error( $user_id ) ) {
			update_user_meta($user_id, EMPLOYER_TYPE, $employer_type);
			//auto login
			bx_signon($request);
			$user_data = get_userdata($user_id);
			$response = array(
				'success' 	=>	true,
				'redirect_url' => box_get_static_link('verify'),
				'msg' 		=> __('You have registered successful','boxtheme'),
				'data' 		=> $user,
				'nextstep' => $is_membership,
			);
			$mail_to = $request['user_email'];
			if( ! box_requires_confirm() ){
				box_auto_gernerat_profile($user_data);
			}
			Box_ActMail::get_instance()->act_signup_mail($user_data, $user_data->user_email);

		} else {
			$response['msg'] =  $user_id->get_error_message();
		}

		wp_send_json($response );
	}
	/**
	 * signout curren user
	 * @since   1.0
	 * @author boxtheme
	 * @return  json
	 */
	public static function fb_signout(){
		wp_logout();
		$response = array( 'success' => true, 'msg' => __( 'You have logout successful', 'boxtheme') );
		wp_send_json( $response );
	}
	/**
	 * catch the ajax for job process.
	 * @version [version]
	 * @since   1.0
	 * @author boxtheme
	 * @return  ajax
	 */
	public static function sync_project(){

		$args 		= $_REQUEST;
		$method 	= $_REQUEST['method'];


		$response 	= array('success' => false, 'msg' => __('Insert project fail','boxtheme'), 'data' => array() );

		// if( $method == 'insert' ) {
		// 	if ( ! wp_verify_nonce( $args['nonce_insert_project'], 'sync_project' ) ) {
		// 		wp_send_json( array( 'success' => false, 'msg'=> _e('The nonce field is incorrect','boxtheme') ) ) ;
		//     }
		// }

		$project 	= BX_Project::get_Instance();
		$return 	= $project->sync($method, $args);

		$msg = array(
			'insert' => __('You have posted job successfully','boxtheme'),
			'update' => __('You have updated job successfully','boxtheme'),
			'delete' => __('You have deleted job successfully','boxtheme'),
			'archived' => __('You have archived job successfully','boxtheme'),
		);
		$premium_post  = false;
		if ( !is_wp_error( $return ) ) {
			$premium_post = isset( $args['premium_post'] ) ? (int) $args['premium_post'] : 0; // Premium type ID

			$redirect_url 	= get_permalink($return);
			if($premium_post){
				$deposit_link = box_get_static_link('deposit');
				$redirect_url = add_query_arg( 'pack',$premium_post, $deposit_link);
			}



			$response = array(
				'msg' => $msg[$method],
				'success' => TRUE,
				'premium_post' => $premium_post,
				'redirect_url' => $redirect_url,
				'job' => $return,
			);
		} else {
			$response = array(
				'success' 	=> 0,
				'msg' 		=> $return->get_error_message(),
				'premium_post' => $premium_post,
			);
		}
		wp_send_json( $response );
	}
	/**
	 *  process all action of bidding.
	 * @author boxtheme
	 * @version 1.0
	 * @return  void
	 */
	public static function sync_bid(){
		$request 	= $_REQUEST;
		$data 		= $_REQUEST['request'];

		$method 	= isset($request['method']) ? $request['method'] : '';
		$response 	= array('success' => true, 'msg'=> __('You have bid successful','boxtheme') );

		if( $method == 'insert' ) {

			// check secutiry
			if ( ! wp_verify_nonce( $data['nonce_bid_field'], 'sync_bid' ) ) {
				wp_send_json( array( 'success' => false, 'msg'=> _e('The nonce field is incorrect','boxtheme') ) ) ;
		    }
		}

		$bid 		= BX_Bid::get_instance();
		$bid_id 	= $bid->sync( $method, $data);

		$response = array( 'success' => true,'msg' => __('You have bid successfully','boxtheme' ) );

		if( is_wp_error( $bid_id )){
			$response = array('success' => false,'msg' => $bid_id->get_error_message() );

		}

		if( isset( $data['ID']) )
			$response['msg'] = __('Update  successful','boxtheme');


		wp_send_json($response );
	}

	static function sync_profile(){
		$request 	= $_REQUEST;
		$method 	= isset($request['method']) ? $request['method'] : '';
		$args 		= $_REQUEST['request'];
		$is_emp 	= 	isset( $args['is_emp'] ) ? $args['is_emp'] : 0;

		if( $is_emp ) {
			global $user_ID;
			// update employer;
			$country = isset($args['country']) ? $args['country'] : 0;
			if( $country ) {
				update_user_meta( $user_ID, 'location', $country );
			}
			$first_name = isset($args['first_name']) ? $args['first_name'] : '';
			$last_name = isset($args['last_name']) ? $args['last_name'] : '';
			$user_email = isset($args['user_email']) ? $args['user_email'] : '';
			$display_name = isset($args['display_name']) ? $args['display_name'] : '';
			$new_arrgs = array(
				'ID' 	=> $user_ID,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'user_email' => $user_email,
				'display_name' => $display_name,
			);
			$emp_id = wp_update_user($new_arrgs);
			if ( ! is_wp_error( $emp_id ) ){
				// udate static location here;
				$location = get_term( $country );
				if( $location && !is_wp_error($location) )
					update_user_meta( $emp_id, 'location_txt', $location->name );
			}

			$response = array('success' => true, 'msg' => 'OK');
			wp_send_json($response );
			wp_die('done');
		}

		$profile 	= BX_Profile::get_instance();
		$result = $profile->sync($args, $method);

		$response 	= array('success' => true, 'msg'=> __('You have updated profile successfull','boxtheme'), 'result' => $result);

		if( is_wp_error( $result ) ){

			$response = array('success' => false,'msg' =>$result->get_error_message());

		}

		wp_send_json($response );

	}
	/**
	 * send a message to partner.
	 * @author boxtheme
	 * @version 1.0
	 * @return  void
	 */
	static function sync_message(){

		$response = array( 'success' => false,'msg' =>'' );
		$submit 	= $_REQUEST;
		$method 	= isset($submit['method']) ? $submit['method'] : '';
		$request 	= $submit['request'];
		$response 	= array('success' => true, 'msg'=> $request['msg_content'] );
		$receiver_id = isset($request['receiver_id']) ? $request['receiver_id']:0;
		$cvs_id 	= isset($request['cvs_id']) ? $request['cvs_id'] : 0;
		if( !$cvs_id && $method == 'insert'){
			$cvs 	= BX_Conversations::get_instance();
			$project_id = isset($request['project_id']) ? $request['project_id'] : 0;
			$cvs_args = array(
				'cvs_content' => $request['msg_content'],
				'project_id' => $project_id,
				'receiver_id' => $receiver_id
			);

			$msg = $cvs->sync($cvs_args, 'insert');

			$response = array('success'=> true,'msg' => 'Createa converstaion done','boxtheme', 'result'=>  $msg);
			wp_send_json( $response );
		} else {

			$message 	= BX_Message::get_instance($cvs_id);
			$msg_id = $message->sync($request, $method);
			if( is_wp_error( $msg_id )){
				$response = array(
					'success' => false,
					'msg' =>$msg_id->get_error_message()
				);
				wp_send_json($response );
			}
			$data = box_get_message($msg_id);
			global $user_ID;
			$data->avatar = get_avatar($user_ID);
			$date = date_create( $data->msg_date );
			$data->msg_date =date_format($date,"m/d/Y");
			$response = array(
					'success' => true,
					'data' =>$data,
				);
		}
		wp_send_json($response );
	}
	static function sync_conversations(){
		$request 	= $_REQUEST;
		$method 	= isset($request['method']) ? $request['method'] : '';
		$args 		= $_REQUEST['request'];

		$response 	= array('success' => true, 'msg'=> __('Create conversation successful','boxtheme') );
		$cvs 	= BX_Conversations::get_instance();

		$msg_id = $cvs->sync($args, $method);
		if( is_wp_error( $msg_id )){
			$response = array('success' => false,'msg' =>$msg_id->get_error_message());
		}
		wp_send_json($response );
	}

	static function award_project(){
		$request 	= $_REQUEST;
		$args 		= $request['request'];
		$method 	= $_REQUEST['method']; // default method/action = 'award'

		$response 	= array('success' => false, 'msg' => __('Award job successful','boxtheme'), 'data' => array() );
		$workspace 	= Box_WorkSpace::get_Instance();
		$return 	= $workspace->award_project($args ); // method in {award,}
		if ( !is_wp_error( $return ) ) {
			$response = array(
				'success' => TRUE,
				'msg' => __('Award job successful','boxtheme'),
				'data' => get_post($return),
				);
		} else {
			$response = array(
				'success' 	=> 0,
				'msg' 		=> $return->get_error_message(),
				'data' 		=> array(),
			);
		}
		wp_send_json( $response );
	}
	static function workspace_act(){
		$request 	= $_REQUEST;
		$args 		= $request['request'];
		$method 	= $_REQUEST['method']; //quit_job,

		$response 	= array('success' => true, 'msg' => __('Review job','boxtheme'), 'data' => array() );
		$workspace 	= Box_WorkSpace::get_Instance();
		$return 	= $workspace->ws_action($args , $method);

		if ( !is_wp_error( $return ) ) {
			$response = array(
				'success' => true,
				'msg' => __('Review job done','boxtheme'),
				'data' => get_post($return),
				);
		} else {
			$response = array(
				'success' 	=> false,
				'msg' 		=> $return->get_error_message(),
				'data' 		=> array(),
			);
		}
		wp_send_json( $response );
	}
	/**
	 * Employer mark as close this project and review freelancer
	*/
	static function act_review(){
		$request 	= $_REQUEST;
		$args 		= $request['request'];
		$method 	= $_REQUEST['method'];

		$response 	= array('success' => true, 'msg' => __('Review job','boxtheme'), 'data' => array() );
		$workspace 	= Box_WorkSpace::get_Instance();
		$return 	= $workspace->act_review($args , $method);
		if ( !is_wp_error( $return ) ) {
			$response = array(
				'success' => true,
				'msg' => __('Review job done','boxtheme'),
				'data' => get_post($return),
				);
		} else {
			$response = array(
				'success' 	=> false,
				'msg' 		=> $return->get_error_message(),
				'data' 		=> array(),
			);
		}
		wp_send_json( $response );
	}



	static function sync_account(){
		$request 	= $_REQUEST;
		$method 	= isset($request['method']) ? $request['method'] : '';
		$args 		= $_REQUEST['request'];
		$response 	= array('success' => true, 'msg'=> __('Update user information successful','boxtheme') );
		$user 		= bx_user::get_instance();
		$user 		= $user->sync($args, $method);


		if( is_wp_error( $user )){
			$response = array('success' => false,'msg' =>$profile_id->get_error_message());
		}
		wp_send_json($response );

	}

	static function update_avatar(){
		global $user_ID;
		$args = $_REQUEST;
		$url = $args['avatar_url'];
		update_user_meta($user_ID,'avatar_url', $url);
		$response 	= array('success' => true, 'msg'=> __('Update avatar ok','boxtheme') );
		wp_send_json($response);
	}

	static function sync_attachment(){

		$request 	= $_REQUEST['request'];
		$method 	= $request['method'];
		$attachmentid = $request['id'];
		$att = get_post($attachmentid);
		$project_id  = $att->post_parent;

		if( ! empty( $project_id) ) {
			$project = get_post($project_id);
			global $user_ID;

			if ( in_array($project->status, array('awared','complete','disputing','resolved') ) &&

				( ! is_current_box_administrator() || $project->post_author != $user_ID)
			   ){
				wp_send_json( array('success'=> false,'msg' => __('You are not allowed to perform this action','boxtheme') ) );
			}
		}

		if ( false !== wp_delete_attachment( $attachmentid, true ) ) {
			wp_send_json(array('success' => true,'msg' => __('Remove attachment successful','boxtheme')) );
		}
		wp_send_json(array('success' => false,'msg' => __('Remove attachment fail','boxtheme')) );
	}
	static function box_upload_file(){

		$post_parent_id = 0;
		$request 		= $_REQUEST;
		$tmp_file 	= $_FILES['file'];
		//$tmp_file['name'] = abc.jpg
		/*array(5) {
		  ["name"]=>
		  string(7) "abc.jpg"
		  ["type"]=>
		  string(10) "image/jpeg"
		  ["tmp_name"]=>
		  string(24) "D:\Xampp\tmp\php6193.tmp"
		  ["error"]=>
		  int(0)
		  ["size"]=>
		  int(220445)
		}
		*/
		do_action( 'box_authentication_upload' );

		if ( isset( $request['post_parent']) )
			$post_parent_id = $request['post_parent'];

		if ( ! wp_verify_nonce( $request['nonce_upload_field'], 'box_upload_file' ) ) {
			wp_die( __('secutiry issues','boxtheme') );
		}


	    $upload_overrides = array( 'test_form' => false );
		$uploaded_file 	= wp_handle_upload( $tmp_file, $upload_overrides );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();
        //if there was an error quit early
        if ( isset( $uploaded_file['error'] ) ) {
        	wp_send_json( array('success'=> false, 'msg' => $uploaded_file['error'] ) );
        } elseif ( isset($uploaded_file['file']) ) {
            // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
            $file_name_and_location = $uploaded_file['file'];
            // Generate a title for the image that'll be used in the media library
            $file_kb = (float)  round( $tmp_file['size']/1024, 1);
            if( $file_kb >= 1024 ){
            	$file_kb = round($tmp_file['size']/1024/1024, 0) . ' mb';
            } else{
            	$file_kb .= ' kb';
            }

            $file_title_for_media_library = sanitize_file_name($tmp_file['name']) . '('. $file_kb.')';
            $wp_upload_dir = wp_upload_dir();

            // Set up options array to add this file as an attachment
            global $user_ID;
            $attachment = array(
                'guid' => $uploaded_file['url'],
                'post_mime_type' => $uploaded_file['type'],
                'post_title' => $file_title_for_media_library,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_ID
            );

            // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.

            $attach_id = wp_insert_attachment($attachment, $file_name_and_location, $post_parent_id);

            if( !is_wp_error($attach_id) ) {
            	$attachment['id'] = $attach_id;
            	require_once (ABSPATH . "wp-admin" . '/includes/image.php');
            	$attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);
        	    wp_update_attachment_metadata($attach_id, $attach_data);

        	    wp_send_json( array('success' => true,'file' => $attachment, 'msg' => __('Uploaded is successful','box_theme') ,'attach_id' => $attach_id ));
        	}
       	    wp_send_json( array('success' => false, 'msg' => $attach_id->get_error_message() ) );
		}
	}
	static function upload_file(){

		$request 		= $_REQUEST;
		$uploadedfile 	= $_FILES['file'];
		$upload_overrides = array( 'test_form' => false );
		$method = isset($request['method']) ? $request['method'] : '';
		$post_parent_id = isset( $request['post_parent'] ) ? $request['post_parent']: 0;
		$cvs_id 	= isset( $request['cvs_id']) ? $request['cvs_id'] : 0;

		do_action( 'box_authentication_upload' );

		$uploaded_file 	= wp_handle_upload( $uploadedfile, $upload_overrides );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();
        //if there was an error quit early
        if ( isset( $uploaded_file['error'] ) ) {

        	wp_send_json( array('success'=> false, 'msg' => $uploaded_file['error'] ) );

        } elseif ( isset( $uploaded_file['file'] ) ) {

            // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
            $file_name_and_location = $uploaded_file['file'];
            // Generate a title for the image that'll be used in the media library
            $file_kb = (float)  round( $uploadedfile['size']/1024, 1);
            if( $file_kb >= 1024 ){
            	$file_kb = round($uploadedfile['size']/1024/1024, 1) . ' mb';
            } else{
            	$file_kb .= ' kb';
            }

            $file_title_for_media_library = sanitize_file_name($uploadedfile['name']) . '('. $file_kb.')';
            $wp_upload_dir = wp_upload_dir();

            // Set up options array to add this file as an attachment
            global $user_ID;
            $attachment = array(
                'guid' => $uploaded_file['url'],
                'post_mime_type' => $uploaded_file['type'],
                'post_title' => $file_title_for_media_library,
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_ID
            );

            // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.
            $section = isset( $request['section'] ) ? $request['section'] : '';
            if( $section == 'workspace' ){
            	$attachment['post_status'] = 'private';
            }
            $attach_id = wp_insert_attachment($attachment, $file_name_and_location, $post_parent_id);
            if( !is_wp_error($attach_id) ) {
            	$attachment['id'] = $attach_id;
            	require_once (ABSPATH . "wp-admin" . '/includes/image.php');
            	$attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);
        	    wp_update_attachment_metadata($attach_id, $attach_data);

				$msg_arg = array(
					'msg_content' 	=> sprintf(__('Upload new file: %s','boxtheme'), $file_title_for_media_library ),
					'cvs_id' 		=> $cvs_id,
				);

				//BX_Message::get_instance()->insert($msg_arg);

        	    wp_send_json( array('success' => true,'file' => $attachment, 'msg' => __('Uploaded is successful','box_theme') ,'attach_id' => $attach_id ));
        	}
       	    wp_send_json( array('success' => false, 'msg' => $attach_id->get_error_message() ) );
		}
	}
	static function sync_search(){

		$request = $_REQUEST['request'];

		$paged = isset($request['paged']) ? $request['paged'] : 1;
		$post_type = isset($request['post_type']) ? $request['post_type'] : 1;

		$args = array(
			'paged' => $paged,
			'post_type' => $post_type,
			'post_status' => 'publish',
		);

		$skills = isset($request['skills']) ? $request['skills'] : '';
		$cats 	= isset($request['cats']) ? $request['cats'] : '';
		$countries 	= isset($request['countries']) ? $request['countries'] : '';
		$from 	= isset($request['from']) ? $request['from']:0;
		$to 	= isset($request['to']) ? $request['to']:100000;
		$keyword = isset($request['keyword']) ? $request['keyword'] : '';
		$href = isset($request['href']) ? $request['href']:'';
		$lat_address  = isset( $request['lat_address'] ) ? $request['lat_address'] : '';
		$distance  = isset( $request['distance'] ) ? $request['distance'] : '';

		if( ! empty( $keyword ) ){
			$args['s'] = $keyword;
		}
		if( $post_type = 'profile' && ! empty( $lat_address) && !empty($distance) ){
			$result = get_list_freelancer_by_distance($request);
			$res = array(
				'success' => true,
				'sql' => $result['sql'],
				'msg' => $result['msg'],
				'result' => $result['posts'],
				'job_found' => $result['msg'],
				'pagination' => false,
				'href' => $href,

			);
			wp_send_json($res);
			die();
		}
		if( ! empty( $skills ) ) {
			//$skills = array_unique( array_map( 'intval', $skills ) );
			$args['tax_query'][] = array(
				'taxonomy' => 'skill',
				'field'    => 'term_id',
				'terms'    => $skills,
				'operator' => 'IN',
			);
		}

		if( ! empty( $cats ) ) {
			//$cats = array_unique( array_map( 'intval', $cats ) );
			$args['tax_query'][] = array(
				'taxonomy' => 'project_cat',
				'field'    => 'term_id',
				'terms'    => $cats,
				'operator' => 'IN',
			);
		}
		if( ! empty( $countries ) ) {
			//$cats = array_unique( array_map( 'intval', $cats ) );
			$args['tax_query'][] = array(
				'taxonomy' => box_get_country_args()->slug,
				'field'    => 'term_id',
				'terms'    => $countries,
				'operator' => 'IN',
			);
		}
		$args['tax_query']['relation '] = 'AND';

		if( $post_type == 'project' && ( !empty($from) || !empty($to) ) ){
			$args['meta_query'][] =	array(
				'key'     => BUDGET,
				'value'   => array( $from, $to ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			);
		}

		// if( current_user_can('manage_option') ){
		// 	unset($args['post_status'] );
		// }

		$the_query = new WP_Query($args);

		$result = array();
		$pagination = false;


		$class = BX_Profile::get_instance();
		$rlable =  sprintf( '<h5>'._n( '%s profiles found', '%s profiles found', $the_query->found_posts, 'boxtheme' ).'</h5>', $the_query->found_posts);

		if( $post_type == 'project' ){
			$class = BX_Project::get_instance();
			$rlable =  sprintf( '<h5>'._n( '%s job found', '%s jobs found', $the_query->found_posts, 'boxtheme' ).'</h5>', $the_query->found_posts);

		}

		if( $the_query->have_posts() ){
			while ( $the_query->have_posts()) {
				global $post;
				$the_query->the_post();
				$result[] = $class->convert($post);
			}

			if( $the_query->max_num_pages ){
				$basic_url = get_post_type_archive_link($post_type);
				$pagination = bx_pagenate( $the_query, array('base' => $basic_url.'page/%#%/'), false);
			}
			// add paging html
		}
		$msg = __('Search result is empty','boxtheme');
		wp_send_json(
			array(
				'success' => true,
				'msg' => $rlable,
				'result' => $result,
				'job_found' => $rlable,
				'pagination' => $pagination,
				'href' => $href,

			)
		);
	}
	/**
		* deposit credit.
	*/

	static function box_checkout(){

		$request 	= $_REQUEST['request'];
		$gateway 	= $request['_gateway'];
		$amount 	= $_REQUEST['amount'];

		$project_id = isset( $request['project_id'] ) ? $request['project_id'] : 0; // post parent of order
		// order_id or url redirect or form html

		$response 	= box_create_deposit_draft_order($gateway, $amount);
		//$response 	= box_create_draft_order($gateway, $amount);

		//$respond = box_checkout_get_redirect($gateway, $order_created);

		wp_send_json($response);

	}
	static function box_membership_checkout(){
		$request = $_REQUEST['request'];
		global $user_ID;
		box_do_confirm_account($user_ID);
		$gateway = $request['gateway'];
		$package_id = $request['package_id'];
		$price = get_post_meta( $package_id, PRICE, true );

		//$project_id = isset( $request['project_id'] ) ? $request['project_id'] : 0; // post parent of order

		$url = create_subcription_draft_order($gateway, $package_id);
\
		wp_send_json( array(
			'msg' => 'Subscription Sucessful.',
			'success'=> true,
			'redirect_url' => $url,
			)
		);


	}
	static function sync_msg(){

		$msg 	= BX_Message::get_instance();
		$args 	= $_REQUEST['request'];

		$method = $args['method'];
		$args['mark_as_read'] = 1;
		$messages 	= $msg->sync( $args,  $method );

		wp_send_json( array('success'=> true, 'data'=>$messages) );

	}
	static function sync_portfolio(){

		$port 	= Box_Portfolio::get_instance();
		$args 	= $_REQUEST['request'];
		$method = $_REQUEST['method'];
		$args['post_content'] = 'Portfolio of user A';

		$port_id 	= $port->sync(  $method, $args  );
		$respond = array('success'=> false,'msg' =>'');
		if( !is_wp_error($port_id ) ){
			$post = get_post($port_id);
			$post->feature_image = get_the_post_thumbnail_url($port_id, 'full');
			$post->thumbnail_id = get_post_thumbnail_id($port_id);
			$respond = array('success'=> true, 'msg'=>__('Add portfolio successful','boxtheme') , 'data' =>  $post );
		} else {
			$respond['msg'] = $port_id->get_error_message();
		}
		wp_send_json( $respond);
	}
	static function custom_avatar(){

		global $user_ID;
		$request = $_REQUEST['request'];
		$avatar_att_id = $request['avatar_att_id'];
		$avatar_url = wp_get_attachment_url($avatar_att_id);
		update_user_meta($user_ID,'avatar_url', $avatar_url);
		update_user_meta($user_ID,'avatar_att_id', $avatar_att_id);

		$response = array('success' => true,'msg'=> 'Avatar is updated','avatar_url'=>$avatar_url);
		wp_send_json( $response );

	}

	static function social_signup(){
		$request = $_REQUEST['request'];
		$instance = Box_Social::get_instance();
		$redirect_url = false;

		$result = $instance->auto_login($request);
		$response = array('success' => true,'msg'=> 'Login done', $redirect_url  => 0);

		if( is_wp_error($result ) ){

			$code = $result->get_error_code();
			if( $code == 'exists_email' ){
				$redirect_url = add_query_arg( array('email' => $request['user_email']), box_get_static_link('login') );
			} else {
				$redirect_url =  box_get_static_link('login');
			}

			$response = array('success' => false,'msg'=> $result->get_error_message(), 'redirect_url' => $redirect_url );
		}
		wp_send_json( $response );
	}

	/**
	 * freelancer request 1 withdraw request.
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @return  [type] [description]
	 */
	static function request_withdraw(){

		$response = array( 'success' => true,'msg'=> __('Your withdrawal request is sent','boxtheme') );
		$request= $_REQUEST['request'];

		$credit = BX_Credit::get_instance();
		$result = $credit->request_withdraw($request); //request_withdraw

		if( is_wp_error( $result ) ){
			$response = array( 'success' => false,'msg' => $result->get_error_message() );
		}
		wp_send_json( $response );
	}

	/**
	 *
	 */
	static function update_withdraw_info(){
		$request= $_REQUEST['request'];
		$response = array( 'success' => true,'msg'=> __('Update withdraw info done.','boxtheme') );
		$credit = BX_Credit::get_instance();
		$result = $credit->update_withdraw_info($request); //request_withdraw
		wp_send_json( $response );
	}
	static function send_new_confirm_email(){
		$response = array('success' => false, 'msg' => __('Can not send new email.','boxtheme') );
		$request = $_REQUEST['request'];
		$new_confirm_email = $request['nonce'];

		if ( ! wp_verify_nonce( $new_confirm_email, 'new_confirm_email' ) ) {
			$response['msg'] = __('Unsecurity request','boxtheme');
			wp_send_json( $response );
		}
		if( is_user_logged_in() ){


			$current_user = wp_get_current_user();
			$current_key = $current_user->user_activation_key;

			list( $pass_request_time, $pass_key ) = explode( ':', $current_key, 2 );
			$count_time  = time() - (int)$pass_request_time;// senconds

			// if( $count_time < 10*60 ){ // 10 minutes check
			// 	$response['msg'] = __('Time between of 2 requests has to greater than 10 minutes','boxtheme');
			// 	wp_send_json( $response );
			// }

			$response = array(
				'success' => Box_ActMail::get_instance()->send_reconfirm_email( $current_user ),
				'msg' => __( 'New email is sent.','boxtheme')
			);

			wp_send_json( $response );

		}
		wp_send_json( $response );
	}
	static function generate_price(){
		$request = $_REQUEST['request'];
		$amount = (float) $request['price'];

		$info = box_get_pay_info($amount);
		wp_send_json(array('sucess'=> true,'data'=>$info) );

	}
	static function sync_notify(){
		$request = $_REQUEST['request'];

		$method = $request['method'];
		$result = false;

		if('delete' == $method){
			$id = $request['id'];
			$response = array('success' => true, 'msg' => 'Remove notify done' );
			$result = Box_Notify::get_instance()->delete($id);
		}
		if('seenall' == $method){
			$response = array('success' => true, 'msg' => 'Seen all' );
			$result = Box_Notify::get_instance()->seen_all();
		}


		if ( is_wp_error( $result ) ){
			$response = array('success' => false, 'msg' => $result->get_error_message());
		}
		wp_send_json( $response );
	}
	static function bx_resetpass(){
		$request = $_REQUEST['request'];
		$method = $request['method'];


		if( 'send_request' == $method ){
			$email = $request['email'];
			$response = array('success' => false, 'msg' => 'Has something wrong.');
			$check = email_exists($email);

			if( ! $check ){
				$response['msg'] = __('This is email not available','boxtheme');
				wp_send_json( $response );
			}
			$user = get_user_by('email', $email);

			if( ! is_wp_error($user ) &&  $user  ){

				$response = array(
					'success' 	=>	true,
					'msg' => 'Please check your mailbox for instructions to reset your password.',
				);


				Box_ActMail::get_instance()->mail_reset_password($user);
			}

			wp_send_json( $response );
		} else if('setpass' == $method ){

			$token = $request['token'];
			$username = $request['username'];
			$new_password = $request['new_password'];
			$confirm_password = $request['confirm_password'];

			$response = array( 'success' => false, 'msg' => 'Fail' );

			if ( $new_password != $confirm_password ) {
				$response = array('success' => false, 'msg' => 'Your confirm password don\'t match.','boxthemes');
				wp_send_json( $response );
			}
			$user = check_password_reset_key( $token, $username );
			if( is_wp_error($user ) ){
				$response['msg'] = $user->get_error_message();
			}
			if( !is_wp_error($user)  ){
				reset_password( $user, $new_password );
				$response = array('success' => true, 'msg' => 'Your password is updated.','boxthemes');
				wp_send_json( $response );
			}
			wp_send_json( $response );
		}
	}

	static function send_job_to_email(){

		$request = $_REQUEST['request'];

		$email = $request['email'];
		$subject = $request['subject'];
		$project_id = $request['project_id'];
		$invite_message = $request['invite_message'];
		$response = array('success' => true,'msg' => __('Mail is sent succesfull','boxtheme') );
		$result = Box_ActMail::get_instance()->send_job_to_mail($email, $subject, $invite_message, $project_id);
		if( !$result ){
			$response = array('success' => false,'msg' => __('Can not send this email.','boxtheme') );
		}

		wp_send_json( $response);
	}
	static function toggleActivateRole(){
		global $user_ID;
		$current_acti = get_role_active();

		$new_role = ( $current_acti == FREELANCER) ? EMPLOYER : FREELANCER;

		update_user_meta($user_ID,'role_activate', $new_role);

		$response = array('success' => false,'msg' => __('Chagne Role Viewer Done.','boxtheme') );
		wp_send_json($response);
	}
	static function send_direct_message(){
		$response =array('success' => false,'msg' => __('Send message fail.','boxtheme') );
		$message = $_POST['message'];
		$receiver_id = $_POST['user_id'];
		$args = array(
			'receiver_id' => $receiver_id,
			'cvs_content' => $message,
		);
		$c_id = BX_Conversations::get_instance()->create_direct_message($args);
		if( $c_id && !is_wp_error($c_id) ){
			$inbox = add_query_arg('c',$c_id,box_get_static_link('inbox')) ;
			$response =array('success' => true,'msg' => __('Send message completed.','boxtheme'), 'link'=> $inbox );
		}
		wp_send_json($response);
	}
	static function invite_bid(){
		$project_id = (int)$_POST['project_id'];
		$freelancer_id = (int) $_POST['freelancer_id'];
		if(! $project_id){
			$response = array('success' => false,'msg' => __('Please select project.','boxtheme') );
			wp_send_json($response);
		}
		if($project_id && $freelancer_id){
			Box_Notify::get_instance()->has_new_invite_to_bid($freelancer_id, $project_id);
			$response = array('success' => true,'msg' => __('Your invititation is send.','boxtheme') );
		}
		wp_send_json($response);

	}
}

BX_AJAX::init();