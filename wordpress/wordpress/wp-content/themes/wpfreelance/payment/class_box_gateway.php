<?php

class Box_Gateway{
	public $id;
	public $possition;
    public $fields;
    public $label;
    public $description;
    public $link;
    public $test_mode;
    public $enabled;
	function __construct(){
		$this->possition      = 20;
        $this->test_mode    = 1;
        $this->enabled      = 0;
        if( $this->api ){
        	if(empty($this->api->possition)){
        		die($this->id .' set possition please');
        	}
        	$this->possition = $this->api->possition;
            $this->test_mode = (boolean) $this->api->test_mode;
            $this->enabled = $this->api->enabled;
             $this->id = $this->api->id;
        }
        $this->df_link    = admin_url('admin.php?page='.BOX_SETTING_SLUG);
        $this->df_link  = add_query_arg('section','payment_gateways',$this->df_link);
        $this->link     = add_query_arg('config',$this->id,$this->df_link);

        array_push($this->fields, array(
                'name' =>'test_mode',
                'label' =>'Enable Test Mode',
                'type' =>'checkbox',
                'class' =>'field-inline',
            )
        );

        $this->form_class = 'df-box-checkout-js  form_js_'.$this->id;
        if ( isset($this->specific_form) && $this->specific_form == 1 ){
            $this->form_class = 'form_js_'.$this->id;
        }

		add_filter('add_payment_gateway', array($this,'add_gateway_backend') );
		add_action('payment_gateway_html_'.$this->id, array($this,'show_checkout_form') );
        add_action('init', array($this, 'box_verify_response') );
        add_action('add_payment_setting_'.$this->id,array($this,'show_heading_gateway_line'), $this->possition ); // show setting in back-end.

        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ), 999 );
       // add_action( 'wp_ajax_box_checkout',array($this,'process_checkout_deposit') );
	}
    function create_deposit_draft_order($amout) {
        $order = new BX_order();
        return $order->create_deposit_draft_order( $amout, $this->api );
    }

    function get_return_link(){
        return box_get_static_link('thankyou');
    }
    function process_checkout_deposit(){


    }
    function get_redirect_response($order_id, $amout){
        $return = add_query_arg('order_id',$order_id,$this->get_return_link());
        return  array(
            'msg' => 'Check done',
            'success'=> true,
            'redirect_url' => $return,
        );
    }
    function create_membership_draft_order( $package_id ){
        $order = new BX_order();
        return $order->create_membership_draft_order( $package_id, $this->api );
    }


    function check_enqueue_script(){
        if( ! $this->enabled )
            return false;
        if ( is_page_template('page-deposit.php') )
            return true;
        return false;
    }
	function add_gateway_backend($list){
        $list[$this->possition] = array( 'id'=> $this->id, 'possition' => $this->possition );
        return $list;
    }
    function payment_scripts(){

    }
    function show_checkout_form(){
        if($this->enabled)
            $this->form_checkout();
    }
    function form_checkout(){
        $checked = (get_first_gateway_id() == $this->id) ? 'checked' :''; ?>
        <div class="payment-item payment-<?php echo $this->id;?>-item  box_default_form_xxxxxxxxxxxxxx ">
            <div class="payment-item-radio">
                <label class="box-radio is-not-empty js-valid" >
                    <input type="radio" name="type" value="<?php echo $this->id;?>" id="<?php echo $this->id;?>" class="checkbox-gateway" required="required" <?php echo $checked;?> >
                    <span class="check"></span>
                        <img src="<?php echo $this->thumbnail;?>" class="paypal-img"  width="100" alt="">
                <i class="ico-valid"></i></label>
            </div>

            <div  class="payment-fields <?php echo $this->id;?> payment-detail">
                <img class="js-complete_order" src="<?php echo $this->big_thumbnail;?>" alt="">
                <div class="text-info">
                    <?php echo $this->description;?>
                </div>
            </div>
            <form method="post" class="<?php echo $this->form_class;?>">
                <input type="hidden" name="_gateway" value="<?php echo $this->id;?>">
                <button type="submit" class="btn-submit-payment btn-js-<?php echo $this->id;?>">Submit</button>
            </form>
        </div>
        <?php
    }

    function box_verify_response(){

    }
    function show_heading_gateway_line($config){
        if( ! $config ){   ?>
           <div class="sub-section " id="<?php echo $this->id;?>">
                <div class="full ">
                    <div class="col-sm-1"><button class="btn-short"><i class="fa fa-bars"></i></button></div>
                    <div  class="col-sm-4 col-form-label">
                        <label class="col-sm-7"><?php echo $this->label;?></label>
                        <div class="col-toggle-btn col-sm-5 float-right pull-right">
                            <?php bx_swap_button('enabled', $this->api->enabled, 1, 'Enabled', 'Disabled');?>
                        </div>
                    </div>

                    <div class="col-sm-5"><?php echo $this->description;?></div>
                    <div class="col-sm-2 text-right"><a class="button alignright btn-config-payment" href="<?php echo $this->link;?>"> SETUP </a></div>

                </div>
                <div class="config-api">
                    <?php $this->show_payment_config_fields();?>
                </div>
            </div>
            <?php
        }

    }
    function show_payment_config_fields(){
        global $text_on, $text_off;
        $fields = $this->fields;      ?>

        <div class="col-sm-1"></div>
        <div class="col-sm-9 ">
            <?php
            foreach ($fields as $key => $field) {
                $field = (object)$field;
                if(!isset($field->type))
                    $field->type = 'text';

                $wrap_class = 'wrap-field wrap-field-'.$field->type.' wrap-field-'.$field->name;
                if( isset($field->class) ){
                    $wrap_class .=' '.$field->class;
                }?>
                <div class="field-item  <?php echo $wrap_class;?>" >
                    <span class="f-left field-label"><?php echo $field->label;?></span>
                    <?php $this->generate_input_field($field,$this->api);?>
                </div>
                <?php
            }?>
        </div>
        <?php

    }
    function generate_input_field($field, $api){
        $class_css = ' form-control auto-save field-type-'.$field->type;
        switch ($field->type) {
            case 'email': ?>
                <input type="email" class="<?php echo $class_css;?>" value="<?php echo $api->{$field->name}; ?>" level="1" name="<?php echo $field->name;?>" placeholder="<?php echo $field->label;?>">
                <?php
                break;
            case 'checkbox':
                ?>
                <input type="checkbox" class="<?php echo $class_css;?>" value="<?php echo $api->{$field->name}; ?>" <?php if($api->{$field->name}) echo 'checked ';?> level="1" name="<?php echo $field->name;?>" placeholder="<?php echo $field->label;?>"> <?php
                break;
             case 'textarea': wp_editor($api->{$field->name},$field->name); ?>
                <textarea class="<?php echo $class_css;?>"  name="<?php echo $field->name;?>"><?php echo $api->{$field->name};?></textarea><?php
                break;

            default: ?>
                <input type="<?php echo $field->type;?>" class="field-type-<?php echo $field->type;?> form-control auto-save" value="<?php echo $api->{$field->name}; ?>" level="1" name="<?php echo $field->name;?>" placeholder="<?php echo $field->label;?>"><?php
                break;
        }
    }
}