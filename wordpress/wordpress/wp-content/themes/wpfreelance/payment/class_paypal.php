<?php
Class Box_Paypal extends Box_Gateway {
	//https://github.com/paypal/ipn-code-samples
	// check IPN return
	// IPN setup https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/

    private $amount;
	public $submit_url;
	public $order_gateway;
	public $receiver_email;
    public $api;
	 /**
     * @var bool $use_sandbox     Indicates if the sandbox endpoint is used.
     */


	public static $instance;
	const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';


    const SUBMIT_URI = 'https://www.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_SUBMIT_URI = 'https://www.sandbox.paypal.com/cgi-bin/webscr/';

      /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';

	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

	function __construct(){


        $this->api = box_get_paypal();
        $this->id = 'paypal';
		$this->order_gateway = 'paypal';
        $this->label = 'PayPal Gateway';
		$this->order_title  = 'Buy credit via paypal';
        $this->enabled = $this->api->enabled;
        $this->api_description = 'Take payments via PayPal';
        $this->description = 'You will be redirected to PayPal to complete your purchase securely.';
        $this->is_realmode = false;
        if(! $this->api->test_mode){
            $this->is_realmode = true;
        }
        $this->test_mode = $this->api->test_mode;
        $this->thumbnail        =  BOX_IMG_URL . '/paypal.jpg';
        $this->big_thumbnail    = BOX_IMG_URL . '/paypal.png';

        $this->fields = array(
            array(
                'name' =>'email',
                'label' =>'PayPal Account',
                'type' => 'email',
            ),

        );
        parent::__construct();
        if( empty( $this->api->email ) ){
            $this->enabled = false;
        }
        add_action('box_subscription_form',array($this,'subcription_pw_checkbox'));
        add_action('init', array($this, 'box_verify_response_paypal') );
	}
    function subcription_pw_checkbox(){
        if($this->enabled){     ?>
            <div class="payment-item payment-paypal-item " id= "xxxx_paypal_subscription">
                <div class="payment-item-radio">
                    <label class="box-radio is-not-empty js-valid">
                        <input type="radio" name="gateway" value="paypal" id="paypal" class="checkbox-gateway" required="required">
                        <span class="check"></span>
                        <img src="<?php echo get_template_directory_uri();?>/img/paypal.jpg" class="paypal-img" width="100" alt="">
                    <i class="ico-valid"></i></label>
                </div>
               <!--  <form method="post" class="df-box-checkout-js  form_js_paypal hide">
                    <input type="hidden" name="_gateway" value="paypal">
                    <button type="submit" class="btn-submit-payment btn-js-paypal">Submit</button>
                </form> -->
            </div>
        <?php
        }
    }

    function box_verify_response_paypal(){

        $txn_type = isset($_REQUEST['txn_type']) ? $_REQUEST['txn_type'] :''; // only paypal return

        if( !empty($txn_type) ){
            box_track('txn_type: '.$txn_type);
           // box_log($_REQUEST);
            try{
                $verified = $this->verifyIPN();
                if ( $verified ) {
                    box_track('PayPal Verify success');
                    // all action buy_creddit, prmium_post will access on this.
                    // appove order_status and add amount of this order to ballance of payer.

                    if($txn_type == 'subscr_payment'){
                        box_update_subscription_profile($_REQUEST);
                    } else {
                        box_track('Approve order start');
                        box_track('Approve order: '.$order_id);
                        $order_id = $_REQUEST['custom'];
                        bx_process_payment( $order_id );
                    }
                    /*
                     * Process IPN
                     * A list of variables is available here:
                     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
                     */
                } else {
                    box_log('verifiedIPN fail.');
                    box_track('Verify paypal Fail');
                }
            }  catch (Exception $e) {
                box_track('Caught exception: '. $e->getMessage() );
            }

        }
}
	 /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
   	 /**
     * Determine endpoint to post the verification data to.
     * @return string
     */
    public function getPaypalUri()   {
        if ( $this->is_realmode ) {
            return self::VERIFY_URI;

        } else {
            return self::SANDBOX_VERIFY_URI;
        }
    }

	function get_submit_url(){
		if($this->is_realmode){ //use_sandbox old
            return self::SUBMIT_URI;

		}
		return self::SANDBOX_SUBMIT_URI;
	}
	function create_order($package_id, $project_id = 0){
        $receiver_email = $this->get_receiver_email();

        if( empty( $receiver_email) ){
            return new WP_Error( '_empty_receiver',__('Please set receiver email','boxtheme') );
        }

		$this->receiver_email = $this->get_receiver_email();
		return $this->create_membership_draft_order($package_id);
	}
    /**
     * get admin's paypal email in setting.
     * This is a cool function
     * @author boxtheme
     * @version 1.0
     * @return  paypal email of admin settings.
     */
    function get_receiver_email(){
        $t = (object) BX_Option::get_instance()->get_option('payment','paypal');
        return $t->email;
    }

    /**
     * create a pending order and return the paypal redirect to check out this order.
     * @author boxtheme
     * @version 1.0
     * @param   [type] $package_id [description]
     * @return  the submit url and system auto redirect to this url
     */

    function create_temp_pending_order(){
        $order_id = $this->create_order($package_id, $project_id);

        if( is_wp_error( $order_id ) || $order_id == null ){
            return new WP_Error( 'add_order_fail', $order_id->get_error_message() );
        }

        return $this->get_redirect_url($this->get_amount($package_id), $order_id);
    }
    function create_subscription_draft_order($package_id){
        $order_id = $this->create_order($package_id);

        if( is_wp_error( $order_id ) || $order_id == null ){
            var_dump('is_wp_error_order');
            return new WP_Error( 'add_order_fail', $order_id->get_error_message() );
        }

        return $this->get_subscription_url($package_id, $order_id);

    }
	function get_redirect_response( $order_id, $amount ) {

        //https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/
        $receiver_email =  $this->get_receiver_email();
        $redirect_link = $this->get_return_link();

        $redirect_link = add_query_arg('order_id', $order_id, $redirect_link );
        $notify_url = $redirect_link;

        global $box_currency;

        //$symbol = box_get_currency_symbol($box_currency->code);
        global $user_ID;
        $redirect_url = $this->get_submit_url().'?cmd=_xclick&custom='.$order_id.'&currency_code='.$box_currency->code.'&business='.$receiver_email.'&item_name=deposit_fund&item_number=1234445&rm=2&amount='.$amount.'&no_note=1&invoice=box_'.$order_id.'&return='.$redirect_link.'&notify_url='.$notify_url;


        return  array(
            'msg' => 'Check done',
            'success'=> true,
            'redirect_url' => $redirect_url,
        );

    }
    function get_subscription_url($package_id,$order_id){
        $order = box_get_order($order_id);

        $amount = $order->amount;
        $pack   = get_post($package_id);
        $item_name = 'Subscription plan '.$pack->post_title.' in '.get_option('blogname'). ' site';
        $receiver_email =  $this->get_receiver_email();
        $redirect_link = $this->get_return_link();

        $redirect_link = add_query_arg('order_id', $order_id, $redirect_link );
        $notify_url = $redirect_link;

        global $user_ID, $box_currency;

        //$symbol = box_get_currency_symbol($box_currency->code);

        //The variable "src" with a value set to "1" means the payment will recur unless your customer cancels the subscription before the end of the billing cycle. If omitted, the subscription payment will not recur at the end of the billing cycle.

        // The variable "sra" with a value set to "1" means if the payment fails, the payment will be reattempted two more times. After the third failure, the subscription will be cancelled. If omitted and the payment fails, payment will not be reattempted and the subscription will be immediately cancelled.
        // SRA controls the reattempts which is two more times if the value is set to "1".

        //     <!-- Set recurring payments until canceled. -->
        // <input type="hidden" name="src" value="1">

        // <!-- PayPal reattempts failed recurring payments. -->
        // <input type="hidden" name="sra" value="1">

        $redirect_url = $this->get_submit_url().'?cmd=_xclick-subscriptions&currency_code='.$box_currency->code.'&business='.$receiver_email.'&item_name='.$item_name.'&src=1&item_number=123&a3='.$amount.'&p3=1&t3=M&invoice=box_'.$order_id.'&custom='.$user_ID.'&return='.$redirect_link.'&notify_url='.$notify_url;
        // t3=D => Day.
        // t3 = M => Month.
        // src (true or false) :auto pay or pay only 1.
        return $redirect_url;
    }
	   /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */

    public function verifyIPN() {


        // Get received values from post data.
        $validate_ipn        = wp_unslash( $_POST ); // WPCS: CSRF ok, input var ok.
        $validate_ipn['cmd'] = '_notify-validate';

        // Send back post vars to paypal.
        $params = array(
            'body'        => $validate_ipn,
            'timeout'     => 60,
            'httpversion' => '1.1',
            'compress'    => false,
            'decompress'  => false,
            'user-agent'  => 'WooCommerce/',
        );

        // Post back to get a response.
        $response = wp_safe_remote_post( $this->test_mode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $params );



        // Check to see if the request was valid.
        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( $response['body'], 'VERIFIED' ) ) {
            box_log('verify IDN successful.');
            return true;
        }


        if ( is_wp_error( $response ) ) {
            box_log( 'Error response: ' . $response->get_error_message() );
        }

        return false;
    }

    public function verifyIPN_old()    {
    	//https://github.com/paypal/ipn-code-samples/tree/master/php

        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
       //box_log($myPost);
        // Build the body of the verification post request, adding the _notify-validate command.
        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getPaypalUri());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            box_log("PayPal responded with http code $http_code");
            throw new Exception("PayPal responded with http code $http_code");
        }

        curl_close($ch);

        // Check if PayPal verifies the IPN data, and if so, return true.
        if ( $res == self::VALID ) {
            return true;
        } else {
            return false;
        }
    }
}
function box_get_paypal(){
    $info = array(
        'id' =>'paypal',
        'email' => '',
        'enabled' => 0,
        'possition' => 2,
        'test_mode' => 1,

    );
    $option = BX_Option::get_instance();
    $payment = $option->get_group_option('payment');
    $res =  (object) wp_parse_args($payment->paypal, $info);

    if( empty($res->email) )
        $res->enabled = false;


    return $res;
}