<?php

Class Box_Stripe extends Box_Gateway{
    private $secret_key;
    private $publishable_key;
    private $live_publishable_key;
    private $live_secret_key;
    private $test_publishable_key;
    private $test_secret_key;
    public static $instance;

    const ENDPOINT = 'https://api.stripe.com/v1/charges';
    const SANBOX_ENDPOINT = 'https://api.stripe.com/v1/charges';

    function __construct(){
        $this->id = 'stripe';
        $this->api = $this->get_stripe_api();

        $this->enabled = $this->api->enabled;


        $this->label = 'Stripe API';
        $this->description  = 'Allow payment via Stripe.com';
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
        parent::__construct();


        $this->publishable_key  = ( $this->test_mode ) ? $this->api->test_publishable_key : $this->api->live_publishable_key;
        $this->secret_key       = ( $this->test_mode ) ? $this->api->test_secret_key : $this->api->live_secret_key;

        if( empty( $this->publishable_key ) || empty( $this->secret_key ) )
            $this->enabled = false;

        add_action( 'wp_head', array($this, 'add_stripe_js_in_headtag') );
        add_action( 'wp_ajax_box_stripe_ajax',array($this,'check_ajax_stripe' ));
        add_action( 'box_subscription_form', array($this, 'subcription_pw_checkout') );
        add_action( 'wp_footer', array( $this, 'add_stripe_js_footer') );
    }
    function add_stripe_js_footer(){
        if( is_page_template('page-deposit.php') && $this->enabled ) {
            $current_user = wp_get_current_user(); ?>
            <script type="text/javascript">
                var stripe = Stripe('<?php echo $this->publishable_key;?>');
                var checkoutButton = document.querySelector('#checkout-button');
                checkoutButton.addEventListener('click', function () {
                    stripe.redirectToCheckout({
                        items: [{
                            // Define the product and plan in the Dashboard first, and use the plan
                            // ID in your client-side code.
                            plan: '2-boxthemes.net-59b7b14aedd06',
                            quantity: 1,

                        }],
                        successUrl: 'https://lab.boxthemes.net/success/',
                        cancelUrl: 'https://lab.boxthemes.net/cancel/',
                        customerEmail: '<?php echo $current_user->user_email;?>',
                        // sessionId: '123',
                    });
                });
            </script>
            <?php
        }
    }
    function add_stripe_js_in_headtag(){
        if( $this->check_enqueue_script() ){ ?>
            <script src="https://js.stripe.com/v3/"></script><?php
        }
        if( is_page_template('page-checkout.php') ) { ?>
            <script src="https://js.stripe.com/v3"></script>
            <script src="https://checkout.stripe.com/checkout.js"></script>
            <?php
        }
    }
    static function get_instance(){
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    function get_endpoint(){
        return self::ENDPOINT;

    }
    function payment_scripts(){
        if ( $this->check_enqueue_script() ){
            wp_enqueue_script( 'box_stripe_js', esc_url(get_template_directory_uri().'/payment/stripe/stripe.js'), array(), BOX_VERSION, true );
            wp_enqueue_style( 'box_stripe', esc_url(get_template_directory_uri().'/payment/stripe/stripe.css'), array(), BOX_VERSION, false );

            $box_params = array(
                'publishable_key'   => $this->publishable_key,
            );
            wp_localize_script( 'box_stripe_js', 'box_stripe', $box_params );

        }
    }
    function subcription_pw_checkout(){
        if($this->enabled){    ?>
            <div class="payment-item payment-stripe-item  stripeeeeeeeeee_xooo hide">
                <div class="payment-item-radio">
                    <label class="box-radio is-not-empty js-valid">
                        <input type="radio" name="gateway" value="stripe" id="stripe" class="checkbox-gateway" required="required">
                        <span class="check"></span>
                        Stripe
                        <img src="<?php echo get_template_directory_uri();?>img/paypal.jpg" class="stripe-img hide" width="100" alt="">
                    <i class="ico-valid"></i></label>
                    <button id="checkout-button" type="reset" >Subscribe Via Stripe</button>
                </div>
               <!--  <form method="post" class="df-box-checkout-js  form_js_paypal hide">
                    <input type="hidden" name="_gateway" value="stripe">
                    <button type="submit" class="btn-submit-payment btn-js-stripe">Submit</button>
                </form> -->
            </div>
        <?php  }
    }
    function form_checkout(){
        $checked = (get_first_gateway_id() == $this->id) ? 'checked' :'';
        $is_selected = (get_first_gateway_id() == $this->id) ? 'is-selected' : '';
        ?>
        <div class="payment-item stripe-gw-item box_stripe_form_boxxxxxxxxxxxxxx <?php echo $is_selected;?>">
          <div class="payment-item-radio">
            <label class="box-radio box-cc is-not-empty" >
                <input type="radio" name="type" value="stripe" id="stripe" required="required" class="checkbox-gateway" <?php echo $checked;?>>
                <span class="check"></span>
                    <span class="payment-title">Credit/Debit Card</span>

                    <span class="cc-description">
                        <img src="<?php echo BOX_IMG_URL;?>/padlock.svg" width="11">&nbsp;Secure 128-bit SSL encrypted payment
                      </span>
                    <img src="<?php echo BOX_IMG_URL;?>/cc.svg" class="credit-card-icon" height="23" alt="">

            <i class="ico-valid"></i></label>
          </div>
          <div class="payment-fields cc">
            <form action="/charge"  id="payment-form" class="act_stripe_js form_js_stripe" method="post">
                <input name="cardholder-name" class="cardholder-name" placeholder="<?php _e('Cardholder Name','boxtheme');?>" />
                <div id="card-element"  class="fieldr"></div>
                <div id="card-errors" role="alert"></div>
                <button type="submit" name="submit" class="btn btn-submit f-right btn-submit-payment btn-js-stripe" value="Complete">Complete Order</button>

            </form>
          </div>
        </div>

        <?php
    }
    function check_ajax_stripe(){

        \Stripe\Stripe::setApiKey( $this->secret_key );
        $submit = $_POST['request'];

        parse_str($submit, $output);

        $token = $output['stripeToken'];
        $amount = $_POST['amount'];

        $post_data =  array(
            'amount' => $amount * 100,
            'currency' => 'usd',
            'source'=> $token,
            'description'=>'Test purchasing'
        );

        $endpoint = 'https://api.stripe.com/v1/apple_pay/domains';
        $endpoint = 'https://api.stripe.com/v1/charges';
        $data = array(
        'domain_name' => $_SERVER['HTTP_HOST']
        );
        $headers = array(
            'User-Agent'    => 'WooCommerce Stripe Apple Pay',
            'Authorization' => 'Bearer ' . $this->secret_key,
        );
        $app_if = array(
            'name'    => 'WooCommerce Stripe Gateway',
            'version' => '1.0',
            'url'     => 'https://woocommerce.com/products/stripe/',
        );

        $user_agent =  array(
          'lang'         => 'php',
          'lang_version' => phpversion(),
          'publisher'    => 'woocommerce',
          'uname'        => php_uname(),
          'application'  => $app_if,
        );
        $app_info   = $user_agent['application'];

        $headers =  apply_filters( 'woocommerce_stripe_request_headers', array(
          'Authorization'              => 'Basic ' . base64_encode( $this->secret_key . ':' ),
          'Stripe-Version'             => '2018-05-21',
          'User-Agent'                 => $app_info['name'] . '/' . $app_info['version'] . ' (' . $app_info['url'] . ')',
          'X-Stripe-Client-User-Agent' => json_encode( $user_agent ),
        ) );

        $order_id = '199xxxxxxxxxxxxxxxxxxxx99'.rand();
        $headers['Idempotency-Key'] = $order_id;
        $response = array();
        try{
            $response = wp_safe_remote_post(
                Box_Stripe::get_instance()->get_endpoint(),
                array(
                    'method'  => 'POST',
                    'headers' => $headers,
                    'body'    =>  $post_data,
                    'timeout' => 70,
                )
            );
        } catch (Exception $e) {

        }
        $headers  = $response['headers']; $response = $response['body'];$resdata = array(); $url = '';

        if ( ! empty( $response->error ) ) {

        } else {
            $response = json_decode ($response);

            $captured = ( isset( $response->captured ) && $response->captured ) ? 'yes' : 'no';

            if ( 'yes' === $captured ) {
                $status = $response->status; //pending, succeeded, failed
                $order_id = $this->create_deposit_draft_order($amount, $this->api);

                if( $order_id && !is_wp_error($order_id) ) {
                    bx_process_payment($order_id);
                    $url = add_query_arg(array( 'order_id' => $order_id), box_get_static_link('thankyou') );
                }
            }
        }

        $resdata= array('success' => true,'url' => $url, 'data'=> $response);
        wp_send_json($resdata);
    }

    function get_stripe_api() {

        $default = array(
            'id'=>'stripe',
            'live_publishable_key' => '',
            'live_secret_key' => '',
            'test_publishable_key' => 'pk_test_zVPplNjodRmXbOpcDqczDqbn',
            'test_secret_key' => 'sk_test_P99RWRejQBToVFteM4GlIn3k',
            'enabled' => 0,
            'test_mode' => 1,
            'possition' => 1,
        );
        $payment =  BX_Option::get_instance()->get_group_option('payment');
        $stripe =  (object) wp_parse_args($payment->stripe, $default);
        return $stripe;
    }
}