<?php
class Box_MobilPay extends Box_Gateway{
	// Setup our Gateway's id, description and other values
	public static $instance;
	function __construct($order_id = 0) {

		$this->order_gateway = 'mobilpay';
		$this->api = box_get_mobilpay_api();
		$this->label = 'MobilPay API';
        //parent::__construct();
        $this->fields = array(
            array(
                'name' =>'account_id',
                'label' =>'Merchant Account ID',
                'type' =>'text',
            ),
        );
		$this->id = "mobilpay";
		$this->description = 'Allow payment via MobilPay';
		parent::__construct();
		$this->method_title = __( "MobilPay", 'sn-wc-mobilpay' );
		$this->method_description = __( "MobilPay Payment Gateway Plug-in for WooCommerce", 'sn-wc-mobilpay' );
		$this->title = __( "MobilPay", 'sn-wc-mobilpay' );
		$this->icon = MOBILPAY_URL . '/img/mobilpay.gif';
		$this->thumbnail        = BOX_IMG_URL . '/mobilpay.png';
        $this->big_thumbnail    = BOX_IMG_URL . '/mobilpay-big.png';

		$this->has_fields = true;

		$confirm_url = home_url('confirm_mobilpay/');
		$this->notify_url        	=  htmlentities($confirm_url);
		$this->confirm_url = htmlentities($confirm_url);
		//$this->service_id = $opt->account_id; // sms
		$this->account_id = $this->api->account_id;

		$this->enabled = $this->api->enabled;
		$this->environment = (boolean) $this->api->test_mode ;
		// Supports the default credit card form
		$this->supports = array(
	               'products',
	               'refunds'
	               );//array( 'default_credit_card_form' );

		// Lets check for SSL

		add_filter('box_order_object_checkout',array($this,'get_mobilpay_order_object'), 10 , 2);
		add_filter('box_checkout_get_redirect', array($this,'mobile_respond_the_redirect_form'), 10 ,3);


	} // End __construct()
	function get_redirect_response1($order, $amount){

		$respond['patch_form'] = $order;

		return $respond;
	}
	function get_redirect_response( $order_id, $amount ) {

        //https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/
        $order = box_get_order($order_id);
		$form =  $this->box_generate_mobilpay_form($order);
        return  array(
            'msg' => 'Check done',
            'success'=> true,
            'patch_form' => $form,
        );

    }

	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function get_mobilpay_order_object($object_order , $gateway){

		if($gateway == 'mobilpay'){
			return self::get_instance();
		}
		return $object_order;
	}

