<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function setup_enroviment() {

	BX_User::add_role();
	global $box_general, $box_currency, $app_api, $checkout_mode, $escrow;


	$box_general = BX_Option::get_instance()->get_general_option(); // return not an object - arrray.
	$box_currency = (OBJECT) BX_Option::get_instance()->get_currency_option($box_general);

	$app_api = (OBJECT) BX_Option::get_instance()->get_app_api_option($box_general);

	$checkout_mode = (int) $box_general->checkout_mode; // 0 - sandbox. 1- real

	$escrow = (object) BX_Option::get_instance()->get_escrow_setting();

}
add_action( 'after_setup_theme','setup_enroviment');
function bx_pre_get_filter( $query ) {
	if(  $query->is_main_query() )
		return $query;

    if ( is_post_type_archive( JOB ) && !is_admin() ) {
        // Display 50 posts for a custom post type called 'movie'
        $query->set( 'post_status', 'publish' );
        $location = isset($_GET['location']) ? $_GET['location']: '';
        if( $location )
        	$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'location',
					'field' => 'slug',
					'terms' => trim($location)
				);
        return $query;
    }
    return $query;
}
add_action( 'pre_get_posts', 'bx_pre_get_filter', 1 );


add_action( 'init', 'bx_theme_init' , 9);

