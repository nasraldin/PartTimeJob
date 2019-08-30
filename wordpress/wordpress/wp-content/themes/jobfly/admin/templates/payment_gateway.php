<?php

// group = escrow
$option = BX_Option::get_instance();

$general = $option->get_general_option();
$checkout_mode = (int) $general->checkout_mode;
$group_option = "payment";
$payment = $option->get_group_option($group_option);

$paypal = (object) $payment->paypal;
$cash = (object) $payment->cash;
$mode = $payment->mode;

// // echo '<pre>';
// // var_dump($cash);
// echo '</pre>';

$cash_enable = $cash->enable;
$pp_enable = 0;

if(isset($paypal->enable) )
    $pp_enable = $paypal->enable;


?>

<div id="general" class="main-group">
	<h2 class="section-title"><?php _e('Payent Gateways Settings','boxtheme');?> </h2>

	<div class="form-group -row">
		<div class="col-md-3"><label><?php _e('Checkout out mode','boxtheme');?></label></div>
		<div class="col-md-9"><?php _e('Live mode is','boxtheme');?> <?php  bx_swap_button('general','checkout_mode', $checkout_mode, 0);?>  <br /><span>if enable this option, all job only appearances in the site after admin manually approve it.</span></div>
	</div>

</div>
<?php
$section = 'paypal';
$item = 'email';
?>
<div id="<?php echo $group_option;?>" class="main-group">
	<div class="full">
		<div class="full sub-item" id="<?php echo $section;?>" >

     		<div class="sub-section" id="paypal">
                <label for="inputEmail3" class="col-sm-3 col-form-label">PayPal</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control auto-save" alt="paypal" value="<?php if( ! empty( $paypal->email ) ) echo $paypal->email;?>" level="1" name="email" placeholder="<?php _e('Your Paypal email','boxtheme');?>">
                     <span class="f-right"><?php _e('Set PayPal Email','boxtheme');?></span>
                </div>
                <div class="col-sm-9">
                </div>
                <div class="col-sm-3 align-right">
                    <?php bx_swap_button('payment','enable', $pp_enable, 1);?>
                </div>
            </div>

            <div class="sub-section" id="cash">
            	 <div class="sub-item" id="cash">
	            	<label for="inputEmail3" class="col-sm-3 col-form-label">Cash</label>
	            	<?php

	                $cash_des = $option->get_default_option($group_option,'cash','description', 1);
	                if( isset($cash->description) )
	                    $cash_des = $cash->description;
	            	?>
	                <div class="col-sm-9 wrap-auto-save">
	                	 <textarea name="description" id="description" class="auto-save simple" level="1"> <?php echo esc_html($cash_des);?></textarea>
	                	<div class="hide">
	                	<?php wp_editor($cash->description,'call');?>

	                	</div>
	                </div>

	                <div class="col-sm-9">           </div>
	                <div class="col-sm-3 align-right">
	                    <?php bx_swap_button('payment','enable', $cash_enable, 1);?>
	                </div>
	            </div>
	       	</div>

	    </div>
	</div>
</div>