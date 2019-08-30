<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BX_Conversations{
	static protected $instance;
	private $table;
	function  __construct(){
		$this->table = 'box_conversations';
	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function sync($args, $method){
		return $this->$method($args);
	}

	function insert($args, $is_assign = 0) {

		global $wpdb, $user_ID;
		$project_id = $args['project_id'];
		$t = $wpdb->insert( $wpdb->prefix . 'box_conversations', array(
				'cvs_author' => $user_ID,
				'project_id' => $args['project_id'],
				'receiver_id' =>  $args['receiver_id'],
				'cvs_content'	=> $args['cvs_content'],

				'cvs_status' => 'publish',
				'msg_unread' => 0,
				'date_created' => current_time('mysql'),
				'date_modify' => current_time('mysql'),
			)
		);
		$cvs_id = $wpdb->insert_id;  // cvs_id just inserted
		$freelancer_id = $args['receiver_id'];
		$msg_arg = array(
			'msg_type' => 'message',
			'sender_id' => $user_ID,
			'receiver_id'=> $freelancer_id, //only freelancer_id
			'msg_content' 	=> $args['cvs_content'],
			'cvs_id' 		=> $cvs_id,
		);

		$msg_id =  BX_Message::get_instance($cvs_id)->insert($msg_arg); // msg_id

		//send mail to freelancer
		$project = get_post($project_id);
		$employer = wp_get_current_user();
		Box_ActMail::get_instance()->has_new_conversation( $freelancer_id,  $employer ,$project );

		// Add notitication for freelancer
		$args = array(
			'msg_content' => sprintf( __('%s sent you a message in project <i>%s</i>','boxtheme'), $employer->display_name, $project->post_title ),
			'msg_link' => get_permalink( $project_id ),
			'receiver_id' => $args['receiver_id'],
		);
		if ( $is_assign ) {
			$args['msg_content'] = sprintf( __('%s assigned you in project <i>%s</i>','boxtheme'), $employer->display_name, $project->post_title );
		}

		$notify = Box_Notify::get_instance()->insert($args);

		return BX_Message::get_instance()->get_message($msg_id);
	}

	function create_conversation( $args ){
		global $wpdb, $user_ID;

		$wpdb->insert( $wpdb->prefix . 'box_conversations', array(
				'cvs_author' => $user_ID,
				'project_id' => $args['project_id'],
				'receiver_id' =>  $args['receiver_id'],
				'cvs_content'	=> $args['cvs_content'],
				'cvs_status' => 1,
				'msg_unread' => 'new',
				'date_created' => current_time('mysql'),
				'date_modify' => current_time('mysql'),
			)
		);
		return $wpdb->insert_id;
	}
	function get_conversation($id){
		global $wpdb;
		return  $wpdb->get_row( "SELECT * FROM  $wpdb->prefix{$this->table}  WHERE ID = ".$id );
	}

	function is_sent_msg( $project_id, $receiver_id ) {
		global $wpdb;
		return $wpdb->get_var( "SELECT ID FROM $wpdb->prefix{$this->table} WHERE project_id = {$project_id} AND receiver_id = {$receiver_id} " );
	}

}


class BX_Message{
	public $author_id;
	public $receiver_id;
	public $content;
	static protected $instance;
	function  __construct( $cvs_id = 0){

		if( $cvs_id ){
			global $user_ID;
			$cvs = BX_Conversations::get_instance()->get_conversation($cvs_id);
			if( $user_ID == $cvs->cvs_author ){
				$this->receiver_id = $cvs->receiver_id;
			} else {
					$this->receiver_id = $cvs->cvs_author;
			}
			$this->cvs_id = $cvs_id;
		}
		$this->msg_type = 'message';

	}
	static function get_instance($cvs_id = 0){
		if (null === static::$instance) {
        	static::$instance = new static($cvs_id);
    	}
    	return static::$instance;
	}
	function sync($args, $method){
		return $this->$method($args);
	}

	function insert( array $args ) { // insert message

		global $wpdb, $user_ID;

		$receiver_id = isset( $args['receiver_id'] ) ? (int) $args['receiver_id'] : '';
		$msg_link = isset($args['msg_link']) ? $args['msg_link']: '';

		if( empty($args['msg_content'] ) ){
			return false;
		}

		if(  is_numeric ( $receiver_id ) ) {
			$this->receiver_id = $receiver_id;
		}

		if( isset( $args['cvs_id']) )
			$this->cvs_id = $args['cvs_id'];

		if( isset( $args['msg_type']) )
			$this->msg_type = $args['msg_type'];

		$sender_id = !empty( $args['sender_id'] ) ? $args['sender_id'] : $user_ID;

		$default = array(
				'sender_id' => $sender_id,
				'msg_content' => $args['msg_content'],
				'cvs_id' => $this->cvs_id,
				'msg_date'	=> current_time('mysql'),
				'msg_unread' => 1,
				'msg_status' => 'new',
				'msg_link' => $msg_link,
				'msg_type' => $this->msg_type,
				'receiver_id' => $this->receiver_id,
				'time_gmt' => current_time( 'timestamp',1),
				//'time' => time(),
			);

		$wpdb->insert( $wpdb->prefix . 'box_messages', $default	);

		// update modify time of this conversiaion.
		$sql = "UPDATE {$wpdb->prefix}box_conversations SET `date_modify` = '".current_time('mysql')."'  WHERE  `ID` = {$this->cvs_id} ";
		$wpdb->query( $sql );

		if( $wpdb->insert_id ) {
			//do_action('box_after_insert_msg_success', $wpdb->insert_id, $args['msg_content'], $this->cvs_id );
		}
		return $wpdb->insert_id;
	}
	function get_message($msg_id){
		global $wpdb;
		$sql = " SELECT * FROM " . $wpdb->prefix . "box_messages WHERE ID = '$msg_id'";
		return $wpdb->get_row($sql);

	}
	function get_converstaion_custom($type = 'message'){
		global $wpdb, $user_ID;

		$sql = "SELECT *
				FROM {$wpdb->prefix}box_messages msg
				WHERE cvs_id = {$this->cvs_id}
					AND msg_type = '{$type}'";
		if( ! current_user_can(  'manage_options' ) )
			$sql .= " AND ( receiver_id = '{$user_ID}' OR sender_id = '{$user_ID}') ";
		$sql .= " ORDER BY id ASC";

		$msgs =  $wpdb->get_results($sql);
		$results = array();
		foreach ($msgs as $key => $msg) {
			$date = date_create( $msg->msg_date );
			$msg->msg_date = date_format($date,"m/d/Y");
			$results[] = $msg;
		}
		return $results;
	}

	function get_converstaion($args){
		$id = $args['id'];
		global $wpdb;
		if( empty( $id ) ){
			return false;
		}
		$sql = "SELECT *
				FROM {$wpdb->prefix}box_messages msg
				WHERE cvs_id = {$id}
					AND msg_type = 'message'
				ORDER BY id ASC";

		$msgs =  $wpdb->get_results($sql);
		$results = array();
		foreach ($msgs as $key => $msg) {
			$date = date_create( $msg->msg_date );
			$msg->msg_date = date_format($date,"m/d/Y");
			$results[] = $msg;
		}
		return $results;
	}
}

function is_sent_msg($project_id, $receiver_id){
	return BX_Conversations::get_instance()->is_sent_msg($project_id, $receiver_id);
}
function box_get_message($msg_id){
	return BX_Message::get_instance()->get_message($msg_id);
}
?>