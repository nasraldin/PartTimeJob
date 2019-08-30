<?php
Class BX_Order {
	public static $instance;
	public $use_sandbox;
	public $post_type;
	public $post_status;
	public $redirect_link;
	public $order_title;
	public $order_gateway; // buy via credit or paypal/stripe ...
	public $pack_type; // prmium post or buy_credit.
	public $receiver_email;
	public $is_realmode;
	public $currency_code;
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function __construct($order_id = 0){
		$this->post_type = ORDER;
		$this->is_realmode = $this->is_realmode();
		$this->receiver_email = '';
		$this->currency_code = $this->get_currency_code();
		$this->redirect_link = box_get_static_link('process-payment');
		if($order_id){
			$metas = $this->meta_fields();

			foreach ($metas as $meta) {
				$this->$meta = get_post_meta($porder_id, $meta, true);
			}
			$post->pack_id = get_post_meta($order_id,'pack_id', true);
		}

	}
	function get_redirect_link(){
		return $this->redirect_link;
	}
	function get_currency_code(){
		global $box_currency;
		return isset($box_currency->code) ? $box_currency->code : 'USD';
	}
	function create($args) {
		$args['post_type'] = $this->post_type;
		return wp_insert_post($args);
	}
	function update_order($staus,$title){
		$args = array(
			'ID' => $this->ID,
			'post_status' => $staus,
			'post_title' => $title
		);
		wp_update_post($args);
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
			'amount', // price of this order
			'payer_id', // user id of
			'payer_email',
			'receiver_id', // user id of receiver
			'receiver_email',
			'customer_address',
			'order_gateway', // paypal, stripe or cash.
			'order_type', // buy_credit, premium_post, withdraw
			'is_realmode', //sandbox or live
			'currency_code',
			'discount_code',
			'discount',

		);
	}
	function is_realmode(){
		global $checkout_mode; // 1= real mode. 0 or empty => sandbox.

		return (int) $checkout_mode;
	}
	function get_order($post) {

		if( is_numeric($post) ){
			$post = get_post($post);
		}
		if( $post && !is_wp_error($post) ){
			$metas = $this->meta_fields();

			foreach ($metas as $meta) {
				$post->$meta = get_post_meta($post->ID, $meta, true);
			}
			$post->pack_id = get_post_meta($post->ID,'pack_id', true);
		}

		return (object)$post;
	}
	function get_total(){
		return $this->amout;
	}
	function get_pre_order_fee(){
		return $this->amout;
	}
	function get_order_number(){
		return $this->ID;
	}

	function get_cancel_order_url(){
		return '';
	}
	function get_package($packge_id){
		$post = get_post($packge_id);
		$metas = array('price','sku','pack_type');
		foreach ($metas as $meta) {
			$post->$meta = get_post_meta($packge_id, $meta, true);
		}
		return (object)$post;
	}
	function get_amount($package_id){
       	return (float) get_post_meta($package_id, 'price', true);
    }

    function create_pending_order($package_id, $project_id = 0){
		$curren_user = wp_get_current_user();
		$pack_type = get_post_meta($package_id, 'pack_type', true);
		$order_title = $curren_user->user_email . ' pay   '.$pack_type.'  via '.$this->order_gateway . '(' .$this->get_amount( $package_id ) .')';

		if( $pack_type == 'buy_credit')
			$order_title =$curren_user->user_email . ' buy credit  via '.$this->order_gateway . '(' .$this->get_amount( $package_id ) .')';
		else if( $pack_type == "premium_post"){
			$order_title =$curren_user->user_email . ' pay for premium post  '.$this->order_gateway . '(' .$this->get_amount( $package_id ) .')';
		}
		$args = array(
			'post_title' => $order_title,
			'post_status' => 'pending',
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amount' => $this->get_amount( $package_id ),
				'payer_id' => $curren_user->ID,
				'is_realmode' => $this->is_realmode(),
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=> $pack_type,// buy_credit, premium_post, withdraw.
				'order_gateway' 	=>$this->order_gateway, // cash, credit, stripe, paypal
				'currency_code' => $this->currency_code,
				'receiver_email' => $this->receiver_email,
				'pack_id' => $package_id,
			),
		);
		if( $project_id ) {
			$args['meta_input']['pay_premium_post'] = $project_id;
		}

		$order_id =  $this->create($args);
		if( $order_id && ! is_wp_error($order_id) ){
			do_action('after_create_pending_order', (object) $args); // not paypal in this because PayPal override this method.
			do_action('after_create_pending_order_via_'.$this->order_gateway,$order_id, (object) $args);
		}
		return $order_id;
	}
	function create_custom_pending_order($args){

		$curren_user = wp_get_current_user();
		$args = array(
			'post_title' => $args['post_title'],
			'post_status' => 'pending',
			'post_content' => $args['post_content'],
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amount' => $args['amount'],
				'payer_id' => $curren_user->ID,
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=>$args['order_type'], // buy credit, withdraw
				'order_gateway' 	=>$args['order_gateway'],
				'currency_code' => $this->currency_code,
				//'receiver_id' => 1,// need to update - default is admin.
				'receiver_email' => $this->receiver_email,
				'is_realmode' => $this->is_realmode,

			)
		);
		return $this->create($args);
	}
	function create_deposit_pending_order_by_package($package_id, $gate_way){
		$curren_user = wp_get_current_user();
		$args = array(
			'post_title' =>  'Deposit Credit By Pack ID:'.$package_id,
			'post_status' => 'pending',
			'post_content' => 'Deposit Credit By Pack ID:'.$package_id .'. Gateway: '.$gate_way,
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amount' => $this->get_amount($package_id),
				'payer_id' => $curren_user->ID,
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=>'buy_credit', // buy credit, withdraw
				'currency_code' => $this->get_currency_code(),
				'order_gateway' 	=> $gate_way,
				'is_realmode' => $this->is_realmode(),
				'pack_id' => $package_id,

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
				'amount' => '',
				'project_id' => '',
				'order_type' => '', //buy credit, withdraw,membership,premium_post
				'order_gateway' => '',//paypal,stripe, ...
			)
		);
		$new_args = wp_parse_args( $args, $default );
		$new_args['post_status'] = 'publish';
		$new_args['post_type'] = $this->post_type;
		$new_args['meta_input']['is_realmode'] = $this->is_realmode;
		return wp_insert_post($new_args);
	}
	/**
	 * create orders
	 * This is a cool function
	 * @author boxtheme
	 * @version 1.0
	 * @param   [type] $package_id [description]
	 * @return  [type]             [description]
	 */
	function create_draft_order( $package_id, $project_id = 0 ){
		$pack_type = get_post_meta( $package_id, 'pack_type', true );
		$current_user = wp_get_current_user();
		$args = array(
			'post_title' => $current_user->user_email . ' buy credit  via '.$this->order_gateway . '(' .$this->get_amount( $package_id ) .')',
			'post_status' => 'draft',
			'author' => $current_user->ID,
			'meta_input' => array(
				'amount' => $this->get_amount( $package_id ),
				'payer_id' => $current_user->ID,
				'payer_email' => $current_user->user_email ,
				'order_type' 	=> $pack_type, //deposit credit, membership.
				'order_gateway' 	=>$this->order_gateway, // cash, paypal, stripe...
				'pack_id' => $package_id,
				'currency_code' => $this->get_currency_code(),
				'receiver_email' => $this->receiver_email,
				'is_realmode' => $this->is_realmode,
				)
			);
		if( $project_id ) {
			$args['meta_input']['pay_premium_post'] = $project_id;
		}
		if($pack_type == 'membership'){
			$args['post_title'] =  $current_user->user_email . ' subscription via '.$this->order_gateway . '(' .$this->get_amount( $package_id ) .')';
		}

		return $this->create($args);
	}
	function create_deposit_draft_order($amount, $api){
		$current_user = wp_get_current_user();
		$is_realmode  = 0;

		if( ! $api->test_mode ){
			$is_realmode = 1;
		}
		$args = array(
		'post_title' =>' Depost Order',
		'post_status' => 'draft',
		'post_type' => $this->post_type,
		'author' => $current_user->ID,
		'meta_input' => array(
			'amount' => $amount,
			'payer_id' => $current_user->ID,
			'payer_email' => $current_user->user_email ,
			'order_type' 	=> 'deposit', //deposit credit, membership.
			'order_gateway' 	=>$api->id, // cash, paypal, stripe...
			'currency_code' => $this->get_currency_code(),
			'receiver_email' => $this->receiver_email,
			'is_realmode' => $is_realmode,
			)
		);
		return wp_insert_post($args);
	}
	function create_membership_draft_order($package_id, $api){
 		$curren_user 	= wp_get_current_user();
        $order 			= $this->get_package($package_id);
        $pack_type 		= get_post_meta($package_id, 'pack_type', true);
        $is_realmode  	= 0;

		if( ! $api->test_mode ){
			$is_realmode = 1;
		}

        $args = array(
            'post_title' => 'Membership order',
            'post_status' => 'draft',
            'author' => $curren_user->ID,
            'meta_input' => array(
                'amount' => $this->get_amount( $package_id ),
                'payer_id' => $curren_user->ID,
                'is_realmode' => $is_realmode,
                'payer_email' => $curren_user->user_email ,
                'order_type'    => $pack_type,// buy_credit, premium_post, withdraw.
                'order_gateway'     =>$api->id, // cash, credit, stripe, paypal
                'currency_code' => $this->currency_code,
                'receiver_email' => $this->receiver_email,
                'pack_id' => $package_id,
            ),
        );

        $order_id =  $this->create($args);

        return $order_id;
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
				'amount' => $emp_pay,
				'pay_for_project' => $project->ID,
				'order_type' => 'deposit',
				'order_gateway' => 'credit',
				'is_realmode' => $this->is_realmode,
				'currency_code' => $this->get_currency_code(),
			)
		);

		wp_insert_post($args_order_emp); // orer for employer

		$args_order_fre = array(
			'post_title' => sprintf( __('The fund transfer to freelancer on project %s','boxtheme'),$project->post_title ),
			'post_status' =>'pending', // will be publish after the project done - release action.
			'post_author' => $freelancer_id,
			'post_type' => $this->post_type,
			'meta_input' => array(
				'amount' => $fre_receive,
				'get_project_id' => $project->ID,
				'order_type' => 'receive',
				'order_gateway' => 'credit',
				'is_realmode' => $this->is_realmode,
				'currency_code' => $this->get_currency_code(),
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
				'amount' => $bid_price,
				'project_id' => $project->ID,
				'order_type' => 'undeposit',
				'order_gateway' => 'credit',
				'is_realmode' => $this->is_realmode,
				'currency_code' => $this->currency_code,
			)
		);
		return wp_insert_post($args_order);
	}

}
function box_get_order($order_id){
	$order = BX_Order::get_instance()->get_order($order_id);
	return $order;
}
function box_get_pack_order($pack_id){
	$order = BX_Order::get_instance()->get_package($pack_id);
	return $order;
}