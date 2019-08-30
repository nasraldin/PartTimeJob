<?php
/**
 *	Template Name: Inbox
 */
?>
<?php get_header(); ?>

<?php
global $user_ID;
$sql = "SELECT *
	FROM  {$wpdb->prefix}box_conversations cv
	INNER JOIN {$wpdb->prefix}box_messages  msg
	ON  cv.ID = msg.cvs_id
	WHERE  ( msg.sender_id = {$user_ID} OR msg.receiver_id = {$user_ID} )
	ORDER BY cv.date_modify DESC";

$sql = "SELECT c.*, c.ID as cid, msg.*, count(case msg.msg_unread and  msg.receiver_id = {$user_ID} when 1 then 1 else null end) as count_unread  FROM {$wpdb->prefix}box_conversations c LEFT JOIN {$wpdb->prefix}box_messages msg ON msg.cvs_id = c.ID WHERE ( msg.sender_id = {$user_ID} OR msg.receiver_id = {$user_ID} ) GROUP BY c.ID ORDER BY c.date_modify DESC";

$current_id = isset($_GET['c']) ? $_GET['c']: 0;
$first_cvs = 0;
if($current_id){
	$first_cvs = $current_id;
}
$current_c = array();

$conversations = $wpdb->get_results($sql); // list conversations
$avatars = array();
$right_boxcss = 'cvs-null';
?>
<div class="full-width box-define-row dashboard-area">
	<?php box_header_link_in_dashboard();?>
	<div class="container site-container ">
		<div class="row site-content" id="content" >
			<div class="col-md-12 top-line">&nbsp;</div>
			<div class="col-md-4 list-conversation">

				<div class="full-content ">
					<?php if( $conversations ){ ?>
						<div class="full search-msg-wrap">
							<div class="col-md-12">
								<form>
									<input type="text" name="s" placeholder="<?php _e('Search conversations','boxtheme');?>" class="form-control" />
								</form>
							</div>
						</div>
					<?php }?>
					<ul class="none-style" id="list_converstaion"><?php
					if( $conversations ) {
						$current_c = $conversations[0];
						$right_boxcss='cvs-not-null';

						foreach ( $conversations as $key=>$cv ) {


							$date = date_create( $cv->date_modify );
							$date = date_format($date,"m/d/Y");

							$user = array();

							if( (int) $cv->sender_id == $user_ID ){
								$user = get_userdata($cv->receiver_id);
							}else{
								$user = get_userdata($cv->sender_id);
							}


							$avatars[$cv->cvs_id] = get_avatar($user->ID );


							$profile_link = '#';
							$profile_id = is_box_freelancer( $user->ID );
							if( $profile_id ){
								$profile_link = get_author_posts_url($user->ID); // only available for freelancer account.
							}
							if( $user  ){
								$class = '';

								if($cv->cid == $first_cvs){	$class = 'acti'; $current_c = $cv;	} ?>

								<li class="cv-item c-item-<?php echo $cv->cvs_id;?> <?php echo $class;?>">
									<div class="cv-left"><?php echo get_avatar($user->ID);?></div>
									<div class="cv-right">
										<small class="mdate"><?php echo $date; ?></small>
										<a has_profile="<?php echo $profile_id;?>" href="<?php echo $profile_link;?>" disable class="render-conv" id="<?php echo $cv->cvs_id;?>"><?php echo $user->display_name;?></a>
										<?php if($cv->count_unread > 0){ ?>
											<span>( <?php  echo $cv->count_unread;?>)</span>
										<?php } ?>
										<?php
										if($cv->project_id){
											$project = get_post($cv->project_id);						?>

											<p class="msg-project"><small><?php printf( __('Project: %s','boxtheme'),'<a target="_blank" href="'.get_permalink($project->ID).'">'.$project->post_title.'</a>');?></small></p>
										<?php } else {?>
											<p class="msg-project"><small><?php _e('Direct Mesasge','boxtheme');?></small></p>
										<?php } ?>
									</div>
								</li><?php
							}
						} // end foreach
					} else { ?>
						<li class="no-item"><?php _e('There is no conversations yet.','boxtheme');?> </li>
					<?php }?>
					</ul>
				</div>
			</div>
			<div id="box_chat" class="col-md-8 right-message no-padding-right">
				<?php


				if(!empty( $current_c) ){
					$current_cid = $current_c->cvs_id;
					$first_cvs = $current_c->cvs_id;
					$first_user_id = $current_c->sender_id;
					if($current_c->sender_id == $user_ID){
						$first_user_id = $current_c->receiver_id;
					}
					$profile_link = '#';
					$first_user = get_userdata($first_user_id);

					if( is_box_freelancer( $first_user_id ) ){
						$profile_link = get_author_posts_url($first_user_id);
						echo '<span class="msg-receiver-name">'.sprintf(__('To: %s','boxtheme'),'<a target="_blank" href="'.$profile_link.'" id="display_name">'.$first_user->display_name.'</span>').'</a>';
					} else {
						echo '<span class="msg-receiver-name">'.sprintf(__('To: %s','boxtheme'),'<span id="display_name">'.$first_user->display_name.'</span>').'</span>';
					}

					echo '<input type="hidden" value="'.$current_cid.'" id="first_cvs" class="get-id-url" />';

				}

				?>
				<div id="container_msg" class="<?php echo $right_boxcss;?>">
					<?php
					if($current_c){
						$msgs = BX_Message::get_instance()->get_converstaion( array( 'id' => $current_c->cvs_id) );
						foreach ($msgs as $key => $msg) {

							$date = date_create( $msg->msg_date );
							$attach_ids  = $msg->attach_ids ;
							$html_media = array();
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
							$temp = (object) array(
								'post_title'	=>'a message',
								'post_status' 	=> 'message',
								'post_content' 	=> $msg->msg_content,
								'post_type' 	=> 'message'
							);
							setup_postdata($temp);

							if( $msg->sender_id != $user_ID ){
								$user_label = get_avatar($msg->sender_id  ); ?>
								<div class="msg-record msg-item">
									<div class="col-md-1 no-padding"><?php echo $user_label;?></div>
									<div class="col-md-10 no-padding-left">
										<span class="wrap-text ">
											<span class="triangle-border left"><?php the_content(); ?><br /> <?php echo join(" ",$html_media);?></span>
											<small class="msg-mdate"><?php echo date_format($date,"m/d/Y");?></small>
										</span>
									</div>
								</div><?php
							} else { ?>
								<div class="msg-record msg-item">
									 <div class="col-md-9 pull-right text-right"><span class="wrap-text-me">
									 	<span class="my-reply"><?php the_content();?><br /> <?php echo join(" ",$html_media);?></span>
									 	<br /><small class="msg-mdate"><?php echo date_format($date,"m/d/Y");?></small></div>
								</div><?php
							}
						}
						BX_Message::get_instance()->mark_as_read_all_msg($current_c->cvs_id);
					}?>
				</div>

				<div id="form_reply">

						<form class="frm-send-message" >
							<textarea name="msg_content" class="full msg_content required" required rows="3" placeholder="<?php _e('Write a message','boxtheme');?>"></textarea>
							<button type="submit" class="btn btn-send-message align-right f-right"><?php _e('Send','boxtheme');?></button>

							<div id="fileupload-container" class="file-uploader-area">
							    <span class="btn-file-uploader ">
							      	<input type="hidden" class="nonce_upload_field" name="nonce_upload_field" value="<?php echo wp_create_nonce( 'box_upload_file' ); ?>" />
							      	<input type="file" name="upload[]" id="sp-upload" multiple="" class="fileupload-input" style="visibility: hidden;">
							      	<i class="fa fa-paperclip" aria-hidden="true"></i>
							  	</span>
						 	</div>

						</form>

				</div>


			</div>
		</div>
	</div>
