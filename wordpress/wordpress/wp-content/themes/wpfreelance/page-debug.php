<?php
/**
 *Template Name: Debug
 */
?>
<?php get_header(); ?>
<div class="full-width">
	<div class="container site-content-contain">
		<div class="site-content" id="content" >
			<div class="col-md-12 detail-project text-justify">
				<?php

				echo '<pre>';
				$blog_id = get_current_blog_id();
				var_dump($blog_id);
				echo '</pre>';
				?>
			</div>

		</div>
	</div>
</div>

<?php get_footer();?>

