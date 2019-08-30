<?php
/**
 *Template Name: Thank you
 */

$type  = isset($_GET['type']) ? $_GET['type'] : '';
$order = array();
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

$order = BX_Order::get_instance()->get_order($order_id);
$order_type = $order->order_type; // buy_credit, premium_post,


if( empty($type) )
	$type = $order->order_gateway; // paypal, stripe,

if( $type == 'cash'){
	global $user_ID;

	$is_access = get_post_meta($order->ID,'is_access', true);

	if( $user_ID == $order->post_author && !$is_access ){
		$credit = BX_Credit::get_instance();
		$credit->increase_credit_pending($order->post_author, $order->amount);
		update_post_meta($order->ID,'is_access', 1);
	}
}
?>
<?php get_header(); ?>
<div class="full-width">
	<div class="container site-container">
		<div class="site-content thankyou" id="content" >
			<div class="" style="width: 100%; min-height: 299px;">
					<?php
					if( is_user_logged_in() && $order->payer_id == $user_ID ){
					$payer = get_userdata($order->payer_id); ?>
					<h1 class="primary-color" style="font-style: 33px;"> Order Received <i class="fa fa-check" aria-hidden="true"></i></h1>
					<h3 style="padding-bottom: 0px; padding-top: 15px;"><?php _e('Thank you for your payment.','boxtheme');?> </h3>
					<p style="font-size: 17px;"><?php _e('We\'ve sent you an email with all the details of your order','boxtheme');?></p>

					<table>
						<tr>
							<td> <label class="text-uppercase1">Order Number</label>
							<td> <label class="text-uppercase1">Total</label>
							<td> <label class="text-uppercase1">Date</label>
							<td> <label class="text-uppercase1">Email</label>
							<td> <label class="text-uppercase1">Payment Method</label>
						</tr>
						<tr>
							<td><strong>BOX_<?php echo $order->ID;?></strong></td>
							<td><strong><?php echo $order->amount;?>(<?php echo $order->currency_code;?>)</strong></td>
							<td><strong><?php  echo date_i18n( get_option( 'date_format' ), strtotime( $order->post_date ) ); ?></strong></td>
							<td><strong><?php echo $payer->user_email;?></strong></td>
							<td><strong><?php echo $order->order_gateway;?></strong></td>

						</tr>
					</table>
					<?php

					if( $order_type == 'buy_credit' ){
						if( $type == 'cash'){
							if( $order->post_status == 'publish') {
								_e('Your order is approved ','boxtheme');
							} else {
								$option = BX_Option::get_instance();
								$payment = $option->get_group_option('payment');
    							$cash = (object) $payment->cash;
            					if ( ! empty( $cash->description) ) {
            						echo $cash->description;
            					}
				 			}
				 		} else {
				 			echo '<p style="font-size: 17px;">';
			 				_e('Your order is completed. The credit is deposited to your balance and you can use your credit now.','boxtheme');
			 				echo '</p>';
			 			}
			 		} else if ($order_type == 'premium_post' ){
			 			$project_id =  get_post_meta( $order->ID, 'pay_premium_post', true );

			 			printf( __('Your order is completed. Check detail job <a href="%s">Detail Project</a>','boxtheme'),get_permalink($project_id) );
			 		}
				}?>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	.thankyou{
		margin-top: 35px;
	}
	table, th, td {
		border:0;
	  	padding: 0;
	  	margin:0;
	  	border-collapse: collapse;
	}
	table {
	 	border-collapse: collapse;
	    width: 100%;
	    margin-bottom: 20px;
	    max-width: 758px;
	}
	td {
  		height: 35px;
	  	padding: 7px 10px;
	  	text-align: left;
	  	text-align: center;
	}
	tr:nth-child(1) td {

	}
	tr td:first-child{
		text-align: left;
		padding-left: 0;
	}
	tr:nth-child(2) td {
	  	border-right: 1px dashed #ccc
	}
	tr:nth-child(2) td:last-child{
		border: 0;
	}

</style>
<?php get_footer();?>
