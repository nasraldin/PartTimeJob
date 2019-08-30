<?php
/**
 *	Template Name: Left sidebar
 */
?>
<?php get_header(); ?>

	<div class="container site-container">
		<div class="site-content" id="content" >
			<div class="col-md-4">
				<?php get_sidebar('single');?>
			</div>
			<div class="col-md-8 detail-project text-justify">
				<?php the_post(); ?>
				<h1><?php the_title();?></h1>
				<?php the_date();?>
				<?php the_content(); ?>
			</div>
		</div>
	</div>

<?php get_footer();?>