/**
 * Register a Job post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function bx_theme_init() {

	$labels = array(
		'name'               => _x( 'Jobs', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Job', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Jobs', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Job', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Job', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Job', 'boxtheme' ),
		'new_item'           => __( 'New Job', 'boxtheme' ),
		'edit_item'          => __( 'Edit Job', 'boxtheme' ),
		'view_item'          => __( 'View Job', 'boxtheme' ),
		'all_items'          => __( 'All Jobs', 'boxtheme' ),
		'search_items'       => __( 'Search Jobs', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Jobs:', 'boxtheme' ),
		'not_found'          => __( 'No Jobs found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Jobs found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
         'description'        => __( 'Description.', 'boxthemes' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'job' ),
		'capability_type'    => 'post',
		'has_archive'        => 'jobs',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'job', $args );

	$labels = array(
		'name'               => _x( 'Resumes', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Resume', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Resumes', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Resume', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Resume', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Resume', 'boxtheme' ),
		'new_item'           => __( 'New Resume', 'boxtheme' ),
		'edit_item'          => __( 'Edit Resume', 'boxtheme' ),
		'view_item'          => __( 'View Resume', 'boxtheme' ),
		'all_items'          => __( 'All Resumes', 'boxtheme' ),
		'search_items'       => __( 'Search Resumes', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Resumes:', 'boxtheme' ),
		'not_found'          => __( 'No Resumes found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Resumes found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
         'description'        => __( 'Description.', 'boxthemes' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'resume' ),
		'capability_type'    => 'post',
		'has_archive'        => 'resumes',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'resume', $args );

	$labels = array(
		'name'                       => _x( 'Categories', 'taxonomy general name', 'boxtheme' ),
		'singular_name'              => _x( 'Category', 'taxonomy singular name', 'boxtheme' ),
		'search_items'               => __( 'Search Categories', 'boxtheme' ),
		'popular_items'              => __( 'Popular Categories', 'boxtheme' ),
		'all_items'                  => __( 'All Categories', 'boxtheme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', 'boxtheme' ),
		'update_item'                => __( 'Update Category', 'boxtheme' ),
		'add_new_item'               => __( 'Add New Category', 'boxtheme' ),
		'new_item_name'              => __( 'New Category Name', 'boxtheme' ),
		'separate_items_with_commas' => __( 'Separate Categories with commas', 'boxtheme' ),
		'add_or_remove_items'        => __( 'Add or remove Categories', 'boxtheme' ),
		'choose_from_most_used'      => __( 'Choose from the most used Categories', 'boxtheme' ),
		'not_found'                  => __( 'No Categories found.', 'boxtheme' ),
		'menu_name'                  => __( 'Categories', 'boxtheme' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'cat' ),
	);
	//register_taxonomy( 'job_cat', 'job', $args );

	$labels = array(
		'name'                       => _x( 'Locations', 'taxonomy general name', 'boxtheme' ),
		'singular_name'              => _x( 'Locations', 'taxonomy singular name', 'boxtheme' ),
		'search_items'               => __( 'Search Locations', 'boxtheme' ),
		'popular_items'              => __( 'Popular Locations', 'boxtheme' ),
		'all_items'                  => __( 'All Locations', 'boxtheme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Location', 'boxtheme' ),
		'update_item'                => __( 'Update Location', 'boxtheme' ),
		'add_new_item'               => __( 'Add New Location', 'boxtheme' ),
		'new_item_name'              => __( 'New Location Name', 'boxtheme' ),
		'separate_items_with_commas' => __( 'Separate Locations with commas', 'boxtheme' ),
		'add_or_remove_items'        => __( 'Add or remove Locations', 'boxtheme' ),
		'choose_from_most_used'      => __( 'Choose from the most used Locations', 'boxtheme' ),
		'not_found'                  => __( 'No Locations found.', 'boxtheme' ),
		'menu_name'                  => __( 'Locations', 'boxtheme' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'location' ),
	);
	register_taxonomy( 'location', 'job', $args );

	$labels = array(
		'name'               => _x( 'Profiles', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Profile', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Profiles', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Profile', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Profile', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Profile', 'boxtheme' ),
		'new_item'           => __( 'New Profile', 'boxtheme' ),
		'edit_item'          => __( 'Edit Profile', 'boxtheme' ),
		'view_item'          => __( 'View Profile', 'boxtheme' ),
		'all_items'          => __( 'All Profiles', 'boxtheme' ),
		'search_items'       => __( 'Search Profiles', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Profiles:', 'boxtheme' ),
		'not_found'          => __( 'No Profiles found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Profiles found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'boxthemes' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'profile' ),
		'capability_type'    => 'post',
		'has_archive'        => 'profiles',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'profile', $args );


	$labels = array(
		'name'                       => _x( 'Skills', 'taxonomy general name', 'boxtheme' ),
		'singular_name'              => _x( 'Category', 'taxonomy singular name', 'boxtheme' ),
		'search_items'               => __( 'Search Skills', 'boxtheme' ),
		'popular_items'              => __( 'Popular Skills', 'boxtheme' ),
		'all_items'                  => __( 'All Skills', 'boxtheme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', 'boxtheme' ),
		'update_item'                => __( 'Update Category', 'boxtheme' ),
		'add_new_item'               => __( 'Add New Category', 'boxtheme' ),
		'new_item_name'              => __( 'New Category Name', 'boxtheme' ),
		'separate_items_with_commas' => __( 'Separate Skills with commas', 'boxtheme' ),
		'add_or_remove_items'        => __( 'Add or remove Skills', 'boxtheme' ),
		'choose_from_most_used'      => __( 'Choose from the most used Skills', 'boxtheme' ),
		'not_found'                  => __( 'No Skills found.', 'boxtheme' ),
		'menu_name'                  => __( 'Skills', 'boxtheme' ),
	);
	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'skill' ),
	);

    register_taxonomy( 'skill', array(JOB,PROFILE), $args );

    $labels = array(
		'name'                       => _x( 'Countries', 'taxonomy general name', 'boxtheme' ),
		'singular_name'              => _x( 'Country', 'taxonomy singular name', 'boxtheme' ),
		'search_items'               => __( 'Search Countries', 'boxtheme' ),
		'popular_items'              => __( 'Popular Countries', 'boxtheme' ),
		'all_items'                  => __( 'All Countries', 'boxtheme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', 'boxtheme' ),
		'update_item'                => __( 'Update Category', 'boxtheme' ),
		'add_new_item'               => __( 'Add New Category', 'boxtheme' ),
		'new_item_name'              => __( 'New Category Name', 'boxtheme' ),
		'separate_items_with_commas' => __( 'Separate Countries with commas', 'boxtheme' ),
		'add_or_remove_items'        => __( 'Add or remove Countries', 'boxtheme' ),
		'choose_from_most_used'      => __( 'Choose from the most used Countries', 'boxtheme' ),
		'not_found'                  => __( 'No Countries found.', 'boxtheme' ),
		'menu_name'                  => __( 'Countries', 'boxtheme' ),
	);
    $args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'country' ),
	);

	register_post_status( ARCHIVED, array(
		'label'                     => __( 'Archived', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>','boxtheme' ),
		)
	);


	$port_label = array(
		'name'               => _x( 'Portfolio', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Portfolio', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Portfolios', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Portfolio', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Portfolio', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Portfolio', 'boxtheme' ),
		'new_item'           => __( 'New Profile', 'boxtheme' ),
		'edit_item'          => __( 'Edit Portfolio', 'boxtheme' ),
		'view_item'          => __( 'View Portfolio', 'boxtheme' ),
		'all_items'          => __( 'All Portfolios', 'boxtheme' ),
		'search_items'       => __( 'Search Portfolios', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Portfolios:', 'boxtheme' ),
		'not_found'          => __( 'No Portfolios found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Portfolios found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $port_label,
                'description'        => __( 'Description.', 'boxthemes' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'portfolio' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'author', 'thumbnail', )
	);

	register_post_type( 'portfolio', $args );


	//global $escrow;
	//$active = isset($escrow->active) ? $escrow->active : 'credit';

	//if( $active == 'credit'){
	$args = array(
      	'public' => false,
      	'label'  => 'Transactions',
      	'show_ui' => true,
      	'menu_position' => 25,
      	'supports'           => array( 'title' ),
    );
	register_post_type( 'transaction', $args );
    //}
	$labels = array(
		'name'               => _x( 'Orders', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Order', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Orders', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Order', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'order', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Order', 'boxtheme' ),
		'new_item'           => __( 'New Order', 'boxtheme' ),
		'edit_item'          => __( 'Edit Order', 'boxtheme' ),
		'view_item'          => __( 'View Order', 'boxtheme' ),
		'all_items'          => __( 'All Orders', 'boxtheme' ),
		'search_items'       => __( 'Search Orders', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Orders:', 'boxtheme' ),
		'not_found'          => __( 'No orders found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No orders found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'boxtheme' ),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => '_order' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'excerpt','editor' )
	);

	register_post_type( ORDER, $args );

	register_post_status( 'sandbox', array(
		'label'                     => __( 'Sandbox', 'post' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Sanbox <span class="count">(%s)</span>', 'Sanbox <span class="count">(%s)</span>','boxtheme' ),
		)
	);


}
//add_filter( 'author_rewrite_rules', 'wpse17106_author_rewrite_rules' );
function wpse17106_author_rewrite_rules( $author_rewrite_rules )
{
    foreach ( $author_rewrite_rules as $pattern => $substitution ) {
        if ( FALSE === strpos( $substitution, 'author_name' ) ) {
            unset( $author_rewrite_rules[$pattern] );
        }
    }
    return $author_rewrite_rules;
}
//add_filter( 'author_link', 'wpse17106_author_link', 10, 2 );
function wpse17106_author_link( $link, $author_id )
{

    $author_level = 'freelancer';

    $author_level = 'employer';

    $link = str_replace( '%author_level%', $author_level, $link );
    return $link;
}
function codex_Package_init() {
	$labels = array(
		'name'               => _x( 'Packages Plan', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Package', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Packages', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Package', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Package', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Package', 'boxtheme' ),
		'new_item'           => __( 'New Package', 'boxtheme' ),
		'edit_item'          => __( 'Edit Package', 'boxtheme' ),
		'view_item'          => __( 'View Package', 'boxtheme' ),
		'all_items'          => __( 'All Packages', 'boxtheme' ),
		'search_items'       => __( 'Search Packages', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Packages:', 'boxtheme' ),
		'not_found'          => __( 'No Packages found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Packages found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'boxtheme' ),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'package' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'excerpt' )
	);

	//register_post_type( '_package', $args );
}

function boxtheme_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'boxtheme-fonts', boxtheme_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'boxtheme-style', get_stylesheet_uri() );

	//wp_enqueue_style( 'main-css', get_template_directory_uri() . '/assets/css/main.css', array( 'boxtheme-style' ), rand() );
	wp_enqueue_style( 'main-css', get_template_directory_uri() . '/assets/css/main.css', array( 'boxtheme-style' ), BX_VERSION );
	//wp_enqueue_style( 'ionicons','//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
	wp_enqueue_style( 'ionicons-css', get_template_directory_uri() . '/library/ionicons/ionicons.min.css', array( 'boxtheme-style' ), BX_VERSION );

	wp_enqueue_style( 'bootraps', get_template_directory_uri() .'/library/bootstrap/css/bootstrap.css' , array( 'boxtheme-style' ), '1.0' );
	//wp_enqueue_style( 'box-responsive', get_template_directory_uri() .'/assets/css/responsive.css' , array( 'main-css' ), rand() );
	wp_enqueue_style( 'box-responsive', get_template_directory_uri() .'/assets/css/responsive.css' , array( 'main-css' ), BX_VERSION );




	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'boxtheme-ie9', get_template_directory_uri(). '/assets/css/ie9.css' , array( 'boxtheme-style' ), '1.0' );
		wp_style_add_data( 'boxtheme-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'boxtheme-ie8', get_template_directory_uri(). '/assets/css/ie8.css' , array( 'boxtheme-style' ), '1.0' );
	wp_style_add_data( 'boxtheme-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_template_directory_uri(). '/assets/js/html5.js', array(), '3.7.3' );
	wp_register_script( 'bootstrap-js', get_template_directory_uri().'/library/bootstrap/js/bootstrap.min.js' , array('jquery'), BX_VERSION );

	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	// load front.js file
	//wp_enqueue_script( 'wp-util' );
	wp_register_script( 'define', get_template_directory_uri(). '/assets/js/define.js' , array( 'jquery','wp-util' ), BX_VERSION, true );
	wp_enqueue_script( 'front', get_template_directory_uri(). '/assets/js/front.js' , array( 'jquery','underscore','define','plupload','bootstrap-js' ),  rand(), true );

	if ( is_singular() ) {

		if( comments_open() && get_option( 'thread_comments' ) ){
			wp_enqueue_script( 'comment-reply' );
		}
		if( is_singular( JOB ) ) {

			wp_enqueue_style( 'single-Job', get_template_directory_uri(). '/assets/css/single-job.css' , array( 'boxtheme-style' ), BX_VERSION );
			wp_enqueue_script( 'single-Job-js', get_template_directory_uri(). '/assets/js/single-job.js' , array( 'front' ), BX_VERSION, true );

			wp_localize_script( 'single-Job-js', 'escrow', get_commision_setting(false) );
		}

		if ( is_page_template('page-login.php') ) {
			wp_enqueue_script('jquery');
		}
	}

	if( is_page_template( 'page-post-job.php')  || is_home() || is_archive('job') ){
		wp_enqueue_script( 'chosen-js', get_template_directory_uri(). '/library/chosen/chosen.jquery.min.js', array( 'jquery' ), BX_VERSION, true );
		wp_enqueue_style( 'chosen-css', get_template_directory_uri(). '/library/chosen/chosen.min.css' , array( 'boxtheme-style' ), BX_VERSION );

	}

	if( is_page_template( 'page-post-job.php') ){

		wp_enqueue_style( 'post-job', get_template_directory_uri(). '/assets/css/post-project.css' , array( 'boxtheme-style' ), rand() );

		wp_enqueue_script( 'post-Job1', get_template_directory_uri(). '/assets/js/post-project.js', array( 'jquery','chosen-js','plupload', 'define' ), BX_VERSION, true );


	}
	if( is_page_template( 'page-apply.php') ){

		wp_enqueue_style( 'apply-job', get_template_directory_uri(). '/assets/css/apply.css' , array( 'boxtheme-style' ), rand() );

	}


	if ( is_page_template( 'page-my-profile.php') ){
		wp_enqueue_style( 'profile-css', get_template_directory_uri(). '/assets/css/profile.css', array( 'boxtheme-style' ), BX_VERSION );
		if ( is_user_logged_in() ){
			wp_enqueue_script( 'chosen-js', get_template_directory_uri(). '/library/chosen/chosen.jquery.min.js' , array( 'jquery' ), BX_VERSION, true );
			wp_enqueue_style( 'chosen-css', get_template_directory_uri(). '/library/chosen/chosen.min.css' , array( 'boxtheme-style' ), BX_VERSION );
			wp_enqueue_script( 'profile', get_template_directory_uri(). '/assets/js/profile.js', array( 'jquery','chosen-js', 'front' ), BX_VERSION, true );

		}
	}
	if( is_page_template('page-buy-credit.php' ) ){
		wp_enqueue_script( 'buy-credit', get_template_directory_uri(). '/assets/js/buy_credit.js' , array( 'front' ), BX_VERSION, true );
	}

	if( is_page_template('page-dashboard.php' ) ){
		wp_enqueue_script( 'dashboard-js', get_template_directory_uri(). '/assets/js/dashboard.js' , array( 'front' ), BX_VERSION, true );
		wp_enqueue_style( 'dashboard-css', get_template_directory_uri(). '/assets/css/dashboard.css' , array( 'boxtheme-style' ), BX_VERSION );
	}
	if( is_page_template('page-my-credit.php' ) ){
		wp_enqueue_script( 'credit-js', get_template_directory_uri(). '/assets/js/credit.js', array( 'front' ), BX_VERSION, true );

	}

	// if( is_post_type_archive( JOB ) ){
	// 	wp_enqueue_script( 'ion.rangeSlider', get_template_directory_uri(). '/assets/js/ion.rangeSlider.js' , array('jquery','front'), BX_VERSION, true );
	// 	wp_enqueue_style( 'ion.rangeSlider', get_template_directory_uri(). '/assets/css/ion.rangeSlider.css', array(), BX_VERSION );
	// 	wp_enqueue_style( 'ion.rangeSlider.Flat', get_template_directory_uri(). '/assets/css/ion.rangeSlider.skinFlat.css', array( ), BX_VERSION );
	// }
	// if( is_page_template( 'page-messages.php' ) ){
	// 	wp_enqueue_script( 'box-msg', get_template_directory_uri(). '/assets/js/messages.js' , array( 'front' ), BX_VERSION, true );
	// }
	if( is_author() ){
		// load scrip for js/css of portfolio gallery.
		wp_enqueue_style( 'ekko-lightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css', BX_VERSION, true );
		wp_enqueue_script( 'ekko-lightbox-js', get_template_directory_uri(). '/library/ekko-lightbox.min.js', array('jquery','front'), BX_VERSION, true );
		//wp_enqueue_script( 'ekko-lightbox-js', '/library/ekko-lightbox.min.js'), array('jquery','front'), BX_VERSION, true );
		//library http://ashleydw.github.io/lightbox/
	}

}
add_action( 'wp_enqueue_scripts', 'boxtheme_scripts' );
function bx_excerpt_more( $more ) {
    return '';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function boxtheme_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link" >%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'boxtheme' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'bx_excerpt_more' );
add_filter( 'excerpt_more', 'boxtheme_excerpt_more' );


function the_excerpt_max_charlength( $excerpt, $charlength, $echo = true) {
	$excerpt = strip_tags($excerpt);
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			echo mb_substr( $subex, 0, $excut );
		} else {
			echo $subex;
		}
		echo '...';
	} else {
		echo strip_tags($excerpt);
	}
}
function bx_page_template_redirect(){
	global $user_ID;


	if( ! is_user_logged_in() ){

		if( is_page_template( 'page-post-job.php' )  ){
			$login_page = add_query_arg( array('redirect'=>box_get_static_link( 'post-job' ) ),box_get_static_link( 'login' ) );
			wp_redirect( $login_page);
			exit();
		}
		if(  is_page_template( 'page-buy-credit.php' ) ){
			$id = isset($_GET['id']) ? $_GET['id'] : '';
			$buy_credit = add_query_arg( 'id',$id, box_get_static_link('buy-credit' ) );
			$login_page = add_query_arg( array('redirect'=>$buy_credit ),box_get_static_link( 'login' ) );
			wp_redirect( $login_page);
			exit();
		}
		if( is_page_template( 'page-my-profile.php' ) ){
			wp_redirect( home_url() );
			exit();
		}

	}

	if( is_user_logged_in() ) {

		if( is_page_template( 'page-login.php' ) || is_page_template( 'page-signup.php' ) || is_page_template( 'page-signup-employer.php' ) || is_page_template( 'page-signup-jobseeker.php' ) ){
			wp_redirect( home_url() );
			exit();
		}

		if ( current_user_can('manage_options') ){
			return ;
		}
		if(  is_page_template( 'page-verify.php' ) ){
			if( is_account_verified( $user_ID) )  {
				wp_redirect( home_url() );
			}
			return;
		}
		if( ! is_account_verified( $user_ID) )  {
	        wp_redirect( box_get_static_link( 'verify' ) );
	        exit();
	    }
	}

}
add_action( 'template_redirect', 'bx_page_template_redirect', 15 );



function bx_custom_avatar_url( $url, $id_or_email) {
    $user = false;
    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if( $user && is_object($user) ){
	    $user_id = $user->data->ID;

		$avatar_url = get_user_meta($user_id, 'avatar_url', true);
		if( !empty( $avatar_url) ){
			$url = $avatar_url;
		}
	}

    return $url;
}
add_filter( 'get_avatar_url' , 'bx_custom_avatar_url' , 99999 , 2 );


// //https://ulrich.pogson.ch/load-theme-plugin-translations
// function my_theme_setup(){
//     //load_theme_textdomain('boxtheme', TRANSLATION_URL);
//     //load_theme_textdomain( 'boxtheme', get_stylesheet_directory() . '/languages' );
//     load_theme_textdomain( 'boxtheme', get_template_directory() . '/languages' );
// }
// add_action('after_setup_theme', 'my_theme_setup');
function load_txtdomain() {
	$locale = get_locale();

	// if ( ! in_array( $locale, $languages ) ) {
	// 	$locale = 'boxtheme';
	// }

	$path = BO_LANG_DIR."/".$locale.'.mo';
	if( file_exists( $path ) ){
		load_textdomain( 'boxtheme', $path );
	}

}
add_action('after_setup_theme','load_txtdomain');


// function create_new_url_querystring(){
// 	//var_dump(expression)
//     add_rewrite_rule('^apply/([0-9]+)/?', 'index.php?page_id=$matches[1]', 'top');

//     add_rewrite_tag('%apply%','([^/]*)');
//     flush_rewrite_rules();
// }
//  add_action('init', 'create_new_url_querystring');
// function custom_rewrite_basic() {
//   add_rewrite_rule('^apply/([0-9]+)/?', 'index.php?page_id=$matches[1]', 'top');
// }
// add_action('init', 'custom_rewrite_basic');