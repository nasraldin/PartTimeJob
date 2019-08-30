<?php
class BX_Cash extends BX_Order{
	//public $redirect_link;
	public $payment_type;
	function __construct(){
		parent::__construct();
		$this->payment_type = 'cash';

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

}