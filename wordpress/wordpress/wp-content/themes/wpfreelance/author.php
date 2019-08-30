<?php get_header(); ?>

<?php
	global $author_id, $profile_id, $profile, $author;
	$author 	= get_user_by( 'slug', get_query_var( 'author_name' ) );
	$author_id =  $author->ID;
	$profile_id = get_user_meta( $author_id, 'profile_id', true);

	if( $profile_id ){

		$profile 	= BX_Profile::get_instance()->convert( $profile_id );

		?>

		<div class="full-width">

			<div class="container site-container">
				<?php box_admin_act_buttons( $profile ); ?>
				<div class="row site-content" id="content" >
					<!-- General Information and Overview !-->
					<?php get_template_part( 'templates/author/section', 'overview' ); // section-overview  section-overview.php ?>
					<!-- End general Information & Overview!-->
					<!-- Work History !-->
					<?php get_template_part( 'templates/author/section', 'reviews' ); // section-reviews reviews.php template-parts/author/section-reviews.php ?>
					<!-- End Work history !-->
					<!-- Line portfortlio !-->
					<?php
					get_template_part( 'templates/author/section', 'portfolio' ); // section-portfolios portfolios.php template-parts/author/section-portfolios.php
					?>
				</div>
			</div><!-- container site-container !-->
		</div><!-- full-width !--> <?php
	}
?>
<?php get_template_part( 'modal/directly', 'message' ); ?>
<?php get_template_part( 'modal/invite', 'modal' ); ?>
<?php get_footer();?>
