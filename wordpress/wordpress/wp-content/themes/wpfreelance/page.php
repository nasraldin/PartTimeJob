<?php
/**
 *Template Name: Default template
 */
?>
<?php get_header(); ?>

<div class="container site-container">
	<div class="row site-content" id="content" >
		<div class="col-md-12 detail-project text-justify">
			<?php the_post(); ?>
			<h1><?php the_title();?></h1>
			<?php the_content(); ?>
		</div>
	</div>
</div>

<?php get_footer();?>