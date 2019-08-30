<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function setup_enroviment() {

	BX_User::add_role();
	global $box_general, $box_currency, $app_api, $checkout_mode, $escrow, $symbol, $box_slugs; // init valuable.

	$box_general = BX_Option::get_instance()->get_general_option(); // return not an object - arrray.
	$app_api = (OBJECT) BX_Option::get_instance()->get_app_api_option($box_general);
	$box_currency = (OBJECT) BX_Option::get_instance()->get_currency_option($box_general);
	$checkout_mode = (int) $box_general->checkout_mode; // 0 - sandbox. 1- real
	$escrow = (object) BX_Option::get_instance()->get_escrow_setting();
	$symbol = box_get_currency_symbol();
	$box_slugs = BX_Option::get_instance()->get_box_slugs($box_general);


}
add_action( 'after_setup_theme','setup_enroviment');
function bx_pre_get_filter( $query ) {

	$action 	= isset($_POST['action']) ? $_POST['action'] :'';
	$request 	= isset($_POST['request']) ? $_POST['request'] : array();
	$post_type 	= isset( $request['post_type'] ) ? $request['post_type'] :'';

    if ( $query->is_main_query() && is_post_type_archive( PROJECT ) && !is_admin()  || ( $post_type == PROJECT  && $action == 'sync_search' )   )  {

        // Display 50 posts for a custom post type called ''
        $query->set( 'post_status', 'publish' );

		$query->set( 'orderby', 'not_exists_clause post_date');
		$query->set( 'order' , 'DESC');
		$query->set('meta_query' , array(
				'relation' => 'OR',
				'exists_clause' => array(
					'key' => 'priority',
					'compare' => 'EXISTS'
				),
				'not_exists_clause' => array(
					'key' => 'priority',
					'compare' => 'NOT EXISTS'
				)
			)
		);
        //https://wordpress.stackexchange.com/questions/188287/orderby-meta-value-only-returns-posts-that-have-existing-meta-key
        //https://wordpress.stackexchange.com/questions/188287/orderby-meta-value-only-returns-posts-that-have-existing-meta-key
        return $query;
    }

    if (  $query->is_main_query() && is_post_type_archive( PROFILE ) && ! is_admin() || ( $post_type == PROFILE  && $action == 'sync_search' )    )  {
    	// or in archive page. or in searching ajax
    	$query->set( 'post_status', 'publish' ); // inactive == not available.

    	$query->set( 'orderby', 'not_exists_clause post_date');
		$query->set( 'order' , 'DESC');
		$query->set('meta_query' , array(
				'relation' => 'OR',
				'exists_clause' => array(
					'key' => 'is_reviewed',
					'compare' => 'EXISTS'
				),
				'not_exists_clause' => array(
					'key' => 'is_reviewed',
					'compare' => 'NOT EXISTS'
				)
			)
		);
		return apply_filters('pre_get_filter_profiles', $query);
    }

}
add_action( 'pre_get_posts', 'bx_pre_get_filter', 11);




function box_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if (  in_array( $cap, array('edit_project','read_project','edit_projects','read_private_projects') )  ) {

		if(isset($args[0])){

			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );


			$post_type  = $post->post_type ;

			if( $post_type == 'project'){

				if ( $user_id == $post->post_author  ){
					$caps[] = 'read';
					if($post->post_status == 'private'){
						$caps[] = 'read_private_projects';
					}
				}
					//$caps[] = $post_type->cap->edit_posts;
					//$caps[] = $post_type->cap->read_private_posts;

			}
		}
	}

	return $caps;
}
//add_filter( 'map_meta_cap', 'box_map_meta_cap', 10, 4 );