	function redirect_mobipay_gateway($gateway){

		if ($gateway == 'mobilpay') {
	        $job_id = $order_pay['product_id'];
	        $order_id = $order_pay['ID'];

            $amount = $order_pay['total'];
            $form = $this->box_generate_mobilpay_form($order_id, $order_pay);
            $response = array(
                'success' => true,
                'data' => array(
                   // 'url' =>$form->paymentUrl,
                    'ACK' => true,
                    'generate_form' => $form,
                ),
                'paymentType' => 'MOBILPAY'
            );
	    }
    	return $response;
	}
	/**
	* Generate payu button link
	**/
	function box_generate_mobilpay_form($order){


		$paymentUrl = ( $this->environment )
						   ? 'http://sandboxsecure.mobilpay.ro/'
						   : 'https://secure.mobilpay.ro/';
		if ($this->environment ) {
			// $x509FilePath 	= 'wp-content/plugins/sn-wc-mobilpay/Mobilpay/sandbox.'.$this->account_id.'.public.cer';
			$x509FilePath = MOBILPAY_PATH.'/Mobilpay/sandbox.'.$this->account_id.'.public.cer';
		}
		else {
			// $x509FilePath 	= 'wp-content/plugins/sn-wc-mobilpay/Mobilpay/live.'.$this->account_id.'.public.cer';
			$x509FilePath = MOBILPAY_PATH.'/Mobilpay/live.'.$this->account_id.'.public.cer';
		}

		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Abstract.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Invoice.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Address.php';

		//$method = $this->get_post( 'method' );
		$method = 'credit_card';
		$name_methods = array(
		          'credit_card'	      => __( 'Credit Card', 'sn-wc-mobilpay' ),
		          'sms'			        => __('SMS' , 'sn-wc-mobilpay' ),
		          'bank_transfer'		      => __( 'Bank Transfer', 'sn-wc-mobilpay' ),
		          'bitcoin'  => __( 'Bitcoin', 'sn-wc-mobilpay' )
		          );
		switch ($method) {
			case 'sms':
				require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Sms.php';
				$objPmReq = new Mobilpay_Payment_Request_Sms();
				$objPmReq->service 		= $this->service_id;
				break;
			case 'bank_transfer':
				require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Transfer.php';
				$objPmReq = new Mobilpay_Payment_Request_Transfer();
				$paymentUrl .= '/transfer';
				break;
			case 'bitcoin':
				require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Bitcoin.php';
				$objPmReq = new Mobilpay_Payment_Request_Bitcoin();
				$paymentUrl = 'https://secure.mobilpay.ro/bitcoin'; //for both sanbox and live
				break;
			default: // credit_card
				require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Card.php';
				$objPmReq = new Mobilpay_Payment_Request_Card();
				break;
		}
		$currency = $order->currency_code;

		$amount_RON = $this->convertAmountToRON($order->amount, $currency);

		srand((double) microtime() * 1000000);
		$objPmReq->signature 			= $this->account_id;
		$objPmReq->orderId 				= md5(uniqid(rand()));
		$url=  box_get_static_link( 'process-payment');
		$url = add_query_arg( array('paymentType' =>'mobilpay','order-id' => $order->ID), $url );
		///$confirm_url = home_url().'/?idn=mobilpay';
		$objPmReq->returnUrl 	= htmlentities($url);
		$objPmReq->confirmUrl 	= $this->confirm_url;

		if($method != 'sms'){
			$objPmReq->invoice = new Mobilpay_Payment_Invoice();
			$objPmReq->invoice->currency	= $currency;//$customer_order->get_order_currency();//;get_woocommerce_currency();
			$objPmReq->invoice->amount		= sprintf('%.2f',$order->amount);//sprintf('%.2f',$customer_order->order_total);
			$objPmReq->invoice->details		= 'Plata pentru comanda cu ID: '.$order->ID;

			$billingAddress 				= new Mobilpay_Payment_Address();
			$billingAddress->type			= 'person';//$_POST['billing_type'];
			$billingAddress->firstName		= 'Test';
			$billingAddress->lastName		= 'Last name';
			$billingAddress->address		= 'Address test';
			$billingAddress->email			= 'abc@gmail.com';
			$billingAddress->mobilePhone	= '098959595';
			$objPmReq->invoice->setBillingAddress($billingAddress);

			$shippingAddress 				= new Mobilpay_Payment_Address();
			$shippingAddress->type			= 'person';//$_POST['shipping_type'];
			$shippingAddress->firstName		= 'test';
			$shippingAddress->lastName		= 'lastName';
			$shippingAddress->address		= '99 ok';
			$shippingAddress->email			= 'abc@gmail.com';
			$shippingAddress->mobilePhone	= '098959595';
			$objPmReq->invoice->setShippingAddress($shippingAddress);
		}
		global $user_ID;
		$objPmReq->params = array('order_id'=>$order->ID,'customer_id'=>$user_ID,'customer_ip'=>$_SERVER['REMOTE_ADDR'],'method'=>$method);
		$objPmReq->encrypt($x509FilePath);
		//echo "<pre>objPmReq: "; print_r($objPmReq); echo "</pre>";
		return '<form action="'.$paymentUrl.'" method="post" id="frmPaymentRedirect">
				<input type="hidden" name="env_key" value="'.$objPmReq->getEnvKey().'"/>
				<input type="hidden" name="data" value="'.$objPmReq->getEncData().'"/>
				<input type="submit" class="button-alt" id="submit_mobilpay_payment_form" value="'.__('Plateste prin NETOPIA payments', 'sn-wc-mobilpay').'" /> <a class="button cancel" href="'.home_url().'">'.__('Anuleaza comanda &amp; goleste cosul', 'sn-wc-mobilpay').'</a>

			</form>';
	}

