<?php
	global $user_ID, $project, $winner_id, $is_owner, $cvs_id, $role;
	$is_fre_review = get_post_meta( $project->ID,'is_fre_review', true );
	$bid_id_win = get_post_meta($project->ID, BID_ID_WIN, true);

	$_bid_price = get_post_meta($bid_id_win, BID_PRICE, true);

?>
<div class="full row-detail-section">
	<div class="col-md-8 wrap-workspace  column-left-detail">
		<div class="full align-right f-right ws-btn-action"> <?php
			if( $project->post_status =='awarded'  ){
				$fre_markedascomplete = get_post_meta( $project->ID, 'fre_markedascomplete', true);
				if( $user_ID == $project->post_author ){ // employer  ?>
					<button type="button" class="btn btn-quit" data-toggle="modal" data-target="#disputeModal" data-whatever="@mdo"><?php _e('Dispute','boxtheme');?></button>
					<button type="button " class="btn btn-finish" data-toggle="modal" data-target="#reviewModal" data-whatever="@mdo"><?php _e('Mark as Finish','boxtheme');?></button>
				<?php } else if( $user_ID == $winner_id  ){
					if( empty($fre_markedascomplete)){ ?>
						<button type="button" class="btn btn-quit" data-toggle="modal" data-target="#quytModal" data-whatever="@mdo"><?php _e('Quit Job','boxtheme');?></button>
					<?php  }?>

					<?php if ( empty( $fre_markedascomplete ) ) { ?>
						<button type="button " class="btn btn-finish" data-toggle="modal" data-target="#freMarkAsComplete" data-whatever="@mdo"><?php _e('Mark as Complete','boxtheme');?></button>
					<?php } else {?>
						<button type="button" class="btn btn-quit" data-toggle="modal" data-target="#disputeModal" data-whatever="@mdo"><?php _e('Dispute','boxtheme');?></button>
					<?php } ?>

				<?php }

			} else if( $project->post_status == 'complete' && $user_ID == $winner_id && !$is_fre_review ) { ?>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#reviewModal" data-whatever="@mdo"><?php _e('Review','boxtheme');?></button> <?php

			} ?>

		</div>
		<?php

		if($project->post_status == COMPLETE) {
			echo '<div class="full review-section">'; ?>

				<?php
				echo '<p>'; _e('Job is complete.','boxtheme'); echo '</p>';

				// show rating here.
				$bid_id = get_post_meta($project->ID, BID_ID_WIN, true);
				$args = array(
					'post_id' => $bid_id,
					'type' => 'emp_review',
					'number' => 1,
				);
				$emp_comment = get_comments($args);

				if( !empty( $emp_comment ) ){
					echo '<div class="full rating-line">';
					if(  ( $role == FREELANCER && $is_fre_review ) || $role != FREELANCER ) {
						echo '<label>'.__('Employer review:','boxtheme').'</label>';
						$rating_score = get_comment_meta( $emp_comment[0]->comment_ID, RATING_SCORE, true );
						bx_list_start($rating_score);
						echo '<i>'.$emp_comment[0]->comment_content.'</i>';
					} else if( !$is_fre_review){
						//freelancer still not review employer yet.
						_e('Employer reviewed and marked this job/project as closed. <br />You have to  review the project to see employer\'s review.','boxtheme');
					}
					echo '</div>';
				} else{
					_e('Employer did not leave a review.','boxtheme');
				}

				$args = array(
					'post_id' => $project->ID,
					'type' => 'fre_review',
					'number' => 1,
				);
				$fre_comment = get_comments($args);
				if( ! empty( $fre_comment) ) {
					echo '<div class="full rating-line">';
						echo '<label>'.__('Freelancer review:','boxtheme').'</label>';
						$rating_score = get_comment_meta( $fre_comment[0]->comment_ID, RATING_SCORE, true );
						bx_list_start($rating_score);
						echo '<i>'.$fre_comment[0]->comment_content.'</i>';
					echo '</div>';
				}
			echo '</div>';
		} ?>
	<div class="fb-history full col-md-12">

		<?php
		if( ! empty ( $fre_markedascomplete ) ){
			//echo 'Freelancer marked as complete in 10/2017';
		}
		if ( $project->post_status == 'disputing' ){
			$user_send_id = get_post_meta($project->ID, 'user_send_dispute', true);
			$disputor = get_userdata( $user_send_id );
			printf( __('%s sent a disputing request. This project is disputed and is subject for admin review. Go to <a href="?dispute=1"> dispute section </a> to see the process. The chat will be disabled while the dispute process in on-going. ','boxtheme'), $disputor->display_name );
		}
		?>
	</div>
	<div class="col-md-12"><h3 class="default-label "><?php _e('Conversation','boxtheme');?></h3></div>
	<div class="full container-wsp-conversation">
		<?php show_conversation($winner_id, $project, $cvs_id); ?>
	</div>

	<?php  echo '<input type="hidden" id="cvs_id" value="'.$cvs_id.'" />';		?>
	</div> <!-- wrap-workspace !-->

	<div class="col-md-4  column-right-detail">
		<div class="full">
			<?php // if( ! $is_fre_review  ){ ?>
				<div id="container_file" class="clear block">
				    <button class="btn f-right btn-add-file" id="pickfiles"> <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 31.059 31.059" style="enable-background:new 0 0 31.059 31.059;" xml:space="preserve">
