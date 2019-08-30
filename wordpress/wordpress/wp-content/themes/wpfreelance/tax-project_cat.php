<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<div class="site-content" id="content" >
			<div class="col-md-12">

				<?php if( have_posts() ):
				while( have_posts() ): the_post();
					get_template_part( 'templates/profile/profile', 'loop' );
				endwhile;
				bx_pagenate();
			endif;
			wp_reset_query(); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer();?>