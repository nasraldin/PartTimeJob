<?php
/**
 * PayFast Payment Gateway
 *
 * Provides a PayFast Payment Gateway.
 *
 * @class  woocommerce_payfast
 * @package WooCommerce
 * @category Payment Gateways
 * @author WooCommerce
 */
define('WC_VERSION ', '1.0');
class Box_PayFast  extends Box_Gateway {
	public static $instance;
	/**
	 * Version
	 *
	 * @var string
	 */
	public $version;
	public $merchant_id;
	public $pass_phrase;
	public $url;
	public $validate_url;
	public $title;
	public $response_url;
	public $send_debug_email;
	public $description;
	public $enabled;
	public $enable_logging;
	/**
	 * @access protected
	 * @var array $data_to_send
	 */
	protected $data_to_send = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->version = 1.0;
		$this->id = 'payfast';
		$this->method_title       = __( 'PayFast', 'woocommerce-gateway-payfast' );
		/* translators: 1: a href link 2: closing href */
		$this->method_description = sprintf( __( 'PayFast works by sending the user to %1$sPayFast%2$s to enter their payment information.', 'woocommerce-gateway-payfast' ), '<a href="http://payfast.co.za/">', '</a>' );
		$this->icon               = WP_PLUGIN_URL . '/' . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/assets/images/icon.png';
		$this->debug_email        = get_option( 'admin_email' );
		$this->available_countries  = array( 'ZA' );
		$this->available_currencies = (array)apply_filters('woocommerce_gateway_payfast_available_currencies', array( 'ZAR' ) );

		// Supported functionality
		$this->supports = array(
			'products',
			'pre-orders',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change', // Subs 1.x support
			//'subscription_payment_method_change_customer', // see issue #39
		);


		if ( ! is_admin() ) {
			$this->setup_constants();
		}
		$this->api = box_get_payfast();
		$this->fields = array(
            array(
                'name' =>'live_merchant_id',
                'label' =>'Live Merchant ID',
                'type' =>'text',
            ),
            array(
                'name' =>'live_merchant_key',
                'label' =>'Live Merchant Key',
                'type' =>'text',
            ),
            array(
                'name' =>'test_merchant_id',
                'label' =>'Tet  Merchant ID',
                'type' =>'text',
            ),
             array(
                'name' =>'test_merchant_key',
                'label' =>'Tet  Merchant Key',
                'type' =>'text',
            ),
        );
		$this->description = 'Allow payment via PayFast';
		$this->label = 'PayFast API';
		parent::__construct();
		// Setup default merchant data.

		//$this->merchant_id      = $this->api->merchant_id; // $this->get_option( 'merchant_id' ); 10010645
		//$this->merchant_key     = $this->api->merchant_key; //$this->get_option( 'merchant_key' );77g8019wcqjj1

		$this->merchant_id  = ($this->test_mode) ? $this->api->test_merchant_id : $this->api->live_merchant_id;
    	$this->merchant_key  = ($this->test_mode) ? $this->api->test_merchant_key : $this->api->live_merchant_key;

		//$this->pass_phrase      = $this->get_option( 'pass_phrase' );
		$this->url              = 'https://www.payfast.co.za/eng/process'; //https://sandbox.payfast.co.za/eng/process
		$this->validate_url     = 'https://www.payfast.co.za/eng/query/validate';
		$this->title            = $this->get_option( 'title' );
		$this->response_url	    = box_get_static_link('process-payment');
		$this->send_debug_email = 'yes' === $this->get_option( 'send_debug_email' );


		if(empty($this->merchant_id) || empty($this->merchant_key))
			$this->enabled = 0;


		$this->enable_logging   = 'yes' === $this->get_option( 'enable_logging' );

