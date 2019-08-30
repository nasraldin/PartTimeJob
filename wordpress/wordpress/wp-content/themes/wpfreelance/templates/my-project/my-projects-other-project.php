<?php


	global $user_ID;

	global $in_other;
	$status = isset( $_GET['status'] ) ? $_GET['status'] : array('pending','disputing','resolved','complete','archived');
	$loadmore = false;
	$link =  box_get_static_link('my-project');
	$check = $status;
	$css = '';
	if( is_array($status) ){
		$check = 'any';
	}
	if( isset( $_GET['status'] ) )
		$css = 'active';

	?>
	<div class="dashboard-tab <?php echo $css;?>" id="dashboard-other">
		<div class="full fillter-project-status">
			<div class="col-md-4 pull-right">
				<form class="pull-right full dashboard-filter">
					<select class="form-control">
						<option <?php selected( $check, 'any' ); ?>  value="<?php echo  add_query_arg('status','any', $link);?>"> <?php _e('All Status','boxtheme');?></option>
						<option <?php selected( $check, 'pending' ); ?>  value="<?php echo add_query_arg('status','pending', $link);?>"> Pending</option>
						<option <?php selected( $check, 'complete' ); ?>  value="<?php echo add_query_arg('status','complete', $link);?>"> Complete</option>
						<option <?php selected( $check, 'disputing' ); ?>  value="<?php echo add_query_arg('status','disputing', $link);?>"> Disputing/Resolved</option>
						<option <?php selected( $check, 'archived' ); ?>  value="<?php echo add_query_arg('status','archived', $link);?>"> Archived</option>
					</select>
				</form>
			</div>
		</div>

		<ul class="ul-list-project <?php echo $in_other;?>" id="ul-other">

			<?php
				$args = array(
				'post_type' => 'project',
				'author'=> $user_ID,
				'posts_per_page' => -1,
			);
			if( $status == 'disputing' )
				$status = array('disputing','resolved');

			$args['post_status'] = $status;
			$query = new WP_Query($args);
			?>
			<li class="heading heading-table list-style-none padding-bottom-10 list-projects-past-project.php">
					<div class ="col-md-5 col-xs-5 "><?php _e('Project Name','boxtheme');?></div>
					<div class ="col-md-2 col-xs-2"> <?php _e('Bids','boxtheme'); ?></div>

					<div class ="col-md-2 col-xs-2"><?php _e('Time','boxtheme'); ?> </div>
					<div class ="col-md-2 col-xs-2 text-center"><?php _e('Status','boxtheme');?></div>
					<div class ="col-md-1 col-xs-1 text-center pull-right">Action</div>
			</li>
			<?php
			if( $query-> have_posts() ){
				while ($query->have_posts()) {
					global $post;
					$query->the_post();
					$project = BX_Project::get_instance()->convert($post);
					$status = $project->post_status;
					$renew_link = add_query_arg('p_id',$project->ID, box_get_static_link('post-project') );
					?>
					<li class="list-style-none padding-bottom-10">
						<div class ="col-md-5 col-xs-5"><a class="primary-color" href="<?php echo get_permalink();?>"><?php echo get_the_title()?></a></div>
						<div class ="col-md-2 col-xs-2"><?php echo count_bids($post->ID); ?></div>
						<div class ="col-md-2 col-xs-2"><?php echo get_the_date(); ?></div>
						<div class ="col-md-2 col-xs-2 text-center text-capitalize">
							<?php echo $project->post_status;?>
						</div>

						<div class ="col-md-1 col-xs-1 pull-right text-center ">

							<?php if( $status == 'archived' ){ ?>
								<a href="<?php echo $renew_link;?>" class="btn-board " id="<?php echo $project->ID;?>"  data-toggle="tooltip" title="<?php printf(__('Renew %s','boxtheme'), $project->post_titile);?>">
									<i class="fa fa-refresh" aria-hidden="true"></i>
								</a>
							<?php }  ?>

							<?php if( $status == 'pending' ){ ?>
								<a href="#" class="btn-board btn-archived-job" id="<?php echo $project->ID;?>"  data-toggle="tooltip" title="<?php printf(__('Archived %s','boxtheme'), $project->post_titile);?>">
									<i class="fa fa-archive" aria-hidden="true"></i>
								</a>
							<?php } ?>

							<?php if( in_array( $status, array('archived','pending') ) ) { ?>
								<a href="#" class="btn-board btn-delete-job" id="<?php echo $project->ID;?>"  data-toggle="tooltip" title="<?php printf(__('Delete %s','boxtheme'), $project->post_titile);?>">
									<i class="fa fa-trash-o" aria-hidden="true"></i>
								</a>

							<?php } ?>

						</div>
					</li> <?php
				}
			} else {?>
				<li class="col-md-12 col-xs-12" style="padding-top: 20px; list-style:none">
					<?php _e('There are no other projects.','boxtheme'); ?>
				</li> <?php
			}

		echo '</ul>';?>