<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class BX_Bid extends BX_Post{
	static private $instance;
	protected $post_type;
	protected $post_title;
	protected $budget;
	function __construct(){
		$this->post_type = BID;

	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function get_meta_fields(){
		return array('_bid_price', '_dealine');
	}

	function delete($args){
		global $user_ID;


		$id = $args['ID'];
		$bid = get_post($id);
		$project = get_post( $bid->post_parent );

		if($user_ID != $bid->post_author){
			return new WP_Error('not_author',__('You can not delete a portfolio of another account','boxtheme'));
			wp_die('not_athor');
		}
		$current_user = wp_get_current_user();
		global $user_ID;
		$args = array(
			'msg_content' => "type=delete_bid&bidder={$user_ID}&project_id={$project->ID}",
			'receiver_id' => $project->post_author,
		);

		wp_delete_post($id, true );
		// delete bid.
		$notify = Box_Notify::get_instance()->insert($args);


		return true;
	}
	function convert($bid){

		$result 			= parent::convert($bid);
		$profile_id 		= get_user_meta( $bid->post_author, 'profile_id', true );
		$project_id 		= $bid->post_parent;
		$result->project_title = '';
		$result->professional_title = '';
		$result->project_link = '';
		$result->project_link = get_permalink($project_id);

		$project 			= get_post($project_id);
		if($project){
			$result->project_title = $project->post_title;
			$result->professional_title = $project->post_excerpt;
		}


		return $result;
	}
	/**
	 * check to know the current user is bidded on project or not yet.
	*/
	function has_bid_on_project( $project_id , $user_id = 0){
		global $wpdb, $user_ID;

		$userID = $user_ID;
		if( $user_id ){
			$userID = $user_id;
		}
		$type = BID;
		$sql  = " SELECT * FROM $wpdb->posts p
			WHERE p.post_type = '{$type}'
				AND p.post_parent = {$project_id}
				AND p.post_author = {$userID}";
		$row = $wpdb->get_row($sql);
		return $row;
	}
	function check_before_update($args){
		$project_id 	= $args['post_parent'];
		$bid_content 	= $args['post_content'];
		$bid_id 		= $args['ID'];

		$bid = get_post( $bid_id );
		if ( $bid->post_author !== $user_ID && $bid->post_parent != $project_id ){
			return new WP_Error('You don\'t permission to update this bid','boxtheme');
		}
		return true;
	}
	function check_before_insert($args, $user_id = 0){

		$project_id 	= $args['post_parent'];
		$bid_content 	= $args['post_content'];

		if( empty( $project_id ) ){
			return new WP_Error( 'empty', __( "Project is empty.", "boxtheme" ) );
		}
		if( empty( $bid_content ) ){
			return new WP_Error( 'empty', __( "Cover letter is empty.", "boxtheme" ) );
		}
		if ( !$user_id ) {
			global $user_ID;
			$user_id = $user_ID;
		}
		if( $this->has_bid_on_project($project_id) && empty($args['update']) ){
			return new WP_Error( 'exists', __( "You've bid on this project", "boxtheme" ) );
		}
		global $escrow;
		$activate = isset($escrow->active) ? $escrow->active : 'credit';

		if( $activate == 'paypal_adaptive' ){
			$pp_email = get_user_meta( $user_id, 'paypal_email', true );
			if( empty($pp_email) || ! is_email( $pp_email) ){
				return new WP_Error( 'empty_ppmail', __( "You have to set your PayPal email to bid.", "boxtheme" ) );
			}
		}

		$project = get_post( $project_id );
		if( $project->post_status == 'publish' && $project->post_author != $user_id ) {
			// project is publish.
			// current user not owner of project
			return true;
		}

		if ( $project->post_author == $user_id ) {
			return new WP_Error( 'is_owner', __( "You can not bid on your own project.", "boxtheme" ) );
		}
		return true;
	}
	/**
	 * add a bidding into the project
	 * @author boxtheme
	 * @version 1.0
	 * @return  [type] [description]
	 */
	function insert($args){
		if( !empty( $args['ID'] ) ){
			return $this->update($args );
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
		$bid_id 	= wp_insert_post( $args );

		if( ! is_wp_error( $bid_id) ){
			// all action after bid success will perform here
			global $user_ID;
			$current_user = wp_get_current_user();
			$project_id = $args['post_parent'];
			$project = get_post($project_id);

			box_update_number_bidded_this_moth($user_ID);

			Box_ActMail::get_instance()->has_new_bid($project);

			$args = array(
				'msg_content'=> "type=new_bid&bidder={$user_ID}&project_id={$project_id}",
				'receiver_id' => $project->post_author,
				);
			// new bidding notificatin
			$notify = Box_Notify::get_instance()->insert_notification($args);

		}
		return $bid_id;

	}
	function update( $args){
		global $user_ID;
		$bid_id = $args['ID'];
		$bid = get_post($bid_id);
		if( $bid->post_author != $user_ID){
			return new WP_Error('permission',__('You can not update this bid','boxtheme') );
		}

		$metas 		= $this->get_meta_fields();
		foreach ($metas as $key) {
			if ( !empty ( $args[$key] )  ){
				$args['meta_input'][$key] = $args[$key];
			}
		}
		$args 		= apply_filters( 'args_pre_update_'.$this->post_type, $args );

		$post_id 	= wp_update_post( $args );

		if( !is_wp_error($post_id) ){
			// test insert notification here.
			$current_user = wp_get_current_user();
			$project_id = $args['post_parent'];
			$project = get_post($project_id);

			$args = array(
				'sender_id' => 0,
				'msg_content' => sprintf(__('%s updated bid','boxtheme'), $current_user->display_name ),
				'receiver_id' => $project->post_author,
				'msg_is_read' => 0,
				'msg_type' => 'notify',
				);

			$msg 	= Box_Notify::get_instance()->insert($args);

		}
		return $post_id;
	}
	function is_can_bid( $project ) {

		if (  $project->post_status == 'publish' ) {

			global $user_ID;
			if( $project->post_author !== $user_ID )
				return true;

		}
		return false;
	}

}
new BX_Bid();
?>