		// Setup the test data, if in test mode.
		if (  $this->test_mode ) {
			$this->url          = 'https://sandbox.payfast.co.za/eng/process';
			$this->validate_url = 'https://sandbox.payfast.co.za/eng/query/validate';

		} else {
			$this->send_debug_email = false;
		}
	}
	static function get_instance(){
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
	function get_option($name){
		return get_option($name);
	}
	function form_checkout(){?>
		    <div class="payment-item payment-payfast-item">
		        <div class="payment-item-radio">
		            <label class="box-radio box-payfast is-not-empty js-valid" for="payfast">
		                <input type="radio" name="type" value="payfast" id="payfast" required="required" class="checkbox-gateway">
		                <span class="check"></span>
		                    <img src="<?php echo BOX_IMG_URL;?>/payfast.png" class="payfast-img" height="23" alt="">
		            <i class="ico-valid"></i></label>
		        </div>

		        <div  class="payment-fields payfast">
		            <img class="js-complete_order" src="<?php echo BOX_IMG_URL;?>/payfast.png" alt="">
		                <div class="text-info">
		                    You will be redirected to PayFast to complete your purchase securely.
		                </div>
		        </div>
		        <form method="post" class="df-box-checkout-js form_js_payfast">
		            <input type="hidden" name="_gateway" value="payfast">
		            <button type="submit" class="btn-submit-payment btn-js-payfast">Submit</button>
		        </form>
		    </div>
	    	<?php

	}


	/**
	 * Generate the PayFast button link.
	 *
	 * @since 1.0.0
	 */
	function get_cancel_order_url(){
		return home_url();
	}
	function get_redirect_response( $order_id, $amount ) {

        //https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/
        $order = box_get_order($order_id);
		$form =  $this->generate_payfast_form($order);
        return  array(
            'msg' => 'Check done',
            'success'=> true,
            'patch_form' => $form,
        );

    }
	public function generate_payfast_form( $order ) {
		//$order         = wc_get_order( $order_id );

		$order_id = $order->ID;
		// Construct variables for post
		$this->data_to_send = array(
			// Merchant details
			'merchant_id'      => $this->merchant_id,
			'merchant_key'     => $this->merchant_key,
			'return_url'       => $this->get_return_url( $order ),
			'cancel_url'       => $this->get_cancel_order_url(),
			'notify_url'       => $this->get_response_url($order),


			// Item details
			'm_payment_id'     => ltrim( $order->ID, _x( '#', 'hash before order number', 'woocommerce-gateway-payfast' ) ),
			'amount'           => $order->amount,
			'item_name'        => get_bloginfo( 'name' ) . ' - ' . $order->ID,
			/* translators: 1: blog info name */
			'item_description' => sprintf( __( 'New order from %s', 'woocommerce-gateway-payfast' ), get_bloginfo( 'name' ) ),

			// Custom strings
			'custom_str1'      => 'oder_abc',
			'custom_str2'      => 'WooCommerce/' . BOX_VERSION . '; ' . get_site_url(),
			'custom_str3'      => $order_id,
			'source'           => 'WPFreelance-Theme',
		);

		// add subscription parameters
		if ( $this->order_contains_subscription( $order_id ) ) {
			// 2 == ad-hoc subscription type see PayFast API docs
			$this->data_to_send['subscription_type'] = '2';
		}

		if ( function_exists( 'wcs_order_contains_renewal' ) && wcs_order_contains_renewal( $order ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $order_id );
			// For renewal orders that have subscriptions with renewal flag,
			// we will create a new subscription in PayFast and link it to the existing ones in WC.
			// The old subscriptions in PayFast will be cancelled once we handle the itn request.
			if ( count ( $subscriptions ) > 0 && $this->_has_renewal_flag( reset( $subscriptions ) ) ) {
				// 2 == ad-hoc subscription type see PayFast API docs
				$this->data_to_send['subscription_type'] = '2';
			}
		}

		// pre-order: add the subscription type for pre order that require tokenization
		// at this point we assume that the order pre order fee and that
		// we should only charge that on the order. The rest will be charged later.
		if ( $this->order_contains_pre_order( $order_id )
		     && $this->order_requires_payment_tokenization( $order_id ) ) {
			$this->data_to_send['amount']            = $this->get_pre_order_fee( $order_id );
			$this->data_to_send['subscription_type'] = '2';
		}

		$payfast_args_array = array();
		foreach ( $this->data_to_send as $key => $value ) {
			$payfast_args_array[] = '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
		}

		$form =  $this->get_sample_payfast($order);

		return $form;

		// return '<form action="' . esc_url( $this->url ) . '" method="post" id="payfast_payment_form">
		// 		' . implode( '', $payfast_args_array ) . '
		// 		<input type="submit" class="button-alt" id="submit_payfast_payment_form" value="' . __( 'Pay via PayFast', 'woocommerce-gateway-payfast' ) . '" /> <a class="button cancel" href="' . $this->get_cancel_order_url() . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-gateway-payfast' ) . '</a>

		// 	</form>';
	}

	function create_temp_pending_order($pack_id){
		global $user_ID;
		$order_id = $this->create_deposit_pending_order_by_package($pack_id,'PayFast');

		$order = box_get_order($order_id);
		$form = $this->generate_payfast_form($order);
		return $form;
	}
	function html_payfast_gateway_sample($order){
		$data = $this->get_data($order); ?>
		<form action="<?php echo $this->url;?>" method="POST">
			<input type="hidden" name="merchant_id" value="<?php echo $data->merchant_id;?>">
			<input type="hidden" name="merchant_key" value="<?php echo $data->merchant_key;?>">
			<input type="hidden" name="return_url" value="<?php echo $data->return_url;?>">
			<input type="hidden" name="cancel_url" value="<?php echo $data->cancel_url;?>">
			<input type="hidden" name="notify_url" value="<?php echo $data->notify_url;?>">
			<input type="hidden" name="m_payment_id" value="01AB">
			<input type="hidden" name="amount" value="<?php echo $data->amount;?>">
			<input type="hidden" name="item_name" value="<?php echo $data->item_name;?>">
			<input type="hidden" name="item_description" value="<?php echo $data->item_description;?>">
			<input type="hidden" name="custom_int1" value="<?php echo $data->custom_int1;?>">
			<input type="hidden" name="custom_str1" value="<?php echo $data->custom_str1;?>">
			<input type="hidden" name="custom_str3" value="<?php echo $data->custom_str3;?>">

			<button type="submit">SEND</button>
		</form>
	<?php }
	function get_sample_payfast($order){
		ob_start();
		$this->html_payfast_gateway_sample($order);
		return ob_get_clean();
	}

	function get_return_url($order){
		$link = box_get_static_link('process-payment');
		add_query_arg('order_id',$order->ID, $link);
		return $link;
	}
	function get_response_url($order){
		$link = box_get_static_link('process-payment');
		$link = add_query_arg(array('order_id'=>$order->ID,'pay'=>'payfast'), $link);
		return $link;
	}
	function get_data($order){
		$cartTotal = $order->amount;
		$current_user = wp_get_current_user();
		$data = array(
		    // Merchant details
		    'merchant_id' => $this->merchant_id,
		    'merchant_key' => $this->merchant_key,
		    'return_url' => $this->get_return_url($order),
		    'cancel_url' => $this->get_cancel_order_url(),
		    'notify_url' =>  $this->get_response_url($order),
		    // Buyer details
		    'name_first' => $current_user->first_name,
		    'name_last'  => $current_user->last_name,
		    'email_address'=> $current_user->user_email,
		    // Transaction details
		    'm_payment_id' => $order->ID, //Unique payment ID to pass through to notify_url
		    // Amount needs to be in ZAR
		    // If multicurrency system its conversion has to be done before building this array
		    'amount' => number_format( sprintf( "%.2f", $cartTotal ), 2, '.', '' ),
		    'item_name' => 'Deposit Credit via Package: '.$order->pack_id,
		    'item_description' => 'Item Description',
		    'custom_int1' => $order->ID, //custom integer to be passed through
		    'custom_str1' => 'custom string is passed along with transaction to notify_url page',
		    'custom_str3' =>$order->ID,
		);

		return (object) $data;
	}

	function get_signarute($order){

		$data = $this->get_data($order);
		// Create GET string
		$pfOutput = '';
		foreach( $data as $key => $val )
		{
		    if(!empty($val))
		     {
		        $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
		     }
		}
		// Remove last ampersand
		$getString = substr( $pfOutput, 0, -1 );
		//Uncomment the next line and add a passphrase if there is one set on the account
		//$passPhrase = '';
		if( isset( $passPhrase ) )
		{
		    $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
		}
		$data['signature'] = md5( $getString );
		return $data['signature'];
	}
	/**
	 * Reciept page.
	 *
	 * Display text and a button to direct the user to PayFast.
	 *
	 * @since 1.0.0
	 */


	/**
	 * Check PayFast ITN response.
	 *
	 * @since 1.0.0
	 */
	public function box_verify_response () { //check_itn_response
		if( ! empty( $_REQUEST['signature'] ) ){
			$this->handle_itn_request( stripslashes_deep( $_POST ) );
			// Notify PayFast that information has been received
			header( 'HTTP/1.0 200 OK' );
			flush();
		}
	}

	/**
	 * Check PayFast ITN validity.
	 *
	 * @param array $data
	 * @since 1.0.0
	 */
	public function handle_itn_request( $data ) {
		$this->log( PHP_EOL
			. '----------'
			. PHP_EOL . 'PayFast ITN call received'
			. PHP_EOL . '----------'
		);
		$this->log( 'Get posted data' );
		$this->log( 'PayFast Data: ' . print_r( $data, true ) );
		$payfast_error  = false;
		$payfast_done   = false; // == false if order_status == completed
		$debug_email    = $this->get_option( 'debug_email', get_option( 'admin_email' ) );
		$session_id     = $data['custom_str1'];
		$vendor_name    = get_bloginfo( 'name', 'display' );
		$vendor_url     = home_url( '/' );
		$order_id       = absint( $data['custom_str3'] );
		//$order_key      = wc_clean( $session_id );
		$order          = box_get_order( $order_id );
		$original_order = $order;

		if ( false === $data ) {
			$payfast_error  = true;

		}

		// Verify security signature
		if ( ! $payfast_error && ! $payfast_done ) {
			$this->log( 'Box Verify security signature' );
			$signature = md5( $this->_generate_parameter_string( $data, false, false ) ); // false not to sort data
			// If signature different, log for debugging
			if ( ! $this->validate_signature( $data, $signature ) ) {
				$payfast_error         = true;
				$payfast_error_message = PF_ERR_INVALID_SIGNATURE;
			}
		}


		// Check data against internal order
		if ( ! $payfast_error && ! $payfast_done ) {
			$this->log( 'Check data against internal order' );

			if( $order->post_status == 'completed' || $order->post_status == 'publish'){
				box_log('This order is verified. Exit');
				return 0;
			}
			// Check order amount
			if( $data['amount_gross'] == $order->amount ) {

				if($order->order_type == 'deposit'){
					box_log('Update credit to balance');
					bx_process_payment($order);
				}
			}
		}


	}

	/**
	 * Handle logging the order details.
	 *
	 * @since 1.4.5
	 */
	public function log_order_details( $order ) {
		if ( version_compare( WC_VERSION,'3.0.0', '<' ) ) {
			$customer_id = get_post_meta( $order->get_id(), '_customer_user', true );
		} else {
			$customer_id = $order->get_user_id();
		}

		$details = "Order Details:"
		. PHP_EOL . 'customer id:' . $customer_id
		. PHP_EOL . 'order id:   ' . $order->get_id()
		. PHP_EOL . 'parent id:  ' . $order->get_parent_id()
		. PHP_EOL . 'status:     ' . $order->get_status()
		. PHP_EOL . 'total:      ' . $order->get_total()
		. PHP_EOL . 'currency:   ' . $order->get_currency()
		. PHP_EOL . 'key:        ' . $order->get_order_key()
		. "";

		$this->log( $details );
	}

	/**
	 * This function mainly responds to ITN cancel requests initiated on PayFast, but also acts
	 * just in case they are not cancelled.
	 * @version 1.4.3 Subscriptions flag
	 *
	 * @param array $data should be from the Gatewy ITN callback.
	 * @param WC_Order $order
	 */
	public function handle_itn_payment_cancelled( $data, $order, $subscriptions ) {

		remove_action( 'woocommerce_subscription_status_cancelled', array( $this, 'cancel_subscription_listener' ) );
		foreach ( $subscriptions as $subscription ) {
			if ( 'cancelled' !== $subscription->get_status() ) {
				$subscription->update_status( 'cancelled', __( 'Merchant cancelled subscription on PayFast.' , 'woocommerce-gateway-payfast' ) );
				$this->_delete_subscription_token( $subscription );
			}
		}
		add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'cancel_subscription_listener' ) );
	}

	/**
	 * This function handles payment complete request by PayFast.
	 * @version 1.4.3 Subscriptions flag
	 *
	 * @param array $data should be from the Gatewy ITN callback.
	 * @param WC_Order $order
	 */
	public function handle_itn_payment_complete( $data, $order, $subscriptions ) {
		$this->log( '- Complete' );
		$order->add_order_note( __( 'ITN payment completed', 'woocommerce-gateway-payfast' ) );
		$order_id = self::get_order_prop( $order, 'id' );

		// Store token for future subscription deductions.
		if ( count( $subscriptions ) > 0 && isset( $data['token'] ) ) {
			if ( $this->_has_renewal_flag( reset( $subscriptions ) ) ) {
				// renewal flag is set to true, so we need to cancel previous token since we will create a new one
				$this->log( 'Cancel previous subscriptions with token ' . $this->_get_subscription_token( reset( $subscriptions ) ) );

				// only request API cancel token for the first subscription since all of them are using the same token
				$this->cancel_subscription_listener( reset( $subscriptions ) );
			}

			$token = sanitize_text_field( $data['token'] );
			foreach ( $subscriptions as $subscription ) {
				$this->_delete_renewal_flag( $subscription );
				$this->_set_subscription_token( $token, $subscription );
			}
		}

		// the same mechanism (adhoc token) is used to capture payment later
		if ( $this->order_contains_pre_order( $order_id )
			&& $this->order_requires_payment_tokenization( $order_id ) ) {

			$token = sanitize_text_field( $data['token'] );
			$is_pre_order_fee_paid = get_post_meta( $order_id, '_pre_order_fee_paid', true ) === 'yes';

			if ( ! $is_pre_order_fee_paid ) {
				/* translators: 1: gross amount 2: payment id */
				$order->add_order_note( sprintf( __( 'PayFast pre-order fee paid: R %1$s (%2$s)', 'woocommerce-gateway-payfast' ), $data['amount_gross'], $data['pf_payment_id'] ) );
				$this->_set_pre_order_token( $token, $order );
				// set order to pre-ordered
				WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );
				update_post_meta( $order_id, '_pre_order_fee_paid', 'yes' );
				WC()->cart->empty_cart();
			} else {
				/* translators: 1: gross amount 2: payment id */
				$order->add_order_note( sprintf( __( 'PayFast pre-order product line total paid: R %1$s (%2$s)', 'woocommerce-gateway-payfast' ), $data['amount_gross'], $data['pf_payment_id'] ) );
				$order->payment_complete();
				$this->cancel_pre_order_subscription( $token );
			}
		} else {
			$order->payment_complete();
		}

		$debug_email   = $this->get_option( 'debug_email', get_option( 'admin_email' ) );
		$vendor_name    = get_bloginfo( 'name', 'display' );
		$vendor_url     = home_url( '/' );
		if ( $this->send_debug_email ) {
			$subject = 'PayFast ITN on your site';
			$body =
				"Hi,\n\n"
				. "A PayFast transaction has been completed on your website\n"
				. "------------------------------------------------------------\n"
				. 'Site: ' . esc_html( $vendor_name ) . ' (' . esc_url( $vendor_url ) . ")\n"
				. 'Purchase ID: ' . esc_html( $data['m_payment_id'] ) . "\n"
				. 'PayFast Transaction ID: ' . esc_html( $data['pf_payment_id'] ) . "\n"
				. 'PayFast Payment Status: ' . esc_html( $data['payment_status'] ) . "\n"
				. 'Order Status Code: ' . self::get_order_prop( $order, 'status' );
			wp_mail( $debug_email, $subject, $body );
		}
	}

	/**
	 * @param $data
	 * @param $order
	 */
	public function handle_itn_payment_failed( $data, $order ) {
		$this->log( '- Failed' );
		/* translators: 1: payment status */
		$order->update_status( 'failed', sprintf( __( 'Payment %s via ITN.', 'woocommerce-gateway-payfast' ), strtolower( sanitize_text_field( $data['payment_status'] ) ) ) );
		$debug_email   = $this->get_option( 'debug_email', get_option( 'admin_email' ) );
		$vendor_name    = get_bloginfo( 'name', 'display' );
		$vendor_url     = home_url( '/' );

		if ( $this->send_debug_email ) {
			$subject = 'PayFast ITN Transaction on your site';
			$body =
				"Hi,\n\n" .
				"A failed PayFast transaction on your website requires attention\n" .
				"------------------------------------------------------------\n" .
				'Site: ' . esc_html( $vendor_name ) . ' (' . esc_url( $vendor_url ) . ")\n" .
				'Purchase ID: ' . self::get_order_prop( $order, 'id' ) . "\n" .
				'User ID: ' . self::get_order_prop( $order, 'user_id' ) . "\n" .
				'PayFast Transaction ID: ' . esc_html( $data['pf_payment_id'] ) . "\n" .
				'PayFast Payment Status: ' . esc_html( $data['payment_status'] );
			wp_mail( $debug_email, $subject, $body );
		}
	}

	/**
	 * @since 1.4.0 introduced
	 * @param $data
	 * @param $order
	 */
	public function handle_itn_payment_pending( $data, $order ) {
		$this->log( '- Pending' );
		// Need to wait for "Completed" before processing
		/* translators: 1: payment status */
		$order->update_status( 'on-hold', sprintf( __( 'Payment %s via ITN.', 'woocommerce-gateway-payfast' ), strtolower( sanitize_text_field( $data['payment_status'] ) ) ) );
	}

	/**
	 * @param string $order_id
	 * @return double
	 */
	// public function get_pre_order_fee( $order_id ) {
	// 	foreach ( wc_get_order( $order_id )->get_fees() as $fee ) {
	// 		if ( is_array( $fee ) && 'Pre-Order Fee' == $fee['name'] ) {
	// 			return doubleval( $fee['line_total'] ) + doubleval( $fee['line_tax'] );
	// 		}
	// 	}
	// }
	/**
	 * @param string $order_id
	 * @return bool
	 */
	public function order_contains_pre_order( $order_id ) {
		if ( class_exists( 'WC_Pre_Orders_Order' ) ) {
			return WC_Pre_Orders_Order::order_contains_pre_order( $order_id );
		}
		return false;
	}

	/**
	 * @param string $order_id
	 *
	 * @return bool
	 */
	public function order_requires_payment_tokenization( $order_id ) {
		if ( class_exists( 'WC_Pre_Orders_Order' ) ) {
			return WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id );
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function cart_contains_pre_order_fee() {
		if ( class_exists( 'WC_Pre_Orders_Cart' ) ) {
			return WC_Pre_Orders_Cart::cart_contains_pre_order_fee();
		}
		return false;
	}
	/**
	 * Store the PayFast subscription token
	 *
	 * @param string $token
	 * @param WC_Subscription $subscription
	 */
	protected function _set_subscription_token( $token, $subscription ) {
		update_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_subscription_token', $token );
	}

	/**
	 * Retrieve the PayFast subscription token for a given order id.
	 *
	 * @param WC_Subscription $subscription
	 * @return mixed
	 */
	protected function _get_subscription_token( $subscription ) {
		return get_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_subscription_token', true );
	}

	/**
	 * Retrieve the PayFast subscription token for a given order id.
	 *
	 * @param WC_Subscription $subscription
	 * @return mixed
	 */
	protected function _delete_subscription_token( $subscription ) {
		return delete_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_subscription_token' );
	}

	/**
	 * Store the PayFast renewal flag
	 * @since 1.4.3
	 *
	 * @param string $token
	 * @param WC_Subscription $subscription
	 */
	protected function _set_renewal_flag( $subscription ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			update_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_renewal_flag', 'true' );
		} else {
			$subscription->update_meta_data( '_payfast_renewal_flag', 'true' );
			$subscription->save_meta_data();
		}
	}

	/**
	 * Retrieve the PayFast renewal flag for a given order id.
	 * @since 1.4.3
	 *
	 * @param WC_Subscription $subscription
	 * @return bool
	 */
	protected function _has_renewal_flag( $subscription ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			return 'true' === get_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_renewal_flag', true );
		} else {
			return 'true' === $subscription->get_meta( '_payfast_renewal_flag', true );
		}
	}

	/**
	 * Retrieve the PayFast renewal flag for a given order id.
	 * @since 1.4.3
	 *
	 * @param WC_Subscription $subscription
	 * @return mixed
	 */
	protected function _delete_renewal_flag( $subscription ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			return delete_post_meta( self::get_order_prop( $subscription, 'id' ), '_payfast_renewal_flag' );
		} else {
			$subscription->delete_meta_data( '_payfast_renewal_flag' );
			$subscription->save_meta_data();
		}
	}

	/**
	 * Store the PayFast pre_order_token token
	 *
	 * @param string   $token
	 * @param WC_Order $order
	 */
	protected function _set_pre_order_token( $token, $order ) {
		update_post_meta( self::get_order_prop( $order, 'id' ), '_payfast_pre_order_token', $token );
	}

	/**
	 * Retrieve the PayFast pre-order token for a given order id.
	 *
	 * @param WC_Order $order
	 * @return mixed
	 */
	protected function _get_pre_order_token( $order ) {
		return get_post_meta( self::get_order_prop( $order, 'id' ), '_payfast_pre_order_token', true );
	}

	/**
	 * Wrapper function for wcs_order_contains_subscription
	 *
	 * @param WC_Order $order
	 * @return bool
	 */
	public function order_contains_subscription( $order ) {
		if ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return false;
		}
		return wcs_order_contains_subscription( $order );
	}

	/**
	 * @param $amount_to_charge
	 * @param WC_Order $renewal_order
	 */
	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$subscription = wcs_get_subscription( get_post_meta( self::get_order_prop( $renewal_order, 'id' ), '_subscription_renewal', true ) );
		$this->log( 'Attempting to renew subscription from renewal order ' . self::get_order_prop( $renewal_order, 'id' ) );

		if ( empty( $subscription ) ) {
			$this->log( 'Subscription from renewal order was not found.' );
			return;
		}

		$response = $this->submit_subscription_payment( $subscription, $amount_to_charge );

		if ( is_wp_error( $response ) ) {
			/* translators: 1: error code 2: error message */
			$renewal_order->update_status( 'failed', sprintf( __( 'PayFast Subscription renewal transaction failed (%1$s:%2$s)', 'woocommerce-gateway-payfast' ), $response->get_error_code() ,$response->get_error_message() ) );
		}
		// Payment will be completion will be capture only when the ITN callback is sent to $this->handle_itn_request().
		$renewal_order->add_order_note( __( 'PayFast Subscription renewal transaction submitted.', 'woocommerce-gateway-payfast' ) );

	}

	/**
	 * @param WC_Subscription $subscription
	 * @param $amount_to_charge
	 * @return mixed WP_Error on failure, bool true on success
	 */
	public function submit_subscription_payment( $subscription, $amount_to_charge ) {
		$token = $this->_get_subscription_token( $subscription );
		$item_name = $this->get_subscription_name( $subscription );

		foreach ( $subscription->get_related_orders( 'all', 'renewal' ) as $order ) {
			$statuses_to_charge = array( 'on-hold', 'failed', 'pending' );
			if ( in_array( $order->get_status(), $statuses_to_charge ) ) {
				$latest_order_to_renew = $order;
				break;
			}
		}
		$item_description = json_encode( array( 'renewal_order_id' => self::get_order_prop( $latest_order_to_renew, 'id' ) ) );

		return $this->submit_ad_hoc_payment( $token, $amount_to_charge, $item_name, $item_description );
	}

	/**
	 * Get a name for the subscription item. For multiple
	 * item only Subscription $date will be returned.
	 *
	 * For subscriptions with no items Site/Blog name will be returned.
	 *
	 * @param WC_Subscription $subscription
	 * @return string
	 */
	public function get_subscription_name( $subscription ) {

		if ( $subscription->get_item_count() > 1 ) {
			return $subscription->get_date_to_display( 'start' );
		} else {
			$items = $subscription->get_items();

			if ( empty( $items ) ) {
				return get_bloginfo( 'name' );
			}

			$item = array_shift( $items );
			return $item['name'];
		}
	}

	/**
	 * Setup api data for the the adhoc payment.
	 *
	 * @since 1.4.0 introduced.
	 * @param string $token
	 * @param double $amount_to_charge
	 * @param string $item_name
	 * @param string $item_description
	 *
	 * @return bool|WP_Error
	 */
	public function submit_ad_hoc_payment( $token, $amount_to_charge, $item_name, $item_description ) {
		$args = array(
			'body' => array(
				'amount'           => $amount_to_charge * 100, // convert to cents
				'item_name'        => $item_name,
				'item_description' => $item_description,
			),
		);
		return $this->api_request( 'adhoc', $token, $args );
	}

	/**
	 * Send off API request.
	 *
	 * @since 1.4.0 introduced.
	 *
	 * @param $command
	 * @param $token
	 * @param $api_args
	 * @param string $method GET | PUT | POST | DELETE.
	 *
	 * @return bool|WP_Error
	 */
	public function api_request( $command, $token, $api_args, $method = 'POST' ) {
		if ( empty( $token ) ) {
			$this->log( "Error posting API request: No token supplied", true );
			return new WP_Error( '404', __( 'Can not submit PayFast request with an empty token', 'woocommerce-gateway-payfast' ), $results );
		}

		$api_endpoint  = "https://api.payfast.co.za/subscriptions/$token/$command";
		$api_endpoint .= 'yes' === $this->get_option( 'testmode' ) ? '?testing=true' : '';

		$timestamp = current_time( rtrim( DateTime::ATOM, 'P' ) ) . '+02:00';
		$api_args['timeout'] = 45;
		$api_args['headers'] = array(
			'merchant-id' => $this->merchant_id,
			'timestamp'   => $timestamp,
			'version'     => 'v1',
		);

		// generate signature
		$all_api_variables                = array_merge( $api_args['headers'], (array) $api_args['body'] );
		$api_args['headers']['signature'] = md5( $this->_generate_parameter_string( $all_api_variables ) );
		$api_args['method']               = strtoupper( $method );

		$results = wp_remote_request( $api_endpoint, $api_args );

		if ( 200 !== $results['response']['code'] ) {
			$this->log( "Error posting API request:\n" . print_r( $results['response'], true ) );
			return new WP_Error( $results['response']['code'], $results['response']['message'], $results );
		}

		$maybe_json = json_decode( $results['body'], true );

		if ( ! is_null( $maybe_json ) && isset( $maybe_json['status'] ) && 'failed' === $maybe_json['status'] ) {
			$this->log( "Error posting API request:\n" . print_r( $results['body'], true ) );

			// Use trim here to display it properly e.g. on an order note, since PayFast can include CRLF in a message.
			return new WP_Error( $maybe_json['code'], trim( $maybe_json['data']['message'] ), $results['body'] );
		}

		return true;
	}

	/**
	 * Responds to Subscriptions extension cancellation event.
	 *
	 * @since 1.4.0 introduced.
	 * @param WC_Subscription $subscription
	 */
	public function cancel_subscription_listener( $subscription ) {
		$token = $this->_get_subscription_token( $subscription );
		if ( empty( $token ) ) {
			return;
		}
		$this->api_request( 'cancel', $token, array(), 'PUT' );
	}

	/**
	 * @since 1.4.0
	 * @param string $token
	 *
	 * @return bool|WP_Error
	 */
	public function cancel_pre_order_subscription( $token ) {
		return $this->api_request( 'cancel', $token, array(), 'PUT' );
	}

	/**
	 * @since 1.4.0 introduced.
	 * @param      $api_data
	 * @param bool $sort_data_before_merge? default true.
	 * @param bool $skip_empty_values Should key value pairs be ignored when generating signature?  Default true.
	 *
	 * @return string
	 */
	protected function _generate_parameter_string( $api_data, $sort_data_before_merge = true, $skip_empty_values = true ) {

		// if sorting is required the passphrase should be added in before sort.
		if ( ! empty( $this->pass_phrase ) && $sort_data_before_merge ) {
			$api_data['passphrase'] = $this->pass_phrase;
		}

		if ( $sort_data_before_merge ) {
			ksort( $api_data );
		}

		// concatenate the array key value pairs.
		$parameter_string = '';
		foreach ( $api_data as $key => $val ) {

			if ( $skip_empty_values && empty( $val ) ) {
				continue;
			}

			if ( 'signature' !== $key ) {
				$val = urlencode( $val );
				$parameter_string .= "$key=$val&";
			}
		}
		// when not sorting passphrase should be added to the end before md5
		if ( $sort_data_before_merge ) {
			$parameter_string = rtrim( $parameter_string, '&' );
		} elseif ( ! empty( $this->pass_phrase ) ) {
			$parameter_string .= 'passphrase=' . urlencode( $this->pass_phrase );
		} else {
			$parameter_string = rtrim( $parameter_string, '&' );
		}

		return $parameter_string;
	}

	/**
	 * @since 1.4.0 introduced.
	 * @param WC_Order $order
	 */
	public function process_pre_order_payments( $order ) {

		// The total amount to charge is the the order's total.
		$total = $order->get_total() - $this->get_pre_order_fee( self::get_order_prop( $order, 'id' ) );
		$token = $this->_get_pre_order_token( $order );

		if ( ! $token ) {
			return;
		}
		// get the payment token and attempt to charge the transaction
		$item_name = 'pre-order';
		$results = $this->submit_ad_hoc_payment( $token, $total, $item_name );

		if ( is_wp_error( $results ) ) {
			/* translators: 1: error code 2: error message */
			$order->update_status( 'failed', sprintf( __( 'PayFast Pre-Order payment transaction failed (%1$s:%2$s)', 'woocommerce-gateway-payfast' ), $results->get_error_code() ,$results->get_error_message() ) );
			return;
		}

		// Payment completion will be handled by ITN callback
	}

	/**
	 * Setup constants.
	 *
	 * Setup common values and messages used by the PayFast gateway.
	 *
	 * @since 1.0.0
	 */
	public function setup_constants() {
		// Create user agent string.
		define( 'PF_SOFTWARE_NAME', 'WooCommerce' );
		define( 'PF_SOFTWARE_VER', '1.0' );
		define( 'PF_MODULE_NAME', 'WooCommerce-PayFast-Free' );
		define( 'PF_MODULE_VER', $this->version );

		// Features
		// - PHP
		$pf_features = 'PHP ' . phpversion() . ';';

		// - cURL
		if ( in_array( 'curl', get_loaded_extensions() ) ) {
			define( 'PF_CURL', '' );
			$pf_version = curl_version();
			$pf_features .= ' curl ' . $pf_version['version'] . ';';
		} else {
			$pf_features .= ' nocurl;';
		}

		// Create user agrent
		define( 'PF_USER_AGENT', PF_SOFTWARE_NAME . '/' . PF_SOFTWARE_VER . ' (' . trim( $pf_features ) . ') ' . PF_MODULE_NAME . '/' . PF_MODULE_VER );

		// General Defines
		define( 'PF_TIMEOUT', 15 );
		define( 'PF_EPSILON', 0.01 );

		// Messages
		// Error
		define( 'PF_ERR_AMOUNT_MISMATCH', __( 'Amount mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_BAD_ACCESS', __( 'Bad access of page', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_BAD_SOURCE_IP', __( 'Bad source IP address', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_CONNECT_FAILED', __( 'Failed to connect to PayFast', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_INVALID_SIGNATURE', __( 'Security signature mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_MERCHANT_ID_MISMATCH', __( 'Merchant ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_NO_SESSION', __( 'No saved session found for ITN transaction', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_ID_MISSING_URL', __( 'Order ID not present in URL', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_ID_MISMATCH', __( 'Order ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_INVALID', __( 'This order ID is invalid', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_NUMBER_MISMATCH', __( 'Order Number mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_ORDER_PROCESSED', __( 'This order has already been processed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_PDT_FAIL', __( 'PDT query failed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_PDT_TOKEN_MISSING', __( 'PDT token not present in URL', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_SESSIONID_MISMATCH', __( 'Session ID mismatch', 'woocommerce-gateway-payfast' ) );
		define( 'PF_ERR_UNKNOWN', __( 'Unkown error occurred', 'woocommerce-gateway-payfast' ) );

		// General
		define( 'PF_MSG_OK', __( 'Payment was successful', 'woocommerce-gateway-payfast' ) );
		define( 'PF_MSG_FAILED', __( 'Payment has failed', 'woocommerce-gateway-payfast' ) );
		define( 'PF_MSG_PENDING', __( 'The payment is pending. Please note, you will receive another Instant Transaction Notification when the payment status changes to "Completed", or "Failed"', 'woocommerce-gateway-payfast' ) );

		do_action( 'woocommerce_gateway_payfast_setup_constants' );
	}

	/**
	 * Log system processes.
	 * @since 1.0.0
	 */
	public function log( $message ) {
		box_log($message);
	}

	/**
	 * validate_signature()
	 *
	 * Validate the signature against the returned data.
	 *
	 * @param array $data
	 * @param string $signature
	 * @since 1.0.0
	 * @return string
	 */
	public function validate_signature( $data, $signature ) {
	    $result = $data['signature'] === $signature;
	    $this->log( 'Signature = ' . ( $result ? 'valid' : 'invalid' ) );
	    return $result;
	}

	/**
	 * Validate the IP address to make sure it's coming from PayFast.
	 *
	 * @param array $source_ip
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_valid_ip( $source_ip ) {
		// Variable initialization
		$valid_hosts = array(
			'www.payfast.co.za',
			'sandbox.payfast.co.za',
			'w1w.payfast.co.za',
			'w2w.payfast.co.za',
		);

		$valid_ips = array();

		foreach ( $valid_hosts as $pf_hostname ) {
			$ips = gethostbynamel( $pf_hostname );

			if ( false !== $ips ) {
				$valid_ips = array_merge( $valid_ips, $ips );
			}
		}

		// Remove duplicates
		$valid_ips = array_unique( $valid_ips );

		// Adds support for X_Forwarded_For
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$source_ip = (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ) ?: $source_ip;
		}

		$this->log( "Valid IPs:\n" . print_r( $valid_ips, true ) );
		$is_valid_ip = in_array( $source_ip, $valid_ips );
		return apply_filters( 'woocommerce_gateway_payfast_is_valid_ip', $is_valid_ip, $source_ip );
	}

	/**
	 * validate_response_data()
	 *
	 * @param array $post_data
	 * @param string $proxy Address of proxy to use or NULL if no proxy.
	 * @since 1.0.0
	 * @return bool
	 */
	public function validate_response_data( $post_data, $proxy = null ) {
		$this->log( 'Host = ' . $this->validate_url );
		$this->log( 'Params = ' . print_r( $post_data, true ) );

		if ( ! is_array( $post_data ) ) {
			return false;
		}

		$response = wp_remote_post( $this->validate_url, array(
			'body'       => $post_data,
			'timeout'    => 70,
			'user-agent' => PF_USER_AGENT,
		));

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			$this->log( "Response error:\n" . print_r( $response, true ) );
			return false;
		}

		parse_str( $response['body'], $parsed_response );

		$response = $parsed_response;

		$this->log( "Response:\n" . print_r( $response, true ) );

		// Interpret Response
		if ( is_array( $response ) && in_array( 'VALID', array_keys( $response ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * amounts_equal()
	 *
	 * Checks to see whether the given amounts are equal using a proper floating
	 * point comparison with an Epsilon which ensures that insignificant decimal
	 * places are ignored in the comparison.
	 *
	 * eg. 100.00 is equal to 100.0001
	 *
	 * @author Jonathan Smit
	 * @param $amount1 Float 1st amount for comparison
	 * @param $amount2 Float 2nd amount for comparison
	 * @since 1.0.0
	 * @return bool
	 */
	public function amounts_equal( $amount1, $amount2 ) {
		return ! ( abs( floatval( $amount1 ) - floatval( $amount2 ) ) > PF_EPSILON );
	}

	/**
	 * Get order property with compatibility check on order getter introduced
	 * in WC 3.0.
	 *
	 * @since 1.4.1
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $prop  Property name.
	 *
	 * @return mixed Property value
	 */
	public static function get_order_prop( $order, $prop ) {
		switch ( $prop ) {
			case 'order_total':
				$getter = array( $order, 'get_total' );
				break;
			default:
				$getter = array( $order, 'get_' . $prop );
				break;
		}

		return is_callable( $getter ) ? call_user_func( $getter ) : $order->{ $prop };
	}

	/**
	*  Show possible admin notices
	*
	*/
	public function admin_notices() {
		if ( 'yes' !== $this->get_option( 'enabled' )
			|| ! empty( $this->pass_phrase) ) {
			return;
		}

		echo '<div class="error payfast-passphrase-message"><p>'
			. __( 'PayFast requires a passphrase to work.', 'woocommerce-gateway-payfast' )
			. '</p></div>';
	}

}
new Box_PayFast();

