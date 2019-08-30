<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BX_Admin{
    static $instance;


    function __construct(){
        global $box_warning;
        add_action( 'admin_menu', array($this,'bx_register_my_custom_menu_page' ), 9);
       	add_action( 'admin_enqueue_scripts', array($this, 'box_enqueue_scripts' ) );
        add_action( 'admin_footer', array($this,'box_admin_footer_html'), 9 );
        add_action('admin_bar_menu', array($this,'add_toolbar_items'), 100);
        $this->generate_default_pages();

    }
    function generate_default_pages(){
        $is_added_page = get_option('is_added_page', false);

       // echo '<pre>';
        if ( $is_added_page !== 'added' ){

            $general = BX_Option::get_instance()->get_general_option();

            //if( empty($general->box_slugs)){
                $general->box_slugs = BX_Option::get_instance()->get_default_box_slugs();
            //}

            $default_slugs = $temp_slugs = $general->box_slugs;

            foreach ($default_slugs as $slug => $value) {

                $value = (object) $value;
                $template_file = 'page-'.$slug.'.php';
                $args_new =  array(
                    'post_title' => $value->label,
                    'post_type' => 'page',
                    'post_status' => 'publish',
                );

                if(  empty( $value->ID) ) {
                    $page = get_pages( array(
                        'meta_key'      => '_wp_page_template',
                        'meta_value'    => $template_file,
                        'number'   => 1,
                        'post_status' => 'publish',
                    ));

                    if( empty( $page ) ) {
                        $id = wp_insert_post($args_new);
                        if( ! is_wp_error( $id ) ){
                            update_post_meta($id,'_wp_page_template',$template_file );
                            $temp_slugs[$slug]['ID'] = $id;
                        }
                    } else {
                        $page = array_shift($page);
                        $id = $page->ID;
                        $temp_slugs[$slug]['ID'] = $id;
                        update_post_meta($id,'_wp_page_template',$template_file );

                    }
                } else {
                    //check ID id option is a page or just a ghost page.

                    $check_id = get_post($value->ID);
                    if( !$check_id || is_wp_error( $check_id ) ){
                        $page_id = wp_insert_post($args_new);
                        if( ! is_wp_error( $page_id ) ){
                            update_post_meta($page_id,'_wp_page_template',$template_file );
                            $temp_slugs[$slug]['ID'] = $page_id;
                        }
                    }
                }
            }// end foreach.

            update_option('is_added_page', 'added');
            $group = 'general';
            $current = get_option($group, false);
            if ( !is_array($current) )
                $current = array();
                $current['box_slugs']= $temp_slugs;
            update_option($group, $current);

        }
    }
    static function get_instance(){
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    public function bx_register_my_custom_menu_page() {
        add_menu_page( __( 'Theme Options', 'boxtheme' ),'Box settings','manage_options', BOX_SETTING_SLUG, array('BX_Admin','box_custom_menu_page'),get_template_directory_uri().'/ico.png', 6);
	}

    public function box_enqueue_scripts($hook) {
        // Load only on ?page=theme-options
    	$credit_page = 'box-settings_page_credit-setting';
        $box_setting = isset($_GET['box-settings'])? $_GET['box-settings']:'';
    	$default = 'toplevel_page_box-settings';
        $hook_wdt = 'box-settings_page_widthraw-order';
        $hook_order = 'box-settings_page_credit-setting';
        $hook_withdrawal = 'box-settings_page_widthraw-history';
        $hook_appearances = 'box-settings_page_appearances';

       // wp_enqueue_script( 'jquery-ui' );
        if( in_array( $hook, array( $default, $hook_wdt, $hook_order, $hook_withdrawal, $hook_appearances) ) ){
	        wp_enqueue_style( 'bootraps', get_template_directory_uri(). '/library/bootstrap/css/bootstrap.css'  );
	        wp_enqueue_style( 'box_wp_admin_css', get_template_directory_uri().'/admin/css/box_style.css', array(), BOX_VERSION );
	        wp_enqueue_style( 'bootraps-toggle', get_template_directory_uri() .'/admin/css/bootstrap-toggle.min.css' );
	        wp_enqueue_script('toggle-button',get_template_directory_uri().'/admin/js/bootstrap-toggle.min.js' );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-sortable' );
	        wp_enqueue_script( 'box-js', get_template_directory_uri().'/admin/js/admin.js', array('jquery','wp-util','jquery-ui-sortable'), BOX_VERSION );

	        if( in_array( $hook, array( $hook_wdt, $hook_order,$hook_withdrawal ) ) ){
	        	wp_enqueue_script('credit-js',get_template_directory_uri().'/admin/js/credit.js', array('jquery','box-js'), BOX_VERSION );
	        }
	   }

    }
    function install(){
        get_template_part( 'admin/templates/install');


    }
    function plugins(){
        get_template_part( 'admin/templates/plugins');
    }
    function general(){
    	get_template_part( 'admin/templates/general');
    }
    function escrow(){
    	get_template_part( 'admin/templates/escrow');

    }
    function currency_package(){
    	get_template_part( 'admin/templates/currency_package');
    }
    function payment_gateways(){
    	get_template_part( 'admin/templates/payment_gateways');
    }
    static function box_admin_footer_html(){
    	$page = isset($_GET['page']) ? $_GET['page'] : '';
    	if( in_array($page, array('credit-setting','box-settings','widthraw-order','widthraw-history','appearances')) ) {	?>
	    	<script type="text/javascript">
	            var bx_global = {
	                'home_url' : '<?php echo home_url() ?>',
	                'admin_url': '<?php echo admin_url() ?>',
	                'ajax_url' : '<?php echo admin_url().'admin-ajax.php'; ?>',
	                'selected_local' : '',
	                'is_free_submit_job' : true,

	            }
	        </script>
    	<?php }
        global $post;

        if( isset($post->post_type) && $post->post_type == '_order'){
            echo '
                <style type="text/css">
                    #submitdiv,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        }
    }
    function email(){
    	global $main_page;
    	$main_page 		= admin_url('admin.php?page='.BOX_SETTING_SLUG);
    	get_template_part( 'admin/templates/email');
	}
    static function box_custom_menu_page(){

        $current_section = isset($_GET['section']) ? $_GET['section'] : 'general';
        $admin = BX_Admin::get_instance();
        $sections = array('escrow','install','currency_package','payment_gateways','email','plugins', 'account');
        $main_setting_link =  admin_url('admin.php?page='.BOX_SETTING_SLUG);
        ?>
        <div class="wrap">
            <div class="setting-logo">
                <a href="<?php echo $main_setting_link;?>"><img src="<?php echo get_template_directory_uri(); ?>/img/boxtheme.png" width="55" height="" alt="" align="left" /></a>
                <h1 class="theme-option"> <?php _e('Theme Options','boxtheme');?></h1>
            </div>
            <div class="wrap-content">
                <?php get_template_part( 'admin/templates/header_menu'); ?>
                <div class="tab-content clear">
                    <div id="main_content" ">
                        <?php
                            if( ! in_array( $current_section, $sections ) ){
                                $current_section = 'general';
                            }
                            $admin->$current_section(); // install/general
                        ?>
                    </div>
                </div>
            </div>
        </div> <?php
    }
    function account(){
        get_template_part( 'admin/templates/account');
    }
    function add_toolbar_items($admin_bar){
        $admin_bar->add_menu( array(
            'id'    => 'box-settings',
            'title' => '<img src="'.get_template_directory_uri().'/ico.png" /> BoxThemes Settings',
            'href'  => admin_url('admin.php?page=box-settings'),
            'meta'  => array(
                'title' => __('BoxThemes Settings'),
            ),
        ));
        $admin_bar->add_menu(
            array(
                'id'    => 'main-setting',
                'parent' => 'box-settings',
                'title' => 'Box Settings',
                'href'  => admin_url('admin.php?page=box-settings'),
                'meta'  => array(
                    'title' => __('Box Settings','boxtheme'),

                    'class' => 'my_menu_item_class'
                ),
            )
        );

        $admin_bar->add_menu(
            array(
                'id'    => 'my-sub-item',
                'parent' => 'box-settings',
                'title' => 'Debosit credit Order',
                'href'  => admin_url('admin.php?page=credit-setting'),
                'meta'  => array(
                    'title' => __('Debosit credit Order'),
                    //'target' => '_blank',
                    'class' => 'my_menu_item_class'
                ),
            )
        );
        $admin_bar->add_menu( array(
            'id'    => 'my-second-sub-item',
            'parent' => 'box-settings',
            'title' => 'Withdrawal History',
           	'href'  => admin_url('admin.php?page=widthraw-history'),
            'meta'  => array(
                'title' => __('Withdrawal Order'),
               // 'target' => '_blank',
                'class' => 'my_menu_item_class'
            ),
        ));
        do_action( 'box_quick_admin_bar', $admin_bar );
    }
}

function box_update_gateway_possition($gateway, $pos){
    $option = BX_Option::get_instance();
    $option->set_option( 'payment', $gateway, 'possition','possition', $pos, 1);
}
new BX_Admin();
?>