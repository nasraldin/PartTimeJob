<?php
global $main_page;

$email_link 	= add_query_arg('section','email', $main_page);

$group_option ="box_mail";
$option = BX_Option::get_instance();
$box_mail = (object)$option->get_mailing_setting();
$list = $option->list_email();

$label = array(
	'new_account_confirm' =>'New account register',
	'new_account' => 'New account register',
	'verified_success' => 'Verified success',
	'new_account_noti' => 'Noti - New accountt',
	'reset_password' => 'Reset password',
 	'new_job' => 'Noti - Has New project',
 	'subscriber_skill' => 'Subscriber mail - new job match with skill',
	'new_bidding' => 'New bidding',
	'new_converstaion' => 'Has new conversation',
	'new_message' => 'New message',
	'assign_job' => 'Assign job',
	'request_withdrawal' => __('New withdrawal request','boxtheme'),
	'withdrawal_request_received' => __('Noti - Withdrawal request received','boxtheme'),
	'cash_order' => __('Send to buyer the detail of cash order.','boxtheme'),
	'cash_approve' => __('Send to buyer the detail of cash order.','boxtheme'),
	'send_job_to_mail' => __('Send mail match with skill to freelancer.','boxtheme'),

);
if( box_requires_confirm() )
	unset($list['new_account']);
else
	unset($list['new_account_confirm']);

$settings = array(
	'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), // note that spaces in this list seem to cause an issue
);

?>
<div id="<?php echo $group_option;?>" class="main-group">
<div class="section box-section" id="email">
   	<div class="sub-section " id="email">
       	<h2><?php _e('Email Notifications','boxtheme');?> </h2>
       	<p><?php _e('Email notifications sent from job board are listed below. Click on an email to configure it.','boxtheme');?></p>
       	<table class="widefat">
       		<thead>
					<tr>
					<th class="page-name"><?php _e( 'Name', 'boxtheme' ); ?></th>
						<th class="page-name"><?php _e( 'Subject', 'boxtheme' ); ?></th>
						<th class="page-name"><?php _e( 'Receiver', 'boxtheme' ); ?></th>
						<th class="page-name">&nbsp;</th>
					</tr>
				</thead>
       		<?php
       		foreach ($list as $key=> $email) {
       			$mail = (object)$email;
       			$receiver = '';
       			if( !empty($mail->receiver) ){
       				$receiver = $mail->receiver;
       			}
       			$edit_link = add_query_arg('name',$key, $email_link);
       			echo '<tr><td>'.$label[$key].'<td>'.$mail->subject.'</td><td>'.$receiver.'</td><td><a href="'.$edit_link.'" class="btn-config"><i class="fa fa-cog" aria-hidden="true"></i></a></td></tr>';
       			echo '<tr class="tr-config-cotent hide"> <td colspan = "4" class="td-config-content">';
       			echo '<div class="form-group row"><form class="frm-update-mail"><div class="col-md-12"><h3> Update Email </h3><label> Subject </label><input type="text" class="form-control " name="subject" value="'.$mail->subject.'" /></div>';
       			echo '<div class="col-md-12"><label> Mail content </label>';
       			//echo '<textarea name="content" id="'.$key.'">';.stripslashes($mail->content).'</textarea>';
       			wp_editor(stripslashes($mail->content),$key, box_editor_settings() );
       			echo '<input type="hidden" class="key-input" name="key" value="'.$key.'" />';
       			echo '</div>';
       			echo '<div class="col-md-12"><button class="btn btn-submit aign-right f-right btn-bg-white" type="submit">Save</button></div>';
       			echo '</form></div>';
       			echo '</td>';
       			echo '</tr>';
       			echo '<tr class="hidden"><td colspan = "4" ></td></tr>';
       		}

       		?>
       	</table>
   	</div>
   	<p>&nbsp;</p>
   	<h2> <?php _e('Setting email','boxtheme');?> </h2>
   	<div id="box_mail" class="main-group">
   	<div class="sub-section " id="box_mail">

		<div class="full">
		<div class="form-group row">
			<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Header Email Image','boxtheme');?></label>
			<div class="col-md-12"><input class="form-control auto-save" multi="0" type="text" value="<?php echo $box_mail->header_image;?>"  multi="0" name="header_image" id="header_image"></div>
		</div>

		<div class="form-group row ">
			<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Main bg color','boxtheme');?></label>
			<div class="col-md-12 "><input class="form-control auto-save"   type="text" name="main_bg"  multi="0"  value="<?php echo $box_mail->main_bg;?>" id="main_bg"></div>
		</div>



		<div class="form-group row">
			<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Footer text','boxtheme');?></label>
			<div class="col-md-12"><input class="form-control auto-save"  multi="0"  type="text" name="footer_text" multi="0"  value="<?php echo $box_mail->footer_text;?>" id="footer_text"></div>
		</div>

		<div class="form-group row">
			<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('From name','boxtheme');?></label>
			<div class="col-md-12"><input class="form-control auto-save"  multi="0"  type="text" name="from_name" multi="0"  value="<?php echo $box_mail->from_name;?>" id="from_name"></div>
		</div>
		<div class="form-group row hide">
			<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('From Address','boxtheme');?></label>
			<div class="col-md-12"><input class="form-control auto-save"  multi="0"  type="text" name="from_address" multi="0"  value="<?php echo $box_mail->from_address;?>" id="from_address"></div>
		</div>

		<div class="hide1"><?php //wp_editor($mail->content,'contenttest' ); ?></div>
		</div>
	</div>
</div>