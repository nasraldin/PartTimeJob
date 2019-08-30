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
		return array('professional_title', 'address', 'hour_rate','phone_number', 'video_url');
	}
	function get_static_meta_fields(){
		return array();
	}
	function check_before_insert($request){
		return true;
	}
	function get_taxonomy_fields(){
		return array('country','skill');
	}
	function sync($args, $method){
		return $this->$method($args);
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

		if( is_numeric($post) ){
			$post = get_post($post);
		}

		$metas = $this->get_meta_fields();
		foreach ( $metas as $key ) {
			$post->$key = get_post_meta( $post->ID, $key, true);
		}

		$pcountry = get_the_terms( $post->ID, 'country' );
		$post->country = __('Not set','boxtheme');
		if( !empty($pcountry) ){
			$post->country =  $pcountry[0]->name;
		}

		$post->avatar = get_avatar($post->post_author, 130 );
		$post->{EARNED}	= (float)get_user_meta($post->post_author,EARNED, true);
		$post->earned_txt = sprintf( __('Earned : %s','boxtheme'), box_get_price($post->{EARNED}) );
		$post->{RATING_SCORE} 	= floatval(get_user_meta($post->post_author,RATING_SCORE, true) );
		$post->{PROJECTS_WORKED} = (int) get_user_meta($post->post_author,PROJECTS_WORKED, true);
		$post->score_class = 'score-'.$post->{RATING_SCORE};


		$post->{HOUR_RATE_TEXT} = sprintf( __('%s %s/h','boxtheme'), $this->symbol, $post->{HOUR_RATE} );
		$post->skill_text = '';
		$post->short_des =  wp_trim_words( $post->post_content, 62);
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
		$post->author_link = get_author_posts_url($post->post_author);
		return $post;
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

		$profile_id = get_user_meta($user_id, 'profile_id', true);
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

		$id = parent::update($args);

		if( !is_wp_error( $id )){

			if( isset($args['professional_title']) ){
				update_post_meta( $id, 'professional_title', $args['professional_title'] );
			}

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
		if($profile->post_author != $user_ID){
			 return new WP_Error('permission',__('You don\'n have permission to update','boxtheme'));
		}
		return $validate;
	}

	function update_profile_meta($args){
		$args['post_type'] = $this->post_type;
		$args['post_status'] = 'publish';
		$id = $this->update($args);
		if( !is_wp_error($id)){
		$metas = $this->get_meta_fields();
			// foreach ($metas as $key) {
			// 	if ( !empty ( $args[$key] )  ){
			// 		update_post_meta($args['ID'], $key, $args[$key] );
			// 	}
			// }
		}
		return $id;
	}
}