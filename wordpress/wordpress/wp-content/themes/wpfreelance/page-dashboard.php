<?php
/**
 *	Template Name: Dashboard
 */
?>
<?php get_header(); ?>

<div class="full-width  dashboard-area">
	<?php box_header_link_in_dashboard();?>
	<?php the_post();?>
	<div class="container site-container ">
		<div class="row site-content" id="content" >
			<div class="col-md-8 left-dashboard">
				<?php
				global $user_ID;
				$role = bx_get_user_role();
				if($role == FREELANCER){
						$args = array(
						'post_type' => 'bid',
						'author'=> $user_ID,
						'posts_per_page' => 10,
					);

					$query = new WP_Query($args);

					echo '<h2>';_e('Top Latest Bidding','boxtheme');echo '</h2>';
					if($query->have_posts() ){
						echo '<ul class="list-project">';
						while($query->have_posts() ){
							$query->the_post();
							global $post;
							$project = get_post($post->post_parent);

							?>
							<li class= "project-row">
								<?php printf(__('Bid on project %s','boxtheme'), '<a href="<?php echo get_permalink($project->ID);?>" class="" target="_blank">'. $project->post_title.'</a>'); ?><span class="js-posted"> - <time><?php the_date();?></time></span>
								<p class="p-des"><?php echo wp_trim_words( get_the_content(), 45); ?></p>
							</li>
							<?php
						}
						echo '</ul>';
				 	}else { ?>
						<p style="padding: 20px 0;">
							<?php _e('List your bidding is empty.','boxtheme'); ?>
						</p>
						<?php
					}


				} else {
					$args = array(
						'post_type' => 'project',
						'author' => $user_ID,
						'posts_per_page' => 10,
						'post_status' => array( 'publish','pending','complete','dispute', AWARDED, ARCHIVED, 'disputing','resolved'),
					);
					$query = new WP_Query($args);
					echo '<h2>';_e('Top Latest Projects','boxtheme');echo '</h2>';
					if($query->have_posts() ){
						echo '<ul class="list-project">';
						while($query->have_posts() ){
							$query->the_post();?>
							<li class= "project-row">
								<a href="<?php the_permalink();?>" class="" target="_blank"><?php the_title(); ?></a> <span class="js-posted"> - <time><?php the_date();?></time></span>
								<p class="p-des"><?php echo wp_trim_words( get_the_content(), 39); ?></p>
							</li>
							<?php
						}
						echo '</ul>';
					} else {?>
						<p style="padding: 20px 0;">
							<?php _e('List your project is empty.','boxtheme'); ?>
						</p>
						<?php
					}
				}

				?>
			</div>
			<div class="col-md-4 right-dashboard">
				<?php
				if( $role == FREELANCER ){
					$plan_id = is_box_check_plan_available();?>
					<div class="box-block">
						<div class="h3 heading">
							<h3><?php _e('Subscription','boxtheme');?></h3>
						</div>
						<?php
						if($plan_id){
							$package = get_post($plan_id);
							printf(__('You are using <i>%s</i> plan','boxtheme'),$package->post_title);
						} else {?>
							<?php _e('You are using "free" plan.','boxtheme');?>
							<p><a href="<?php echo box_get_static_link('membership-plans');?>"><?php _e('Upgrade','boxtheme');?></a></p>
					<?php } ?>
					</div>
				<?php } ?>

				<div class="box-block">
					<?php
					$bid_info = get_bid_info();
					printf( __('BIDS LEFT(%s/%s)','boxtheme'), $bid_info->remain, $bid_info->total );?>.

				</div>

				<div class="box-block">
					<div class="h3 heading">
						<h3><?php _e('Overview Credit','boxtheme');?></h3>
					</div>
					<?php
					$ins_credit = BX_Credit::get_instance();
					$credit = $ins_credit->get_ballance($user_ID);

					printf(__('You have remain %s credit.','boxtheme'), max(0, $credit->available) );?>
					<p><a href="<?php echo box_get_static_link('deposit');?>"><?php _e('Deposit Credit','boxtheme');?></a></p>
				</div>

			</div>

		</div>
	</div>
</div>
<?php get_footer();?>

