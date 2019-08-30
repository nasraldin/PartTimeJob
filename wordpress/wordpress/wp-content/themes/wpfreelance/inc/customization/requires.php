<?php
	require get_parent_theme_file_path( '/inc/customization/live_notification.php' );
	//require get_parent_theme_file_path( '/inc/customization/add_profile_social_link.php' );

function box_include_module(){

	//if( defined('ENABLED_AVATAR_FIELD') && ENABLED_AVATAR_FIELD )
	require_once get_parent_theme_file_path( '/inc/customization/signup_avatar_fields.php' );

	if( defined('ENABLED_SOCIALS_PROFILE_FIELD') && ENABLED_SOCIALS_PROFILE_FIELD )
		require_once get_parent_theme_file_path( '/inc/customization/add_profile_social_link.php' );

	if( defined('ENABLED_EQUIMENT') && ENABLED_EQUIMENT )
		require_once get_parent_theme_file_path( '/inc/customization/equiment_fields.php' );
}
add_action('init','box_include_module',12);

