<?php
/**
 * be included in page-dashboard and list all bidded of current freelancer
 * Only available for freelancer account.
**/

	global $user_ID;
	wp_reset_query();
	$status = isset( $_GET['status'] ) ? $_GET['status'] : array('publish','pending','disputing','resolved','complete','awarded');
	$check = $status;
	if( is_array($status) )
		$check = 'any';
	$loadmore = false;
	$link =  box_get_static_link('my-bid');
	$link = add_query_arg('section','my-bidding',$link);
	?>
	<div class=" active">
		<div class="full bid-heading">
			<div class="col-md-8 pull-left np-left">
				<h1 class="page-title"><?php _e('My Bidding','boxtheme');?></h1>
			</div>
			<div class="col-md-4 pull-right no-padding-right">
				<form class="pull-right full dashboard-filter">
						<select class="form-control">
							<option <?php selected( $check, 'publish' ); ?>  value="<?php echo $link;?>"> <?php _e('All Status','boxtheme');?></option>
							<option <?php selected( $check, 'publish' ); ?> value="<?php echo add_query_arg('status','publish', $link);?>"><?php _e('Activity','boxtheme');?></option>
							<option <?php selected( $check, 'awarded' ); ?>  value="<?php echo add_query_arg('status','awarded', $link);?>"><?php _e('Working','boxtheme');?></option>
							<option <?php selected( $check, 'complete' ); ?>  value="<?php echo add_query_arg('status','done', $link);?>"> <?php _e('Complete','boxtheme');?></option>
							<option <?php selected( $check, 'disputing' ); ?>  value="<?php echo add_query_arg('status','disputing', $link);?>"><?php _e('Disputing/Resolved','boxtheme');?>

							</option>
						</select>
				</form>
			</div>
			</div>
		<ul class="ul-list-project template-parts\dashboard\list-bids.php">

			<?php
				$args = array(
				'post_type' => 'bid',
				'author'=> $user_ID,
				'posts_per_page' => -1,
			);
			if( $status == 'disputing' )
				$status = array('disputing','resolved');

			$args['post_status'] = $status;

			$query = new WP_Query($args);

			echo '<li class="heading heading-table list-style-none padding-bottom-10  dashboard\list-bids.php">';
					echo '<div class ="col-md-2 col-xs-5">'; _e('Project Title','boxtheme');				echo '</div>';
					echo '<div class ="col-md-5 col-xs-5 hidden-xs">';_e('Cover letter','boxtheme');				echo '</div>';
					echo '<div class ="col-md-1 col-xs-2">';_e('Price','boxtheme');				echo '</div>';
					echo '<div class ="col-md-2 col-xs-2">'; _e('Date','boxtheme');echo '</div>'; ?>
					<div class ="col-md-2 col-xs-3 text-center"><?php _e('Status','boxtheme');?></div><?php

				echo '</li>';
			if( $query-> have_posts() ){
				$count = 0;
				while ($query->have_posts()) {
					global $post;
					$query->the_post();
					$bid = BX_Bid::get_instance()->convert($post);
					$project = get_post($bid->post_parent);

					if( $project && ! is_wp_error($project) ){ ?>
						<li class="list-style-none padding-bottom-10 full dashboard\list-bids.php">
							<div class ="col-md-2 col-xs-5"> <a class="primary-color project-title" href="<?php echo get_permalink($project->ID);?>"> <?php echo $project->post_title;?></a></div>
							<div class ="col-md-5 col-xs-5 hidden-xs"> <?php the_content();?></div>
							<div class ="col-md-1 col-xs-2"> <?php	box_price($bid->{BID_PRICE}); ?></div>
							<div class ="col-md-2 col-xs-2"> <?php	echo get_the_date(); ?></div>
							<div class ="col-md-2 col-xs-3 text-center">
								<?php echo box_project_status($bid->post_status);?>
							</div>
						</li><?php
						$count ++;
					}


				}
				if($count == 0){ ?>
					<li class="no-result" style="padding-top: 20px; list-style:none">
						<p class="col-md-12 col-xs-12"> <?php _e('There are no bid.','boxtheme');?></p>
					</li>
					<?php
				}
				wp_reset_postdata();
			} else {?>
				<li class="no-result" style="padding-top: 20px; list-style:none">
					<p class="col-md-12 col-xs-12"> <?php _e('There are no bid.','boxtheme');?></p>
				</li>
				<?php
			}

			echo '</ul>';
			wp_reset_query();?>
</ul>