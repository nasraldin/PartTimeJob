<?php
$option = BX_Option::get_instance();
$general = $option->get_general_option();

$box_slugs = BX_Option::get_instance()->get_box_slugs($general);
$pages = get_pages();
$html_option = array();
$html_options = '';
$group_option = 'general';
$set_pages = $static_pages =0;
$warning = '';
?>,
<div id="<?php echo $group_option;?>" class="main-group install-tab">
	<div class="full">
		<div class="col-md-12"> <h2> Set pages default. </h2></div>
	    <form>
	        <div class="form-row">
	            <?php
	            foreach ($box_slugs as $slug => $value) {
	                $value = (object) $value;
	                $debug = array();
	                $html_option = array();
	                foreach( $pages as $page ) {
	                    $selected = '';
	                    if( $page->ID == $value->ID ) {
	                        $selected = ' selected ';
	                        $debug[] = $value->label. ' doesnt set.';
	                        $set_pages++;
	                    }
	                    $html_option[$slug][]= '<option '.$selected.' value="'.$page->ID.'" class="form-control ">'.$page->post_title.'</option>';
	                }

	                ?>
	                <div class="form-group col-md-3">
		                <div class=" sub-section" id="box_slugs">
		                	<div class="full sub-item" id="<?php echo $slug;?>">
			                    <label for="inputState"><?php echo $value->label;?></label>
			                    <select id="inputState" class="form-control auto-save" name="ID" level="3">
			                        <option >Choose...</option>
			                        <?php echo join('',$html_option[$slug]);?>
			                    </select>
			                </div>
		                </div>
		            </div><?php
		            $static_pages++;
		        }
		        if($set_pages < $static_pages){
		        	echo '<div class="warning"></div>';
		        }
		        ?>
	        </div>
	    </form>
	    <div class="full">
	    	<div class="col-md-12"
		    	<?php
			    $install = (int) get_option('install_sample', 3);

			    if( $install != 1){   ?>
			    ><h3><?php _e('Click into this button to setup sample data.','boxtheme');?></h3>
			        <button class="btn-big btn-install">Install Sample data</button>
			    <?php } else { ?>
			       <h3><?php _e('Sample data is imported.','boxtheme');?></h3>
			        <button class="btn-big btn-install">Re Import</button>
			        <?php
			    }?>
			</div>

	</div>

</div>