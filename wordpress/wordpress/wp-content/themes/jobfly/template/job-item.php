<?php
global $post;
$job = BX_Project::get_instance()->convert($post);
// echo '<pre>';
// var_dump($job);
// echo '</pre>';
?>
<div class="job">
	<div class="job_content" itemscope="" itemtype="http://schema.org/JobPosting">
		<div class="logo">
			<!-- Last updated: "2017-12-18 00:43:34 +0700"-->
			<div class="logo-wrapper" title="" data-original-title="GREVO">
			<meta content="GREVO" itemprop="hiringOrganization">
			<meta content="Information Technology" itemprop="industry">
			<a target="_blank" href="#"><?php echo box_get_avatar( $job->post_author, 1);?>
			</a></div>
			<div class="clearfix"></div>
		</div>
		<div class="job__description">
			<div class="job__body">
				<div class="details">
					<h2 class="title" itemprop="title"><a  itemprop="mainEntityOfPage" href="<?php the_permalink( );?>"><?php the_title();?></a>
					</h2>
					<div class="salary not-signed-in">
						<span class="salary-icon-stack">
							<i class="ion-ios-circle-outline"></i>
							<i class="ion-social-usd"></i>
						</span>
						<?php if( ! is_user_logged_in() ){ ?>
							<a class="popup-link view-salary show-modal-login" data-popup-id="sign_in" data-link-status="sign_in_tab" href="javascript:void(0)">Sign in to view</a>
							<div class="address__arrow"></div> <?php

						} else { ?>
							<span class="salary-text"><?php if( ! empty( $job->min_salary) ) echo $job->min_salary .' - '; echo $job->max_salary;
							if( ! empty($job->min_salary) || !empty($job->max_salary)) echo ' USD'; else _e('You will love it','boxtheme'); ?> </span>
						<?php  }?>

					</div>


					<div class="hidden-xs">

					</div>
					<div class="description hidden-xs" itemprop="description"><?php echo box_excerpt(  get_the_content(), 259 ); ?></div>
					<div class="visible-xs">
						<div class="city"><div class="text"><?php echo get_job_location($job->ID)?></div></div>

					</div>
				</div>
				<div class="city_and_posted_date hidden-xs">
					<div class="feature new text" title="" data-original-title="This job is urgent. Apply today!">Hot Job</div>
					<div class="city" itemprop="jobLocation" itemscope="" itemtype="http://schema.org/Place">
						<?php show_job_location($job->ID);?>
					</div>
					<div class="distance-time-job-posted">
						<span class="distance-time">
							<?php
							$_expired_date = get_post_meta($job->ID,'_expired_date', true);
                            if( !empty( $_expired_date)){?>
                                 <?php printf(__('Expires %s.',ET_DOMAIN), human_time_diff( time(), strtotime($_expired_date)) ); ?>
                            <?php } else {
                                printf(__('%s ago','boxtheme'), human_time_diff( get_the_time('U'), time() ) );
                            } ?>

						</span>
					</div>
				</div>
			</div>
			<div class="job-bottom">
				<?php

				$terms = get_the_terms( get_the_ID(), 'skill' );

				if ( $terms && ! is_wp_error( $terms ) ) :

				    $draught_links = array();

				    foreach ( $terms as $term ) {
				        $draught_links[] = '<a class="job__skill ilabel mkt-track" href="'.get_term_link($term).'">'.$term->name.'</a>';
				    }

				    $on_draught = join( " ", $draught_links );
				    ?>

				    <div class="tag-list">
				        <?php echo  $on_draught  ; ?>
				    </div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>