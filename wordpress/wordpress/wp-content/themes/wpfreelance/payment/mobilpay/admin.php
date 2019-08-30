<?php


class Box_Mobilpay_Setting extends Box_GateWay_Setting{
    function __construct(){

        parent::__construct();
        $this->id = 'mobilpay';
        $this->api = box_get_mobipay_api();
        $this->label = 'MobilPay API';
        parent::__construct();

    }
     function show_payment_config($config){
        global $text_on, $text_off, $gatew_link;
        if($config == $this->id){ ?>
            <div class="col-sm-9 ">
                    <div class="field-item">
                        <span class="f-left field-label"><?php _e('Merchant Account ID','boxtheme');?></span>
                        <input type="email" class="form-control  auto-save" alt="mobilpay" value="<?php if( ! empty( $mobilpay->account_id ) ) echo $mobilpay->account_id;?>" level="1" name="account_id" placeholder="<?php _e('Merchant Account ID','boxtheme');?>">
                    </div>
                    <div class="field-item">
                        <span class="f-left f-left-inline  inline field-label"><?php _e('Testing Mode','boxtheme');?></span>
                        <input type="checkbox" class="form-control input-type-checkbox auto-save" alt="mobilpay"  <?php if($mobilpay->is_testing_mode) echo 'checked ';?> level="1" name="is_testing_mode" >
                    </div>
                   <p>File manual install <a href="<?php echo MOBILPAY_URL;?>/manual_eninstall.pdf">manual_eninstall.pdf</a></p>
                </div>
                <div class="col-sm-9">
                </div>
                <div class="col-sm-3 align-right row-switch">
                    <?php bx_swap_button('enabled', $mobilpay->enabled, 1, 'Enabled','Disabled');?>
                </div>
        <?php
        }
    }

}
new Box_Mobilpay_Setting();