<?php

class Box_PayFast_Setting extends Box_GateWay_Setting{
    function __construct(){

        $this->id = 'payfast';
        $this->api = box_get_payfast();
        $this->label = 'PayFast API';
        parent::__construct();
    }
     function show_payment_config($config){
        global $text_on, $text_off, $gatew_link;
        if($config == $this->id){ ?>
             <div class="col-sm-9 ">
                    <div class="field-item">
                        <span class="f-left field-label"><?php _e('Real Secret Key','boxtheme');?></span>
                        <input type="email" class="form-control auto-save" alt="paystack" value="<?php if( ! empty( $paystack->secret_key ) ) echo $paystack->secret_key;?>" level="1" name="secret_key" placeholder="<?php _e('Real Secret Key','boxtheme');?>">
                    </div>
                    <div class="field-item">
                        <span class="f-left field-label"><?php _e('Real Public Key','boxtheme');?></span>
                        <input type="email" class="form-control auto-save" alt="paystack" value="<?php if( ! empty( $paystack->merchant_key ) ) echo $paystack->merchant_key;?>" level="1" name="merchant_key" placeholder="<?php _e('Set Real Public Key','boxtheme');?>">
                    </div>
                    <div class="field-item">
                        <span class="f-left field-label"><?php _e('Test Secret Key','boxtheme');?></span>
                        <input type="email" class="form-control auto-save" alt="paystack" value="<?php if( ! empty( $paystack->test_secret_key ) ) echo $paystack->test_secret_key;?>" level="1" name="test_secret_key" placeholder="<?php _e('Test  Secret key','boxtheme');?>">
                    </div>
                    <div class="field-item">
                        <span class="f-left field-label"><?php _e('Test Public Key','boxtheme');?></span>
                        <input type="email" class="form-control auto-save" alt="paystack" value="<?php if( ! empty( $paystack->test_public_key ) ) echo $paystack->test_public_key;?>" level="1" name="test_public_key" placeholder="<?php _e('Set Test Publick Key','boxtheme');?>">
                    </div>





                    <span class="f-left field-label"><?php _e('Test Merchant ID ','boxtheme');?></span>
                    <input type="email" class="form-control auto-save" alt="payfast" value="<?php if( ! empty( $payfast->test_merchant_id ) ) echo $payfast->test_merchant_id;?>" level="1" name="test_merchant_id" placeholder="<?php _e('Set  Test Merchant ID ','boxtheme');?>">

                    <span class="f-left field-label"><?php _e('Test Merchant  Key','boxtheme');?></span>
                    <input type="email" class="form-control auto-save" alt="payfast" value="<?php if( ! empty( $payfast->test_merchant_key ) ) echo $payfast->test_merchant_key;?>" level="1" name="test_merchant_key" placeholder="<?php _e('Set Your Test Merchant  key','boxtheme');?>">
                    <div class="col-sm-9">
                    </div>
                    <div class="col-sm-3 align-right row-switch">
                        <?php bx_swap_button('enabled', $paystack->enabled, 1,  'Enabled', 'Disabled' );?>
                    </div>

                </div>
        <?php
        }
    }

}
new Box_PayFast_Setting();