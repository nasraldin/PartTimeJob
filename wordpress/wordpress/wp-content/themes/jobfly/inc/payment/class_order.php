<?php
Class BX_Order {
	public $use_sandbox;
	public $post_type;
	public $post_status;
	public static $instance;
	public $redirect_link;
	public $order_title;
	public $payment_type; // buy via credit or paypal/stripe ...
	public $pack_type; // prmium post or buy_credit.
	public $receiver_email;
	public $mode;
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function __construct(){
		$this->post_type = ORDER;
		$this->use_sandbox = $this->get_sandbox_mode();
		$this->receiver_email = '';

		$this->redirect_link = box_get_static_link('process-payment');

	}
	function get_redirect_link(){
		return $this->redirect_link;
	}
	function create($args) {
		$args['post_type'] = $this->post_type;
		return wp_insert_post($args);
	}
	function approve($order_id){
		return wp_update_post( array(
			'ID' => $order_id,
			'post_status' =>'publish'
			)
		);
	}
	function meta_fields(){
		return array(
			'amout', // price of this order
			'payer_id', // user id of
			'payer_email',
			'receiver_id', // user id of receiver
			'receiver_email',
			'customer_address',
			'payment_type', // paypal, stripe or cash.
			'order_type', // buy_credit, premium_post, withdraw
			'order_mode', //sandbox or live

		);
	}
	function get_sandbox_mode(){
		global $checkout_mode; // 1= real mode. 0 or empty => sandbox.

		return ! $checkout_mode;
	}
	function get_order($post) {

		if( is_numeric($post) ){
			$post = get_post($post);
		}
		$metas = $this->meta_fields();
		foreach ($metas as $meta) {
			$post->$meta = get_post_meta($post->ID, $meta, true);
		}
		return (object)$post;
	}
	function get_package($packge_id){
		$post = get_post($packge_id);
		$metas = array('price','sku','pack_type');
		foreach ($metas as $meta) {
			$post->$meta = get_post_meta($packge_id, $meta, true);
		}
		return (object)$post;
	}
	function get_amout($package_id){
       	return (float) get_post_meta($package_id, 'price', true);
    }

    function create_pending_order($package_id, $project_id = 0){
		$curren_user = wp_get_current_user();
		$pack_type = get_post_meta($package_id, 'pack_type', true);
		$order_title = $curren_user->user_email . ' pay   '.$pack_type.'  via '.$this->payment_type . '(' .$this->get_amout( $package_id ) .')';

		if( $pack_type == 'buy_credit')
			$order_title =$curren_user->user_email . ' buy credit  via '.$this->payment_type . '(' .$this->get_amout( $package_id ) .')';
		else if( $pack_type == "premium_post"){
			$order_title =$curren_user->user_email . ' pay for premium post  '.$this->payment_type . '(' .$this->get_amout( $package_id ) .')';
		}
		$args = array(
			'post_title' => $order_title,
			'post_status' => 'pending',
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amout' => $this->get_amout( $package_id ),
				'payer_id' => $curren_user->ID,
				'order_mode' => $this->mode,
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=> $pack_type,// buy_credit, premium_post, withdraw.
				'payment_type' 	=>$this->payment_type, // cash, credit, stripe, paypal
				//'receiver_id' => 1,// need to update - default is admin.
				'receiver_email' => $this->receiver_email,
				'pack_id' => $package_id,
			),
		);
		if( $project_id ) {
			$args['meta_input']['pay_premium_post'] = $project_id;
		}
		return $this->create($args);
	}
	function create_custom_pending_order($args){

		$curren_user = wp_get_current_user();
		$args = array(
			'post_title' => $args['post_title'],
			'post_status' => 'pending',
			'post_content' => $args['post_content'],
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amout' => $args['amout'],
				'payer_id' => $curren_user->ID,
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=>$args['order_type'], // buy credit, withdraw
				'payment_type' 	=>$args['payment_type'],
				//'receiver_id' => 1,// need to update - default is admin.
				'receiver_email' => $this->receiver_email,
				'order_mode' => $this->use_sandbox,

			)
		);
		return $this->create($args);
	}
	function create_order( $args ){
		$curren_user = wp_get_current_user();

		$default =  array(
			'post_title' =>'Pay service',
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amout' => '',
				'project_id' => '',
				'order_type' => '',
				'payment_type' => '',
			)
		);
		$new_args = wp_parse_args( $args, $default );
		$new_args['post_status'] = 'publish';
		$new_args['post_type'] = $this->post_type;
		$new_args['meta_input']['order_mode'] = $this->use_sandbox;
		return wp_insert_post($new_args);
	}
	/**
	 * create orders
	 * This is a cool function
	 * @author danng
	 * @version 1.0
	 * @param   [type] $package_id [description]
	 * @return  [type]             [description]
	 */
	function create_draft_order( $package_id, $project_id = 0 ){
		$pack_type = get_post_meta( $package_id, 'pack_type', true );
		$current_user = wp_get_current_user();
		$args = array(
			'post_title' => $current_user->user_email . ' buy credit  via '.$this->payment_type . '(' .$this->get_amout( $package_id ) .')',
			'post_status' => 'draft',
			'author' => $current_user->ID,
			'meta_input' => array(
				'amout' => $this->get_amout( $package_id ),
				'payer_id' => $current_user->ID,
				'payer_email' => $current_user->user_email ,
				'order_type' 	=> $pack_type,
				'payment_type' 	=>$this->payment_type, // cash, paypal, stripe...
				'pack_id' => $package_id,
				//'receiver_id' => 1,// need to update - default is admin.
				'receiver_email' => $this->receiver_email,
				'order_mode' => $this->mode,
				)
			);
		if( $project_id ) {
			$args['meta_input']['pay_premium_post'] = $project_id;
		}
		return $this->create($args);
	}
	/**
	 * this method run after employer assign 1 job to 1 freelancer.
	*/
	function create_deposit_orders( $emp_pay, $fre_receive, $project, $freelancer_id ){ //$bid_price, $project ){

		$current_user = wp_get_current_user();

		$args_order_emp = array(
			'post_title' => sprintf( __('Deposit for the project %s','boxtheme'),$project->post_title ),
			'post_status' =>'publish',
			'post_author' => $current_user->ID,
			'post_type' => $this->post_type,
			'meta_input' => array(
				'amout' => $emp_pay,
				'pay_for_project' => $project->ID,
				'order_type' => 'deposit',
				'payment_type' => 'credit',
				'order_mode' => $this->mode,
			)
		);

		wp_insert_post($args_order_emp); // orer for employer

		$args_order_fre = array(
			'post_title' => sprintf( __('The fund transfer to freelancer on project %s','boxtheme'),$project->post_title ),
			'post_status' =>'pending', // will be publish after the project done - release action.
			'post_author' => $freelancer_id,
			'post_type' => $this->post_type,
			'meta_input' => array(
				'amout' => $fre_receive,
				'get_project_id' => $project->ID,
				'order_type' => 'receive',
				'payment_type' => 'credit',
				'order_mode' => $this->mode,
			)
		);

		$order_id = wp_insert_post($args_order_fre); // orer for freelancer
		update_post_meta( $project->ID,  'fre_order_id', $order_id );
	}
	/**
	 * This method run after admin make a descision for employer win and in disputing time.
	*/
	function create_undeposit_order($bid_price, $project){
		global $user_ID;
		$args_order = array(
			'post_title' => sprintf( __('Refund for the project %s','boxtheme'),$project->post_title ),
			'post_status' =>'publish',
			'author' => $user_ID,
			'post_type' => $this->post_type,
			'meta_input' => array(
				'amout' => $bid_price,
				'project_id' => $project->ID,
				'order_type' => 'undeposit',
				'payment_type' => 'credit',
				'order_mode' => $this->mode,
			)
		);
		return wp_insert_post($args_order);
	}

}
