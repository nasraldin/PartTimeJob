<?php
if ( ! defined( 'ABSPATH' ) ) exit;
	class BX_Post {

		private static $instance;
		protected $post_type;
		function __construct(){
			$this->post_type = 'post';
			//add_filter( 'query_vars',array( $this, 'add_query_vars_filter' ) );



		}
		static function get_instance(){

			if (null === static::$instance) {
            	static::$instance = new static();
        	}
        	return static::$instance;
		}
		function sync( $method, $args){
			return $this->$method($args);
		}

		/**
		 * check security
		 * @since   1.0
		 * @author boxtheme
		 * @param   [type]    $request [description]
		 * @return  [type]             [description]
		 */
		function check_security($method, $request){
			$name = "nonce_insert_job";
			if($method == 'edit')
				$name = "nonce_edit_job";
	    	if(! wp_verify_nonce( $request[$name], 'jb_submit_job' ) ){
	    		return  new WP_Error( 'unsecurity', __( "Has something wrong", "boxtheme" ) );
	    	}
	    	return true;
		}
		function get_meta_fields(){
			return array();
		}
		function get_static_meta_fields(){
			return array();
		}
		function get_taxonomy_fields(){
			return array();
		}

		function insert($args){
			global $user_ID;
			// check security
			if( !empty( $args['ID'] ) ){
				return $this->update($args);

			}
			$check = $this->check_before_insert($args);

			if( is_wp_error($check) ){
				return $check;
			}
			$args['post_type'] 		= $this->post_type;
			$args['post_status']	= 'publish';
			if ( is_wp_error( $check ) ){
				return $check;
			}

			$metas 		= $this->get_meta_fields();
			foreach ($metas as $key) {
				if ( !empty ( $args[$key] )  ){
					$args['meta_input'][$key] = $args[$key];
				}
			}
			$args 		= apply_filters( 'args_pre_insert_'.$this->post_type, $args );
			$post_id 	= wp_insert_post( $args );
			do_action('after_insert_'.$this->post_type, $post_id, $args);

			//https://developer.wordpress.org/reference/functions/wp_insert_post/
			if ( ! is_wp_error( $post_id ) ) {
				$this->update_post_taxonomies($post_id, $args);
			}
			return $post_id;
		}
		function update($args){

			global $user_ID;
			// check security
			$check = $this->check_before_update($args);

			if( is_wp_error($check) ){
				return $check;
			}


			$metas 		= $this->get_meta_fields();
			foreach ($metas as $key) {
				if ( !empty ( $args[$key] )  ){
					$args['meta_input'][$key] = $args[$key];
				}
			}
			$args 		= apply_filters( 'args_pre_update_'.$this->post_type, $args );

			$post_id 	= wp_update_post( $args );
			do_action('after_update_'.$this->post_type,$post_id, $args);
			//https://developer.wordpress.org/reference/functions/wp_insert_post/

			if ( ! is_wp_error( $post_id ) ) {

				$this->update_post_taxonomies($post_id, $args);
			}
			return $post_id;
		}
		function check_author($args){
			$id = $args['ID'];
			$post = get_post($id);
			global $user_ID;

			if($user_ID != $post->post_author){
				return new WP_Error('not_author',__('You can not renew this job','boxtheme'));

			}
		}
		function renew($args){

			global $user_ID;
			// check security
			$check = $this->check_before_update($args);

			if( is_wp_error($check) ){
				return $check;
			}

			if( is_wp_error($this->check_author($args) ) ) {
				return $this->check_author($args);
			}
			$metas 		= $this->get_meta_fields();
			foreach ($metas as $key) {
				if ( !empty ( $args[$key] )  ){
					$args['meta_input'][$key] = $args[$key];
				}
			}
			$args 		= apply_filters( 'args_pre_update_'.$this->post_type, $args );

			$post_id 	= wp_update_post( $args );
			do_action('after_update_'.$this->post_type,$post_id, $args);
			//https://developer.wordpress.org/reference/functions/wp_insert_post/

			if ( ! is_wp_error( $post_id ) ) {

				$this->update_post_taxonomies($post_id, $args);
			}
			return $post_id;
		}

		function delete($args){
			$id = $args['ID'];
			$post = get_post($id);
			global $user_ID;
			if($user_ID != $post->post_author){
				return new WP_Error('not_author',__('You can not delete a portfolio of another account','boxtheme'));
				wp_die('not_athor');
			}
			wp_delete_post($id, true );
			do_action('box_after_delete_'.$this->post_type);
			return true;
		}

		function archived($args){
			global $user_ID;
			$project_id = $args['ID'];
			$post = get_post($project_id);

			if($user_ID != $post->post_author){
				return new WP_Error('not_author',__('You can not archived this job','boxtheme'));
				wp_die('not_athor');
			}

			wp_update_post(array('ID' => $project_id, 'post_status' => 'archived'));
			return true;

		}
		function update_post_taxonomies( $post_id, $args ){
			//var_dump($args);

			$taxonomies =$this->get_taxonomy_fields();
			//var_dump($taxonomies);
			foreach ($taxonomies as $tax) {
				if( !empty( $args[$tax]) ){
					$t = wp_set_post_terms($post_id, $args[$tax], $tax);
					//$t = wp_set_object_terms($post_id, $args[$tax], $tax);
				}
			}

		}
		/**
			check validte when post a job vai front-end.
		*/
		function check_before_update($request){
			$validate = true;
			if( empty($request['post_title']) ){
				return  new WP_Error('empty_title',__('Empty post title','boxtheme'));
			}
			if( empty($request['post_content']) ){
				return new WP_Error('empty_content',__('Empty job content','boxtheme'));
			}

			return $validate;
		}

		function check_before_insert($request){
			$validate = true;
			if( empty($request['post_title']) ){
				return  new WP_Error('empty_title',__('Empty post title','boxtheme'));
			}
			if( empty($request['post_content']) ){
				return new WP_Error('empty_content',__('Empty job content','boxtheme'));
			}
			$validate =  apply_filters('check_before_insert_'.$this->post_type, $validate, $request );

			return $validate;
		}
		/**
		 * depend on the setting , use this method to get the post_status when insert new job.
		 * only use this method in step -2 of submit job page.
		 * @since   1.0
		 * @author boxtheme
		 * @return  string post_status
		 */
		function get_post_status_step_2(){
			$post_status = 'draft';

			if( current_user_can('manager_options') )
				return 'publish';

			if( is_free_submit_job() ) {
				if ( is_admin_role() )
					return  'publish';
				if( is_pending_job() ){
					return 'pending';
				}
				return 'publish';
			}
			return $post_status;
		}

		public static function get_post_status_check_out(){
			if( is_admin_role() )
				return 'publish';
			if( is_pending_job() ){
					return 'pending';
			}
			return 'publish';
		}

		function convert($post){

			if( is_numeric($post) ){
				$post = get_post($post);
			}

			$metas = $this->get_meta_fields();
			foreach ( $metas as $key ) {
				$post->$key = get_post_meta( $post->ID, $key, true);
			}
			$static_metas = $this->get_static_meta_fields();
			foreach ( $static_metas as $key ) {
				$post->$key = get_post_meta( $post->ID, $key, true);
			}

			return $post;
		}
	}
?>