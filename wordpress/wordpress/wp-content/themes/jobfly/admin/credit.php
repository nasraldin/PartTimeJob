<?php
class BX_Credit_Setting{
	function __construct(){
		add_action('admin_menu', array($this,'box_register_my_custom_submenu_page') );
	}

	public function box_register_my_custom_submenu_page() {
	    add_submenu_page(
	        BX_Admin::$main_setting_slug,
	        'Buy Credit order',
	        'Buy Credit order',
	        'manage_options',
	        'credit-setting',
	        array($this,'cedit_menu_link')
	    );

	    add_submenu_page(
	        BX_Admin::$main_setting_slug,
	        'Widthdraw order',
	        'Widthdraw order',
	        'manage_options',
	        'widthraw-order',
	        array($this,'widthraw_menu_link')
	    );
	}
	function cedit_menu_link(){
		$args = array(
			'post_type' => '_order',
			'posts_per_page' => 35,
			'meta_key' => 'order_type',
			'meta_value' => 'buy_credit',
		);
		$query = new WP_query($args);
		echo '<div id="main_content" class="wrap">';
			echo '<h3>List Order</h3>';
			if( $query->have_posts() ){

				echo '<ul class="box-table">';
				echo '<li class="row li-heading">';echo '<div class="col-md-1"> ID</div><div class="col-md-2">Buyer</div>';echo '<div class="col-md-2">Type</div>';
				echo '<div class="col-md-2">';echo "Price";	echo '</div>';echo '<div class="col-md-2">Date</div>';echo '<div class="col-md-1">';	echo "Status";	echo '</div>';echo '<div class="col-md-2">Action</div>';
				echo '</li>';
				$bx_order = BX_Order::get_instance();
				while ($query->have_posts()) {
					global $post;
					$query->the_post();
					$order = $bx_order->get_order($post);

					echo '<li class="row">';
						echo '<div class="col-md-1">'.get_the_ID().'</div>';
						echo '<div class="col-md-2">'.get_the_author();				echo '</div>';
						echo '<div class="col-md-2">';				echo $order->payment_type;				echo '</div>';

						echo '<div class="col-md-2">';					echo $order->amout;					echo '</div>';
						echo '<div class="col-md-2">';					echo get_the_date();					echo '</div>';
						echo '<div class="col-md-1">';					echo $order->post_status;					echo '</div>';

						echo '<div class="col-md-2">';
						if( $order->post_status != 'publish' )
							echo '<button class="btn-approve-order" id="'.get_the_ID().'">Approve</button>';
						echo '</div>';
					echo '</li>';
				}
				echo '</ul>';
			} else {
				_e('There is not any order yet','boxtheme');
			}
		echo '</div>';
	}

	/**
	 *
	 */
		function widthraw_menu_link(){
			echo '<div class="full" style=" padding:30px 0;  margin-top:50px; ">';
				$args = array(
					'post_type' => '_order',
					'posts_per_page' => 35,
					'meta_key' => 'order_type',
					'meta_value' => 'withdraw',
				);
				$query = new WP_query($args);
				echo '<h3>List Order</h3>';
				if( $query->have_posts() ){

					echo '<table class="widefat">';
					echo '<thead><tr class="li-heading">'; echo '<td class="col-md-1">ID</td>';	echo '<th class="col-md-2">Author</th>';echo '<th class="col-md-2">Type</th>';echo '<th class="col-md-2">';echo "Amount";	echo '</th>';
					echo '<th class="col-md-2">Date</th>'; echo '<th class="col-md-1">';	echo "Status";	echo '</th>';echo '<th class="col-md-2">Action</th>';
					echo '</tr></thead>';
					$bx_order = BX_Order::get_instance();
					while ($query->have_posts()) {
						global $post;
						$query->the_post();
						$order = $bx_order->get_order($post);
						?>
						<tr  class="">
							<td class="col-md-2"><?php echo get_the_ID();?></td>
							<td class="col-md-3"><?php echo get_the_author();?></td>
							<td class="col-md-2"><?php echo $order->payment_type;?></td>
							<td class="col-md-2"><?php echo $order->amout; ?></td>
							<td class="col-md-2"><?php echo get_the_date();?></td>
							<td class="col-md-1"><?php echo $order->post_status; ?></td>

							<td class="col-md-1"> <?php
							if( $order->post_status != 'publish' )
								echo '<button class="btn-approve-widthraw " id="'.get_the_ID().'"><span class="	glyphicon glyphicon-ok"></span></button>'; ?>
							</td>
							<td class="col-md-1"><a href="<?php echo get_edit_post_link(get_the_ID() )?>"> Detail</a></td>
						</tr> <?php
					}
					echo '</table>';
				} else {
					_e('There is not any order yet','boxtheme');
				}
			echo '</div>';
	}
}