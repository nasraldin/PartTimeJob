<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Display custom color CSS.
 */
function boxtheme_colors_css_wrap() {
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) {
		return;
	}

	require_once( get_parent_theme_file_path( '/inc/color-patterns.php' ) );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );
?>
	<style type="text/css" id="custom-theme-colors" <?php if ( is_customize_preview() ) { echo 'data-hue="' . $hue . '"'; } ?>>
		<?php echo boxtheme_custom_colors_css(); ?>
	</style>
<?php }
add_action( 'wp_head', 'boxtheme_colors_css_wrap' );