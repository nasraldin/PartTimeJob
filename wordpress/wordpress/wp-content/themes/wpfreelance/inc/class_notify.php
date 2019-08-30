<?php
class Box_Notify extends Box_Custom_Type{
	public $author_id;
	public $receiver_id;
	public $content;
	public $inbox_link;
	public $type;
	static protected $instance;
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function  __construct(){

		parent::__construct();
		$this->type = 'notify';
		$this->inbox_link = box_get_static_link('inbox');
		//add_action( 'after_receive_message', array( $this,'add_noti_new_message'), 10,  3  );

		add_action('after_approve_cash_deposit',array($this,'add_noti_approve_cash') );
		add_action('after_approve_cash_deposit',array($this,'add_noti_approve_cash') );

		add_action('after_employer_review', array($this,'add_noti_after_employer_review'), 10 ,2);
		add_action('after_fre_delivery', array( $this,'add_noti_after_freelancer_delivery') );

		add_action('has_new_conversation',array($this,'add_noti_fre_new_conversation'), 10 ,3 );

		add_action('after_award_job', array( $this, 'add_noti_award_job_to_freelancer'), 10 ,2 );

	}

	function add_noti_award_job_to_freelancer($freelancer_id, $project){
		global $user_ID; // freelancer id
		$args = array(
			'receiver_id' => $freelancer_id, // employer
			'sender_id' => $user_ID,
			'msg_content' =>"type=adward_job&freelancer_id={$freelancer_id}&project_id={$project->ID}",
		);
		$this->insert_notification($args);
	}
	function add_noti_after_freelancer_delivery($project){
		global $user_ID; // freelancer id
		$args = array(
			'receiver_id' => $project->post_author, // employer
			'sender_id' => $user_ID,
			'msg_content' =>"type=fre_delivery&freelancer_id={$user_ID}&project_id={$project->ID}",
		);
		$this->insert_notification($args);
	}

	function add_noti_fre_new_conversation( $freelancer_id, $project, $is_assign){
		global $user_ID;
		$args = array(
			'msg_content' => "type=new_conversation&freelancer_id={$freelancer_id}&employer={$user_ID}&project_id={$project->ID}",
			'receiver_id' => $freelancer_id,
		);


		$noti_id = Box_Notify::get_instance()->insert_notification($args);
	}
	function add_noti_after_employer_review($freelancer_id, $project){
		$args = array(
			'receiver_id' => $freelancer_id,
			'sender_id' => $project->post_author,
			'msg_content' =>"type=emp_review&reviewer={$project->post_author}&project_id={$project->ID}",
		);
		$this->insert_notification($args);
	}

	function add_noti_new_message($receiver_id, $msg_id, $cvs_id){
		global $user_ID;
		$args = array(
			'receiver_id' => $receiver_id,
			'sender_id' => $user_ID,
			'msg_content' =>"type=new_message&sender_id={$user_ID}&cvs_id=$cvs_id",
		);
		$this->insert_notification($args);
	}
	function add_noti_approve_cash($order){
		global $user_ID;
		$args = array(
			'receiver_id' => $order->payer_id,
			'sender_id' => 0,
			'msg_content' =>"type=approve_order&buyer={$order->payer_id}&order_id={$order->ID}",
		);
		$this->insert_notification($args);
	}
	function sync($args, $method){
		return $this->$method($args);
	}
	function render_noti_item($noti){

		$date = date_create( $noti->msg_date );
		$noti->date = date_format($date,"m/d/Y");

		parse_str($noti->msg_content, $output);
		if($noti->msg_type == 'message'){
			$this->render_notification_new_message($noti);
		} else {
			$hook_name = 'render_default_notification';

			if( ! empty ( $output['type']) )
				$hook_name = "render_item_".$output['type'];



			$this->$hook_name($noti, $output);
		}
	}
	function render_notification_new_message($noti){
		$cvs_id = $noti->cvs_id;
		$inbox_link = add_query_arg('c',$cvs_id,$this->inbox_link);
		$unread = 'noti-read';
		if($noti->msg_unread == '1')
			$unread = 'noti-unread';
		?>
		<li class="dropdown-item <?php echo $unread;?>" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti">
				<a href="<?php echo $inbox_link;?>"><?php echo get_avatar( $noti->sender_id ); ?></a>
				<?php
					$css = 'number-msg  number-unread-zero';
					if($noti->count_unread > 0){
						$css = 'number-msg number-unread-notzero';
					}
					echo '<span class="'.$css.'">'.$noti->count_unread.'</span>';
				?>

			</div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo esc_url($inbox_link);?>"><?php _e('Sent a new message','boxtheme');?></a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li>
		<?php
	}
	function has_new_invite_to_bid($freelancer_id, $project_id){
		global $user_ID; // freelancer id
		$args = array(
			'receiver_id' => $freelancer_id, // employer
			'sender_id' => $user_ID,
			'msg_content' =>"type=invite_bid&freelancer_id={$freelancer_id}&project_id={$project_id}&employer={$user_ID}",
		);
		$this->insert_notification($args);
	}

