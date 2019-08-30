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
		$project_id = absint($args['project_id']);
		$receiver_id = absint($args['receiver_id']);
		$cvs_content = sanitize_textarea_field($args['cvs_content']);
		$t = $wpdb->insert( $wpdb->prefix . 'box_conversations', array(
				'cvs_author' => $user_ID,
				'project_id' => $project_id,
				'receiver_id' =>  $receiver_id,
				'cvs_content'	=> $cvs_content,

				'cvs_status' => 'publish',
				'msg_unread' => 0,
				'date_created' => current_time('mysql'),
				'date_modify' => current_time('mysql'),
			)
		);
		$cvs_id = $wpdb->insert_id;  // cvs_id just inserted

		$cvs_content = sanitize_textarea_field($args['cvs_content']);

		$msg_arg = array(
			'msg_type' => 'message',
			'sender_id' => $user_ID,
			'receiver_id'=> $receiver_id, //only freelancer_id
			'msg_content' 	=> $cvs_content,
			'cvs_id' 		=> $cvs_id,
		);

		$msg_id =  BX_Message::get_instance($cvs_id)->insert($msg_arg, $add_noti = false); // msg_id

		//send mail to freelancer
		$project = get_post($project_id);
		$employer = wp_get_current_user();
		Box_ActMail::get_instance()->has_new_conversation( $receiver_id,  $employer ,$project );

		// Add notitication for freelancer
		//do_action('has_new_conversation', $freelancer_id, $project, $is_assign);


		return BX_Message::get_instance()->get_message($msg_id);
	}

	function create_direct_message($args){
		global $wpdb, $user_ID;

		$receiver_id = absint($args['receiver_id']);
		if( has_directly_message( $receiver_id ) ){
			return false;
		}

		$cvs_args = array(
				'cvs_author' => $user_ID,
				'project_id' => 0,
				'receiver_id' =>  $receiver_id,
				'cvs_content'	=> 'Directly Message',
				'cvs_status' => 'publish',
				'msg_unread' => 0,
				'date_created' => current_time('mysql'),
				'date_modify' => current_time('mysql'),
		);

		$wpdb->insert( $wpdb->prefix . 'box_conversations',$cvs_args);

		$cvs_id = $wpdb->insert_id;  // cvs_id just inserted
		$cvs_content = sanitize_textarea_field($args['cvs_content']);
		$msg_arg = array(
			'msg_type' => 'message',
			'sender_id' => $user_ID,
			'receiver_id'=>  $receiver_id, //only freelancer_id
			'msg_content' 	=> $cvs_content,
			'cvs_id' 		=> $cvs_id,
		);

		$msg_id =  BX_Message::get_instance($cvs_id)->insert($msg_arg, $add_noti = true); // msg_id
		return $cvs_id;

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

	function insert( array $args , $add_noti = true) { // insert message

		global $wpdb, $user_ID;

		$receiver_id = isset( $args['receiver_id'] ) ? (int) $args['receiver_id'] : '';

		if( empty($args['msg_content'] ) ){
			return false;
		}

		if(  is_numeric ( $receiver_id ) ) {
			$this->receiver_id = $receiver_id;
		}

		if( isset( $args['cvs_id']) )
			$this->cvs_id = absint($args['cvs_id']);

		if( isset( $args['msg_type']) )
			$this->msg_type = $args['msg_type'];
		$attach_ids = isset( $args['attach_ids'] ) ? $args['attach_ids'] : 0;
		if($attach_ids){
			$attach_ids = implode(",",$attach_ids);
		}

		$msg_content = sanitize_textarea_field($args['msg_content']);
		$default = array(
				'sender_id' => $user_ID,
				'msg_content' => $msg_content,
				'cvs_id' => $this->cvs_id,
				'msg_unread' => 1,
				'msg_status' => 'new',
				'msg_type' => $this->msg_type, //message
				'receiver_id' => $this->receiver_id,
				'msg_date'	=> current_time('mysql'),
				'time_gmt' => current_time( 'timestamp', 1),
				'attach_ids' => $attach_ids,

			);
		$wpdb->insert( $wpdb->prefix . 'box_messages', $default	);

		$msg_id = $wpdb->insert_id;

		if( $msg_id ) {
			// update modify time of this conversiaion.
			$sql = "UPDATE {$wpdb->prefix}box_conversations SET `date_modify` = '".current_time('mysql')."'  WHERE  `ID` = {$this->cvs_id} ";
			$wpdb->query( $sql );

			if( $add_noti ){
				//do_action('after_receive_message', $this->receiver_id,  $msg_id, $this->cvs_id);
			}
		}
		return $msg_id;
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
		$mark_as_read = isset($args['mark_as_read']) ? $args['mark_as_read'] : 0;
		$id = (int) $args['id'];
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
			$html_media = array();
			$attach_ids  = $msg->attach_ids ;
			if($attach_ids ){
				$arr_temp = explode(',', $attach_ids);
				foreach ($arr_temp as $aid) {
					$att = get_post($aid);
					$att_url = wp_get_attachment_url($att->ID);
					$auido_type = $att->post_mime_type; //audio/mpeg == mp3 audio/wav == wav

					if($auido_type == 'audio/mpeg'){
						$html_media[]= '<audio controls>
										  <source src="'.$att_url.'" type="audio/mpeg">
										Your browser does not support the audio element.
										</audio>';
					} else if($auido_type == 'audio/wav'){
						$html_media[]= '<audio controls>
										  <source src="'.$att_url.'" type="audio/wav">
										Your browser does not support the audio element.
										</audio>';
					} else {
						$html_media[] = '<a class="msg-att-link"  download href="'.$att_url.'" >'.$att->post_title.' <i class="fa fa-download" aria-hidden="true"></i></a>';
					}
				}
			}
			$msg->html_media = '';
			if( !empty( $html_media) ){
				$msg->html_media =  join(" ",$html_media);
			}
			$temp = (object) array(
				'post_title'=>'',
				'post_status' => '',
				'post_content' =>'',
				'post_type' => '',
			);
			$temp->post_title = 'a message';
			$temp->post_type ='message';
			$temp->post_status = 'publish';
			$temp->post_content = $msg->msg_content;
			global $post;
			setup_postdata($temp);
			ob_start();
			the_content();
			$t = ob_get_clean();

			$msg->msg_content = $t ;
			$results[] = $msg;
		}
		if( $mark_as_read ){
			$this->mark_as_read_all_msg($id);
		}
		return $results;
	}
	function mark_as_read_all_msg($cvs_id){
		$cvs_id = (int) $cvs_id;
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}box_messages msg
				SET msg_unread = %d
				WHERE msg_unread = %d AND msg.cvs_id = %d",0,1,$cvs_id) );


	}
}

function is_sent_msg($project_id, $receiver_id){
	return BX_Conversations::get_instance()->is_sent_msg($project_id, $receiver_id);
}
function box_get_message($msg_id){
	return BX_Message::get_instance()->get_message($msg_id);
}
function box_get_content(){
	ob_start();
	the_content();
	return ob_get_clean();
}
?>