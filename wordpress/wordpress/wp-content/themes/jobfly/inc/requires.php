<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require get_parent_theme_file_path( '/inc/define.php' );
require get_parent_theme_file_path( '/inc/class-bx-install.php' );

require get_parent_theme_file_path( '/inc/core_wp.php' );
require get_parent_theme_file_path( '/inc/core_functions.php' );
require get_parent_theme_file_path( '/inc/plugable.php' );
require get_parent_theme_file_path( '/inc/themes.php' );
require get_parent_theme_file_path( '/inc/init_hook.php' );
require get_parent_theme_file_path( '/inc/class_option.php' );

require get_parent_theme_file_path( '/inc/class_mail.php' );
require get_parent_theme_file_path( '/inc/class_user.php' );
require get_parent_theme_file_path( '/inc/class_post.php' );

require get_parent_theme_file_path( '/inc/payment/requires.php' );
require get_parent_theme_file_path( '/inc/class_job.php' );
require get_parent_theme_file_path( '/inc/class_resumes.php' );
require get_parent_theme_file_path( '/inc/class_profile.php' );
require get_parent_theme_file_path( '/inc/class_portfolio.php' );

require get_parent_theme_file_path( '/inc/class_bid.php' );
require get_parent_theme_file_path( '/inc/class-wp-ajax.php' );

require get_parent_theme_file_path( '/inc/social/requires.php' );

require get_parent_theme_file_path( '/inc/class_customizer.php' );
/**
 * Implement the Custom Header feature.
 */


/**
 * Customizer additions.
 */
///require get_parent_theme_file_path( '/inc/customizer.php' );
add_action('init','box_init_class', 19 );
function box_init_class() {
global $fb_activate, $is_social; // init is_social
global $gg_activate;
	$fb = new BX_Facebook();
	$fb_activate = $fb->is_active;
	$gg = new Box_Google();
	$gg_activate = $gg->is_active;
	//var_dump($gg_activate);
}
?>