<?php

// group = escrow
$option = BX_Option::get_instance();
global $text_on,$text_off;
$general = $option->get_general_option();
$is_real_payment = (int) $general->checkout_mode;

$class=" show-real-payment-api";
if(!$is_real_payment){
    $class=" show-sandbox-api";
}
$group_option = "payment";
$payment = $option->get_group_option($group_option);

$paypal = (object) $payment->paypal;

$mode = $payment->mode;

$pp_enable = 0;
$config = isset($_GET['config']) ? $_GET['config'] : '';
global $gatew_link;
$gatew_link         = admin_url('admin.php?page='.BOX_SETTING_SLUG);

$stripe_config   = add_query_arg(array('config'=>'stripe','section'=>'payment_gateways'), $gatew_link);
$show_config = false;
if(!empty($config))
$show_config = true;

if(isset($paypal->enabled) )
    $pp_enable = $paypal->enabled;
$text_on = __('Enabled','boxtheme');
$text_off = __('Disabled','boxtheme');
?>
<div id="general" class="main-group">
	<h2><?php _e('Payent Gateways Settings','boxtheme');?> </h2>
    <h5> Payment gateways use to deposit credit/post a premium job - Not available for subscription member.</h5>
    <p> Installed payment methods are listed below and can be sorted to control their display order on the frontend.</p>
    <br />
    <br />
</div>

<div id="<?php echo $group_option;?>" class="main-group">
	<div class="full">
		<div class="full setting-payments sub-item <?php echo $class;?>" >
            <?php if(!$config){?>
            <div class="heading full">
                <div class="col-sm-1">
                    <img src="<?php echo BOX_IMG_URL;?>/sort.png" width="20" />
                </div>
                <div  class="col-sm-4 col-form-label">
                    <label for="stripe" class=" col-form-label col-md-7">Method</label>
                    <label class="col-md-5 pull-right float-right">Enabled</label>
                </div>

                <div class="col-sm-5"><label>Description</label></div>
                <div class="col-sm-2 text-right"></div>
            </div>
            <?php } ?>
            <?php box_gateway_settings($config);?>

            <?php do_action('show_payment_config', $config);?>


	    </div>
	</div>
</div>