	function render_item_invite_bid($noti, $output){
		$freelancer_id = isset($output['freelancer_id']) ?$output['freelancer_id'] : 0;
		$employer_id = isset($output['employer']) ?$output['employer'] : 0;
		$project_id = isset($output['project_id']) ?$output['project_id'] : 0;
		$project_link = get_permalink($project_id);

		?>
		<li class="dropdown-item noti-read id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $employer_id ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo $project_link;?>">Invited you bon on a project.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li> <?php
	}

	function render_item_new_conversation($noti, $output){
		$employer = isset($output['employer']) ?$output['employer'] : 0;
		$project_id = isset($output['project_id']) ?$output['project_id'] : 0;
		$project_link = get_permalink($project_id);
		$link = add_query_arg('workspace',1, $project_link);
		$inbox = box_get_static_link('inbox');
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $employer ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo $inbox;?>">Have a new conversation.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li> <?php
	}
	function render_item_emp_review($noti, $output){
		$reviewer = isset($output['reviewer']) ?$output['reviewer'] : 0;
		$project_id = isset($output['project_id']) ?$output['project_id'] : 0;
		$project_link = get_permalink($project_id);
		$link = add_query_arg('workspace',1,$project_link);
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $reviewer ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo $link;?>">Mark as complete the project.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li> <?php
	}
	function render_item_fre_review($noti, $output){
		$reviewer = isset($output['reviewer']) ?$output['reviewer'] : 0;
		$project_id = isset($output['project_id']) ?$output['project_id'] : 0;
		$project_link = get_permalink($project_id);
		$link = add_query_arg('workspace',1,$project_link);
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $reviewer ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo $link;?>">Freelancer review your feedback.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li> <?php
	}
	function render_item_fre_delivery($noti, $output){ //fre_delivery
		$freelancer_id = isset($output['freelancer_id']) ?$output['freelancer_id'] : 0;
		$project_id = isset($output['project_id']) ?$output['project_id'] : 0;
		$project_link = get_permalink($project_id);
		$link = add_query_arg('workspace',1,$project_link);
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $freelancer_id ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo $link;?>">Delivery the result.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li> <?php
	}

