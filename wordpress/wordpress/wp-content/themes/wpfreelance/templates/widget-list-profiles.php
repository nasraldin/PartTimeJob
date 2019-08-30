<?php

$symbol = box_get_currency_symbol();

$profile_query = new WP_Query( array (
	'post_type' => PROFILE,
	'post_status' => 'publish',
	//'meta_key'  => RATING_SCORE,
	//'order'     => 'DESC',
	//'orderby'    => 'meta_value_num',
	'orderby'        => 'rand',
	'order'      => 'DESC',
	'showposts' => 6,
	// 'meta_key' =>'is_available',
	// 'meta_value' => 'on',
	)
);

global $widget_title;
if(empty($widget_title)){
	$widget_title = __('Looking for Professional Freelancers?','boxtheme');
}

if( $profile_query->have_posts() ){ ?>
	<section class="full-width top-profile ">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2 class="pypl-heading elementor-heading-title"> <?php echo $widget_title; //_e('Looking for Professional Freelancers?','boxtheme');?></h2>
				</div> <?php

					$i = 0;
					while( $profile_query->have_posts() ){
						global $post;
						$column_css = "left-column";
						if($i%2 == 1){
							$column_css = 'right-column';
						}
						$profile_query->the_post();
						$profile 	= BX_Profile::get_instance()->convert($post);
						//$profile_id = get_user_meta($post->post_author,'profile_id', true);
						$profile_id = $profile->ID;
						$rating = get_post_meta($post->ID,RATING_SCORE, true);
						$start_class = 'score-'.$profile->{RATING_SCORE};
						if ((int) $profile->{RATING_SCORE} != $profile->{RATING_SCORE}){
							$start_class = 'score-'.(int)$profile->{RATING_SCORE}.'-half';
						}

						$skills = get_the_terms( $profile_id, 'skill' );
						$skill_html = '';

						if ( $skills && ! is_wp_error( $skills ) ){
						  	$draught_links = array();
						  	foreach ( $skills as $term ) {
						     	//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
						     	$draught_links[] = '<span class="skill-item" >'.$term->name.'</span>';
						     	$list_ids[] = $term->term_id;
						  	}
						  	$skill_html = join( " ", $draught_links );
						}
						?>

						<div class="col-md-6 col-xs-12 profile-item <?php echo  $column_css;?>" >
							<div class="full box-bg">
								<div class="left avatar col-md-4 col-xs-4 no-padding-right">
									<?php echo '<a class="primary-color" href = "'.get_author_posts_url($profile->post_author).'">'.get_avatar($profile->post_author).'</a>';
									$userdata = get_userdata($post->post_author); ?>
								</div>
								<div class="right col-md-8 col-xs-8">
									<h3 class="profile-title no-margin">
										<?php echo '<a class="profile-link" href = " '.get_author_posts_url($profile->post_author).'">'.$profile->profile_name.'</a>';?>
									</h3>
									<h5 class="professional-title primary-color">
										<?php if( !empty($profile->professional_title) ){ ?>
											<?php echo $profile->professional_title;?>
										<?php } ?>
									</h5>
									<span class="absolute abs-top abs-right-15 hour-rate "><?php echo $symbol.'<span>'.$profile->hour_rate;?></span>/hr</span>
									<span class="padding-top-15"><span><?php printf( __("Join since %s",'boxtheme'), date( "M Y", strtotime($profile->post_date) ) );?> </span></span>

									<span class="full">
										<start class="rating-score <?php echo $start_class;?> ">
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
										</start>
										<span></span>
										<!--<span class="absolute  abs-right-15"><img src="<?php // echo get_stylesheet_directory_uri();?>/img/flag.png"></span> !-->
									</span>
								</div>
								<div class="right col-md-12 list-skill padding-top-10 ">
										<?php echo $skill_html;?>
									</div>
							</div>
						</div> <?php

					}?>
					<div class="col-md-12 f-right align-right" ><a href="<?php echo get_post_type_archive_link('profile');?>" class="primary-color view-all"> <?php _e('View All Profiles','boxtheme');?> &nbsp; <span class=" glyphicon glyphicon-menu-right"></span></a></div>

			</div>
		</div> <!-- .row !-->
	</section>
<?php } ?>