<?php
Class Box_Live_Notification{

	function __construct() {

		add_filter( 'heartbeat_received', array($this, 'box_receive_heartbeat'), 1, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'box_heartbeat_scripts' ) );
		add_action( 'wp_footer', array ($this, 'live_scrip_onfooter') );
		add_filter( 'heartbeat_settings', array( $this, 'update_time_check' ) , 99 );
	}
	function update_time_check($config){
		$config['interval'] = 10;
        return $config;
	}
	public function box_heartbeat_scripts(){
		if( is_user_logged_in() ) {
		  	wp_enqueue_script('heartbeat');
		}
	}
	function get_unread_message(){
		global $wpdb, $user_ID;
		$sql = $wpdb->prepare(
				"	SELECT msg.*, count( case msg.msg_unread = '1' when 1 then 1 else null end) as count_unread
					FROM {$wpdb->prefix}box_messages msg
					WHERE msg.receiver_id = %d AND( msg.msg_type ='notify' OR (msg.msg_type = 'message' ) )
					group by (case when msg.msg_type='message' then msg.cvs_id end) DESC
					ORDER BY msg.msg_date  DESC LIMIT 8 ",
					$user_ID
			);
		$list_noti  = $wpdb->get_results($sql);
		$notifies = array();
		$count_unread = 0;
		if( $list_noti){

			foreach ( $list_noti as $noti ) {
				$notifies[] = $noti;
				if( $noti->count_unread  ){
					$count_unread = $noti->count_unread + $count_unread;

					parse_str($noti->msg_content, $output);
					$noti->noti_type = 'new_message'; // custom argument - don't have this column in db;
					if( ! empty ( $output['type']) ){
						$noti->noti_type = $output['type'];
						$this->msg_content = set_notify_content($output['type'], $output);
					}
					$date = date_create( $noti->msg_date );
					$noti->date = date_format($date,"m/d/Y");
					$noti->sender_avatar = $noti->noti_link = '';

					if($noti->noti_type == 'new_message'){
						$cvs_id = $noti->cvs_id;
						$inbox_link = box_get_static_link('inbox');
						$noti->noti_link= add_query_arg('c',$cvs_id,$inbox_link);
						$noti->sender_avatar = get_avatar( $noti->sender_id );

					}
				}
			}
		}

		return  array('msgs' => $notifies,'count' => $count_unread);


	}

	public function box_receive_heartbeat( $response, $data ) {
		if( isset($data['check_live']) ){
			$unread_messages = $this->get_unread_message();
		    $response['unread_messages'] = $unread_messages['msgs'];

		    $response['number_unread'] =  $unread_messages['count'];;
		}
	    return $response;
	}
	function live_scrip_onfooter() {
		if(! is_user_logged_in() )
			return ;
	?>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					if( typeof  wp.heartbeat !== 'underfined'){
						wp.heartbeat.enqueue(
						    'check_live', true,
						   	false
						);
					}
				});

				$( document ).on( 'heartbeat-tick', function ( event, data ) {

					wp.heartbeat.enqueue(
					    'check_live', true,
					   	false
					);

				});
				$(document).on( 'heartbeat-tick.unread_messages', function( event, data ) {
			        if ( data.hasOwnProperty( 'unread_messages' ) ) {

			            var message_template = wp.template('noti_new_message' );
			            var new_notify = wp.template('new_notify' );

			            jQuery.each(data['unread_messages'], function(index, item) {

			            	var ttt = item.msg_content.split('&');


			            	if( $("#noti_id_"+item.ID).length ) {
			            		$("#noti_id_"+item.ID).find(".number-msg").html(item.count_unread);
			            	} else {
			            		if(item.noti_type == 'new_message'){
				           	  		html = message_template(item );
				           	  	} else {
				           	  		html = new_notify(item );
				           	  	}
			           	  		$(".ul-notification").append(html);
			           	  	}
			           	});

			            $(".notify-number ").html(data['number_unread']);
			            if( parseInt(data['number_unread']) > 0 ){
			            	$(".notify-number ").addClass('notify-acti');
			            } else {
			            	$(".notify-number ").removeClass('notify-acti');
			            }
			        }
			    });

			})(jQuery);
		</script>
		<script type="text/html" id="tmpl-noti_new_message">
			<li class="dropdown-item noti-read" id="noti_id_{{{data.ID}}}">
				<div class="left-noti">
					<a href="{{{data.noti_link}}}">{{{data.sender_avatar}}}<span class="number-msg number-unread-notzero">{{{data.count_unread}}}</span></a>
				</div>
				<div class="right-noti">
					<a class="noti-link" href="{{{data.noti_link}}}">Sent a new message</a>
					<small class="mdate">{{{data.date}}}</small>			</div>
				<span class="btn-del-noti" title="Delete" rel="{{{data.ID}}}" href="#"><?php box_close_icon();?></span>
			</li>
		</script>
		<script type="text/html" id="tmpl-new_notify">
			<li class="dropdown-item noti-read">
				<div class="left-noti">
					<a href="#"><img alt="" src=""></a>
					<span class="number-msg-unread">{{{data.count_unread}}}</span>

				</div>
				<div class="right-noti">
					<a class="noti-link" href="http://localhost:8080/tut/freelance/inbox/?c=1">{{{data.msg_content}}}</a>
					<small class="mdate">{{{data.date}}}</small>			</div>
				<span class="btn-del-noti" title="Remove" rel="1" href="#"><i class="fa fa-times primary-color" aria-hidden="true"></i></span>
			</li>
		</script>
		<?php
	}
}

new Box_Live_Notification();
?>