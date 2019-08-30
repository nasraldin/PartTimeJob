<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/**
 * Load Hello World
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function ec_hello_world_fail_load(){
	_e('Please install elementor plugin to use the theme.','boxtheme');
}
function box_membership_types(){
	$pack_type = 'membership';
	$list_package = array();
	$args = array(
	        'post_type' => '_package',
	        'meta_key' => 'pack_type', // buy credit or premium_post
	        'meta_value' => $pack_type,

	    );

	$the_query = new WP_Query($args);

	// The Loop
	$options = array();
	$options[0] = 'Select Plan';
	if ( $the_query->have_posts() ) {
		while($the_query->have_posts() ){
			$the_query->the_post();
			global $post;
			$options[$post->ID] = $post->post_title;
		}
	}
	return $options;
}
function box_pricing_load() {
	// Load localization file


	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'ec_hello_world_fail_load' );

		return;
	}

	// Check required version
	$elementor_version_required = '1.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'box_pricing_fail_load_out_of_date' );
		return;
	}

	// Require the main plugin file
	require( BOX_ELEMENTOR_PATH . '/pricing/Pricing_Namespace.php' );
}
add_action( 'init', 'box_pricing_load' ,155);


function box_pricing_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Elementor Hello World is not working because you are using an old version of Elementor.', 'hello-world' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'hello-world' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}