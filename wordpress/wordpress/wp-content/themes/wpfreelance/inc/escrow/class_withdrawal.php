<?php

class Box_Withdrawal{
	public $post_type;
	private $is_realmode;
	private $amount;
	static $instance;
	function __construct(){
		$this->post_type  = 'withdrawal';
		$this->is_realmode = (int) $this->is_realmode(); // 1= real mode. 0 or empty => sandbox.
		$this->amount = 0;

	}

	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function is_realmode(){
		global $checkout_mode; // 1= real mode. 0 or empty => sandbox.

		return  $checkout_mode;
	}
	function save_withdrawal($args){

		$curren_user = wp_get_current_user();

		$args = array(
			'post_title' => $args['post_title'],
			'post_type' => $this->post_type,
			'post_status' => 'pending',
			'post_content' => $args['post_content'],
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amount' => $args['amount'],
				'is_realmode' => $this->is_realmode,
			)
		);
		return wp_insert_post($args);

	}
	function get_withdrawal_mode($id){
		return get_post_meta( $id,'is_realmode', true);

	}
	function approve_withdrawal_status($id){

		return wp_update_post( array(
			'ID' => $id,
			'post_status' =>'publish'
			)
		);

	}
	function get_withdrawal($post) {

		if( is_numeric($post) ){
			$post = get_post($post);
		}

		$post->amount = get_post_meta($post->ID,'amount', true);
		$post->is_realmode = (int) get_post_meta($post->ID,'is_realmode', true);

		return (object)$post;
	}

	function do_after_withdraw(){

	}

}


function register_post_type_withdrawal(){
	$labels = array(
		'name'               => _x( 'Withdrawals', 'post type general name', 'boxtheme' ),
		'singular_name'      => _x( 'Withdrawal', 'post type singular name', 'boxtheme' ),
		'menu_name'          => _x( 'Withdrawals', 'admin menu', 'boxtheme' ),
		'name_admin_bar'     => _x( 'Withdrawal', 'add new on admin bar', 'boxtheme' ),
		'add_new'            => _x( 'Add New', 'order', 'boxtheme' ),
		'add_new_item'       => __( 'Add New Withdrawal', 'boxtheme' ),
		'new_item'           => __( 'New Withdrawal', 'boxtheme' ),
		'edit_item'          => __( 'Edit Withdrawal', 'boxtheme' ),
		'view_item'          => __( 'View Withdrawal', 'boxtheme' ),
		'all_items'          => __( 'All Withdrawals', 'boxtheme' ),
		'search_items'       => __( 'Search Withdrawals', 'boxtheme' ),
		'parent_item_colon'  => __( 'Parent Withdrawals:', 'boxtheme' ),
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

	register_post_type( 'withdrawal', $args );
}


class Box_Withdrawal_Backend {
	function __construct(){
		add_action('pre_post_update', array( $this, 'disable_update_withdrawal_post'), 10 ,2  );
		add_action('edit_form_after_editor', array($this,'show_detail_withdrawal') );
	}
	function disable_update_withdrawal_post($withdraw_id, $post_data){

		if( isset($post_data['post_type']) && $post_data['post_type'] == 'withdrawal'){
			wp_die('This action is disabled.');
		}
	}
	function show_detail_withdrawal($post){
		if($post->post_type == 'withdrawal' ){	?>
			<style type="text/css">
				#side-sortables { display: none; }
			</style>
			<?php
		}
	}

}

if( is_admin() && ! wp_doing_ajax() ) {
	new Box_Withdrawal_Backend();
}
?>