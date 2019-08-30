<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function self_customizer_section($wp_customize) {
    $wp_customize->add_section( 'section_name' , array(
        'title'       => __( 'Home page', 'my_theme' ),
        'description' => 'Select banner image',
    ));

    /* LOGO */
    $wp_customize->add_setting( 'main_img', array(
        'default' => get_template_directory_uri().'/img/banner.jpg'
    ));

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'main_img', array(
        'label'    => __( 'Main banner', 'my_theme' ),
        'section'  => 'section_name',
        'settings' => 'main_img',
    )));


}

add_action('customize_register', 'self_customizer_section');

add_action('customize_register', 'box_customizer_footer');
function box_customizer_footer($wp_customize){
	$wp_customize->add_section( 'footer_setup', array(
			'title' => __( 'Footer setup','boxtheme' ),
			'priority' => 120,
			'description' => __( 'Setup for the footer section.','boxtheme' ),
			//'active_callback' => 'box_get_menu',
		) );



	$menus = wp_get_nav_menus();
	$result = array();
	if( !is_wp_error($menus ) && !empty($menus) ){
		foreach ($menus as $key => $menu) {
			$result[$menu->slug] = $menu->name;
		}
	} else {
		$result[] = __('No menu to select','boxtheme');
	}


$label = array(
        'first_title' => __('Contact Us','boxtheme'),
        'second_title' => __('Help & Resources','boxtheme'),
        'third_title' => __('Commercial','boxtheme'),
    );

	$wp_customize->add_setting( 'general[first_title]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => $label['first_title'],
	) );
	$wp_customize->add_control( 'general[first_title]', array(
		'label' => __( 'Set title Menu 1','boxtheme' ),
		'section' => 'footer_setup',
		//'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[first]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[first]', array(
		'label' => __( 'Select Footer Menu 1','boxtheme' ),
		'section' => 'footer_setup',
		'type' => 'select',
		'choices'     => $result,
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[second_title]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
			'default' => $label['second_title'],
		) );
	$wp_customize->add_control( 'general[second_title]', array(
		'label' => __( 'Set title Menu 2','boxtheme' ),
		'section' => 'footer_setup',
		//'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[second]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[second]', array(
		'label' => __( 'Select Footer Menu 2','boxtheme' ),
		'section' => 'footer_setup',
		'type' => 'select',
		'choices'     => $result,
		'allow_addition' => true,
	) );
	$wp_customize->add_setting( 'general[third_title]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
			'default' => $label['third_title'],
		) );
	$wp_customize->add_control( 'general[third_title]', array(
		'label' => __( 'Set title Menu 3','boxtheme' ),
		'section' => 'footer_setup',
		///'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[third]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[third]', array(
		'label' => __( 'Select Footer Menu 3','boxtheme' ),
		'section' => 'footer_setup',
		'type' => 'select',
		'choices'     => $result,
		'allow_addition' => true,
	) );
	$default = '<h5 class="footer-list-header">Contact Us</h5><p>Start a 14 Day Free Trial on any of our paid plans. No credit card required.</p>
<p>Call us at <a href="tel:+1 855.780.6889">+1 179.170.6889</a></p>';
	$wp_customize->add_setting( 'general[contact]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => $default
		)
	);
	$wp_customize->add_control( 'general[contact]', array(
		'label' => __( 'Set contact information','boxtheme' ),
		'section' => 'footer_setup',
		'type' => 'textarea',
		'allow_addition' => true,
		)
	);

}
function box_get_menu(){

	//return  $result;
}
$t = box_get_menu();
