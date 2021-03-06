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
<div class="full-width has-search fw-min-height">
	<?php //get_search_form();?>
	<div class="container site-container">
		<div class=" site-content" id="content" >

			<div class="col-md-12">
				<?php global $wp_query; if( $wp_query->found_posts > 0 ){ ?><h2 class="lable-count-job"> Has <?php echo $wp_query->found_posts; ?> jobs  for you </h2><?php }?>
			</div>

			<?php get_sidebar('resumes');?>

			<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
			<?php

			if( have_posts() ) :
				echo ' <ul class="cs-candidate-list">';
				while(have_posts()){
					the_post();
					get_template_part( 'template/resume','item');
				}
				echo '</ul>';
				bx_pagenate();
			else :
				_e('The query is empty','boxtheme');
			endif;
			?>
			</div>
		</div>
	</div>
</div>
<?php get_footer();?>