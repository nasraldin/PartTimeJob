<?php
if ( ! defined( 'ABSPATH' ) ) exit;

Class BX_Resume extends BX_Post{
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
	 * @author danng
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
}
?>