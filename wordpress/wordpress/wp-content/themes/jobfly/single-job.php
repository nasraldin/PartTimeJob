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
global $post;


$cviews = (int) get_post_meta( $post->ID, BOX_VIEWS, true );

if ( $post->post_status == 'publish' ) {
	$cookie = 'cookie_' . $post->ID . '_visited';
	if ( ! isset( $_COOKIE[$cookie] ) ) {
		$cviews = $cviews + 1;
		update_post_meta( $post->ID, BOX_VIEWS , $cviews );
		setcookie( $cookie, 'is_visited', time() + 5 *60 * 60 );
	}
}
$job = BX_Project::get_instance()->convert($post);
?>
<?php get_header(); ?>

<div class="full-width page-job-detail has-background fw-min-height">
	<div class="page-background">
        <div class="background-overlay"></div>
    </div>
	<div class="container site-container">
		<div class=" site-content page-job-detail__detail" id="content" >
			<?php get_template_part( 'template/single-job', 'header' ); ?>
			<?php get_template_part( 'template/single-job', 'middle' ); ?>
			<script type="text/template" id="json_job"><?php  echo json_encode($job); ?></script>
		</div>
	</div>
</div>
<?php get_footer();?>