	function render_item_approve_order($noti, $output){

		$buyer = isset($output['buyer']) ?$output['buyer'] : 0;
		$order_id = isset($output['order_id']) ?$output['order_id'] : 0;

		?>
		<li class="dropdown-item noti-read " id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $buyer ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="#">Your cash order is approved.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li>
		<?php
	}
	function render_default_notification($noti, $output){

		$class ="noti-read";
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="#"><?php echo get_avatar( $noti->sender_id ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="#"><?php echo $noti->msg_content;?></a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li>
		<?php
	}

	function render_item_new_message($noti, $output){
		$cvs_id = $output['cvs_id'];
		$inbox_link = add_query_arg('c',$cvs_id,$this->inbox_link);

		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="<?php echo $inbox_link;?>"><?php echo get_avatar( $noti->sender_id ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo esc_url($inbox_link);?>"><?php _e('Sent a new message','boxtheme');?></a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li>
		<?php
	}
	function render_item_new_bid($noti,$output){
		$bidder = $output['bidder'];
		$project_id = isset($output['project_id']) ? $output['project_id'] : 0;
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="<?php echo get_author_posts_url($bidder);?>"><?php echo get_avatar( $bidder ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo get_permalink($project_id);?>"><?php _e('New bid on your project.','boxtheme');?></a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Delete','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
		</li>
		<?php
	}
	function render_item_adward_job($noti, $output){
		$owner = isset($output['owner']) ?$output['owner'] : 0;
		$owner = get_userdata($owner);
		$project_id = isset($output['project_id']) ? $output['project_id'] : 0;
		if( $owner && ! is_wp_error($owner) ){	?>
			<li class="dropdown-item noti-read" id="noti-id-<?php echo $noti->ID;?>">
				<div class="left-noti"><a href="<?php echo get_author_posts_url($owner);?>"><?php echo get_avatar( $owner ); ?></a></div>
				<div class='right-noti'>
					<a class="noti-link" href="<?php echo get_permalink($project_id);?>"> Congrat.<?php echo $owner->user_login;?> awarded job for you.</a>
					<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
				</div>
				<span class="btn-del-noti" title="<?php _e('Delete','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
			</li><?php
		} else { ?>
			<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
				<div class="left-noti"></div>
				<div class='right-noti'>
					<a class="noti-link" href="<?php echo get_permalink($project_id);?>"> Congrat.You are awarded on a project.</a>
					<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
				</div>
				<span class="btn-del-noti" title="<?php _e('Delete','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><?php box_close_icon();?></span>
			</li>
		<?php }

	}
	function render_item_delete_bid($noti, $output){
		$bidder = $output['bidder'];
		$project_id = isset($output['project_id']) ? $output['project_id'] : 0;
		?>
		<li class="dropdown-item noti-read" id="noti_id_<?php echo $noti->ID;?>">
			<div class="left-noti"><a href="<?php echo get_author_posts_url($bidder);?>"><?php echo get_avatar( $bidder ); ?></a></div>
			<div class='right-noti'>
				<a class="noti-link" href="<?php echo get_permalink($project_id);?>">Cancel bid on your project.</a>
				<?php echo '<small class="mdate">'.$noti->date.'</small>'; ?>
			</div>
			<span class="btn-del-noti" title="<?php _e('Delete','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><i class="fa fa-times primary-color" aria-hidden="true"></i></span>
		</li><?php
	}


	function insert_notification( $args ) {

		global $wpdb, $user_ID;

		$receiver_id = isset($args['receiver_id'])? $args['receiver_id']: 0;

		$sender_id = isset($args['sender_id']) ? $args['sender_id']: $user_ID;

		$noti_args =  array(
			'msg_content' => $args['msg_content'],
			'msg_unread' => 1,
			'sender_id' => $sender_id,
			'msg_status' => 'new',
			'msg_type' => $this->type,
			'msg_date'	=> current_time('mysql'),
			'time_gmt' => current_time( 'timestamp', 1),
			'receiver_id' => $receiver_id
		);

		$wpdb->insert( $wpdb->prefix . 'box_messages', $noti_args);

		$number_new_notify = (int) get_user_meta( $receiver_id, 'number_new_notify', true ) + 1;
		update_user_meta( $receiver_id,'number_new_notify', $number_new_notify );

		return $wpdb->insert_id;
	}

	function insert( array $args ) {

		global $wpdb, $user_ID;

		$receiver_id = isset($args['receiver_id'])? $args['receiver_id']: 0;
		$sender_id = isset($args['sender_id']) ? $args['sender_id']: $user_ID;

		$noti_args =  array(
			'msg_content' => $args['msg_content'],
			'msg_date'	=> current_time('mysql'),
			'msg_unread' => 1,
			'sender_id' => $sender_id,
			'msg_status' => 'new',
			'msg_type' => $this->type,
			'time_gmt' => current_time( 'timestamp', 1),
			'receiver_id' => $receiver_id
		);

		$wpdb->insert( $wpdb->prefix . 'box_messages', $noti_args);

		$number_new_notify = (int) get_user_meta( $receiver_id, 'number_new_notify', true ) + 1;
		update_user_meta( $receiver_id,'number_new_notify', $number_new_notify );

		return $wpdb->insert_id;
	}
	function get_message($msg_id){
		global $wpdb;
		$sql = " SELECT * FROM " . $wpdb->prefix . "box_messages WHERE ID = '$msg_id'";
		return $wpdb->get_row($sql);

	}
	function seen_all(){
		global $wpdb, $user_ID;
		update_user_meta($user_ID, 'number_new_notify', 0);

		return $wpdb->query( $wpdb->prepare(			"
				UPDATE {$this->table}
				SET msg_unread = %d
				WHERE receiver_id = %d and msg_type = %s
			", 0 , $user_ID, 'notify')
		);
	}

	function delete($id){
		global $wpdb, $user_ID;
		return $wpdb->query(
			$wpdb->prepare( "
		        DELETE FROM {$wpdb->prefix}box_messages
				WHERE ID = %d
				AND receiver_id = %d
				", $id, $user_ID
		    )
		);

	}
}
new Box_Notify();
function set_notify_content($type, $output){
	$html = '';
	switch ($type) {
		case 'approve_order':
			$html = __('Your order is approved','boxtheme');
			break;
		case 'approve_order':
			$html = __('Your order is approved','boxtheme');
			break;
		case 'delete_bid':
			$html = __('Use deleted bid on your project','boxtheme');
			break;
		case 'adward_job':
			$html = __('Congrat.You are awarded on a project.','boxtheme');
			break;
		case 'invite_bid':
			$html = __('There is an invitation to you.','boxtheme');
			break;
		case 'new_bid':
			$html = __('There is a new bid on your project.','boxtheme');
			break;


		default:
			# code...
			break;
	}
	return $html;
}
function box_close_icon(){
	?>
	<svg aria-hidden="true" data-prefix="fal" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-times fa-w-10 fa-3x"><path fill="currentColor" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" class=""></path></svg>
	<?php
}
// function box_insert_notification($args){
// 	$args = array(
// 			'msg_content' => sprintf( __('%s marked as complete the project <i>%s</i>','boxtheme'), $current_user->user_login, $project->post_title ),
// 			'receiver_id' => $project->post_author,
// 		);
// 	Box_Notify::get_instance()->insert($args);
// }