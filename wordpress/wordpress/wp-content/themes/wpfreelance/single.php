<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<div class=" site-content" id="content" >
			<div class="col-md-8 detail-project text-justify">
				<?php the_post(); ?>
				<h1 class="h1 primary-font post-title"><?php the_title();?></h1>
				<div class="full pdate"><?php _e('Posted: ','boxtheme'); the_date(); ?></div>
				<p>Written by: <?php echo get_the_author_link(); ?></p>
				<?php the_content(); ?>
			</div>
			<div class="col-md-4 sidebar" id="sidebar">
				<?php get_sidebar('blog');?>
			</div>
		</div>
	</div>
</div>

<?php get_footer();?>