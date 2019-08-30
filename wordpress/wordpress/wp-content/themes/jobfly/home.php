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
	<?php get_search_form();?>
	<div class="container site-container">
		<div class=" site-content" id="content" >
			<div class="col-md-12">
				<?php if( wp_count_posts('job')->publish >0 ){ ?><h2 class="lable-count-job">  Has <?php echo wp_count_posts('job')->publish; ?> jobs  for you</h2> <?php }?>
			</div>
			<div class="col-md-12 detail-project">
				<?php
				$args = array(
					'post_type' => 'job',
				);
				$query = new WP_Query($args);

				while($query->have_posts()){
					$query->the_post();
					get_template_part( 'template/job','item');
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php get_footer();?>