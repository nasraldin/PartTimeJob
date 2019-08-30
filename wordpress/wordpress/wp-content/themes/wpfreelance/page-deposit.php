<?php
/**
 *Template Name: Deposit Credit
 */
?>
<?php get_header(); ?>

	<div class="container site-container" style="max-width: 1024px;">
		<div  id="content" >
			<h1 class="page-title" style="padding: 10px 0 20px 0; "><?php _e('Select Payment Method','boxtheme');?></h1>
			<div class="col-checkout col-left-checkout">
				<div class="col-content">
					<?php if( is_user_logged_in() ){ ?>
					<div class="frm-buy-credit frm-main-checkout">
						<div class="step step-2 select-gateway step-select-gateway">
							<?php deposit_list_payment();?>
						</div>
					</div>
					<?php } else {
                        _e('Please login to buy credit','boxtheme');
					}?>
				</div>
			</div>
			<div class=" col-checkout col-right-checkout float-right pull-right">
                <div class="col-content">
                    <table class="shop_table membership-order">
                        <tbody>
                            <tr class="cart_item">
                                <?php
                                $label_text = __ ('Deposit Amount','boxtheme');
                                $amout_input= '<input type="number" value="30" min="10" step="5" id="deposit_amount" name="amount" data-number-to-fixed="2" data-number-stepfactor="5" class="form-control currency" id="c2" />';
                                if(isset($_GET['pack'])){
                                    $label_text = __ ('Pay to post premium project','boxtheme');
                                    $amout_input =  '<input type="number" readonly  value="30" min="10" step="5" id="deposit_amount"  data-number-to-fixed="2" data-number-stepfactor="5" class="form-control currency" id="c2" /> <input type="hidden"   value="'.$_GET['pack'].'" min="10" step="5"  name="amount"  data-number-to-fixed="2" data-number-stepfactor="5" class="form-control currency" id="c2" />';;

                                }
                                ?>
                                <td class="product-name"  width="60%"><?php echo $label_text;?></td>
                                <td class="product-total text-right" width="40%">
                                   <div class="input-group">
								        <span class="input-group-addon">$</span>
								        <?php echo $amout_input;?>
								    </div>
                                </td>
                            </tr>
                            <tr class="cart_item">
                                <td class="product-name" width="60%"><?php _e('Processing Fee','boxtheme');?> </td>
                                <td class="product-total text-right"  >
                                    <span class="woocommerce-Price-amount amount"><?php echo box_get_price_format(0);?></span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="order-total">
                                <td width="60%"><strong><?php _e('Total','boxtheme');?></strong></td>
                                <td class="text-right"><strong><span class="woocommerce-Price-amount amount"><?php echo box_get_price_format(30);?></span></strong> </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" class = "btn btn-checkout btn-pay-js">Confirm And Pay <?php echo box_get_price_format(30);?></button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div> <!-- end .right-membership !-->
		</div>
	</div>
</div>
<?php get_footer();?>