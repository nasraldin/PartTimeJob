<?php
function box_update_subscription_profile($order){
	global $user_ID;

	$pack_id = $order->pack_id;
	$order_id = $order->ID;
	$value = $order_id.','.$pack_id;

	$user_id = $order->post_author;
	box_log('user_ID:'.$user_id);
	update_user_meta( $user_id, 'current_order_plan', $value );

}

function is_box_check_plan_available($user_id = 0){

	if( ! $user_id ){
		global $user_ID;
		$user_id = $user_ID;
	}
	$current_order_plan = get_user_meta( $user_id,'current_order_plan', true );
	if( ! empty( $current_order_plan ) ){
		$detail = explode(",", $current_order_plan);

		if( count($detail) == 2 ) {
			$order_id = $detail[0];
			$pack_id = $detail[1];

			$order = $order = BX_Order::get_instance()->get_order($order_id);
			$order_gmt_date = $order->post_date_gmt; //2018-05-10 06:21:23
			$day_purchase = date("Y-m-d",strtotime($order_gmt_date)); // 2012-01-30
			$next_month = date( "Y-m-d", strtotime("$day_purchase +1 month") );
			$expired_time = strtotime($next_month);
			if( time() < $expired_time ){
				return $pack_id;
			}
		}

	}
	return 0;
}
function box_get_number_bid_of_plan($pack_id){
	$number_bids = get_post_meta($pack_id, 'number_bids', true);
	return $number_bids;
}


function box_get_number_bid_of_subscription(){
	$pack_value_id = is_box_check_plan_available();
	if( $pack_value_id ){
		return (int) get_post_meta($pack_value_id,'number_bids', true);
	}
	return 0;
}
function get_number_bid_remain(){

	global $user_ID;

	$bids_free = box_get_number_free_bid_in_a_month();
	$subscription_bids = box_get_number_bid_of_subscription($user_ID);

	$total_bid_allow = $bids_free + $subscription_bids;
	$bidded_in_month = box_get_number_bidded_this_moth();
	$bids_remain = $total_bid_allow - $bidded_in_month;
	if( $bids_remain > 0)
		return $bids_remain;
	return 0;
}
function get_bid_info($user_id = 0){
	if( ! $user_id ){
		global $user_ID;
		$user_id= $user_ID;
	}
	$bids_free = box_get_number_free_bid_in_a_month();
	$subscription_bids = box_get_number_bid_of_subscription($user_id);

	$total_bid_allow = $bids_free + $subscription_bids;
	$bidded_in_month = box_get_number_bidded_this_moth();
	$bids_remain = $total_bid_allow - $bidded_in_month;
	$bid_info = array(
		'free' => $bids_free,
		'total' => $total_bid_allow,
		'bidded' => $bidded_in_month,
		'remain' => max(0, $bids_remain),
	);
	return (object)$bid_info;

}

function box_get_current_month(){
	return date('M');// jan,feb,may,april,june.
}
function membership_plans_html( $atts ) {
	 $args = array(
        'post_type' => '_package',
        'meta_key' => 'pack_type', // buy credit or premium_post
        'meta_value' => 'membership',

    );
	// $args = shortcode_atts( array(
 //        'hide_empty' => true,
 //        'style' => 1,
 //        // ...etc
 //    ), $atts );

    $the_query = new WP_Query($args);
    ob_start();
    if($the_query->have_posts() ){
    	echo '<div class = "elementor-row">';

    	while ($the_query->have_posts() ) {
    		$the_query->the_post();
    		global $post;
    		$price = get_post_meta($post->ID,'price', true);
            $sku = get_post_meta($post->ID,'sku', true);
            $number_bids = get_post_meta($post->ID,'number_bids', true);
            $post->zero_commision = (int) get_post_meta($post->ID,'zero_commision', true);

    		?>

			<div data-id="eed332f" class="elementor-element elementor-element-eed332f elementor-column elementor-col-33 elementor-top-column" data-element_type="column">
				<div class="elementor-column-wrap elementor-element-populated" style="padding: 10px;">
					<div class="elementor-widget-wrap">
						<div data-id="a8b4c81" class="elementor-element elementor-element-a8b4c81 elementor-widget elementor-widget-box-price-table" data-element_type="box-price-table.default">
							<div class="elementor-widget-container">

								<div class="elementor-price-table">

									<div class="elementor-price-table__header">
										<h3 class="elementor-price-table__heading"><?php echo $post->post_title;?></h3>
									</div>

									<div class="elementor-price-table__price">
										<span class="elementor-price-table__currency">$</span>
										<span class="elementor-price-table__integer-part"><?php echo $price;?></span>
										<span class="elementor-price-table__period elementor-typo-excluded">Per month</span>
									</div>

									<?php the_content();?>

									<div class="elementor-price-table__footer 3333">
									<?php if($price == 0){?>
										<button class="elementor-price-table__button disable unable-click btn-membership elementor-button elementor-size-md">Sign Up</button>
										<?php } else {?>
										<a href="<?php echo box_get_static_link('signup');?>?plan=<?php echo $post->ID;?>" class="elementor-price-table__button btn-membership elementor-button elementor-size-md">Choose Plan 111 </a>
										<?php } ?>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
    		<?php
    	}
    	echo '</div>';
    }
	return ob_get_clean();
}
add_shortcode( 'membership_plans', 'membership_plans_html' );