</div>
<script type="text/html" id="tmpl-msg_record">
	<div class="row">{{{username_sender}}}: {{{msg_content}}} {{{msg_date}}}</div>
</script>
<script type="text/html" id="tmpl-msg_record_not_me"> <!-- use for not current user !-->
	<div class="msg-record msg-item">
		<div class="col-md-1 no-padding">{{{data.avatar}}}</div>
		<div class="col-md-10 no-padding-left"><span class="wrap-text "><span class="triangle-border left">{{{data.msg_content}}} <br /> {{{data.html_media}}}</span> <br /><small class="msg-mdate">{{{data.msg_date}}}</small></span></div>
	</div>
</script>
<script type="text/html" id="tmpl-msg_record_me"> <!-- use for not current user !-->
	<div class="msg-record msg-item">
		<div class="col-md-9 pull-right text-right"><span class="wrap-text-me"><span class="my-reply">{{{data.msg_content}}}
		<br /> {{{data.html_media}}}
	</span><br /><small class="msg-mdate">{{{data.msg_date}}}</small> </span></div>
	</div>

</script>
<script type="text/template" id="json_avatar"><?php  echo json_encode($avatars); ?></script>
<style type="text/css">
	.search-msg-wrap{
		background: #f9f9f9;
		padding: 15px 0;
		float: left;
	}
	.site-content{
		background: transparent;
		padding-top: 0;
		padding-bottom: 0;
		min-height: auto;
	}
	.triangle-border.left {
	    margin-left: 30px;
	}

	.triangle-border {
		width: 95%;
		float: left;
	    position: relative;
	    padding: 3px 15px;
	    margin: 0;
	    border: 5px solid #ecf8ff;
	    color: #616161;
	    background: #ecf8ff;
	    -webkit-border-radius: 10px;
	    -moz-border-radius: 10px;
	    border-radius: 10px;
	}
	.triangle-border.left:before {
	    top: -5px;
	    bottom: auto;
	    left: -30px;
	    border-width: 7px 38px 6px 0px;
	    border-color: transparent #ecf8ff;
	}

	.triangle-border:before {
	    content: "";
	    position: absolute;
	    bottom: -20px;
	    left: 40px;
	    border-width: 20px 20px 0;
	    border-style: solid;
	    border-color: #ecf8ff transparent;
	    display: block;
	    width: 0;
	}
	.wrap-text-me{
		float: right;
		clear: both;
	}
	.my-reply{
		float: right;
	    background: rgba(0, 157, 175, 0.95);
	    padding: 5px 15px;
	    color: #fff;
	    border-radius: 5px;
	}
	.my-reply small{
		float: right;
		clear: both;
	}
	.msg-mdate{
		font-size: 13px;
		display: block;
		clear: both;
	}
	#list_msg{
		height: 200px;
		padding-left: 15px;
		padding-left: 15px;
		border:1px solid #e6e6e6;

	}
	.top-line{
		height: 30px;
		border-bottom: 1px solid #e2e2e2;
		background: #fff;
	}
	#container_msg{
	    border: 0;
	    padding-left: 15px;
	    overflow-y: auto;
	    border-bottom: 1px solid #e6e6e6;
	    border-right: 1px solid #e6e6e6;
	    margin-bottom: 0;
	}
	#container_msg.cvs-null{
		height: 487px;
	}
	.list-conversation{
		background: #fff;
		border-right: 1px solid #e6e6e6;
		border-left: 1px solid #e6e6e6;
		border-bottom: 1px solid #e6e6e6;
		padding: 0;
	}
	ul#list_converstaion{
		float: left;
		width: 100%;
		padding: 0;
		overflow-y: auto;
		margin: 0;
		max-height: 405px;
	}
	.list-conversation .full-content{
		background: #fff;
		overflow: hidden;
		min-height: 486px;
		padding: 15px 0;
		padding-bottom: 0;
	}
	.right-message{
		background: #fff;
		padding-bottom: 0;
		padding-left: 0;
	}
	.msg-item{
		overflow: hidden;
		padding-bottom: 15px;
		width: 100%;
		clear: both;
		margin-bottom: 10px;
	}
	.msg-item img.avatar{
		width: 50px;
		height: 50px;
		margin-right: 13px;
		float: left;
		border-radius: 50%;
	}
	.msg-item .wrap-text{
		position: relative;
		float: left;
		min-width: 120px;
		padding-bottom: 20px;
	}
	.msg-item .wrap-text small{
		position: absolute;
		right: 0;
		bottom: 0;
	}
	.cv-item{
		clear: both;
		display: block;
		width: 100%;
		float: left;
		border-bottom: 1px solid #f1f1f1;
		padding: 9px 10px;
		position: relative;
		border-left:3px solid transparent;
	}
	.cv-item.acti{
		border-left:3px solid #54bf03;
		background-color: #f3f6f8;
	}
	.cv-item:hover{
		background-color: #f3f6f8;
	}
	.cv-item img{
		width: 55px;
		height: 55px;
		border-radius: 50%;
		vertical-align: top;
	}
	.no-item{
		padding-bottom: 21px;
		padding-left: 15px;
	}
	.cv-left{
		width: 25%;
		float: left;
	}
	.cv-right{
		width: 75%;
		float: left;
		overflow: hidden;
		position: relative;
	}
	.mdate{
		position: absolute; top:0;
		right: 0px;
	}
	.msg-record{
		width: 100%;
		clear: both;
	}
	.msg-record .msg-att-link{
		color: #313131;
		display: block;
		clear: both;
		margin-bottom: 10px;
	}
	.cv-right small{
		display: inline-block;
	    overflow: hidden;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
	textarea{
		border-color: #ececec;
	}
	#form_reply{
		background: #f9f9f9;
		overflow: hidden;
		padding: 17px 30px 17px 30px;
		border:1px solid #e6e6e6;
		border-left: 0;
		border-top: 0;
	}

	.frm-send-message .btn-send-message {
	    right: 2px;
	    top: 2px;
	}
	.msg-receiver-name{
		display: block;
		padding: 15px;
	}
	.btn-attach-file{
		font-size: 20px;
		margin-top: 15px;
		display: inline-block;
		cursor: pointer;
	}
	.file-uploader-area{
		display: block;
		clear: both;
		overflow: hidden;
	}
	.fileupload-input{
		position: absolute;
		display: inline-block;
	}
	.file-uploader-area {
		cursor: pointer;
		overflow: hidden;
		width: 15px;
		height: 15px;
		float: left;
		cursor: pointer;
	}
</style>
<?php get_footer();?>

