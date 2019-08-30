<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo home_url();?>">Home</a></li>
		  	<li class="breadcrumb-item"><span><?php echo single_tag_title();?></span></li>
		</ol>
		<div class="site-content" id="content" >

			<div class="col-md-12">

				<?php if( have_posts() ):
				while( have_posts() ): the_post();
					get_template_part( 'templates/project/project', 'loop' );
				endwhile;
				bx_pagenate();
			endif;
			wp_reset_query(); ?>
			</div>
		</div>
	</div>
</div>
<?php get_footer();?>