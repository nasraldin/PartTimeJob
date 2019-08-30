<?php
/**
 * single-project-detail.php
*/
	global $project;
	?>
	<div class="full job-content second-font">
		<h3 class="default-label"> <?php _e('Job Details','boxtheme');?> </h3>
		<?php
			global $can_access_workspacem, $is_workspace;
			box_social_share();
		?>
		<div class="job-detail-content">
			<?php the_content(); ?>

		</div>
		<?php do_action( 'box_meta_fields', $project);?>
	</div>
	<?php

	$args = array(
	    'post_status' => 'inherit',
	    'post_type'   => 'attachment',
	    'post_parent' => $project->ID,
	);
	$att_query = new WP_Query( $args );
	if( $att_query-> have_posts() ){
	    echo '<p>';
	    echo '<h3>'.__('Files attach: ','boxtheme').'</h3>';
	    $files = array();
	    while ( $att_query-> have_posts()  ) {
	        global $post;
	        $att_query->the_post();
	        $feat_image_url = wp_get_attachment_url( $post->ID );
	        $files[] = '<span><i class="fa fa-paperclip primary-color" aria-hidden="true"></i>&nbsp;<a class="text-color " href="'.$feat_image_url.'" download>'.get_the_title().'</a></span> ';
	    }
	    echo join(",",$files);
	    echo '</p>';
	}

	global $skill_slug;
	echo '<div class="full bottom-detail-job">';
		$skills_tax = get_the_terms( $project, 'skill' );
		if ( ! empty( $skills_tax ) && ! is_wp_error( $skills_tax ) ) {
			echo '<h3 class="default-label">'.__('Skills Required','boxtheme').'</h3>';
			echo '<ul class="list-skill">';

			foreach ( $skills_tax as $skill ) {
				$skill_slug[] = $skill->slug;
			  	echo '<li><a href="' . get_term_link($skill).'">' . $skill->name . '</a></li>';

			}
			echo '</ul>';
		}

		$cats = get_the_terms( $project, 'project_cat' );
		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ){
			echo '<h3 class="sb-heading default-label">'.__('Categories','boxtheme').'</h3>';
			echo '<ul class="list-category none-style inline">';

			foreach ( $cats as $cat ) {
			  echo '<li><a href="' . get_term_link($cat).'">' . $cat->name . '</a></li>';
			}
			echo '</ul>';
		}
		do_action('show_acf_fields', $project);
		do_action('show_milestone', $project);
	echo '</div>';
	wp_reset_query();
	?>
