<?php


global $user_ID, $project, $is_owner, $bidding, $is_logged, $current_user_can_bid, $role, $symbol, $box_currency;
$user = get_userdata($project->post_author );

$country_id  = get_user_meta( $project->post_author, 'location', true );
$txt_country = 'Unset';
$ucountry = get_term( $country_id, 'country' );

$score = (int) get_user_meta($project->post_author,RATING_SCORE, true);
if(empty($score) || !$score){
	$score = 0;
}
if( ! is_wp_error( $ucountry ) && $ucountry ){
	$txt_country = $ucountry->name;
}
$project_posted = (int) get_user_meta( $project->post_author, 'project_posted', true);
$fre_hired = (int) get_user_meta( $project->post_author, 'fre_hired', true);
$total_spent = (float) get_user_meta( $project->post_author, 'total_spent', true);
$employer = get_userdata($project->post_author);
?>
	<div class="main-btn-react  hide">
		<button class="contact-me primary-bg"> <?php _e('WorkSpace','boxtheme');?></button><button class="contact-me primary-bg"><?php _e('Dispute','boxtheme');?></button>
	</div>

<div class="block-employer-info">
	<h3> <?php _e('Employer Information','boxtheme');?></h3>
	<ul class="list-employer-info">
		<li class="item-avatar">
			<div class="left-emp-avatar">
				<?php echo get_avatar( $user->ID, 39);?> &nbsp;
			</div>
			<div class="right-emp-avatar">
				<label class="emp-name"><a class="author-url" href="#<?php //echo esc_url(get_author_posts_url( $project->post_author )); ?>"><?php echo $user->display_name ;?>  </a> </label>
				<span class="member-since"><?php printf( __("Member Since %s",'boxtheme'), date( "M d, Y", strtotime($employer->user_registered) ) );?></span>
				<span class="rating rating-score score-<?php echo $score;?>"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i></span>
			</div>
		</li>
		<li><i class="fa fa-map-marker bcon" aria-hidden="true"></i><?php printf(__('From <span class="pull-right"> %s</span>','boxtheme'),$txt_country);?></li>
		<li><i class="fa fa-flag bcon" aria-hidden="true"></i><?php printf(__("Project posted <span class='text-right pull-right'>%d</span>",'boxtheme'), $project_posted);?></li>
		<li><i class="fa fa-address-book-o bcon" aria-hidden="true"></i><?php printf(__("Freelancers hired <span class='text-right pull-right'>%d</span>",'boxtheme'), $fre_hired);?></li>
		<li><i class="fa fa-money bcon" aria-hidden="true"></i><?php printf( __( "Total spent (<small>%s/</small>) <span class='text-right pull-right'>%s</span>",'boxtheme'), $box_currency->code, floatval( $total_spent )  );?></li>

		<?php show_sidebar_project_buttons($project, $bidding);?>

	</ul>
</div>

<div class="company-pictures hide">
	<img src="<?php echo  get_template_directory_uri().'/img/custom-img.png';?>" title = "Your custom image" alt ="Your custom image" />
</div>