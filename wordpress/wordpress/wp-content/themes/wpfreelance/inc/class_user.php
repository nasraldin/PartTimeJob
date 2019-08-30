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

		$is_set_role = get_option('is_set_role', true);

	    if (   $is_set_role !== 'ok' ) {
	    	remove_role( FREELANCER );
			remove_role( EMPLOYER );
			// remove role was set before - it has some conflict. @since: version 1.0.6
	        add_role( FREELANCER, 'Freelancer', array(
	            'read' => true,
	            'edit_project' => true,
	            'read_project' => true,
	            'publish_project' => true,
	            //'read_private_projects'=> true,
	            //'upload_files' => true
	        ));
	        add_role( EMPLOYER, 'Employer', array(
	        	'read' => true,
	        	'edit_project' => true,
	        	'read_project' => true,
	        	'publish_project' => true,
	        	//'read_private_projects'=> true,
	           // 'upload_files' => true
	        ));
	        // $fre_role = get_role( 'administrator' );
	        // $fre_role->add_cap( 'edit_project' );
        	// $fre_role->add_cap( 'read_project' );
        	// $fre_role->add_cap( 'read_private_projects' );

	        $role = get_role( 'administrator' );
        	$role->add_cap( 'publish_project' );
        	$role->add_cap( 'edit_project' );
        	$role->add_cap( 'edit_projects' );
        	$role->add_cap( 'read_project' );
        	$role->add_cap( 'delete_project' );
        	$role->add_cap( 'delete_projects' );
        	$role->add_cap( 'edit_others_projects' );
        	$role->add_cap( 'delete_others_projects' );
        	$role->add_cap( 'read_private_projects' );

			update_option('is_set_role','ok');
	    }

	    //http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
	}
	function sync($args, $method){
		$user = self::get_instance();
		return $user->$method($args);
	}
	/**
	 * @keyword confirm verified
	 * check account confirm or not
	*/

	function is_confirmed($user_ID){

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

		if ( empty( $role ) || ! in_array( $role, array( EMPLOYER,FREELANCER ) ) )
			$args['role'] = FREELANCER;

		$user_id =  wp_insert_user($args);

		if( ! is_wp_error( $user_id ) ){

			if( ! empty( $args['address'] ) )
				update_user_meta($user_id,'address',$args['address']);
			if( ! empty($args['lat_address']) )
				update_user_meta($user_id,'lat_address',$args['lat_address']);
			if( ! empty($args['long_address']) )
				update_user_meta($user_id,'long_address',$args['long_address']);
			if( ! empty($args['country']) )
				update_user_meta($user_id,'country',$args['country']);

			if( ! empty($args['avatar_url']) ){
				update_user_meta( $user_id, 'avatar_url', $args['avatar_url'] );
			}
		}
		return $user_id;
	}


	function get_meta_users(){
		return array('');
	}
	function update($args){
		global $user_ID;

		$role = isset($args['role']) ? strtolower($args['role']) : '';

		if (  in_array( $role, array( 'administrator','editor' ) ) )
			return  new WP_Error( 'security', __( "You can not update this role", "boxtheme" ) );

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