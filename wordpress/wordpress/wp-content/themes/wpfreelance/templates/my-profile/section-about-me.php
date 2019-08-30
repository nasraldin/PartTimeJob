<?php
	global $post, $user_ID, $current_user, $profile, $box_general;
	$placehoder_txt = $box_general->professional_default;
	$professional_title = !empty( $profile->professional_title ) ? $profile->professional_title : $placehoder_txt;
	$url = get_user_meta($user_ID,'avatar_url', true);
	setup_postdata( $profile );
	$is_available = ($profile->post_status == 'publish') ? 'checked' : ''; //publish or inactive

	$video_id = get_post_meta($profile->ID, 'video_id', true);
	$view_as_other = '';

	if( $profile->post_status == 'publish' && $profile->ID > 0 ){
		$view_as_other = '<a target="_blank" class = "view-as-other primary-color" href="'.get_permalink($profile->ID).'"> <i class="fa fa-eye"></i> '.__('View as other','boxtheme').'</a>';
	}

?>
<div id="profile" class="col-md-12 edit-profile-section overview-section edit-fre-overview.php">
	<input type="hidden" name="profile_id"  id= "profile_id" value="<?php echo $profile->ID;?>" >
	<div class="form-group "><h2 class="col-md-12"> <?php _e('About me','boxtheme');?></h2></div>
	<div class="col-md-3 update-avatar">
		<?php if ( ! empty($url ) ){ echo '<img class="avatar" src=" '.$url.'" />'; } else {echo get_avatar($user_ID); } ?>
		<div class="full">
			<p> &nbsp;</p>
			<p class="text-left"> Not Available/Available </p>
			<div class="wrap-toggle-available" style="float: left;">
		    <input class="tgl tgl-flat " rel="Title" id="is_available" <?php echo $is_available;?> type="checkbox"/>
			    <label class="tgl-btn" for="is_available"></label>
			</div>

		</div>
	</div>
	<div class="col-md-9 col-sm-12">
		<form id="update_profile" class="row-section">
	      		<span class="btn-edit btn-edit-default"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php _e('Edit','boxtheme');?></span>
	            <div class="form-group">
	        	   <h2 class="static visible-default profile-display-name" > <?php echo $current_user->display_name;?><?php echo $view_as_other;?></h2>
	        	   <input class=" update hide form-control" type="text" placeholder="<?php _e('Display Name','boxtheme');?>" value="<?php echo $current_user->display_name;?>" name="post_title">
	            </div>
	            <div class="form-group">
	            	<h3 class=" static visible-default no-padding primary-color" ><?php echo $professional_title;?></h3>
	            	<input type="text" class="update hide  form-control" placeholder = "<?php echo $placehoder_txt; ?>"  value="<?php echo $professional_title;?>" name="post_excerpt" >
	            	<input type="hidden" name ="ID" value="<?php echo $profile->ID;?>">
	            </div>
	            <div class="form-group ">
	            	<div class="static visible-default edit-profile-content author-overview"> <?php if( empty($profile->post_content) ) _e('Update your cover letter here','boxtheme'); else echo get_the_content(); ?></div>
	            	<textarea class="update hide form-control" name="post_content" cols="50" rows="6" placeholder="<?php _e("Update your cover letter here","boxtheme");?>" ><?php echo get_the_content();?></textarea>
	            </div>
	            <?php if( function_exists('box_edit_equiment_field') ) box_edit_equiment_field($profile); ?>
	            <div class="form-group">
	            	<span class=" static visible-default no-padding primary-color" ><?php _e('Your Youtube Video ID: ','boxtheme');?><?php echo $video_id;?></span>
	            	<input type="text" class="update hide  form-control" placeholder = "<?php _e('Set youtube video ID here','boxtheme'); ?>"  value="<?php echo $video_id;?>" name="video_id" class="youtube-link" >
	            </div>
	            <?php if( function_exists ('box_edit_social_link') ){ box_edit_social_link($profile);} ?>

	      	<div class="form-group">
		      	<div class="offset-sm-10 col-sm-12 align-right  no-padding-right">
		        	<button type="submit" class="btn btn-primary update hide"> &nbsp; <?php _e('Save','boxtheme');?> &nbsp;</button>
		      	</div>
		    </div>
		</form>

	</div> <!-- end left !-->
</div>