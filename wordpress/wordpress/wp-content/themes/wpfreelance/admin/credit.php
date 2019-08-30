<?php
class BX_Credit_Setting{
	function __construct(){
		add_action('admin_menu', array($this,'box_register_my_custom_submenu_page') );
	}

	public function box_register_my_custom_submenu_page() {
	    add_submenu_page(
	        BOX_SETTING_SLUG,
	        'Deposit Credit order',
	        'Deposit Credit order',
	        'manage_options',
	        'credit-setting',
	        array($this,'cedit_menu_link')
	    );
	}
	function cedit_menu_link(){
		$args = array(
			'post_type' => '_order',
			'post_status' => array('pending','publish'),
			'posts_per_page' => -1,
			'meta_key' => 'order_type',
			'meta_value' => 'buy_credit',
		);
		$query = new WP_query($args);
		global $symbol;
		echo '<div id="main_content" class="wrap">';
			echo '<h3>List Deposit Credit Order</h3>';
			if( $query->have_posts() ){

				echo '<ul class="box-table">';
				echo '<li class="row li-heading">';echo '<div class="col-md-1"> ID</div><div class="col-md-2">Buyer</div>';echo '<div class="col-md-2">Type</div>';
				echo '<div class="col-md-2">';echo "Price";	echo '</div>';echo '<div class="col-md-2">Date</div>'; echo '<div class="col-md-2">Approve</div>';
				echo '</li>';
				$bx_order = BX_Order::get_instance();
				while ($query->have_posts()) {
					global $post;
					$query->the_post();
					$order = $bx_order->get_order($post);
					$order_status = $order->post_status;
					if($order_status == 'publish'){
						$order_status = 'Approved';
					}?>
					<li class="row">
						<div class="col-md-1"><?php echo $order->ID;?></div>
						<div class="col-md-2"><?php echo get_the_author();?></div>
						<div class="col-md-2"><?php echo $order->order_gateway;?></div>

						<div class="col-md-2"><?php echo $order->amount;?>(<?php echo $order->currency_code;?>)</div>
						<div class="col-md-2"><?php echo get_the_date();?></div>

						<div class="col-md-2">
						<?php if( $order->post_status != 'publish' ){
							echo '<button class="btn-approve btn-approve-order" id="'.get_the_ID().'">Approve</button>';
						} else {
							echo '<span class="primary-color"><i class="fa fa-check"></i></span>';
						}?>
						</div>
					</li>
					<?php
				}
				echo '</ul>';
			} else {
				_e('There is no any order.','boxtheme');
			}
		echo '</div>';
	}

}