/**
 * Register a Project post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function bx_theme_init() {

	$p_labels = array(
		'name'               => _x( 'Projects', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Project', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Projects', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Project', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Project', 'boxtheme' ),
		'new_item'           => __( 'New Project', 'boxtheme' ),
		'edit_item'          => __( 'Edit Project', 'boxtheme' ),
		'view_item'          => __( 'View Project', 'boxtheme' ),
		'all_items'          => __( 'All Projects', 'boxtheme' ),
		'search_items'       => __( 'Search Projects', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Projects:', 'boxtheme' ),
		'not_found'          => __( 'No Projects found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Projects found in Trash.', 'boxtheme' )
	);
	$project_args = array(
		'labels'             => $p_labels,
         'description'        => __( 'Description.', 'boxtheme' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),

		//'map_meta_cap' => true, // important
		'capability_type' => 'project',
		'capabilities' => array(
			'read_post' => 'read_project',
			'edit_post' => 'edit_project',
			'edit_posts' => 'edit_projects',
			'delete_post' => 'delete_project',
			'delete_posts' => 'delete_projects',
			'publish_posts' => 'publish_project',
			'edit_others_posts' => 'edit_others_projects',
			'read_private_posts' => 'read_private_projects',
			'delete_others_posts' => 'delete_others_projects',

		),

		'has_archive'        => 'projects',
		'hierarchical'       => false,
		'menu_position'      => 21,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'project', $project_args );

	$cat_label = array(
		'name'                       => __( 'Categories', 'boxtheme' ),
		'singular_name'              => __( 'Category',  'boxtheme' ),
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

	$cat_args = array(
		'hierarchical'          => true,
		'labels'                => $cat_label,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'cat' ),
	);
	register_taxonomy( 'project_cat', 'project', $cat_args );

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
		'menu_position'      => 21,
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
		'hierarchical'          => false, // 1 level or many level : have descendants or no.
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'skill' ),
	);

    register_taxonomy( 'skill', array(PROJECT,PROFILE), $args );


	$tax_args =  box_get_country_args();

    $country_args = array(
		'hierarchical'          => false,
		'labels'                => $tax_args->label,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $tax_args->slug ),
	);
	register_taxonomy( $tax_args->slug, 'profile', $country_args );

	$labels = array(
		'name'               => _x( 'Bids', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Bid', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Bids', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Bid', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'Bid', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Bid', 'boxtheme' ),
		'new_item'           => __( 'New Bid', 'boxtheme' ),
		'edit_item'          => __( 'Edit Bid', 'boxtheme' ),
		'view_item'          => __( 'View Bid', 'boxtheme' ),
		'all_items'          => __( 'All Bids', 'boxtheme' ),
		'search_items'       => __( 'Search Bids', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Bids:', 'boxtheme' ),
		'not_found'          => __( 'No Bids found.', 'boxtheme' ),
		'not_found_in_trash' => __( 'No Bids found in Trash.', 'boxtheme' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'boxthemes' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'bid' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'bid', $args );

	register_post_status( 'inactive', array(
		'label'                     => _x( 'Inactive', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>' ),
		)
	);

	register_post_status( AWARDED, array(
		'label'                     => _x( 'Awarded', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Awarded <span class="count">(%s)</span>', 'Awarded <span class="count">(%s)</span>' ),
		)
	);
	register_post_status( ARCHIVED, array(
		'label'                     => _x( 'Archived', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>' ),
		)
	);
	register_post_status( 'complete', array(
		'label'                     => _x( 'Completed', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>' ),
		)
	);
	register_post_status( 'disputing', array(
		'label'                     => _x( 'Disputing', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Disputing <span class="count">(%s)</span>', 'Disputing <span class="count">(%s)</span>' ),
		)
	);
	register_post_status( 'resolved', array(
		'label'                     => _x( 'Resolved', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Resolved <span class="count">(%s)</span>', 'Resolved <span class="count">(%s)</span>' ),
		)
	);
	// register_post_status( 'private', array(
	// 	'label'                     => _x( 'Private', 'post' ),
	// 	'public'                    => false,
	// 	'exclude_from_search'       => true,
	// 	'show_in_admin_all_list'    => true,
	// 	'show_in_admin_status_list' => true,
	// 	'label_count'               => _n_noop( 'Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>' ),
	// 	)
	// );
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

	$args = array(
      	'public' => false,
      	'label'  => 'Transactions',
      	'show_ui' => true,
      	'menu_position' => 25,
      	'supports'           => array( 'title','editor','custom-fields' ),
    );
	register_post_type( 'transaction', $args );

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
		'label'                     => _x( 'Sandbox', 'post' ),
		'public'                    => false,
		'exclude_from_search'       => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Sanbox <span class="count">(%s)</span>', 'Sanbox <span class="count">(%s)</span>' ),
		)
	);
	register_post_type_withdrawal();

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

	register_post_type( '_package', $args );

}
add_action( 'init', 'bx_theme_init' , 9);


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

}
function has_box_map(){
	if( is_page_template( 'page-maps.php' ) )
		return true;

	if ( is_post_type_archive(PROFILE) && map_in_archive() )
		return true;

	return false;
}
function boxtheme_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'boxtheme-fonts', boxtheme_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'boxtheme-style', get_stylesheet_uri(), array(), BOX_VERSION );

	wp_enqueue_style( 'main-css', get_template_directory_uri() . '/assets/css/main.css', array( 'boxtheme-style' ), rand() );
	//wp_enqueue_style( 'main-css', get_template_directory_uri() . '/assets/css/main.min.css', array( 'boxtheme-style' ), BOX_VERSION );


	if( BOX_CDN ){
		wp_enqueue_style( 'bootraps', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' , array( 'boxtheme-style' ), BOX_VERSION );
	} else {
		wp_enqueue_style( 'bootraps', get_template_directory_uri() .'/library/bootstrap/css/bootstrap.css' , array( 'boxtheme-style' ), BOX_VERSION );
	}


	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'boxtheme-ie9', get_template_directory_uri(). '/assets/css/ie9.css' , array( 'boxtheme-style' ), BOX_VERSION);
		wp_style_add_data( 'boxtheme-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'boxtheme-ie8', get_template_directory_uri(). '/assets/css/ie8.css' , array( 'boxtheme-style' ), '1.0' );
	wp_style_add_data( 'boxtheme-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_template_directory_uri(). '/assets/js/html5.js', array(), '3.7.3' );
	wp_register_script( 'bootstrap-js', get_template_directory_uri().'/library/bootstrap/js/bootstrap.min.js' , array('jquery'), BOX_VERSION );
	//https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js
	//wp_register_script( 'btvalidate', get_template_directory_uri().'/library/bootstrap/js/validator.js' , array('jquery','bootstrap-js'), BOX_VERSION );
	wp_register_script( 'btvalidate', get_template_directory_uri().'/library/bootstrap/js/jquery.validate.min.js' , array('jquery','bootstrap-js'), BOX_VERSION );
	//http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js

	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_style( 'bootraps-toggle', get_template_directory_uri() .'/admin/css/bootstrap-toggle.min.css', array( 'boxtheme-style' ), '1.0' );
	wp_enqueue_script('toggle-button',get_template_directory_uri().'/admin/js/bootstrap-toggle.min.js', array('jquery'), BOX_VERSION );

	// load front.js file
	//wp_enqueue_script( 'wp-util' );
	wp_register_script( 'define', get_template_directory_uri(). '/assets/js/define.js' , array( 'jquery','wp-util' ), BOX_VERSION, true );
	wp_enqueue_script( 'front', get_template_directory_uri(). '/assets/js/front.js' , array( 'jquery','underscore','define','plupload','bootstrap-js' ),  BOX_VERSION , true );

	if ( is_singular() ) {

		if( comments_open() && get_option( 'thread_comments' ) ){
			wp_enqueue_script( 'comment-reply' );
		}
		if( is_singular( PROJECT ) ) {

			wp_enqueue_style( 'single-project', get_template_directory_uri(). '/assets/css/single-project.css' , array( 'boxtheme-style' ), BOX_VERSION );
			wp_enqueue_script( 'single-project-js', get_template_directory_uri(). '/assets/js/single-project.js' , array( 'front' ), BOX_VERSION, true );

			wp_localize_script( 'single-project-js', 'escrow', get_commision_setting(false) );
		}

		if ( is_page_template('page-login.php') ) {
			wp_enqueue_script('jquery');
		}
	}

	if( is_page_template( 'page-post-project.php') || is_post_type_archive(PROJECT) ||
		is_post_type_archive(PROFILE) || is_page_template( 'page-maps.php' ) ){


		wp_enqueue_script( 'chosen-js', get_template_directory_uri(). '/library/chosen/chosen.jquery.min.js', array( 'jquery' ), BOX_VERSION, true );
		wp_enqueue_style( 'chosen-css', get_template_directory_uri(). '/library/chosen/chosen.min.css' , array( 'boxtheme-style' ), BOX_VERSION );

		if( is_page_template( 'page-post-project.php') ) {

			wp_enqueue_style( 'post-project', get_template_directory_uri(). '/assets/css/post-project.css' , array( 'boxtheme-style' ), BOX_VERSION );

			wp_enqueue_script( 'post-project', get_template_directory_uri(). '/assets/js/post-project.js', array( 'jquery','chosen-js','plupload','wp-util', 'define','bootstrap-js','btvalidate' ), BOX_VERSION, true );
			wp_localize_script( 'post-project', 'submit_project', submit_project_localize() );
		}


		if( is_post_type_archive( PROJECT ) || is_page_template( 'page-maps.php' ) || is_post_type_archive(PROFILE)  ){
			wp_enqueue_script( 'ion.rangeSlider', get_template_directory_uri(). '/assets/js/ion.rangeSlider.js' , array('jquery','front'), BOX_VERSION, true );
			wp_enqueue_style( 'ion.rangeSlider', get_template_directory_uri(). '/assets/css/ion.rangeSlider.css', array(), BOX_VERSION );
			wp_enqueue_style( 'ion.rangeSlider.Flat', get_template_directory_uri(). '/assets/css/ion.rangeSlider.skinFlat.css', array( ), BOX_VERSION );
		}
		if( has_box_map()  ){
			wp_enqueue_script( 'gmap-js', get_template_directory_uri(). '/gmap/page_map.js' , array('jquery','front'), BOX_VERSION, true );
		}

	}


	if( is_page_template('page-deposit.php' ) || is_page_template('page-checkout.php' ) ){
		wp_enqueue_style( 'payment-style', get_template_directory_uri(). '/payment/payment.css' , array( 'boxtheme-style' ), BOX_VERSION );
		wp_enqueue_script( 'box-checkout', get_template_directory_uri(). '/assets/js/checkout.js' , array( 'front' ), BOX_VERSION, true );
	}

	if( is_page_template('page-my-project.php' ) ||  is_page_template('page-my-bid.php' ) ||  is_page_template('page-dashboard.php' || is_page_template( 'page-inbox.php' ) ) ){
		wp_enqueue_script( 'dashboard-js', get_template_directory_uri(). '/assets/js/dashboard.js' , array( 'front' ), BOX_VERSION, true );
		wp_enqueue_style( 'dashboard-css', get_template_directory_uri(). '/assets/css/dashboard.css' , array( 'boxtheme-style' ), BOX_VERSION );
	}

	if( is_user_logged_in() ){

		if ( is_page_template( 'page-my-profile.php') ){
			wp_enqueue_style( 'profile-css', get_template_directory_uri(). '/assets/css/profile.css', array( 'boxtheme-style' ), BOX_VERSION );
			if ( is_user_logged_in() ){
				wp_enqueue_script( 'chosen-js', get_template_directory_uri(). '/library/chosen/chosen.jquery.min.js' , array( 'jquery' ), BOX_VERSION, true );
				wp_enqueue_style( 'chosen-css', get_template_directory_uri(). '/library/chosen/chosen.min.css' , array( 'boxtheme-style' ), BOX_VERSION );
				wp_enqueue_script( 'profile', get_template_directory_uri(). '/assets/js/profile.js', array( 'jquery','chosen-js', 'front' ), BOX_VERSION, true );

			}
		}


		if( is_page_template( 'page-inbox.php' ) ){
			wp_enqueue_script( 'box-msg', get_template_directory_uri(). '/assets/js/messages.js' , array( 'front' ), BOX_VERSION, true );
			wp_localize_script( 'box-msg', 'inbox', array(
				'type_msg' => __('Type your message here','boxtheme'),
				'btn_send' => __('Send','boxtheme'),
			) );
			wp_enqueue_style( 'inbox', get_template_directory_uri(). '/assets/css/inbox.css' , array( 'boxtheme-style' ), BOX_VERSION );
		}

		if( is_page_template('page-my-credit.php' ) ){
			wp_enqueue_script( 'credit-js', get_template_directory_uri(). '/assets/js/credit.js', array( 'front' ), BOX_VERSION, true );
			wp_enqueue_style( 'my-credit', get_template_directory_uri() .'/assets/css/my-credit.css' ,array(), BOX_VERSION );

		}

	}


	if( is_author() ){
		// load scrip for js/css of portfolio gallery.
		wp_enqueue_style( 'ekko-lightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css', BOX_VERSION, true );
		wp_enqueue_script( 'ekko-lightbox-js', get_template_directory_uri(). '/library/ekko-lightbox.min.js', array('jquery','front'), BOX_VERSION, true );
		//wp_enqueue_script( 'ekko-lightbox-js', '/library/ekko-lightbox.min.js'), array('jquery','front'), BOX_VERSION, true );
		//library http://ashleydw.github.io/lightbox/
	}
	wp_enqueue_style( 'box-responsive', get_template_directory_uri() .'/assets/css/responsive.css' , array( 'main-css' ), rand() );


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

		if( is_page_template( 'page-post-project.php' )  ){
			$login_page = add_query_arg( array('redirect'=>box_get_static_link( 'post-project' ) ),box_get_static_link( 'login' ) );
			wp_redirect( $login_page);
			exit();
		}
		if(  is_page_template( 'page-deposit.php' ) ){
			$id = isset($_GET['id']) ? $_GET['id'] : '';
			$deposit_credit = add_query_arg( 'id',$id, box_get_static_link('deposit' ) );
			$login_page = add_query_arg( array('redirect'=>$deposit_credit ), box_get_static_link( 'login' ) );
			wp_redirect( $login_page);
			exit();
		}
		//$slugs = ('dashboard','my-profile','inbox');
		if( is_page_template( 'page-my-profile.php' ) ||  is_page_template( 'page-inbox.php' ) ||  is_page_template( 'page-dashboard.php' ) ){
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
			if( is_account_confirmed( $user_ID) )  {
				wp_redirect( home_url() );
			}
			return;
		}
		if( ! is_account_confirmed( $user_ID) && box_requires_confirm() )  {
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

// function load_txtdomain() {
//     $locale = apply_filters( 'plugin_locale', get_locale(), 'my-plugin' );
//     load_textdomain( 'my-plugin', WP_LANG_DIR . '/my-plugin-' . $locale . '.mo' );
// }
// add_action('plugins_loaded','load_txtdomain');