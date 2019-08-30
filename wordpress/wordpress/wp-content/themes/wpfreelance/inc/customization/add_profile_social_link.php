<?php

function add_meta_fields_to_profiles($fields){
	array_push($fields, 'facebook_link','linkedin_link','twitter_link');
	return  $fields;
}
add_filter('box_profile_meta','add_meta_fields_to_profiles');


function box_edit_social_link($profile){


	?>
	<div class="form-group">
		<span class=" static visible-default no-padding primary-color" ><?php _e('Your facebook link: ','boxtheme');?> <span class="profile-meta-value"><?php echo esc_url($profile->facebook_link);?></span></span>
		<input type="url" class="update hide  form-control" placeholder = "<?php _e('Set facebook link here','boxtheme'); ?>"  value="<?php echo esc_url($profile->facebook_link);?>" name="facebook_link" class="youtube-link" >
	</div>
	<div class="form-group">
		<span class=" static visible-default no-padding primary-color" ><?php _e('Your twitter link: ','boxtheme');?> <span class="profile-meta-value"><?php echo esc_url($profile->twitter_link);?></span></span>
		<input type="url" class="update hide  form-control" placeholder = "<?php _e('Set twitter link here','boxtheme'); ?>"  value="<?php echo esc_url($profile->twitter_link);?>" name="twitter_link" class="youtube-link" >
	</div>
	<div class="form-group">
		<span class=" static visible-default no-padding primary-color" ><?php _e('Your Linkedin link:  ','boxtheme');?> <span class="profile-meta-value"><?php echo $profile->linkedin_link;?></span></span>
		<input type="url" class="update hide  form-control" placeholder = "<?php _e('Set Linkedin link here','boxtheme'); ?>"  value="<?php echo esc_url($profile->linkedin_link);?>" name="linkedin_link" class="youtube-link" >
	</div>

<?php }
function box_social_link_of_profile($profile){
	echo '<div class="profile-social-link">';
	if(!empty($profile->facebook_link))
		echo '<a class="social-item facebook-link" href ="'.esc_url($profile->facebook_link).'"><i class="fa fa-facebook-f"></i></a>';
	if(!empty($profile->twitter_link))
		echo '<a class="social-item twitter-link" href ="'.esc_url($profile->twitter_link).'"><i class="fa fa-twitter"></i></a>';
	if(!empty($profile->linkedin_link))
		echo '<a class="social-item linkedin-link"  href ="'.esc_url($profile->linkedin_link).'"><i class="fa fa-linkedin"></i></a>';
	?>

	<?php echo '</div>';

}