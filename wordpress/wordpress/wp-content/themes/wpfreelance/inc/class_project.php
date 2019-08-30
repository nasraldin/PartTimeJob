<?php
if ( ! defined( 'ABSPATH' ) ) exit;

Class BX_Project extends BX_Post{
	static protected $instance;
	function __construct(){
		$this->post_type = PROJECT;
		add_action( 'after_insert_'.$this->post_type, 'do_after_insert');
	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function get_meta_fields(){
		return array( BUDGET,WINNER_ID,'priority');
	}
	function get_taxonomy_fields(){
		return array('project_cat', 'skill');
	}

	function get_hierarchica_taxonomy(){
		return array('project_cat');
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
		global $box_general;
		if( $box_general->pending_post && ! is_current_box_administrator() ){
			$args['post_status']	= 'pending';
		}
		if( isset($args['is_private'] ) && $args['is_private'] == 'yes' ){
			$args['post_status'] = 'private';  //dont check the pending_post condition.
		}

		$args = apply_filters( 'args_before_insert_job', $args );

		//https://developer.wordpress.org/reference/functions/wp_insert_post/
		$check = $this->check_before_insert( $args );
		if ( ! is_wp_error( $check ) ){
			$project_id = wp_insert_post($args);
			if ( ! is_wp_error( $project_id ) ){
				if( isset($args['attach_ids']) ){
					$atts = explode(",", $args['attach_ids']);
					foreach ($atts as $attach_id) {
						wp_update_post( array(
							'ID' => $attach_id,
							'post_parent' => $project_id
							)
						);
					}
					do_action('after_save_attachment',$project_id, $atts);
				}
				if ( ! current_user_can( 'manage_option' ) ){
					$this->update_post_taxonomies($project_id, $args); // #222 - back up for #111 when employer post project.
				}

				$count_posted = (int) get_user_meta( $user_ID,'project_posted', true ) + 1;
				update_user_meta( $user_ID, 'project_posted', $count_posted);
				do_action('update_acf_fields', $project_id, $args);
				$this->do_after_insert_job( $project_id, $args);
				return $project_id;

			}
			return new WP_Error( 'insert_fail',  $project_id->get_error_message() );

		} else {

			return new WP_Error( 'insert_fail',$check->get_error_message()  );
		}

		return $id;
	}
	/**
	 * send email to subscriber .
	 * @since 1.0
	*/
	function do_after_insert_job($project_id, $args){
		do_action('box_after_insert_job',$project_id, $args);
		$skills = isset($args['skill']) ? $args['skill']:'';

		if ( ! empty($skills) && is_array( $skills ) ) {
			$args  = array(
				'post_type' => 'profile',
				'post_status' => 'publish',
				'tax_query' => array(
					array(
						'taxonomy' => 'skill',
						'field'    => 'slug',
						'terms'    => $skills,
						'operator' => 'IN'
					),
				),

				'meta_query' => array(
					array(
						'key'     => 'is_subscriber',
						'value'   => 1,
					),
				),
				'posts_per_page' => -1,
			);

			$profiles = new WP_Query($args);
			if ( $profiles->have_posts() ) {
				$emails = array();
				$admin_email = get_option( 'admin_email' );
				$headers[] = 'From: '.get_bloginfo( 'name', 'display' ).' <'.$admin_email.'>';
				while($profiles->have_posts()){
					global $post;
					$profiles->the_post();
					$freelancer_data = get_userdata( $post->post_author );
					$headers[] = 'Bcc: '.$freelancer_data->user_email;
				}

				Box_ActMail::get_instance()->subscriber_match_skill($project_id, $headers, $admin_email);
			}
		}
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

			if( !is_wp_error($pcountry) && !empty ( $pcountry ) ) {
				$result->location_txt =  $pcountry[0]->name;
			}

		}
		$result = apply_filters('convert_project',$result);

		return $result;
	}
	function update_status($data){

		global $user_ID;

		$project_id = $data['ID'];
		$status = isset($data['post_status']) ? $data['post_status'] : '';
		if( $status == 'publish' && ! is_current_box_administrator() ){
			return new WP_Error('deny',__('You are not allow to perform this action','boxtheme') );
		}
		if( $status != 'publish'){
			$project = get_post($project_id);
			if( ! is_current_box_administrator() && $user_ID != $project->post_author ){
				return new WP_Error('deny',__('You are not allow to perform this action','boxtheme') );
			} else if($user_ID == $project->post_author) { // if owner => can not market pending.
				$post_status = 'archived';
			}
		}
		return wp_update_post( array('ID' => $project_id,'post_status' => $status) );

	}

	function check_before_post($args){

		if( !is_user_logged_in() ){
			return new WP_Error( 'not_logged', __( "Please log in your account again.", "boxtheme" ) );
		}
		return true;
	}
}