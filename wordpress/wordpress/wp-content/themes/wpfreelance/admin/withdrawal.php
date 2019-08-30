<?php

class BX_Withdrawal{
	function __construct(){
		add_action('admin_menu', array($this,'box_register_my_custom_submenu_page') );
	}

	public function box_register_my_custom_submenu_page() {

	    add_submenu_page(
	        BOX_SETTING_SLUG,
	        'Widthraw history',
	        'Widthraw history',
	        'manage_options',
	        'widthraw-history',
	        array($this,'widthraw_menu_link')
	    );
	}

	/**
	 *
	 */
		function widthraw_menu_link(){
			global $checkout_mode;
			$is_realmode = (int) $checkout_mode;
			$mode_txt = '(Sandbox Mode)';
			If($is_realmode){
				$mode_txt = '(Real mode)';
			}

			echo '<div class="full" style=" padding:30px 0;  margin-top:50px; ">';
				$args = array(
					'post_type' => 'withdrawal',
					'posts_per_page' => 35,
					//'meta_key' => 'is_realmode',
					//'meta_value' => $is_realmode,
					);
				$query = new WP_query($args);
				echo '<h3>List Withdrawal Requested '.$mode_txt.'</h3>';
				if( $query->have_posts() ){

					echo '<table class="widefat">';
					echo '<thead><tr class="li-heading">';
						echo '<th class="col-md-1">ID</th>';
						echo '<th class="col-md-2">Author</th>';
						echo '<th class="col-md-2">Mode</th>';
						echo '<th class="col-md-2">Amount</th>';
						echo '<th class="col-md-2">Date</th>';
						echo '<th class="col-md-1">Status</th>';
						echo '<th class="col-md-2">Action</th>';
					echo '</tr></thead>';
					$bx_wdt = Box_Withdrawal::get_instance();
					while ($query->have_posts()) {
						global $post;
						$query->the_post();
						$withdrawal = $bx_wdt->get_withdrawal($post);
						$mode = 'Sandbox';
						if($withdrawal->is_realmode)
							$mode = 'Real';
						?>
						<tr  class="">
							<td class="col-md-1"><?php echo get_the_ID();?></td>
							<td class="col-md-2"><?php echo get_the_author();?></td>
							<td class="col-md-2"><?php echo $mode;?></td>
							<td class="col-md-2"><?php echo $withdrawal->amount; ?></td>
							<td class="col-md-2"><?php echo get_the_date();?></td>
							<td class="col-md-1"><?php echo $withdrawal->post_status; ?></td>

							<td class="col-md-1"> <?php
							if( $withdrawal->post_status != 'publish' )
								echo '<button class="btn-approve btn-approve-withdrawal " id="'.get_the_ID().'"><span class="glyphicon glyphicon-ok"></span> Approve</button>'; ?>
							</td>
							<td class="col-md-1"><a href="<?php echo get_edit_post_link(get_the_ID() )?>"> Detail</a></td>
						</tr> <?php
					}
					echo '</table>';
				} else {
					_e('There is no any order yet','boxtheme');
				}
			echo '</div>';
	}
}