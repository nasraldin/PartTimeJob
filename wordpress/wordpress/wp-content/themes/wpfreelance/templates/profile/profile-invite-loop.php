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
<div class="profile-invite-loop profile-<?php echo $profile->ID;?> ">
	<div class="full">
		<div class="col-md-2 no-padding col-xs-3 col-avatar">
		<?php echo '<a class="avatar" href = "'.get_author_posts_url($profile->post_author).'">'.get_avatar($profile->post_author, 150).'</a>';
		$userdata = get_userdata($post->post_author); ?>
		</div>
		<div class="col-md-10 align-left  col-xs-9 res-content res-second-line no-padding-right">

			<h3 class="profile-title no-margin col-xs-12">
				<?php echo '<a class="" href = " '.get_author_posts_url($profile->post_author).'">'.$profile->post_title.'</a>';?>
			</h3>

			<span class="item professional-title primary-color">
					<?php if( !empty($profile->professional_title) ){?>
						<?php echo $profile->professional_title;?>
					<?php } else { echo '&nbsp;'; } ?>
			</span>


			<span class="full">
				($)30/h - 6 reviews.
			</span>

			<span class="item profile-rating full">
					<start class="rating-score <?php echo $start_class;?> ">
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
						<i class="fa fa-star" aria-hidden="true"></i>
					</start>
			</span>
			<span class="full">
				<button class="btn f-right">Invite</button>
			</span>

		</div>

	</div>
</div>