<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function self_customizer_section($wp_customize) {
    $wp_customize->add_section( 'section_name' , array(
        'title'       => __( '[box] Banner Image', 'boxtheme ' ),
        'description' => 'Select banner image',
        'priority' => 25,
    ));

    /* LOGO */
    $wp_customize->add_setting( 'main_img', array(
        'default' => get_template_directory_uri().'/img/banner.jpg'
    ));

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'main_img', array(
        'label'    => __( 'Main Image', 'boxtheme' ),
        'section'  => 'section_name',
        'settings' => 'main_img',
    )));
    /* slogan */
   	$wp_customize->add_setting( 'general[slogan]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => '#Join Our Freelance Community',
	) );
	$wp_customize->add_control( 'general[slogan]', array(
		'label' => __( 'Set slogan' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	//banner text
	$wp_customize->add_setting( 'general[banner_text]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'We know it\'s hard to find an online expert when you need one,
	            which is why we\'ve set on a mission to bring them all to one place.',
	) );
	$wp_customize->add_control( 'general[banner_text]', array(
		'label' => __( 'Banner Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	// i want to hire text
	$wp_customize->add_setting( 'general[i_wthire]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'I want to hire',
	) );
	$wp_customize->add_control( 'general[i_wthire]', array(
		'label' => __( 'I wanto to hire Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	// i want to work text
	$wp_customize->add_setting( 'general[i_wtwork]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'I want to work',
	) );
	$wp_customize->add_control( 'general[i_wtwork]', array(
		'label' => __( 'I want to work Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	// Find a freelancer
	$wp_customize->add_setting( 'general[find_fre]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'Find a Freelancer',
	) );
	$wp_customize->add_control( 'general[find_fre]', array(
		'label' => __( 'Find a freelancer Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	// Find a job
	$wp_customize->add_setting( 'general[find_ajob]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'Find a Job',
	) );
	$wp_customize->add_control( 'general[find_ajob]', array(
		'label' => __( 'Find a Job Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );
	// Post a job
	$wp_customize->add_setting( 'general[post_ajob]', array(
		'type'       => 'option',
		'capability' => 'manage_options',
		'default' => 'Post a Job',
	) );
	$wp_customize->add_control( 'general[post_ajob]', array(
		'label' => __( 'Post a Job Text' ),
		'section' => 'section_name',
		'allow_addition' => true,
	) );



}

add_action('customize_register', 'self_customizer_section',15);


function box_customizer_footer($wp_customize){
	$wp_customize->add_section( 'footer_setup', array(
			'title' => __( '[box] Footer setup','boxtheme' ),
			'priority' => 26,
			'description' => __( 'Setup for the footer section.' ),
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
		'label' => __( 'Set title Menu 1' ),
		'section' => 'footer_setup',
		//'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[first]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[first]', array(
		'label' => __( 'Select Footer Menu 1' ),
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
		'label' => __( 'Set title Menu 2' ),
		'section' => 'footer_setup',
		//'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[second]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[second]', array(
		'label' => __( 'Select Footer Menu 2' ),
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
		'label' => __( 'Set title Menu 3' ),
		'section' => 'footer_setup',
		///'type' => 'textarea',
		'allow_addition' => true,
	) );

	$wp_customize->add_setting( 'general[third]', array(
			'type'       => 'option',
			'capability' => 'manage_options',
		) );
	$wp_customize->add_control( 'general[third]', array(
		'label' => __( 'Select Footer Menu 3' ),
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
		'label' => __( 'Set contact information' ),
		'section' => 'footer_setup',
		'type' => 'textarea',
		'allow_addition' => true,
		)
	);

}
add_action('customize_register', 'box_customizer_footer', 11);