<g>
	<g>
		<path style="fill:#fff;" d="M29.959,31.059H1.1c-0.483,0-0.875-0.392-0.875-0.875v-9.735c0-0.483,0.392-0.875,0.875-0.875
			c0.482,0,0.875,0.392,0.875,0.875v8.86h27.108v-8.86c0-0.483,0.392-0.875,0.875-0.875s0.875,0.392,0.875,0.875v9.735
			C30.834,30.667,30.442,31.059,29.959,31.059z"/>
	</g>
	<g>
		<g>
			<path style="fill:#fff;" d="M15.529,23.622c-0.483,0-0.875-0.392-0.875-0.875V1.22c0-0.482,0.392-0.875,0.875-0.875
				c0.483,0,0.875,0.393,0.875,0.875v21.527C16.404,23.231,16.012,23.622,15.529,23.622z"/>
		</g>
		<g>
			<path style="fill:#fff;" d="M26.292,12.858c-0.229,0-0.457-0.089-0.629-0.267L15.529,2.12L5.395,12.591
				c-0.336,0.347-0.889,0.356-1.237,0.021c-0.347-0.336-0.355-0.89-0.02-1.237L14.9,0.254c0.33-0.339,0.928-0.339,1.258,0
				l10.763,11.12c0.336,0.348,0.326,0.901-0.021,1.237C26.73,12.776,26.511,12.858,26.292,12.858z"/>
		</g>
	</g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
	<i class="fa fa-upload hide" aria-hidden="true"></i> &nbsp;  <?php _e(' Add File','boxtheme');?> </button>
				</div>
			<?php // } ?>
			<div id="filelist" class="full">
				<!-- // nho check case post_parent when set featured 1 image -->
				<?php
				$args = array(
				   'post_type' => 'attachment',
				   'numberposts' => -1,
				   'post_status' => 'private',
				   'post_parent' => $post->ID
				  );

				$fre_acc = get_userdata($winner_id);
				$emp_acc = get_userdata($project->post_author);

				$attachments = get_posts( $args );
				$display = array(
					$project->post_author => $emp_acc->display_name,
					$winner_id =>$fre_acc->display_name,
				);
				echo '<ul class="list-attach clear block none-style">';
			    if ( $attachments ) {
			        foreach ( $attachments as $attachment ) {
			           echo '<li class="full f-left">';
				           echo '<span class="clearboth full"><span class="attach-author"><strong>'.$display[$attachment->post_author].'</strong> -  </span><span class="date-upload">'. date(' F j, Y', strtotime($attachment->post_date)).'</span></span>';
				           echo '<span class="primary-color attach-name">'.$attachment->post_title.'</span>';
				           	if($user_ID === $attachment->post_author && $project == AWARDED ){
				           		echo '<span id="'.$attachment->ID.'" class="btn-del-attachment">(x)</span> ';
				       		}
				       		$att_url = wp_get_attachment_url($attachment->ID);

				       		?>
				       		<a class="link-download-att " href="<?php echo $att_url;?>" download > <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 41.712 41.712" style="enable-background:new 0 0 41.712 41.712;" xml:space="preserve">
<path style="fill:#1E201D;" d="M31.586,21.8c0.444-0.444,0.444-1.143,0-1.587c-0.429-0.444-1.143-0.444-1.571,0l-8.047,8.032V1.706
	c0-0.619-0.492-1.127-1.111-1.127c-0.619,0-1.127,0.508-1.127,1.127v26.539l-8.031-8.032c-0.444-0.444-1.159-0.444-1.587,0
	c-0.444,0.444-0.444,1.143,0,1.587l9.952,9.952c0.429,0.429,1.143,0.429,1.587,0L31.586,21.8z M39.474,29.086
	c0-0.619,0.492-1.111,1.111-1.111c0.619,0,1.127,0.492,1.127,1.111v10.92c0,0.619-0.508,1.127-1.127,1.127H1.111
	C0.492,41.133,0,40.625,0,40.006v-10.92c0-0.619,0.492-1.111,1.111-1.111s1.127,0.492,1.127,1.111v9.809h37.236V29.086z"/> </a>
				       		<?php //echo wp_get_attachment_link($attachment->ID);?>
				       	</li>
			       		<?php

			        }
			    }
			    echo '</ul>'; ?>
			</div>
		</div>
	</div>
</div>
