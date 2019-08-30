 <?php
/**
 * Template Name: Checkout Membership
 * Show list payment gateway and price to checkout/subscription.
*/
get_header();
$pack_id = isset($_GET['plan']) ? $_GET['plan'] : 0;
$price = get_post_meta($pack_id,'price', true);
$plan = get_post($pack_id);
?>
<div class="container site-container membership">
    <div class="sign-block col-xs-12 " style="background-color: #f2f2f2;">
        <div class="wrap-ck-form">
            <div class="form-row">
                <div class="col-md-12">
                    <div class="setup-header setup-org">
                        <h1> <?php _e('Select Payment Method','boxtheme');    ?></h1>
                        <p class="lead"><?php _e('Please deposit funds to upgrade your membership and receive great benefits right away!','boxtheme');?></p>
                    </div>
                </div>
            </div>
            <div class="checkout-step">
                <form class="frm-membership">
                    <div class="col-md-6 col-membership left-membership ">
                        <div class="col-content">
                            <?php do_action('box_subscription_form');?>
                        </div>
                    </div>
                    <div class="col-md-6 col-membership right-membership">
                        <div class="col-content">
                            <table class="shop_table membership-order">
                                <tbody>
                                    <tr class="cart_item">
                                        <td class="product-name"  width="70%"><?php printf(__('Pay for subcription the  <i>%s</i> plan.','boxtheme'),$plan->post_title);?> </td>
                                        <td class="product-total text-right">
                                            <span class="woocommerce-Price-amount amount"><?php echo box_get_price_format($price);?></span>
                                        </td>
                                    </tr>
                                    <tr class="cart_item">
                                        <td class="product-name" width="70%"><?php _e('Processing Fee .','boxtheme');?> </td>
                                        <td class="product-total text-right">
                                            <span class="woocommerce-Price-amount amount"><?php echo box_get_price_format(0);?></span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="order-total">
                                        <td width="70%">Total</td>
                                        <td class="text-right"><strong><span class="woocommerce-Price-amount amount"><?php echo box_get_price_format($price);?></span></strong> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <button type="submit" class = "btn btn-checkout-membership">Confirm And Pay <?php echo box_get_price_format($price);?></button>
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>
                            <input type="hidden" name="package_id" value="<?php echo $pack_id;?>">
                        </div>
                    </div> <!-- end .right-membership !-->
                </form>
            </div>
       </div>
    </div>
</div>

<style type="text/css">
    .left-membership{

    }
    .right-membership{
        max-width: 450px;
    }
    .col-content{
        background-color: #fff;
        min-height: 120px;
        border-radius: 5px;
        border: 1px solid #dfe2e5;
        padding: 15px 0;
    }
    .left-membership .col-content{
        padding: 15px 25px;
    }
    .btn-checkout-membership{
        display: block;
        border: 0;
        box-shadow: 0;
        width: 100%;
        padding: 10px 0;
        text-align: center;
        font-size: 20px;
        color: #fff;
        border-radius: 6px;
    }
    .membership table tr td{
        padding:  10px 29px;
    }
    .membership table{
        display: block;
        padding: 20px 0;
    }
</style>
<?php get_footer();?>