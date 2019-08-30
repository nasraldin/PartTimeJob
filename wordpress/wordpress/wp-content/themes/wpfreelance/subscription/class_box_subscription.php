<?php
CLASS Box_Subscription{
	public $id;
	public $api;
	public $fields;
	function __construct(){
		add_action('box_subscription_form', array($this,'add_subcription_button') );
		add_action('init',array($this,'verify_response'));
	}
	function html_fields(){

	}
	function add_subcription_button(){

	}
}
class Stripe_Subscription extends Box_Subscription{
	function __construct(){
		$this->fields 	= $this->get_fields();
		$this->api 		= $this->get_api();
		parent::__construct();
		add_action('wp_head',array($this,'add_script_to_head_tag'));
		add_action('wp_footer',array($this,'add_script_to_footer_tag'));
	}

    function verify_response(){

   	 	$uri_confirming = isset( $_SERVER['SCRIPT_URL'] ) ? $_SERVER['SCRIPT_URL'] : 0;

    	if( $uri_confirming == '/success/' ){
	        \Stripe\Stripe::setApiKey("pk_test_WcPh2d4JR4Psog2FQHTHMiPK");

	        // You can find your endpoint's secret in your webhook settings
	        $endpoint_secret = 'whsec_xwwf5kzL4EbAOIJM8NOKx4VYoLvab0xh';

	        $payload    = file_get_contents('php://input');
	        $sig_header = isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ?  $_SERVER['HTTP_STRIPE_SIGNATURE'] : 0;

	        $event = null;
	        //box_log($_POST);
	       // box_log($_SERVER);

	        try {
	            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret );
	        } catch(\UnexpectedValueException $e) {

	            // Invalid payload
	            box_log('line 22');
	            box_log($e->getMessage());
	            //box_log($e);
	            http_response_code(400); // PHP 5.4 or greaterr
	            return;
	           //exit();
	        } catch(\Stripe\Error\SignatureVerification $e) {
	            // Invalid signature
	            box_log('line 27');
	            box_log($e->getMessage() );
	            http_response_code(400); // PHP 5.4 or greater
	            exit();
	        }
	        //box_log($event);

	        // Handle the checkout.session.completed event
	        if ($event->type == 'checkout.session.completed') {
	            $session = $event->data->object;
	            box_log('verifed done.');
	            // Fulfill the purchase...
	           // handle_checkout_session($session);
	        }

	        http_response_code(200);
    	}
    }
	function add_script_to_head_tag(){?>
		<script src="https://js.stripe.com/v3"></script>
		<script src="https://checkout.stripe.com/checkout.js"></script>
	<?php }
	function add_subcription_button(){ ?>
		<button id="checkout-button" >Subscribe Via Stripe</button>
		<button id="customButton">Purchase</button>
		<form action="/create_subscription.php" method="POST" class="stripe_subscription_js">
              <?php $price = 79*100;?>
              <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="pk_test_WcPh2d4JR4Psog2FQHTHMiPK"

                data-name="Emma's Farm CSA"
                data-description="Subscription for 1 weekly box"
                data-amount="<?php echo $price;?>"
                data-label="Sign Me Up!">
              </script>
            </form>
            <script type="text/javascript">
                (function($){
                  $(document).ready(function(){
                    $(".stripe_subscription_js").clic(function(){
                      console.log('123');
                    })
                  })
                })
            </script>
		<?php
	}
	function get_fields(){
		return array(
			array(
				'name' => 'test_publishable_key',
                'label' => 'Test Publishable key',
                'type' =>'text',
			),
			array(
				'name' => 'test_secret_key',
                'label' => 'Test secret key',
                'type' =>'text',
			),
			array(
				'name' => 'test_endpoint_secret',
                'label' => 'Test endpoint secret',
                'type' =>'text',
			),
			array(
				'name' => 'live_publishable_key',
                'label' => 'Live Publishable key',
                'type' =>'text',
			),
			array(
				'name' => 'live_secret_key',
                'label' => 'Live secret key',
                'type' =>'text',
			),
			array(
				'name' => 'live_endpoint_secret',
                'label' => 'Live endpoint secret',
                'type' =>'text',
			),
		);
	}
	function get_api(){

	}
	function add_script_to_footer_tag(){
		if( ! is_page_template('page-checkout.php') ){
			return;
		}
		$current_user = wp_get_current_user();?>
		<script type="text/javascript">
			var stripe 			= Stripe('pk_test_WcPh2d4JR4Psog2FQHTHMiPK');
            var checkoutButton 	= document.querySelector('#checkout-button');

            checkoutButton.addEventListener('click', function () {
              	stripe.redirectToCheckout({
	                items: [{
	                  // Define the product and plan in the Dashboard first, and use the plan
	                  // ID in your client-side code.
	                  plan: '2-boxthemes.net-59b7b14aedd06',
	                  quantity: 1,

	                }],
	                successUrl: 'https://lab.wpable.net/success/',
	                cancelUrl: 'https://lab.wpable.net/cancel/',
	                customerEmail: '<?php echo $current_user->user_email;?>',
	               // sessionId: '123',
             	});
            });
        </script>

        	 <script>
                var handler = StripeCheckout.configure({
                    key: 'pk_test_WcPh2d4JR4Psog2FQHTHMiPK',
                    image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
                    locale: 'auto',
                    token: function(token) {
                        console.log(token);
                        console.log('call ajax here');
                        // You can access the token ID with `token.id`.
                        // Get the token ID to your server-side code for use.
                    }
                });

                document.getElementById('customButton').addEventListener('click', function(e) {
                  // Open Checkout with further options:
                    handler.open({
                        name: 'CE Testing',
                        description: '2 widgets',
                        amount: 2000,
                        email:'abc@gmail.com',
                        allowRememberMe: false,
                    });
                    e.preventDefault();
                });
                // Close Checkout on page navigation:
                window.addEventListener('popstate', function() {
                  handler.close();
                });
            </script>

        <?php
    }
}
//new Stripe_Subscription();