<?php

$group_option = "general";$option = BX_Option::get_instance();
$general = $option->get_general_option();

$text_on = __('Enabled','boxtheme');
$text_off = __('Disabled','boxtheme');
$number_bid_free = $general->number_bid_free;
if(empty($number_bid_free))
	$number_bid_free = 15;
?>

<div id="<?php echo $group_option;?>" class="main-group">
	<div class="full">
		<div class="full sub-item " >
			<div class="sub-section " id="general">
				<h3 class="groupd-title col-md-12">Personal Account Setting</h3>
			</div>
     		<div class="sub-section " id="general">

                <div class="full ">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">Number Bid Free</label>
                    <div class="col-sm-9 ">
                        <div class="field-item no-label">
                            <input type="number" class="form-control auto-save" alt="paypal" value="<?php echo $number_bid_free;?>" name="number_bid_free" placeholder="<?php _e('Number Bid Free','boxtheme');?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="sub-section " id="general">
                <div class="full ">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">Professional Label Default</label>
                    <div class="col-sm-9 ">
                        <div class="field-item no-label ">
                          	<input class="form-control auto-save"   name="professional_default" value="<?php echo stripslashes($general->professional_default);?>"/>
                        </div>
                    </div>
                </div>
            </div>


             <div class="sub-section " id="general">
                <label for="inputEmail3" class="col-sm-3 col-form-label"><?php _e('Requires Avatar In Signup Form','boxtheme');?></label>
                <div class="col-md-9">
                    <div class="field-item no-label switch-field">
                        <?php bx_swap_button('singup_avatar_field', $general->singup_avatar_field, $multipe = false);?><br /><span><?php _e('if enable this option, Users must to upload avatar in the signup form.','boxtheme');?></span>

                    </div>
                </div>
            </div>


            <div class="sub-section " id="general">
				<label for="inputEmail3" class="col-sm-3 col-form-label"><?php _e('Requires Confirmation','boxtheme');?></label>
				<div class="col-md-9">
					<div class="field-item no-label switch-field">
						<?php bx_swap_button('requires_confirm', $general->requires_confirm, $multipe = false);?><br /><span><?php _e('if enable this option,Freelancers has to confirm account after register account.','boxtheme');?></span>

					</div>
				</div>
			</div>

            <div class="sub-section " id="general">
                <label for="inputEmail3" class="col-sm-3 col-form-label"><?php _e('Send Diectly Message','boxtheme');?></label>
                <div class="col-md-9">
                    <div class="field-item no-label switch-field">
                        <?php bx_swap_button('direct_message', $general->direct_message, $multipe = false);?><br /><span><?php _e('if enable this option,Anyone(logged) can send a directly message to freelancer.','boxtheme');?></span>

                    </div>
                </div>
            </div>

	    </div>
	</div>
</div>