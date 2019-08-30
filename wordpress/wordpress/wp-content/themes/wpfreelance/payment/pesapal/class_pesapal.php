<?php
/*
Plugin Name: TheBunch KE Pesapal Woocommerce
Description: Add PesaPal payment gateway to your Woocommerce plugin
Version: 1.1.9
Author: rixeo
Author URI: http://thebunch.co.ke/
Plugin URI: http://dev.thebunch.co.ke/
*/

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

//Define constants
define( 'THEBUNCHKE_PESAPAL_WOO_PLUGIN_DIR', dirname(__FILE__).'/' );
define( 'THEBUNCHKE_PESAPAL_WOO_PLUGIN_URL', plugin_dir_url(__FILE__));


function thebunchke_pesapal_woo_init() {

	//Load PesaPal OAuth Library
	require_once(THEBUNCHKE_PESAPAL_WOO_PLUGIN_DIR . 'lib/OAuth.php');

	add_filter('woocommerce_payment_gateways', 'add_pesapal_gateway_class' );
	function add_pesapal_gateway_class( $methods ) {
		$methods[] = 'WC_TheBunchKE_PesaPal_Pay_Gateway';
		return $methods;
	}

	/**
	 * Add Currencies
	 *
	 */
	add_filter( 'woocommerce_currencies', 'thebunchke_pesapal_woo_add_shilling' );
	function thebunchke_pesapal_woo_add_shilling( $currencies ) {
		if( !isset( $currencies['KES'] ) ||!isset( $currencies['KSH'] ) ) {
			$currencies['KES'] = __( 'Kenyan Shilling', 'woocommerce' );
			$currencies['TZS'] = __( 'Tanzanian Shilling', 'woocommerce' );
			$currencies['UGX'] = __( 'Ugandan Shilling', 'woocommerce' );
			return $currencies;
		}
	}

	/**
	 * Add Currency Symbols
	 *
	 */
	add_filter('woocommerce_currency_symbol', 'thebunchke_pesapal_woo_add_shilling_symbol', 10, 2);
	function thebunchke_pesapal_woo_add_shilling_symbol( $currency_symbol, $currency ) {
		switch( $currency ) {
			case 'KES':
				$currency_symbol = 'KShs';
			break;
			case 'TZS':
				$currency_symbol = 'TZs';
			break;
			case 'UGX':
				$currency_symbol = 'UShs';
			break;
		}
		return $currency_symbol;
	}



	class BOX_PesaPal_Pay_Gateway extends Box_Gateway{

		function __construct(){

			//Settings
			$this->id 			= 'pesapal';
			$this->method_title = 'Pesapal';
			$this->has_fields 	= false;
			$this->testmode 	= ($this->get_option('testmode') === 'yes') ? true : false;
			$this->debug 		= $this->get_option( 'debug' );
			$this->title 		= $this->get_option('title');
			$this->description 	= $this->get_option('description');

			//Set up logging
			if ( 'yes' == $this->debug ) {
				if ( class_exists('WC_Logger') ) {
					$this->log = new WC_Logger();
				} else {
					$this->log = $woocommerce->logger();
				}
			}

			//Set up API details
			if( $this->testmode ) {
				$api = 'https://demo.pesapal.com/';
				$this->consumer_key 	= $this->get_option('testconsumerkey');
				$this->consumer_secret 	= $this->get_option('testsecretkey');
			} else {
				$api  = 'https://www.pesapal.com/';
				$this->consumer_key 	= $this->get_option('consumerkey');
				$this->consumer_secret 	= $this->get_option('secretkey');
			}

			//OAuth Signatures
			$this->consumer 			= new PesaPalOAuthConsumer($this->consumer_key, $this->consumer_secret);
			$this->signature_method  	= new PesaPalOAuthSignatureMethod_HMAC_SHA1();
			$this->token 				= $this->params = NULL;

			//PesaPal End Points
			$this->gatewayURL 						= $api.'api/PostPesapalDirectOrderV4';
			$this->QueryPaymentStatus 				= $api.'API/QueryPaymentStatus';
			$this->QueryPaymentStatusByMerchantRef  = $api.'API/QueryPaymentStatusByMerchantRef';
			$this->querypaymentdetails 				= $api.'API/querypaymentdetails';

			//IPN URL
			$this->notify_url   					= str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Pesapal_Gateway', home_url( '/' ) ) );

			$this->init_form_fields();
			$this->init_settings();

			if (is_admin()){
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			 $this->fields = array(

	            array(
	                'name' => 'live_publishable_key',
	                'label' => 'Live Publishable Key',
	                'type' =>'text',
	            ),
	             array(
	                'name' => 'live_secret_key',
	                'label' => 'Live Secret Key',
	                'type' =>'text',
	            ),
	            array(
	                'name' => 'test_publishable_key',
	                'label' => 'Test Publishable Key',
	                'type' =>'text',
	            ),
	            array(
	                'name' => 'test_secret_key',
	                'label' => 'Test Secret Key',
	                'type' =>'text',
	            )
	        );

			add_action('woocommerce_receipt_'.$this->id, array(&$this, 'payment_page'));
			add_action('woocommerce_api_wc_'.$this->id.'_gateway', array( $this, 'ipn_response' ) );
			add_action($this->id.'_process_valid_ipn_request', array($this, 'process_valid_ipn_request'));

		}


		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woothemes' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Pesapal Payment', 'woothemes' ),
					'default' => 'no'
				),
				'title' => array(
					'title' => __( 'Title', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
					'default' => __( 'Pesapal Payment', 'woothemes' )
				),
				'description' => array(
					'title' => __( 'Description', 'woocommerce' ),
					'type' => 'textarea',
					'description' => __( 'This is the description which the user sees during checkout.', 'woocommerce' ),
					'default' => __("Payment via Pesapal Gateway, you can pay by either credit/debit card or use mobile payment option such as Mpesa.", 'woocommerce')
				),
				'testmode' => array(
					'title' => __( 'Use Demo Gateway', 'woothemes' ),
					'type' => 'checkbox',
					'label' => __( 'Use Demo Gateway', 'woothemes' ),
					'description' => __( 'Use demo pesapal gateway for testing from your account at <a href="https://demo.pesapal.com">https://demo.pesapal.com</a>', 'woothemes' ),
					'default' => 'no'
				),
				'consumerkey' => array(
					'title' => __( 'Pesapal Consumer Key', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'Your Pesapal consumer key which should have been emailed to you.', 'woothemes' ),
					'default' => ''
				),
				'secretkey' => array(
					'title' => __( 'Pesapal Secret Key', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'Your Pesapal secret key which should have been emailed to you.', 'woothemes' ),
					'default' => ''
				),

				'testconsumerkey' => array(
					'title' => __( 'Pesapal Demo Consumer Key', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'Your demo Pesapal consumer key which can be seen at demo.pesapal.com.', 'woothemes' ),
					'default' => ''
				),

				'testsecretkey' => array(
					'title' => __( 'Pesapal Demo Secret Key', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'Your demo Pesapal secret key which can be seen at demo.pesapal.com.', 'woothemes' ),
					'default' => ''
				),

				'debug' => array(
					'title' => __( 'Debug Log', 'woocommerce' ),
					'type' => 'checkbox',
					'label' => __( 'Enable logging', 'woocommerce' ),
					'default' => 'no',
					'description' => sprintf( __( 'Log PesaPal events, such as IPN requests, inside <code>woocommerce/logs/pesapal-%s.txt</code>', 'woocommerce' ), sanitize_file_name( wp_hash( 'pesapal' ) ) ),
				)


			);
		}

		public function admin_options() {
			?>
			<h3><?php _e('Pesapal', 'woothemes'); ?></h3>
			<p>
				<?php _e('PesaPal requires Full names and email/phone number. To handle APN return requests, please set the url '); ?>
				<strong><?php echo $this->notify_url; ?></strong>
				<?php _e(' in your <a href="https://www.pesapal.com/merchantdashboard" target="_blank">PesaPal</a> account settings'); ?>
			</p>
			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
			<script type="text/javascript">
			jQuery(function(){
				var testMode = jQuery("#woocommerce_pesapal_testmode");
				var live_consumer = jQuery("#woocommerce_pesapal_consumerkey");
				var live_secrect = jQuery("#woocommerce_pesapal_secretkey");
				var test_consumer = jQuery("#woocommerce_pesapal_testconsumerkey");
				var test_secrect = jQuery("#woocommerce_pesapal_testsecretkey");
				if (testMode.is(":not(:checked)")){
					test_consumer.parents("tr").hide();
					test_secrect.parents("tr").hide();

					live_consumer.parents("tr").show();
					live_secrect.parents("tr").show();
				}
				testMode.click(function(){
					// If checked
					if (testMode.is(":checked")) {
						//show the hidden div
						test_consumer.parents("tr").show("fast");
						test_secrect.parents("tr").show("fast");

						live_consumer.parents("tr").hide("fast");
						live_secrect.parents("tr").hide("fast");
					} else {
						//otherwise, hide it
						test_consumer.parents("tr").hide("fast");
						test_secrect.parents("tr").hide("fast");

						live_consumer.parents("tr").show("fast");
						live_secrect.parents("tr").show("fast");
					}
				});
			});
			</script>
			<?php
		}


		function process_payment( $order_id ) {
			global $woocommerce;

			$order = wc_get_order( $order_id );

			if($order->get_status() === 'completed'){
				//Redirect to payment page
				return array(
					'result'    => 'success',
					'redirect'  => $this->get_return_url( $order )
				);
			}else{
				return array(
					'result'    => 'success',
					'redirect'  => $order->get_checkout_payment_url(true)
				);
			}
		}


		//Create Payment Page
		function payment_page($order_id){
			if(isset($_REQUEST['pesapal_merchant_reference'])){
				$order = wc_get_order( $order_id );
				$pesapalMerchantReference = $_REQUEST['pesapal_merchant_reference'];
				$pesapalTrackingId = $_REQUEST['pesapal_transaction_tracking_id'];

				$transactionDetails	= $this->getTransactionDetails($pesapalMerchantReference,$pesapalTrackingId);

				add_post_meta( $order_id, '_order_pesapal_transaction_tracking_id', $transactionDetails['pesapal_transaction_tracking_id']);
				add_post_meta( $order_id, '_order_thebunchke_pesapalment_method', $transactionDetails['payment_method']);

				add_post_meta( $order_id, '_order_payment_method', $transactionDetails['payment_method']);

				if($transactionDetails['status'] === 'COMPLETED'){
					$order->update_status('wc-completed', 'order_note');
					$order->payment_complete();
				}else{
					$order->update_status('wc-processing', 'Payment accepted, awaiting confirmation');
				}
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);

			}else{
				$url = $this->create_url($order_id);
				?>
				<div class="pesapal_container" style="position:relative;">
					<img class="pesapal_loading_preloader" src="<?php echo THEBUNCHKE_PESAPAL_WOO_PLUGIN_URL; ?>/assets/img/loader.gif" alt="loading" style="position:absolute;"/>
					<iframe class="pesapal_loading_frame" src="<?php echo $url; ?>" width="100%" height="700px"  scrolling="yes" frameBorder="0">
						<p><?php _e('Browser unable to load iFrame', 'woothemes'); ?></p>
					</iframe>
				</div>
				<script>
					jQuery(document).ready(function () {
						jQuery('.pesapal_loading_frame').on('load', function () {
							jQuery('.pesapal_loading_preloader').hide();
						});
					});
				</script>
				<?php
			}
		}

		function thankyou_page($order_id) {

			if(isset($_REQUEST['pesapal_transaction_tracking_id'])){
				$order = wc_get_order( $order_id );
				$pesapalMerchantReference = $_REQUEST['pesapal_merchant_reference'];
				$pesapalTrackingId = $_REQUEST['pesapal_transaction_tracking_id'];

				$transactionDetails	= $this->getTransactionDetails($pesapalMerchantReference,$pesapalTrackingId);

				$order->add_order_note( __('Payment accepted, awaiting confirmation.', 'woothemes') );
				add_post_meta( $order_id, '_order_pesapal_transaction_tracking_id', $transactionDetails['pesapal_transaction_tracking_id']);
				add_post_meta( $order_id, '_order_thebunchke_pesapalment_method', $transactionDetails['payment_method']);

				add_post_meta( $order_id, '_order_payment_method', $transactionDetails['payment_method']);

				if($transactionDetails['status'] === 'COMPLETED'){
					$order->update_status('wc-completed', 'order_note');
					$order->payment_complete();
				}else{
					$order->update_status('wc-processing', 'Payment accepted, awaiting confirmation');
				}
			}
			WC()->cart->empty_cart();
		}


		/**
		 * Create iframe URL
		 *
		 */
		function create_url($order_id){
			$order = wc_get_order( $order_id );
			$order_xml = $this->pesapal_xml($order,$order_id);
			$callback_url = $this->get_return_url( $order ); //add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, $this->get_return_url( $order )));
			//$callback_url = add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, $order->get_checkout_order_received_url()));

			$url = PesaPalOAuthRequest::from_consumer_and_token($this->consumer, $this->token, "GET", $this->gatewayURL, $this->params);
			$url->set_parameter("oauth_callback", $callback_url);
			$url->set_parameter("pesapal_request_data", $order_xml);
			$url->sign_request($this->signature_method, $this->consumer, $this->token);
			return $url;
		}

		/**
		 * Generate PesaPal XML
		 */
		function pesapal_xml($order,$order_id) {
			$pesapal_args['total'] = $order->get_total();
			$pesapal_args['reference'] = $order_id;
			$pesapal_args['first_name'] = $order->billing_first_name;
			$pesapal_args['last_name'] = $order->billing_last_name;
			$pesapal_args['email'] = $order->billing_email;
			$pesapal_args['phone'] = $order->billing_phone;



			$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
				<PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
				Amount=\"" . $pesapal_args['total'] . "\"
				Description=\"Order from " . bloginfo('name') . ".\"
				Type=\"MERCHANT\"
				Reference=\"" . $pesapal_args['reference'] . "\"
				FirstName=\"" . $pesapal_args['first_name'] . "\"
				LastName=\"" . $pesapal_args['last_name'] . "\"
				Email=\"" . $pesapal_args['email'] . "\"
				PhoneNumber=\"" . $pesapal_args['phone'] . "\"
				Currency=\"" . get_woocommerce_currency() . "\"
				xmlns=\"http://www.pesapal.com\" />";

			return htmlentities($xml);
		}


		function status_request($transaction_id, $merchant_ref){
			$request_status = PesaPalOAuthRequest::from_consumer_and_token($this->consumer, $this->token, "GET", $this->gatewayURL, $this->params);
			$request_status->set_parameter("pesapal_merchant_reference", $merchant_ref);
			$request_status->set_parameter("pesapal_transaction_tracking_id", $transaction_id);
			$request_status->sign_request($this->signature_method, $this->consumer, $this->token);

			return $this->checkTransactionStatus($merchant_ref);
		}


		/**
		 * Check Transaction status
		 *
		 * @return PENDING/FAILED/INVALID
		 **/
		function checkTransactionStatus($pesapalMerchantReference,$pesapalTrackingId = NULL){
			if($pesapalTrackingId)
				$queryURL = $this->QueryPaymentStatus;
			else
				$queryURL = $this->QueryPaymentStatusByMerchantRef;

			//get transaction status
			$request_status = PesaPalOAuthRequest::from_consumer_and_token(
								$this->consumer,
								$this->token,
								"GET",
								$queryURL,
								$this->params
							  );

			$request_status->set_parameter("pesapal_merchant_reference", $pesapalMerchantReference);

			if($pesapalTrackingId)
				$request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);

			$request_status->sign_request($this->signature_method, $this->consumer, $this->token);

			return $this->curlRequest($request_status);
		}

		/**
		 * Check Transaction status
		 *
		 * @return PENDING/FAILED/INVALID
		**/
		function getTransactionDetails($pesapalMerchantReference,$pesapalTrackingId){

			$request_status = PesaPalOAuthRequest::from_consumer_and_token(
								$this->consumer,
								$this->token,
								"GET",
								$this->querypaymentdetails,
								$this->params
							  );

			$request_status->set_parameter("pesapal_merchant_reference", $pesapalMerchantReference);
			$request_status->set_parameter("pesapal_transaction_tracking_id",$pesapalTrackingId);
			$request_status->sign_request($this->signature_method, $this->consumer, $this->token);

			$responseData = $this->curlRequest($request_status);

			$pesapalResponse = explode(",", $responseData);
			$pesapalResponseArray = array('pesapal_transaction_tracking_id'=>$pesapalResponse[0],
                                'payment_method'=>$pesapalResponse[1],
                                'status'=>$pesapalResponse[2],
                                'pesapal_merchant_reference'=>$pesapalResponse[3]
                              );

			return $pesapalResponseArray;
		}

		/**
		 * Check Transaction status
		 *
		 * @return ARRAY
		 **/
		function curlRequest($request_status){

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request_status);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			if(defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True'){
				$proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
				curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
				curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
			}

			$response = curl_exec($ch);
			$header_size  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$raw_header = substr($response, 0, $header_size - 4);
			$headerArray = explode("\r\n\r\n", $raw_header);
			$header = $headerArray[count($headerArray) - 1];

			//transaction status
			$elements = preg_split("/=/",substr($response, $header_size));
			$pesapal_response_data = $elements[1];

			return $pesapal_response_data;

		}

		/**
		 * IPN Response
		 *
		 * @return null
		 **/
		function ipn_response(){
			$pesapalTrackingId 		= '';
			$pesapalNotification 		= '';
			$pesapalMerchantReference	= '';

			if(isset($_REQUEST['pesapal_merchant_reference']))
				$pesapalMerchantReference = $_REQUEST['pesapal_merchant_reference'];

			if(isset($_REQUEST['pesapal_transaction_tracking_id']))
				$pesapalTrackingId = $_REQUEST['pesapal_transaction_tracking_id'];

			if(isset($_REQUEST['pesapal_notification_type']))
				$pesapalNotification=$_REQUEST['pesapal_notification_type'];

			$transactionDetails	= $this->getTransactionDetails($pesapalMerchantReference,$pesapalTrackingId);
			$order = wc_get_order( $pesapalMerchantReference );

			// We are here so lets check status and do actions
			switch ( $transactionDetails['status'] ) {
				case 'COMPLETED' :
				case 'PENDING' :

					// Check order not already completed
					if ( $order->get_status() == 'completed' ) {
						 if ( 'yes' == $this->debug )
							$this->log->add( 'pesapal', 'Aborting, Order #' . $order->id . ' is already complete.' );
						 exit;
					}

					if ( $transactionDetails['status'] == 'COMPLETED' ) {
						$order->add_order_note( __( 'IPN payment completed', 'woocommerce' ) );
						$order->payment_complete();
					} else {
						$order->update_status( 'on-hold', sprintf( __( 'Payment pending: %s', 'woocommerce' ), 'Waiting PesaPal confirmation' ) );
					}

					if ( 'yes' == $this->debug )
						$this->log->add( 'pesapal', 'Payment complete.' );

					break;
				case 'INVALID' :
				case 'FAILED' :
					// Order failed
					$order->update_status( 'failed', sprintf( __( 'Payment %s via IPN.', 'woocommerce' ), strtolower( $transactionDetails['status'] ) ) );
					break;

				default :
					// No action
					break;
			}

			$newstatus  = $order->get_status();

			$dbupdated = (strtolower($transactionDetails['status'])  === strtolower($newstatus));

			if($pesapalNotification =="CHANGE" && $dbupdated && $transactionDetails['status'] != "PENDING"){
				$resp = "pesapal_notification_type=$pesapalNotification".
						"&pesapal_transaction_tracking_id=$pesapalTrackingId".
						"&pesapal_merchant_reference=$pesapalMerchantReference";

				ob_start();
				echo $resp;
				ob_flush();
				exit;
			}
		}
	}
	new BOX_PesaPal_Pay_Gateway();
}

//Initialize the plugin
add_action('plugins_loaded', 'thebunchke_pesapal_woo_init', 0);

?>