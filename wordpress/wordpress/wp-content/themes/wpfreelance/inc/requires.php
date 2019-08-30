<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require get_parent_theme_file_path( '/inc/define.php' );

require get_parent_theme_file_path( '/inc/class-bx-install.php' );

require get_parent_theme_file_path( '/inc/core_wp.php' );

require get_parent_theme_file_path( '/inc/core_functions.php' );

require get_parent_theme_file_path( '/inc/plugable.php' );

require get_parent_theme_file_path( '/inc/theme.php' );
require get_parent_theme_file_path( '/inc/init_hook.php' );
require get_parent_theme_file_path( '/inc/class_option.php' );

require get_parent_theme_file_path( '/inc/class_mail.php' );
require get_parent_theme_file_path( '/inc/class_user.php' );
require get_parent_theme_file_path( '/inc/class_post.php' );

//require get_parent_theme_file_path( '/inc/payment/requires.php' );
require get_parent_theme_file_path( '/payment/requires.php' );
//require get_parent_theme_file_path( '/inc/class_credit.php' );
require get_parent_theme_file_path( '/inc/escrow/requires.php' );
require get_parent_theme_file_path( '/inc/escrow/class_workspace.php' );

require get_parent_theme_file_path( '/inc/class_custom_type.php' );
require get_parent_theme_file_path( '/inc/class_message.php' );
require get_parent_theme_file_path( '/inc/class_notify.php' );
require get_parent_theme_file_path( '/inc/class_project.php' );
require get_parent_theme_file_path( '/inc/class_profile.php' );
require get_parent_theme_file_path( '/inc/class_portfolio.php' );

require get_parent_theme_file_path( '/inc/class_bid.php' );
require get_parent_theme_file_path( '/gmap/map.php' );

require get_parent_theme_file_path( '/inc/class-wp-ajax.php' );

require get_parent_theme_file_path( '/inc/social/requires.php' );

require get_parent_theme_file_path( '/inc/class_customizer.php' );
require get_parent_theme_file_path( '/inc/shortcode/requires.php' );
require get_parent_theme_file_path( '/inc/plugins/requires.php' );
require get_parent_theme_file_path( '/inc/milestones/requires.php' );
require get_parent_theme_file_path( '/inc/elementor/requires.php' );
require get_parent_theme_file_path( '/icons/icons.php' );
require get_parent_theme_file_path( '/inc/membership/requires.php' );
require get_parent_theme_file_path( '/inc/customization/requires.php' );
require get_parent_theme_file_path( '/inc/debug.php' );
/**
 * Implement the Custom Header feature.
 */
//require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme.
 */
//require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
//require get_parent_theme_file_path( '/inc/template-functions.php' );

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
}
?>