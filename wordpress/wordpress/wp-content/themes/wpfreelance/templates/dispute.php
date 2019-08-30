<div class="full wrap-workspace dispute-section" style="background-color: #fff !important; padding-bottom: 15px; border:0px solid #e6e6e6 !important;  border-top:1px solid #e6e6e6 !important;">
		<div class="col-md-12">
			<?php global $project, $winner_id, $cvs_id, $user_ID; ?>
			<h3><?php _e('Dispute section','boxtheme');?></h3>
			<label> <?php _e('Based on your feedback. We (adminstrator) will make a decision for this case. Please clarify your claim here.','boxtheme');?></label>
			<h3><?php _e('Feedback','boxtheme');?> </h3>

			<?php

			$feebacks = BX_Message::get_instance($cvs_id)->get_converstaion_custom('disputing');
			$bid_author = get_userdata($winner_id);
			$employer = get_userdata($project->post_author);

			$display_name = array(
				$winner_id => 'Freelancer '.$bid_author->display_name,
				$project->post_author => 'Employer '. $employer->display_name,
			);
			foreach ($feebacks as $key => $msg) {
				//echo '<div class="col-avatar">'.get_avatar( $msg->sender_id, 39).'<div>';
				if( (int) $msg->receiver_id == 0 ){ // employer or freelancer send.

					echo '<strong>'.$display_name[$msg->sender_id].'</strong> send a feedback to admin ';
				} else {
					if( (int) $msg->receiver_id == $winner_id ){
						echo '<strong>Administrator </strong> send a message to freelancer';
					} else {
						echo '<strong>Administrator </strong> send a message to employer';
					}

				}
				echo ' ('.$msg->msg_date.') : <br />';
				echo $msg->msg_content .'<br />' ;

				# code...
			}
			$place_holder = __('Freelancer send feedback to admin here','boxtheme');
			if ( $user_ID == $project->post_author){
				$place_holder = __('Employer send feedback to admin here','boxtheme');
			}
			if( $project->post_status == 'resolved' ){

				$end_msg = get_post_meta($project->ID, 'choose_dispute_msg', true);
				echo '<h2> Job is resolved </h2>';
				if( !empty( $end_msg) ){
					echo 'Admin resolved this case with the feedback: <br />';
					echo '"<strong><i>'. $end_msg . '</i></strong>"';
				}
			}
			$project_status = $project->post_status;
			if( $project_status == 'disputing' && ($user_ID == $winner_id || $user_ID == $project->post_author) ){
				// don't show the form if job is resolved. ?>
				<form class="swp-send-message"  >
					<textarea name="msg_content" class="full msg_content" required rows="3" placeholder="<?php echo $place_holder;?>"></textarea>
					<input type="hidden" name="cvs_id" value="<?php echo $cvs_id;?>">
					<input type="hidden" name="receiver_id" value="0">

					<input type="hidden" name="msg_type" value="disputing">
					<input type="hidden" name="method" value="insert">
					<br />
					<button type="submit" class="btn btn-send-message align-right f-right"><?php _e('Send','boxtheme');?></button>
				</form>
			<?php } ?>
			<?php if( current_user_can( 'manage_options' ) && $project_status == 'disputing' ){ ?>
				<label>Admin action:</label>
				<form id="frmAdminAct" class="frm-admin-act form-inline"  >

					<input type="hidden" name="cvs_id" value="<?php echo $cvs_id;?>">
					<input type="hidden" name="fre_id" value="<?php echo $winner_id;?>">
					<input type="hidden" name="emp_id" value="<?php echo $project->post_author;?>">
					<input type="hidden" name="project_id" value="<?php echo $project->ID;?>">

					<input type="hidden" name="msg_type" value="disputing">
					<input type="hidden" name="method" value="insert">
					<div class="form-row align-items-center">
						<div class="col-auto">
					      	<div class="input-group col-md-12">
					        	<div class="input-group-addon" style="width: 165px;">
					        	<select name="act" class="custom-select required" style="background: #eeeeee; border:0;" required>
					        		<option><?php _e('Select option','boxtheme');?></option>
									<option value="ask_fre"><?php _e('Send a messsage to Freelancer','boxtheme');?></option>
									<option value="ask_emp"><?php _e('Send a message to Employer','boxtheme');?></option>
									<option value="choose_fre_win"><?php _e('Choose freelancer winner','boxtheme');?></option>
									<option value="choose_emp_win"><?php _e('Choose employer winner','boxtheme');?></option>
								</select>
								</div>
					        <textarea type="text" class="form-control required" name="msg_content" id="msg_content" required placeholder="<?php _e('Admin add feedback here','boxtheme');?>" style="height: 39px; width: 100%;"></textarea>

					      </div>
					    </div>
					</div>
					 <button type="submit" class="btn btn-send-message align-right f-right"><?php _e('Send','boxtheme');?></button>
				</form>
			<?php } ?>

		</div>

</div>
<style type="text/css">
	.frm-admin-act{
		position: relative;
	}
	.frm-admin-act .btn-send-message {
	    z-index: 100;
	    height: 25px;
	    padding: 2px 9px;
	}
	.sl-ask{
		position: 1absolute;
	}
	.dispute_wrap{
		max-width: 850px;
		margin: 0 auto;
	}
</style>