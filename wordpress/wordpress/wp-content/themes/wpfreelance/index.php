<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 *
 * @package boxtheme
 * @subpackage boxtheme
 * @since 1.0
 * @version 1.0
 */
?>
<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<div class=" site-content" id="content" >
			<div class="col-md-8 detail-project text-justify">
				<?php the_post(); ?>
				<h1><?php the_title();?></h1>
				<?php the_content(); ?>
			</div>
			<div class="col-md-4 sidebar" id="sidebar">
				<?php get_sidebar('main_sidebar');?>
			</div>
		</div>
	</div>
</div>
<?php get_footer();?>