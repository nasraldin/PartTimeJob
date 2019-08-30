<?php
class Box_Appearances{
	function __construct(){
		add_action('admin_menu', array($this,'box_register_my_custom_submenu_page') );
	}

	public function box_register_my_custom_submenu_page() {
	    add_submenu_page(
	        BOX_SETTING_SLUG,
	        'Appearances',
	        'Box Appearances',
	        'manage_options',
	        'appearances',
	        array($this,'box_sappearances')
	    );
	}
	function box_sappearances(){
		$group_option = "general";$option = BX_Option::get_instance();
		$general = $option->get_general_option();

		?>

	<div id="<?php echo $group_option;?>" class="main-group">
		<div class="full">
			<div class="full sub-item " >
				<div class="sub-section " id="general">
					<h3>Appearances Settings</h3>
				</div>
	     		<div class="sub-section " id="general">

	                <div class="sub-section " id="general">
						<label for="inputEmail3" class="col-sm-3 col-form-label"><?php _e('Display Map on Archive Profiles Page','boxtheme');?></label>
						<div class="col-md-9">
							<div class="field-item no-label switch-field">
								<?php bx_swap_button('map_in_archive', $general->map_in_archive, $multipe = false);?><br /><span><?php _e('if enable this option,Freelancers has to confirm account after register account.','boxtheme');?></span>

							</div>
						</div>
					</div>

					 <div class="sub-section" id="general">
						<label for="inputEmail3" class="col-sm-3 col-form-label"><?php _e('One Column Style','boxtheme');?></label>
						<div class="col-md-9">
							<div class="field-item no-label switch-field">
								<?php bx_swap_button('one_column', $general->one_column, $multipe = false);?><br /><span><?php _e('if enable this option,sidebar on the archive profile will be hidden.','boxtheme');?></span>

							</div>
						</div>
					</div>
	            </div>

		    </div>
		</div>
	</div>
	<?php
	}

}
new Box_Appearances();