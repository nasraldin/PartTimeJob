<?php

if ( ! defined( 'ABSPATH' ) ) exit;

Class BX_Profile extends BX_Post{
	static protected $instance;
	protected $post_type;
	public $symbol;
	function __construct(){
		$this->post_type = PROFILE;
		$this->symbol = box_get_currency_symbol();

	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function get_meta_fields(){
		$args = array('address', 'hour_rate','phone_number', 'video_id','is_available','is_subscriber','lat_address','long_address');
		if( BOX_VERIFICATION ){
			array_push($args, 'is_reviewed');
		}
		return apply_filters('box_profile_meta',$args);
	}
	function get_static_meta_fields(){
		return array();
	}
	function check_before_insert($request){
		return true;
	}
	function get_taxonomy_fields(){
		return apply_filters('box_profile_taxes', array(box_get_country_args()->slug,'skill') );
	}
	function sync($args, $method){
		return $this->$method($args);
	}
	function run_after_insert($id){
		//update_post_meta( $id, 'is_available','on' );
	}
	function insert1($args){
		global $user_ID;
		$args['author'] 		= $user_ID;
		$args['post_status'] 	= 'publish';
		$args['post_type'] 		= $this->post_type;
		$args['meta_input'] 	= array();
		$metas = $this->get_meta_fields();
		foreach ($metas as $key) {
			if ( !empty ( $args[$key] )  ){
				$args['meta_input'][$key] = $args[$key];
			}
		}
		$check = $this->check_before_bid( $args );
		$check = 1;
		if ( ! is_wp_error( $check ) ){
			$id = wp_insert_post($args);
		} else {
			wp_send_json( array('success' => false, 'msg' => $check->get_error_message() ) );
		}

		return $id;
	}
	function convert($post){

		if(is_string($post) ){
			$post = (int) $post;
		}
		if( is_numeric($post) ){
			$post = get_post($post);
		}


		$metas = $this->get_meta_fields();

		$p_id = $post->ID;
		foreach ( $metas as $key ) {
			$post->{$key} = get_post_meta( $p_id, $key, true);
		}
		$country_slug = box_get_country_args()->slug;
		$pcountry = get_the_terms( $p_id, $country_slug );
		$post->country = __('Not set','boxtheme');
		if( !empty($pcountry) ){
			$post->country =  $pcountry[0]->name;
		}
		global $symbol;

		$post->avatar = get_avatar($post->post_author, 130 );
		$post->{EARNED}	= (float)get_user_meta($post->post_author,EARNED, true);
		$post->earned_txt = sprintf( __('%s%s','boxtheme'), $symbol,  box_get_price($post->{EARNED}) );
		$post->{RATING_SCORE} 	= floatval(get_user_meta($post->post_author,RATING_SCORE, true) );
		$post->{PROJECTS_WORKED} = (int) get_user_meta($post->post_author,PROJECTS_WORKED, true);
		$post->score_class = 'score-'.$post->{RATING_SCORE};
		$post->professional_title = $post->post_excerpt;

		$post->{HOUR_RATE_TEXT} = sprintf( __('%s %s/h','boxtheme'), $this->symbol, $post->{HOUR_RATE} );
		$post->skill_text = '';
		$post->short_des =  wp_trim_words( $post->post_content, 36);

		$skill_text = '';
		$skills = get_the_terms( $post->ID, 'skill' );
		if ( $skills && ! is_wp_error( $skills ) ){
			$draught_links = array();
			foreach ( $skills as $term ) {
				//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
				$draught_links[] = '<span>'.$term->name.'</span>';
			}
			$skill_text = join( " ", $draught_links );
			$post->skill_text = $skill_text;
		}
		$post->profile_name = $post->post_title;
		if( BOX_VERIFICATION ){
			if( $post->is_reviewed )
				$post->profile_name = $post->profile_name.='<span class="is-reviewed" title = "'.__('Verified','boxtheme').'">&nbsp;</span>';
		}
		$post->html_marker = box_html_marker($post);
		$post->author_link = get_author_posts_url($post->post_author);

		return apply_filters('box_convert_profile', $post);
	}

	function get_full_info($args){
		$user_id = $args['user_id'];
		$full_info = array();
		$user_info  = get_userdata( $user_id );
		if( ! is_wp_error( $user_info  ) ){

			$full_info['user_ID'] = $user_info->ID;

			$full_info['user_login'] = $user_info->user_login;
			$full_info['user_nicename'] = $user_info->user_nicename;
			//$full_info['user_email'] = $user->user_email;
			$full_info['display_name'] = $user_info->display_name;
			$full_info['first_name'] = $user_info->first_name;
			$full_info['last_name'] = $user_info->last_name;
			$full_info['description'] = $user_info->description;
			$full_info['avatar'] =  get_avatar($user_info->user_email, 96 );

		}

		$profile_id = (int) get_user_meta($user_id, 'profile_id', true);

		$profile_info = $this->convert($profile_id);
		$full_info['skill_text'] = '';
		$skills 	= get_the_terms( $profile_id, 'skill' );
		$skill_text = '';
		if ( $skills && ! is_wp_error( $skills ) ){
			$draught_links = array();
			foreach ( $skills as $term ) {
				$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
			}
			$skill_text = join( ", ", $draught_links );
			$full_info['skill_text'] = $skill_text;
		}
		foreach ($profile_info as $key => $value) {
			$full_info[$key] = $value;
		}
		$args = array(
			'user_id' => $user_id,
			'type' => 'emp_review',
			'number' => 5,
		);
		$feedback = array();

		$comments = get_comments( $args);
		foreach ($comments as $key => $cmn) {
			$bid_id = $cmn->comment_post_ID;
			$bid = get_post($bid_id);
			$feedback[$key] = $cmn;
			$feedback[$key]->project_link = '<a class ="project-link" href="'.get_permalink($bid->post_parent).'">'. get_the_title($bid->post_parent) .'</a>';
			$feedback[$key]->rating = get_comment_meta( $cmn->comment_ID, RATING_SCORE, true );
		}
		$full_info['feedbacks'] = $feedback;

		return $full_info;

	}
	function update($args){

		$profile_id = parent::update($args);

		if( ! is_wp_error( $profile_id  )){

			global $user_ID;
			if( isset($args['post_title']) ){

				// update display name of user
				wp_update_user(array('ID' => $user_ID, 'display_name' => $args['post_title']) );
			}
			if( isset($args['user_email']) ){
				// update user email of user
				wp_update_user(array('ID' => $user_ID, 'user_email' => $args['user_email']) );
			}
			if( isset($args['paypal_email']) ){
				update_user_meta( $user_ID, 'paypal_email', $args['paypal_email'] );
			}

			// update lat_geo, lng_geo

			if( ! empty( $args['lat_address'] ) ){
				update_geo_location( $profile_id, $args['lat_address'],  $args['long_address'] );
			}


			do_action('after_update_profile',$profile_id);
		}

	}
	function update_one_meta($args){
		$request = $_REQUEST['request'];
		$profile_id = $request['ID'];
		$video_id = $request['video_id'];
		return update_post_meta( $profile_id, 'video_id', $video_id );
	}
	function check_before_update($request){
		$validate = true;
		global $user_ID;
		$profile = get_post($request['ID']);
		if( $profile->post_author != $user_ID ){
			 return new WP_Error('permission',__('You don\'t have permission to update.','boxtheme'));
		}
		return $validate;
	}

	function update_profile_meta($args){
		$args['post_type'] = $this->post_type;
		$args['post_status'] = 'publish';
		$args['is_subscriber'] =  !empty( $args['is_subscriber']) ? $args['is_subscriber'] : 0;

		$id = $this->update($args);

		if( ! is_wp_error( $id ) ){

		}
		return $id;
	}
	function update_subscriber_skills($args){
		$profile_id = $args['ID'];

		$skills = isset($args['skill']) ? $args['skill'] : '';
		if ( ! empty( $skills ) ){
			$string = implode(";", $skills);
			update_post_meta( $profile_id,'subscriber_skills', $string );
		} else {
			update_post_meta( $profile_id,'subscriber_skills', '' );
		}

	}
	function update_meta_only($args){
		global $user_ID;

		$profile_id = $args['ID'];
		$p = get_post($profile_id);
		if ($p->post_author == $user_ID){

			$metas = $this->get_meta_fields();
			foreach ($metas as $key) {
				if( $key == 'is_reviewed' ){
					continue;
				}
				if ( ! empty ( $args[$key] )  ){
					update_post_meta($args['ID'], $key, $args[$key] );
				}
			}

		} else {
			die('authenticate - deny');
		}
		return $id;
	}
	function update_profile_status($args){

		global $user_ID;

		$profile_id = $args['ID'];
		$p = get_post($profile_id);
		if( box_manuall_approve() ){
			return new WP_Error( 'none_permision', __('You doesn\'t allow to perform this action.','boxtheme') );
		}

		if ( $p->post_author == $user_ID || is_current_box_administrator() ){

			$request = $_POST['request'];
			$is_available = $request['is_available'];

			$args = array(
				'ID' => $profile_id,
				'post_status' => '',
			);
			if( $is_available == 'inactive' ){
				$args['post_status'] = 'inactive';

			} else  if( $is_available == 'activate' ){
				$args['post_status'] = 'publish';
			}
			return wp_update_post($args);;

		} else {

			return false;
			die('authenticate - deny');
		}
		return new WP_Error( 'something_wrong', __('Can not update the status.','boxtheme') );
	}
	function toggle_review_status($args){

		$response = array('success' => true,'msg' =>'Review is updated.');
		$profile_id = $args['profile_id'];
		$status = $args['status'];

		if( is_current_box_administrator() ){

			if( $status == "review"){
				// mark as unreview this profile.
				$result = update_post_meta($profile_id,'is_reviewed', 1);
				do_after_verify_account($profile_id, $result);
			} else if($status == 'unreview'){
				$result = update_post_meta($profile_id,'is_reviewed', 0);
				do_after_unverify_account($profile_id, $result);
			}

		}

		wp_send_json($response);
	}
}