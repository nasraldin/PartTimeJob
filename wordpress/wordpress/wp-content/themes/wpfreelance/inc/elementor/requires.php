<?php
define( 'BOX_ELEMENTOR_PATH', get_parent_theme_file_path().'/inc/elementor' );
define( 'ELEMENTOR_HELLO_WORLD__FILE__', __FILE__ );
require get_parent_theme_file_path( '/inc/elementor/wg_pack_plans.php');
require get_parent_theme_file_path( '/inc/elementor/wg_list_profiles.php');
require get_parent_theme_file_path( '/inc/elementor/wg_list_categories.php');
require get_parent_theme_file_path( '/inc/elementor/wg_membership.php');


require BOX_ELEMENTOR_PATH .'/pricing/box_pricing_init.php';

