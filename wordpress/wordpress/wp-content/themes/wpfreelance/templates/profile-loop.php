<?php
global $post;
global $user_ID;
$profile 	= BX_Profile::get_instance()->convert($post);
//$profile_id = get_user_meta($post->post_author,'profile_id', true);
$skills = get_the_terms( $profile->ID, 'skill' );
$skill_val = '';
if ( $skills && ! is_wp_error( $skills ) ){

  	$draught_links = array();

  	foreach ( $skills as $term ) {
    	//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
    	$draught_links[] = '<span >'.$term->name.'</span>';
     	$list_ids[] = $term->term_id;
  }
  $skill_val = join( " ", $draught_links );
}
$start_class = 'score-'.$profile->{RATING_SCORE};
if ((int) $profile->{RATING_SCORE} != $profile->{RATING_SCORE}){
	$start_class = 'score-'.(int)$profile->{RATING_SCORE}.'-half';
}

?>
<div class="archive-profile-item profile-<?php echo $profile->ID;?> templates\profile-loop.php">
	<div class="full archive-full">
		<div class="col-md-2 no-padding col-xs-3 col-avatar">
		<?php echo '<a class="avatar" href = "'.get_author_posts_url($profile->post_author).'">'.get_avatar($profile->post_author, 150).'</a>';
		$userdata = get_userdata($post->post_author); ?>
		</div>
		<div class="col-md-10 align-left  col-xs-9 res-content res-second-line no-padding-right">

			<h3 class="profile-title no-margin col-xs-12">
				<?php echo '<a class="" href = " '.get_author_posts_url($profile->post_author).'">'.$profile->profile_name.'</a>';?>
			</h3>
			<span class="inline second-line col-md-12 col-xs-12">
				<span class="item professional-title primary-color">
					<?php if( !empty($profile->professional_title) ){?>
						<?php echo $profile->professional_title;?>
					<?php } else { echo '&nbsp;'; } ?>
				</span>
			</span>

			<span class="inline list-info col-md-12 no-padding-right no-padding-left">
				<span class=" item hour-rate col-md-3  no-padding-left"><i class="fa fa-clock-o " aria-hidden="true"></i><span class="txt-rate"><?php echo $profile->{HOUR_RATE_TEXT};?> </span></span>
				<span class=" item eared-txt col-md-3 col-xs-4 text-center"><?php the_box_icon('earning');?><?php echo $profile->earned_txt;?> </span>
				<span class=" item country-profile col-md-3 col-xs-4 text-center"> <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $profile->country;?> </span>
				<span class="item profile-rating col-md-3 col-xs-4 no-padding-right text-right hidden-xs">
					<start class="rating-score <?php echo $start_class;?> ">
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
					</start>
				</span>
			</span>
		</div>
		<div class="col-md-10 align-left  col-xs-12 res-content no-padding-right">
			<span class="overview-profile clear col-xs-12">
			<?php echo wp_trim_words($post->short_des, 62);	?>

			</span>
			<small class="clear skills"><?php echo $skill_val;?></small>
		</div>
	</div>
</div>