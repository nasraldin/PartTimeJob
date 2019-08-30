<?php
class BX_Cash extends Box_Gateway{
	//public $redirect_link;
	public $order_gateway;
	public static $instance;
	function __construct(){

		$this->order_gateway = 'cash';
		$this->id = 'cash';
		$this->api = box_get_cash();
		$this->enabled = $this->api->enabled;

		$this->label = 'CASH';
		$this->description = 'Allow checkout via cash option. Offiline';
		$this->api_description = 'Cash settings';
		$this->thumbnail = BOX_IMG_URL .'/cash.svg';
		$this->big_thumbnail    = BOX_IMG_URL . '/cash.png';
        $this->fields = array(
        	array(
        		'name' =>'description',
        		'label' =>'Description',
        		'type' => 'textarea',
        	)
        );

		parent::__construct();

	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}
	function get_redirect_link($order_id = 0){

		return add_query_arg( array('type'=>'cash','order_id'=>$order_id), $this->redirect_link );
	}
	function form_checkout(){?>
		<div class="payment-item payment-cash-item">

			<div class="payment-item-radio">
			    <label class="box-radio box-cash is-not-empty js-valid" for="cash">
			        <input type="radio" name="type" value="cash" id="cash" required="required" class="checkbox-gateway">
			        <span class="check"></span>
			        <span class="payment-title">Cash on Delivery</span>
			       	<img src="<?php echo BOX_IMG_URL;?>/cash.svg" class="payment-img" height="23" alt="">
			    	<i class="ico-valid"></i>
				</label>
			</div>
			<div class="payment-fields cash">

				<img class="js-complete_order" src="<?php echo BOX_IMG_URL;?>/cash.png">
			    <div class="text-info">
			        Click Complete your order to finish your purchase.
			    </div>
			    <form method="post" class="<?php echo $this->form_class;?>">
		            <input type="hidden" name="_gateway" value="cash">
		            <button type="submit" class="btn-submit-payment">Submit</button>
		        </form>
			</div>
		</div>
		<?php

	}
	function create_temp_pending_order($pack_id){

		$curren_user = wp_get_current_user();

		$order_title = $curren_user->user_email . ' deposit   credit  via '.$this->order_gateway . '(' .$this->get_amount( $pack_id ) .')';


		$order_title = $curren_user->user_email . ' buy credit  via '.$this->order_gateway . '(' .$this->get_amount( $pack_id ) .')';

		$args = array(
			'post_title' => $order_title,
			'post_status' => 'pending',
			'author' => $curren_user->ID,
			'meta_input' => array(
				'amount' => $this->get_amount( $pack_id ),
				'payer_id' => $curren_user->ID,
				'is_realmode' => $this->is_realmode,
				'payer_email' => $curren_user->user_email ,
				'order_type' 	=> 'deposit',// buy_credit, premium_post, withdraw.
				'order_gateway' 	=>$this->order_gateway, // cash, credit, stripe, paypal

				//'receiver_email' => $this->receiver_email,
				'pack_id' => $pack_id,
			),
		);

		$order_id =  $this->create($args);
		if( $order_id && ! is_wp_error($order_id) ){
			do_action('after_create_pending_order_via_cash',$order_id, (object) $args);
		}
		return $order_id;
	}
	// function box_verify_response(){
	// 	$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
	// 	if($order_id){
	// 		$order = box_get_order();
	// 	}
 //    }

}
function box_get_cash(){

	$payment =  BX_Option::get_instance()->get_group_option('payment');
    $default = array(
    	'id' => 'cash',
        'description' => '',
        'enabled' => 0,
        'possition' => 100,
        'test_mode' => 1,

    );
    $cash =  (object) wp_parse_args($payment->cash, $default);
    return $cash;
}
new BX_Cash();