	/**
	* Check for valid MobilPay server callback
	**/
	function box_verify_response(){

		require_once MOBILPAY_PATH. '/Mobilpay/Payment/Request/Abstract.php';

		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Card.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Sms.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Transfer.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Bitcoin.php';

		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Request/Notify.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Invoice.php';
		require_once MOBILPAY_PATH.'/Mobilpay/Payment/Address.php';

		$errorCode 		= 0;
		$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_NONE;
		$errorMessage	= '';

		$msg_errors = array('16'=>'card has a risk (i.e. stolen card)', '17'=>'card number is incorrect', '18'=>'closed card', '19'=>'card is expired', '20'=>'insufficient funds', '21'=>'cVV2 code incorrect', '22'=>'issuer is unavailable', '32'=>'amount is incorrect', '33'=>'currency is incorrect', '34'=>'transaction not permitted to cardholder', '35'=>'transaction declined', '36'=>'transaction rejected by antifraud filters', '37'=>'transaction declined (breaking the law)', '38'=>'transaction declined', '48'=>'invalid request', '49'=>'duplicate PREAUTH', '50'=>'duplicate AUTH', '51'=>'you can only CANCEL a preauth order', '52'=>'you can only CONFIRM a preauth order', '53'=>'you can only CREDIT a confirmed order', '54'=>'credit amount is higher than auth amount', '55'=>'capture amount is higher than preauth amount', '56'=>'duplicate request', '99'=>'generic error');

		if ($this->environment ) {
			$privateKeyFilePath 	= MOBILPAY_PATH.'/Mobilpay/sandbox.'.$this->account_id.'private.key';
		}
		else {
			$privateKeyFilePath 	= MOBILPAY_PATH.'/Mobilpay/live.'.$this->account_id.'private.key';
		}

		$uri_confirming = $_SERVER['REQUEST_URI'];
		$is_confirming = strcasecmp($uri_confirming,'/confirm_mobilpay/');
		if( $is_confirming == 0 ){


			if ( strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0){

				if(isset($_POST['env_key']) && isset($_POST['data'])){
					try
					{
						$objPmReq = Mobilpay_Payment_Request_Abstract::factoryFromEncrypted($_POST['env_key'], $_POST['data'], $privateKeyFilePath);
						$action = $objPmReq->objPmNotify->action;

						$params = $objPmReq->params;
						mobilpay_track('Check response:  '.$action . ' order_id :' .$params['order_id']);

						$order_id = $params['order_id'];
						$user = new WP_User( $params['customer_id'] );

						$transaction_id = $objPmReq->objPmNotify->purchaseId;
						if($objPmReq->objPmNotify->errorCode==0){
							$order = new BX_Order($order_id);
							switch($action)    		{
				    			case 'confirmed':
				    			 	bx_process_payment( $order_id );
							    	break;
								case 'confirmed_pending':
								case 'paid_pending':
									$order->update_status('pending','Your payment is currently being processed.');
									//$order->update_status
								case 'canceled':
									$errorMessage = $objPmReq->objPmNotify->errorMessage;
									$message = 	'Thank you for shopping with us. <br />However, the transaction wasn\'t successful, payment wasn\'t received.';
									break;
								case 'credit':
									#cand action este credit inseamna ca banii sunt returnati posesorului de card. Daca s-a facut deja livrare, aceasta trebuie oprita sau facut un reverse.
									//update DB, SET status = "refunded"
									$errorMessage = $objPmReq->objPmNotify->errorMessage;

								    break;
				    		}
						} else {
							$this->bpLog('check_mobilpay_response___fail ');
							$message = $objPmReq->objPmNotify->errorMessage;
							$message_type = 'error';
						}
					}catch(Exception $e){
						$this->bpLog('privateKeyFilePath: '.$privateKeyFilePath);
						$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_TEMPORARY;
						$errorCode		= $e->getCode();
						$errorMessage 	= $e->getMessage();
						$this->bpLog('check_mobilpay_response___fail '. $errorMessage);
					}
				} else 	{
					$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
					$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
					$errorMessage 	= 'mobilpay.ro posted invalid parameters';
				}
			} else {
				$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
				$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_METHOD;
				$errorMessage 	= 'invalid request method for payment confirmation';
			}
			// $this->bpLog('errorType: '.$errorType.' -- errorCode: '.$errorCode.' -- errorMessage: '.$errorMessage);
			header('Content-type: application/xml');
			echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			if($errorCode == 0){
				echo "<crc>{$errorMessage}</crc>";
			} else {
				echo "<crc error_type=\"{$errorType}\" error_code=\"{$errorCode}\">{$errorMessage}</crc>";
			}
			// wc_empty_cart();
			die();
		} // end check uri of confirm_page
	}


	// Check if we are forcing SSL on checkout pages
	// Custom function not required by the Gateway
	public function do_ssl_check() {
		if( $this->enabled == "yes" ) {
			if( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
				echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";
			}
		}
	}

	/**
	 * Get post data if set
	 */
	private function get_post( $name ) {
		if ( isset( $_REQUEST[ $name ] ) ) {
			return $_REQUEST[ $name ];
		}
		return null;
	}

	public function convertAmountToRON($amount, $currency){
		$rates = array();
		if(strtoupper($currency) == 'RON'){
			$new_amount = $amount;
		}
		else{
			$new_amount = null;
			if($feed = @file_get_contents('http://api.fixer.io/latest?base=RON')){
				$rates=json_decode($feed, true);
				$rates_RON = $rates['rates'];
				//echo "<pre>currency: "; print_r($currency); echo "</pre>";
				//echo "<pre>Rates: "; print_r($rates_RON); echo "</pre>";
				if(isset($rates_RON[$currency])){
					$rate_RON_cur = $rates_RON[$currency];
					$new_amount = $amount/$rate_RON_cur;
				}
			}
		}

		return $new_amount;
	}

	public function bpLog($contents,$file=false){
		if(!$file)	$file = MOBILPAY_PATH.'/bplog.txt';
		file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

		if (is_array($contents))
			$contents = var_export($contents, true);
		else if (is_object($contents))
			$contents = json_encode($contents);

		file_put_contents($file, $contents."\n", FILE_APPEND);
	}

}
new Box_MobilPay();