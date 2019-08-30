<?php
/**
 *	Template Name: No sidebar
 */
?>
<?php get_header(); ?>
<div class="container page-nosidebar  site-container">
	<div class="row">
		<div class="col-md-12 detail-project text-justify">
			<?php the_post(); ?>
			<h1><?php the_title();?></h1>
			<?php the_content(); ?>
		</div>

	</div>
</div>

<?php get_footer();?>