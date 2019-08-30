<?php
Class BX_Paypal extends BX_Order {
	//https://github.com/paypal/ipn-code-samples
	// check IPN return
	// IPN setup https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/

    private $amout;
	public $submit_url;
	public $payment_type;
	public $receiver_email;

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
		parent::__construct();
		$this->payment_type = 'paypal';
		$this->order_title  = 'Buy credit via paypal';
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
        if ($this->use_sandbox) {
            return self::SANDBOX_VERIFY_URI;
        } else {
            return self::VERIFY_URI;
        }
    }

	function get_submit_url(){
		if($this->use_sandbox){
			return self::SANDBOX_SUBMIT_URI;
		}
		return self::SUBMIT_URI;
	}
	function create_order($package_id, $project_id = 0){
        $receiver_email = $this->get_receiver_email();

        if( empty( $receiver_email) ){
            return new WP_Error( '_empty_receiver',__('Please set receiver email','boxtheme') );
        }

		$this->receiver_email = $this->get_receiver_email();
		return $this->create_draft_order($package_id, $project_id);
	}
    /**
     * get admin's paypal email in setting.
     * This is a cool function
     * @author danng
     * @version 1.0
     * @return  paypal email of admin settings.
     */
    function get_receiver_email(){
        $t = (object) BX_Option::get_instance()->get_option('payment','paypal');
        return $t->email;
    }

    /**
     * create a pending order and return the paypal redirect to check out this order.
     * @author danng
     * @version 1.0
     * @param   [type] $package_id [description]
     * @return  the submit url and system auto redirect to this url
     */
    function create_pending_order( $package_id, $project_id = '' ){
		$order_id = $this->create_order($package_id, $project_id);

		if( is_wp_error( $order_id ) || $order_id == null ){
			return new WP_Error( 'add_order_fail', $order_id->get_error_message() );
		}

		return $this->get_redirect_url($this->get_amout($package_id), $order_id);
	}

	function get_redirect_url( $amount, $order_id ) {

        //https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/formbasics/
        $receiver_email =  $this->get_receiver_email();
        $redirect_link = $this->get_redirect_link();

        $redirect_link = add_query_arg('order_id', $order_id, $redirect_link );
        $notify_url = $redirect_link;

        global $box_currency;

        //$symbol = box_get_currency_symbol($box_currency->code);

        $redirect_url = $this->get_submit_url().'?cmd=_xclick&currency_code='.$box_currency->code.'&business='.$receiver_email.'&item_name=abc act&item_number=123&amount='.$amount.'&invoice=box_'.$order_id.'&return='.$redirect_link.'&notify_url='.$notify_url;

        return $redirect_url;
    }

	   /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()    {
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

        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        // if ($this->use_local_certs) {
        //     curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
        // }
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