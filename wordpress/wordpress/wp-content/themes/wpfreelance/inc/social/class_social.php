<?php
/**
 * @keysearch: class_social.php social.php box_social
 */
Class Box_Social{
	static $instance;
	function construct(){

	}
	static function get_instance(){
		if (null === static::$instance) {
    		static::$instance = new static();
    	}
    	return static::$instance;

	}

	function auto_login($userdata){

		$user_id = $this->social_id_exists($userdata['social_id']); // get user id of this social id

		if( ! $user_id ){

			if( email_exists($userdata['user_email'] ) ){
				return  new WP_Error( 'exists_email', __( "Sorry, that email address is already used!", "boxtheme" ) );
			}
			$userdata['role'] = FREELANCER;
			$userdata['user_pass'] = wp_generate_password(15);

			$user_id = wp_insert_user($userdata);

			if( is_wp_error( $user_id ) ){
				return $user_id;
				wp_die();
			}
			update_user_meta( $user_id, 'social_id', $userdata['social_id'] );
			// insert profile

			$metas	= array(
				HOUR_RATE => 0,
				RATING_SCORE => 0,
				//'address' => $address,
				//'lat_address' => $lat_address,
				//'long_address' => $long_address,

			);
			if( BOX_VERIFICATION ){
				$metas['is_reviewed'] = 0;
			}

			$args = array(
				'post_title' 	=> $userdata['user_login'] ,
				'post_type'  	=> PROFILE,
				'post_author' 	=> $user_id,
				'post_status' 	=> 'publish',
				'meta_input'	=> $metas,
			);

			$profile_id = wp_insert_post($args);


			update_user_meta( $user_id, 'profile_id', $profile_id );

			global $wpdb;
			$wpdb->update( $wpdb->users, array( 'user_status' => 1 ), array( 'user_login' => $userdata['user_login'] ) );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => '' ), array( 'user_login' => $userdata['user_login'] ) );


		}

		// set the auth cookie to current user id
		wp_set_auth_cookie($user_id, true);
		// log the user in
		wp_set_current_user($user_id);
		// do redirect  here
		return $user_id;
	}

	function social_id_exists($social_id){
		global $wpdb;
		$sql =  $wpdb->prepare(
			"
				SELECT user_id
				FROM $wpdb->usermeta
				WHERE meta_key = %s AND meta_value = %s
			", 'social_id', $social_id
		);

		$record = $wpdb->get_row($sql );

		return ($record) ? $record->user_id : 0;
	}
}

function bx_social_button_signup(){
	global $gg_activate, $fb_activate;
	if( $gg_activate || $fb_activate ){ ?>
	 	<div class="form-row">
            <div class="hr-or"><center><?php _e('or','boxtheme');?></center></div>
                <div class="col-md-12 social-login">
		        <?php btn_fb_login() ;?>
		        <?php btn_google_login();?>
		        <a href='#' class="btn btn-default twitter hide"> <i class="fa fa-twitter modal-icons"></i><?php _e('<span class="hidden-xs">Sign In with </span>Twitter','boxtheme');?> </a>
		        <a href='#' class="btn btn-default linkedin hide"> <i class="fa fa-linkedin modal-icons"></i><?php _e('<span class="hidden-xs"> Sign In with </span>Linkedin','boxtheme');?> </a>
		 	</div>
     	</div>
	<?php } ?>
<?php } ?>