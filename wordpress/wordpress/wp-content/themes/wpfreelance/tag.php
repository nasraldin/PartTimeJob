<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<div class="row site-content" id="content" >
			<div class="col-md-8 detail-project text-justify">
				<h2>Tags: <?php echo single_tag_title("", false);?></h2>
				<?php
				// The Query
				$args = array('post_type' => 'post');
				$the_query = new WP_Query( $args );
				// The Loop
				if ( $the_query->have_posts() ) {

					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						get_template_part( 'templates/post', 'loop' );
					}
					/* Restore original Post Data */
					wp_reset_postdata();
				} else {
					// no posts found
				}
				?>
			</div>
			<div class="col-md-4 sidebar" id="sidebar">
				<?php get_sidebar('blog');?>
			</div>
		</div>
	</div>
</div>

<?php get_footer();?>