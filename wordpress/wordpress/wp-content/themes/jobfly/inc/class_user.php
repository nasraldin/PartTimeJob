<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class BX_User{
	static protected $instance;
	function __construct(){

	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	public static function add_role(){
		 $role = get_role( FREELANCER );

		if ( !empty( $role ) ) {
			$role->add_cap( 'unfiltered_upload' );
		}
		$role = get_role( EMPLOYER );

			if ( !empty( $role ) ) {
				$role->add_cap( 'unfiltered_upload' );
			}

		$roles_set = false;
		get_option('jobseeker_role_is_set');
	    if ( !$roles_set ){

	        add_role(FREELANCER, 'Freelancer', array(
	            'read' => true,
	            //'edit_posts' => true,
	            'delete_posts' => false,
	            'upload_files' => true
	        ));
	        $role = get_role( FREELANCER );

			if ( !empty( $role ) ) {
				$role->add_cap( 'unfiltered_upload' );
			}

	    }
	    if(!$roles_set){
	        add_role( EMPLOYER, 'Employer', array(
	            'read' => true,
	            'upload_files' => true
	        ));
	        $role = get_role( EMPLOYER );

			if ( !empty( $role ) ) {
				$role->add_cap( 'unfiltered_upload' );
			}
			update_option('jobseeker_role_is_set',true);
	    }

	}
	function sync($args, $method){
		$user = self::get_instance();
		return $user->$method($args);
	}
	function is_verified($user_ID){

		global $wpdb;
		$mylink = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE ID = $user_ID AND user_status = '1'" );
		return ( null !== $mylink );

	}
	function insert($args) {

		if ( empty( $args['user_email'] ) )
			return  new WP_Error( 'empty_email', __( "Email is empty", "boxtheme" ) );

		if ( empty( $args['user_login'] ) )
			return  new WP_Error( 'empty_username', __( "User name is empty", "boxtheme" ) );

		$role = isset($args['role']) ? $args['role'] : '';

		if ( empty($role) || ! in_array( $role, array(EMPLOYER,FREELANCER) ) )
			$args['role'] = FREELANCER;

		return wp_insert_user($args);
	}

	function get_meta_users(){
		return array('');
	}
	function update($args){
		global $user_ID;
		$args['ID'] = $user_ID;
		$metas = $this->get_meta_users();
		foreach ($metas as $key) {
			if ( !empty ( $args[$key] )  ){
				update_user_meta($user_ID, $key, $args[$key]);
			}
		}
		wp_update_user($args);
	}
}
?>