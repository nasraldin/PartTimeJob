<?php
global $post;
$premium_type = box_get_premium_types();
$project = BX_Project::get_instance()->convert($post);
$priority = get_post_meta( $project->ID , 'priority', true);
?>
<div class="project-loop-item priority-<?php echo $priority;?>-type project-item-<?php the_ID();?>">
	<div class="col-md-12 job-heading-line">
	<?php echo '<h3 class="project-title"><a class="primary-color second-font" href="'.get_permalink().'">'.get_the_title().'</a></h3>';?>
		<?php if( $priority > 0 ){ ?>
			<span id="premium_type"><?php echo isset( $premium_type[$priority] )? $premium_type[$priority] :'';?></span>
		<?php } ?>
	</div>
	<div class="col-md-12 project-second-line">
		<span class="text-muted display-inline-block m-sm-bottom m-sm-top">
		    <span><?php _e('Fixed Price','boxtheme');?></span>
            <span >
	            <span class="js-budget"> - <span  data-itemprop="baseSalary"><?php echo $project->budget_txt; ?> </span></span>
			</span>
			<span class="js-posted"> - <time><?php echo bx_show_time($post);?></time></span>

		</span>
	</div>
	<div class="col-md-12 project-third-line">
			<?php echo wp_trim_words( get_the_content(), 62); ?>
	</div>
	<div class="col-md-12 employer-info">
		<span class="text-muted display-inline-block m-sm-bottom m-sm-top">
            <strong class="text-muted display-inline-block m-sm-top"><?php _e('Client:','boxtheme');?></strong>
			<span class="inline">
				<span><?php echo $project->total_spent_txt;?></span>
			</span>
			<span  class="nowrap">
			    <span> <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $project->location_txt;?></span>
			</span><!---->
        </span>
	</div> <!-- . employer-info !-->
</div>