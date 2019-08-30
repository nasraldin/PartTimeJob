<?php
class Box_Paystack extends Box_Gateway{
    // Setup our Gateway's id, description and other values
    public static $instance;
    function __construct($order_id = 0) {

        $this->order_gateway = 'paystack';
        $this->id = 'paystack';
        $this->label = 'Paystack API';
        $this->api_description  = 'Allow payment via PayStack';
        $this->description      = 'Checkout inline via PayStack payment';
        $this->thumbnail        =  BOX_IMG_URL . '/paystack.png';
        $this->big_thumbnail    = BOX_IMG_URL . '/paystack-big.png';

        $this->api =box_get_paystack();
         $this->fields = array(
            array(
                'name' =>'live_secret_key',
                'label' =>'Live secret key',
                'type' =>'text',
            ),
            array(
                'name' =>'live_public_key',
                'label' =>'Live public key',
                'type' =>'text',
            ),
            array(
                'name' =>'test_secret_key',
                'label' =>'Tet  secret key',
                'type' =>'text',
            ),
             array(
                'name' =>'test_public_key',
                'label' =>'Tet  public key',
                'type' =>'text',
            ),
        );
         $this->description = 'Allow payment via PayStack';
        parent::__construct();

        $this->title                = $this->get_option( 'title' );
        $this->enabled              = $this->api->enabled;


        $this->payment_page         = home_url();

        $this->test_public_key      = $this->api->test_public_key;
        $this->test_secret_key      = $this->api->test_secret_key;

        $this->live_public_key      = $this->api->live_public_key;
        $this->live_secret_key      = $this->api->live_secret_key;

        $this->saved_cards          = $this->get_option( 'saved_cards' ) === 'yes' ? true : false;

        $this->split_payment        = $this->get_option( 'split_payment' ) === 'yes' ? true : false;
        $this->subaccount_code      = $this->get_option( 'subaccount_code' );
        $this->charges_account      = $this->get_option( 'split_payment_charge_account' );
        $this->transaction_charges  = $this->get_option( 'split_payment_transaction_charge' );

        $this->custom_metadata      = $this->get_option( 'custom_metadata' ) === 'yes' ? true : false;

        $this->meta_order_id        = $this->get_option( 'meta_order_id' ) === 'yes' ? true : false;
        $this->meta_name            = $this->get_option( 'meta_name' ) === 'yes' ? true : false;
        $this->meta_email           = $this->get_option( 'meta_email' ) === 'yes' ? true : false;
        $this->meta_phone           = $this->get_option( 'meta_phone' ) === 'yes' ? true : false;
        $this->meta_billing_address = $this->get_option( 'meta_billing_address' ) === 'yes' ? true : false;
        $this->meta_shipping_address= $this->get_option( 'meta_shipping_address' ) === 'yes' ? true : false;
        $this->meta_products        = $this->get_option( 'meta_products' ) === 'yes' ? true : false;

        $this->public_key           = $this->test_mode ? $this->test_public_key : $this->live_public_key;
        $this->secret_key           = $this->test_mode ? $this->test_secret_key : $this->live_secret_key;
        if( empty($this->public_key) || empty($this->secret_key) )
            $this->enabled = 0;
        //add_action('init', array($this, 'check_paystack_response'), 9);

        add_filter('box_order_object_checkout',array($this,'get_paystack_order_object'), 10 , 2);
        //add_filter('box_checkout_get_redirect', array($this,'mobile_respond_the_redirect_form'), 10 ,3);

        add_action( 'wp_ajax_manual_approved_paystack_order', array($this,'manual_approved_paystack_order' ));

    } // End __construct()
    function get_option(){
        return '';
    }
    function manual_approved_paystack_order(){
        $order_id = $_REQUEST['order_id'];
        $order = box_get_order($order_id);
        if($order->order_gateway == 'paystack'){
            bx_process_payment($order);
            $link = box_get_static_link('process-payment');
            $link = add_query_arg(array('order_id'=>$order->ID,'pay'=>'paystack'), $link);
            $response = array(
                'success' => true,
                'rediect_url' => htmlspecialchars_decode($link),
            );
            wp_send_json($response);
        }
    }
    function payment_scripts(){
        if( $this->check_enqueue_script() ){
            wp_enqueue_script( 'lib_paystack', esc_url('https://js.paystack.co/v1/inline.js'), array( 'jquery'), '1.1', false );
            wp_enqueue_script( 'box_paystack', esc_url(PAYSTACK_URL.'/assets/paystack.js'), array('lib_paystack'), '1.1', false );
            $current_user = wp_get_current_user();
            global $box_currency;
            $paystack_params = array(
                'key'   => $this->public_key,
                'customer_email' => $current_user->user_email,
                'firstname' => $current_user->user_firstname,
                'lastname' => $current_user->user_lastname,
                'currency_code' =>$box_currency->code,
            );
            wp_localize_script( 'box_paystack', 'box_paystack_params', $paystack_params );
        }
    }
    function get_redirect_response($order_id, $amount){
        $order = box_get_order($order_id);
        $response =   array(
            'msg'       => 'Deposit Done',
            'success'   => true,
            'order_id'  =>  $order->ID,
            'amount'    =>   $order->amount,
            'custom_js' => 'custom_redirect_paystack',
        );
        return $response;
    }
    static function get_instance(){
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    function create_temp_pending_order($pack_id, $project_id = 0){
        global $user_ID;
        $order_id = $this->create_deposit_pending_order_by_package($pack_id,'paystack');

        return box_get_order($order_id);
    }
    function get_paystack_order_object($object_order , $gateway){

        if($gateway == 'paystack'){
            return self::get_instance();
        }
        return $object_order;
    }

    function redirect_mobipay_gateway($gateway){

        if ($gateway == 'paystack') {

            $job_id = $order_pay['product_id'];
            $order_id = $order_pay['ID'];

            $amount = $order_pay['total'];
            $form = $this->generate_paystack_form($order_id, $order_pay);

            $response = array(
                'success' => true,
                'data' => array(
                    'ACK' => true,
                    'generate_form' => $form,
                ),
                'paymentType' => 'paystack'
            );

        }
    return $response;
    }
    function form_checkout1(){    ?>
            <div class="payment-item payment-paystack-item">
                <div class="payment-item-radio">
                    <label class="box-radio box-paystack is-not-empty js-valid" for="paystack">
                        <input type="radio" name="type" value="paystack" id="paystack" required="required" class="checkbox-gateway">
                        <span class="check"></span>
                            <img src="<?php echo PAYSTACK_URL;?>/assets/paystack.png" class="paystack-img" height="23" alt="" width="106">
                    <i class="ico-valid"></i></label>
                </div>
                <form method="post" class="frm-main-checkout" id="pay_stack_form">
                    <input type="hidden" name="_gateway" value="paystack">
                    <button type="submit" class="btn-submit-payment btn-js-paystack"> Pay </button>
                </form>
                <div  class="payment-fields paystack" style="text-align: center; padding: 15px;">
                    <img class="js-complete_order" src="<?php echo PAYSTACK_URL;?>/assets/paystack-big.png" alt="" width="150">
                    <div class="text-info">
                        You will checkout inline onsite - no need to redirect.
                    </div>
                </div>
            </div>
        <?php


    }

    /**
    * Check for valid MobilPay server callback
    **/
    function check_paystack_response(){
        $this->bpLog('check_IDN');
        $this->bpLog($_POST);
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
        if(!$file)  $file = PAYSTACK_PATH.'/bplog.txt';
        file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

        if (is_array($contents))
            $contents = var_export($contents, true);
        else if (is_object($contents))
            $contents = json_encode($contents);

        file_put_contents($file, $contents."\n", FILE_APPEND);
    }

}
new Box_Paystack();


