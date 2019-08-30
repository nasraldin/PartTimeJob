<?php
$group_option = "general";
$section = "currency";

$box_general = BX_Option::get_instance()->get_general_option();
$currency = BX_Option::get_instance()->get_currency_option($box_general);

$code = $currency->code;

$position = $currency->position;
$price_thousand_sep = $currency->price_thousand_sep;
$price_decimal_sep = $currency->price_decimal_sep;
global $box_currency;
$symbol = box_get_currency_symbol($box_currency->code);

?>
<div id="<?php echo $group_option;?>" class="main-group">
	<div class="sub-section " id="<?php echo $section;?>">
		<div class="full sub-item" id="<?php echo $section;?>" >
			<h2 class="section-title">Currency Options </h2>
			<div class="form-group row">
				<div class="col-md-3"> <span><?php _e('Select currency','boxtheme');?></span> 		</div>
				<div class="col-md-9">
		        	<select name="code" id="woocommerce_currency" level="1" style="min-width: 350px;" class="wc-enhanced-select enhanced auto-save" tabindex="-1" title="Currency">
			        	<?php
			        	$list = list_currency();
			        	foreach ($list as $cur => $value) { ?>
			        		<option <?php selected($code, $cur );?> value='<?php echo $cur;?>'><?php echo $value;?></option>
			        		<?php
			        	}
			        	?>
					</select>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-3"> <span><?php _e('Currency Position','boxtheme');?></span>       		</div>
				<div class="col-md-9">
					<select name="position" id="woocommerce_currency_pos" style="min-width: 350px; " level="1" class="wc-enhanced-select enhanced auto-save" tabindex="-1" title="<?php _e('Currency Position','boxtheme');?>">
						<option value="left" <?php selected($position, 'left' );?>  >Left ($99.99)</option>
						<option value="right" <?php selected($position, 'right' );?>>Right (99.99$)</option>
						<option value="left_space" <?php selected($position, 'left_space' );?>>Left with space ($ 99.99)</option>
						<option value="right_space"<?php selected($position, 'right_space' );?> >Right with space (99.99 $)</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-3"> <span><?php _e('Thousand Separator','boxtheme');?></span>       		</div>
				<div class="col-md-9"> <input name="price_thousand_sep" id="woocommerce_price_thousand_sep" level="1" type="text" style="width:50px;" value="<?php echo $price_thousand_sep;?>" class="auto-save" placeholder="" />   </div>
			</div>
			<div class="form-group row">
				<div class="col-md-3"><span><?php _e('Decimal Separator','boxtheme');?></span></div>
				<div class="col-md-9"><input name="price_decimal_sep" id="woocommerce_price_decimal_sep" type="text" level="1" style="width:50px;" value="<?php echo $price_decimal_sep;?>" class="auto-save" placeholder="">       		</div>
			</div>
		</div>
	</div>
</div>

<!-- start subscription !-->
<?php

$group_option = "payment";
$payment = BX_Option::get_instance()->get_group_option($group_option);
$pack_type = 'membership';
?>

<div class="section box-section" id="<?php echo $group_option;?>">
    <h2 class="section-title"><?php _e('Membership Package plans','boxtheme');?></h2>
    <div class="sub-section" id="package_plan">
        <div class="row">
            <div class="col-md-3 text-transform"><h4> <?php _e('Membership plans','boxtheme');?> </h4>        </div>
            <div class="col-md-9"> <?php
                $args = array(
                    'post_type' => '_package',
                    'meta_key' => 'pack_type', // buy credit or premium_post
                    'meta_value' => 'membership',

                );
                $list_subscriptions = array();
                $the_query = new WP_Query($args);

                // The Loop
                if ( $the_query->have_posts() ) {
                    echo '<div class="widefat " id="list_package">';
                    $i = 1; ?>
                    <div class=" form-group heading-line">
                            <div class="col-md-3 page-name"><?php _e( 'Type', 'boxtheme' ); ?></div>
                            <div class=" col-md-2 page-name"><?php _e( 'SKU', 'boxtheme' ); ?></div>
                            <div class="col-md-2 page-name"><?php _e( 'Price', 'boxtheme' ); ?></div>
                            <div class="col-md-3 page-name"><?php _e( 'Number Bids', 'boxtheme' ); ?></div>
                            <div class="col-md-2 page-name text-right">Remove/Edit</div>

                    </div> <?php

                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $class = "item-le"; if($i% 2 == 0) $class = "item-chan";
                        $price = get_post_meta(get_the_ID(),'price', true);
                        $sku = get_post_meta(get_the_ID(),'sku', true);
                        $post->price = $price; $post->sku = $sku;
                        $post->pack_type = $pack_type;
                        $post->number_bids = get_post_meta($post->ID,'number_bids', true);
                        $post->zero_commision = (int) get_post_meta($post->ID,'zero_commision', true);
                        $list_subscriptions[$post->ID] = $post; ?>
                        <div class="block  row-item <?php echo $class;?>">
                            <div class="col-md-3"> <?php  echo get_the_title(); ?> </div>
                            <div class="col-md-2"> <?php echo $sku;?> </div>
                            <div class="col-md-2"> <?php box_price($price); ?> </div>
                            <div class="col-md-3"> <?php echo $post->number_bids;?> </div>
                            <div class="col-md-2 text-right">
                                <span class="btn-act-wrap" id="<?php echo get_the_ID();?>">
                                <span attr="<?php echo get_the_ID();?>" class="btn-act btn-delete"> <i class="fa fa-trash-o" aria-hidden="true"></i> </span> &nbsp; <span  class=" btn-act btn-edit-membership"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></span>
                            </div>
                        </div><?php

                        $i ++;
                    }
                    echo '</div>';
                    /* Restore original Post Data */
                    wp_reset_postdata();
                } else {
                    echo '<div class="form-group">';
                    _e('List package plan is empty','boxtheme');
                    echo '</div>';
                } ?>
            </div>
        </div> <!-- .row !-->
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <form class="frm-add-package row subscription-form">
                    <div class="col-md-12"><h4 class="form-heading text-transform"><?php _e('Insert new membership plan','boxtheme');?> </h4></div>
                    <div class="full">
                        <div class="col-sm-12 one-line">
                            <input type="text" class="form-control" id="post_title" required name="post_title" placeholder="<?php _e('Membership Type');?>">&nbsp; <i><?php _e('Membership type','boxtheme');?></i>
                        </div>
                        <div class="col-sm-4 one-line">
                            <input type="text" class="form-control" required name="sku" placeholder="<?php _e('SKU');?>">&nbsp; <i><?php _e('SKU code','boxtheme');?></i>
                        </div>
                        <div class="col-sm-4 one-line">
                            <input type="number" class="form-control" required name="price"  placeholder="<?php _e('Price');?>"  >&nbsp;<i><?php _e('Price of this plan','boxtheme');?></i>
                        </div>

                        <div class="col-sm-4 one-line">
                            <input type="number" class="form-control" required name="number_bids"  min="1"
                            placeholder="<?php printf(__('Ex: %s','boxtheme'), box_get_number_free_bid_in_a_month() ) ;?>"  >&nbsp;<i><?php _e('Number bids  can bid','boxtheme');?></i>
                        </div>

                        <div class="col-sm-12 one-line">
                            <textarea id="post_content" name="post_content" class="" placeholder="<?php _e('Description of new package','boxtheme');?>"></textarea>
                            <input type="hidden" name="ID" id="ID" value="0" />
                            <input type="hidden" name="pack_type" value="<?php echo $pack_type;?>" />
                        </div>
                        <div class="col-sm-8 pull-left">
                            <label class="label-checkbox"><input type="checkbox" class="form-control"  name="zero_commision"  placeholder="<?php _e('Price');?>"  >&nbsp;<i><?php _e('No charge commision for this plan.','boxtheme');?></i></label>
                        </div>
                        <div class="col-sm-2 align-right one-line pull-right">
                            <button class="btn btn-back-end btn-act"><?php _e('Create','boxtheme');?></button>
                        </div>
                    </div>
                </form>
           </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-frm_edit_subscription">
    <form class="frm-add-package row">

        <div class="full">
            <div class="col-sm-12 one-line">  <h3><?php _e('Edit package plan','boxtheme');?></h3>            </div>
            <div class="col-sm-12 one-line">
                <input type="text" class="form-control" required name="post_title" value="{{{data.post_title}}}" placeholder="<?php _e('Membership type');?>">&nbsp; <i><?php _e('Membership type','boxtheme');?></i>
            </div>
            <div class="col-sm-4 one-line">
                <input type="text" class="form-control" required="" name="sku" placeholder="SKU" value="{{{data.sku}}}"><small><?php _e('SKU code','boxtheme');?></small>
            </div>
            <div class="col-sm-4 one-line">
                <input type="number" class="form-control" required="" name="price" placeholder="Price" value="{{{data.price}}}"><small><?php printf(__('%s Price of this plan','boxtheme'),$symbol );?></small>
            </div>
             <div class="col-sm-4 one-line">
                <input type="number" class="form-control" required name="number_bids"  value="{{{data.number_bids}}}" placeholder="<?php _e('Ex: 10');?>"  >&nbsp;<i><?php _e('Number bids  can bid.','boxtheme');?></i>
            </div>
            <div class="col-sm-12 one-line">
                <textarea id="post_content" name="post_content" class="">{{{data.post_content}}}</textarea>
            </div>
            <input type="hidden" name="ID" id="ID" value="{{{data.ID}}}" />
            <input type="hidden" name="pack_type" value="<?php echo $pack_type;?>" />

            <div class="col-sm-8 pull-left ">
                <label class="label-checkbox" ><input type="checkbox" class="form-control" <# if (data.zero_commision == 1 ) { #> checked <# } #>  name="zero_commision"  placeholder="<?php _e('Price');?>"  >&nbsp;<i><?php _e('No charge commision for this plan.','boxtheme');?></i></label>
            </div>
            <div class="col-sm-2 align-right one-line pull-right">
                <button class="btn"><?php _e('Update','boxtheme');?></button>
            </div>
        </div>
    </form>
</script>
<!-- end subscription !-->

<!-- start deposit credit !-->
<?php

$group_option = "payment";
$payment = BX_Option::get_instance()->get_group_option($group_option);
$pack_type = 'buy_credit';
?>

<div class="section box-section" id="<?php echo $group_option;?>">
	<h2 class="section-title"><?php _e('Deposit Credit Package plans','boxtheme');?></h2>
    <div class="sub-section" id="package_plan">
    	<div class="row">
        	<div class="col-md-3"><h4> <?php _e('List package plan','boxtheme');?> </h4>        </div>
            <div class="col-md-9"> <?php
                $args = array(
                    'post_type' => '_package',
                    'meta_key' => 'pack_type', // buy credit or premium_post
                    'meta_value' => $pack_type,

                );
                $list_package = array();
                $the_query = new WP_Query($args);

                // The Loop
                if ( $the_query->have_posts() ) {
                    echo '<div class="widefat " id="list_package">';
                    $i = 1; ?>
                    <div class=" form-group heading-line">

							<div class="col-md-1 page-name"><?php _e( 'STT', 'boxtheme' ); ?></div>
	  						<div class=" col-md-2 page-name"><?php _e( 'SKU', 'boxtheme' ); ?></div>
                            <div class="col-md-2 page-name"><?php _e( 'Price', 'boxtheme' ); ?></div>
	  						<div class="col-md-5 page-name"><?php _e( 'Detail', 'boxtheme' ); ?></div>
	  						<div class="col-md-2 page-name">&nbsp;</div>

   					</div> <?php

                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $class = "item-le"; if($i% 2 == 0) $class = "item-chan";
                        $price = get_post_meta(get_the_ID(),'price', true);
                        $sku = get_post_meta(get_the_ID(),'sku', true);
                        $pack_type = get_post_meta(get_the_ID(),'pack_type', true);
                        echo '<div class="block  row-item '.$class.'">'; echo '<div class="col-md-1">'.$i.'</div>';   echo '<div class="col-md-2">'.$sku.'</div>';
                        echo '<div class="col-md-2">';  box_price($price);  echo '</div>';
                        echo '<div class="col-md-5">';  echo get_the_title();  echo '</div>';
                        echo '<div class="col-md-2 align-center">
                        	<span class="btn-act-wrap" id="'.get_the_ID().'"><span attr="'.get_the_ID().'" class="btn-act btn-delete"> <i class="fa fa-trash-o" aria-hidden="true"></i> </span> &nbsp; <span  class=" btn-act btn-edit-package"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></span>';
                        echo '</div>';
                        echo '</div>';
                        $post->price = $price; $post->sku = $sku;
                        $post->pack_type = $pack_type;
                        $list_package[$post->ID] = $post;
                        $i ++;
                    }
                    echo '</div>';
                    /* Restore original Post Data */
                    wp_reset_postdata();
                } else {
                	echo '<div class="form-group">';
                    _e('List package plan is empty','boxtheme');
                    echo '</div>';
                } ?>
            </div>
        </div> <!-- .row !-->
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <form class="frm-add-package row">
                    <div class="col-md-12"><h4 class="form-heading"><?php _e('Insert new package','boxtheme');?> </h4></div>
                  	<div class="full">
                  		<div class="col-sm-4 one-line">

                            <input type="text" class="form-control" id="post_title" required name="post_title" placeholder="<?php _e('Name');?>">&nbsp; <i>Package name</i>
                        </div>
                        <div class="col-sm-4 one-line">
                            <input type="text" class="form-control" required name="sku" placeholder="<?php _e('SKU');?>">&nbsp; <i>SKU code</i>
                        </div>
                        <div class="col-sm-4 one-line">
                            <input type="number" class="form-control" required name="price"  min="1" placeholder="<?php _e('Price');?>"  >&nbsp;<i>Price of this package</i>
                        </div>
                        <div class="col-sm-12 one-line">
                        	<textarea id="post_content" name="post_content" class="" placeholder="<?php _e('Description of new package','boxtheme');?>"></textarea>
                        	<input type="hidden" name="ID" id="ID" value="0" />

                        </div>
                        <div class="col-sm-2 align-right one-line pull-right">
                        	<button class="btn btn-act"><?php _e('Create','boxtheme');?></button>
                        </div>
                   	</div>
                </form>
           </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-frm_edit_package">
	<form class="frm-add-package row">

      	<div class="full">
			<div class="col-sm-12 one-line">  			<h3><?php _e('Edit package plan','boxtheme');?></h3>    		</div>
      		<div class="col-sm-4 one-line">
                <input type="text" class="form-control" required name="post_title" value="{{{data.post_title}}}" placeholder="<?php _e('Name');?>">&nbsp; <i><?php _e('Package name','boxtheme');?></i>
            </div>
            <div class="col-sm-4 one-line">
                <input type="text" class="form-control" required="" name="sku" placeholder="SKU" value="{{{data.sku}}}"><small>SKU</small>
            </div>
            <div class="col-sm-4 one-line">
                <input type="number" class="form-control" required="" min="1" name="price" placeholder="Price" value="{{{data.price}}}"><small><?php echo $symbol;?></small>
            </div>
            <div class="col-sm-12 one-line">
            	<textarea id="post_content" name="post_content" class="">{{{data.post_content}}}</textarea>
            </div>
            <input type="hidden" name="ID" id="ID" value="{{{data.ID}}}" />
            <div class="col-sm-10 one-line"></div>
            <div class="col-sm-2 align-right one-line">
            	<button class="btn"><?php _e('Update','boxtheme');?></button>
            </div>
       	</div>
    </form>
</script>
<!-- end buy credit !-->

<div class="section box-section" id="<?php echo $group_option;?>">
    <h2 class="section-title"><?php _e('Post project option','boxtheme');?></h2>
    <div class="sub-section" id="package_plan">
        <div class="row">
            <div class="col-md-3"><h4> <?php _e('Options plan','boxtheme');?> </h4>        </div>
            <div class="col-md-9"> <?php
                $args = array(
                    'meta_key' => 'pack_type', // buy credit or post premium_post
                    'post_type' => '_package',
                    'meta_value' => 'premium_post'
                );
               // $premium_type = array();
                $the_query = new WP_Query($args);
                $premium_types = box_get_premium_types();
                // The Loop
                if ( $the_query->have_posts() ) {
                    echo '<div class="widefat " id="list_package">';
                    $i = 1; ?>
                    <div class=" form-group heading-line">

                            <div class="col-md-1 page-name"><?php _e( 'STT', 'boxtheme' ); ?></div>
                            <div class=" col-md-2 page-name"><?php _e( 'SKU', 'boxtheme' ); ?></div>
                            <div class="col-md-2 page-name"><?php _e( 'Price', 'boxtheme' ); ?></div>
                            <div class="col-md-3 page-name"><?php _e( 'Detail', 'boxtheme' ); ?></div>
                            <div class="col-md-2 page-name">Type</div>
                            <div class="col-md-2 page-name">&nbsp;</div>

                    </div> <?php

                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $class = "item-le"; if($i% 2 == 0) $class = "item-chan";
                        $price = get_post_meta(get_the_ID(),'price', true);
                        $sku = get_post_meta(get_the_ID(),'sku', true);
                        $pack_type = get_post_meta(get_the_ID(),'pack_type', true);
                        $priority =  get_post_meta( get_the_ID(), 'priority', true );
                        if( empty($priority) )
                            $priority = 0;

                        echo '<div class="block  row-item '.$class.'">'; echo '<div class="col-md-1">'.$i.'</div>';   echo '<div class="col-md-2">'.$sku.'</div>';
                        echo '<div class="col-md-2">';  echo box_price($price);  echo '</div>';
                        echo '<div class="col-md-3">';  echo get_the_title();  echo '</div>';
                        ?>
                        <div class="col-md-2 page-name"><?php echo isset ( $premium_types[$priority] ) ? $premium_types[$priority] : __('Unset','boxtheme');?> </div>
                        <?php
                        echo '<div class="col-md-2 align-center">
                            <span class="btn-act-wrap" id="'.get_the_ID().'"><span attr="'.get_the_ID().'" class="btn-act btn-delete"> <i class="fa fa-trash-o" aria-hidden="true"></i> </span> &nbsp; <span  class=" btn-act btn-edit-premium-pack"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></span>';
                        echo '</div>';
                        echo '</div>';

                        $post->price = $price; $post->sku = $sku;
                        $post->pack_type = $pack_type;
                        $post->priority = get_post_meta( get_the_ID(), 'priority', true );
                        $list_package[$post->ID] = $post;
                        $i ++;
                    }
                    echo '</div>';
                    /* Restore original Post Data */
                    wp_reset_postdata();
                } else {
                    echo '<div class="form-group">';
                    _e('List package plan is empty','boxtheme');
                    echo '</div>';
                } ?>
            </div>
        </div> <!-- .row !-->
        <div class="row">
            <div class="col-md-3"><h4 class="form-heading">&nbsp;</div>
            <div class="col-md-9">
                <form class=" frm-add-package frm-add-premiumtype row">
                    <div class="col-md-3"><h4 class="form-heading"><?php _e('Insert new package','boxtheme');?> </h4></div>
                    <div class="full">
                        <div class="col-sm-3 one-line">
                            <input type="text" class="form-control" id="post_title" required name="post_title" placeholder="<?php _e('Name');?>">&nbsp; <i>Package name</i>
                        </div>
                        <div class="col-sm-3 one-line">
                            <input type="text" class="form-control" required name="sku" placeholder="<?php _e('SKU');?>">&nbsp; <i>SKU code</i>
                        </div>
                        <div class="col-sm-3 one-line">
                            <input type="number" class="form-control" required name="price"  min="1" placeholder="<?php _e('Price');?>"  >&nbsp;<i>Price of this package</i>
                        </div>
                         <div class="col-sm-3 one-line">
                            <select required class="form-control required"  name="priority" >
                                <option  value=""><?php _e('Select Priority','boxtheme');?></option>
                                <?php foreach ( $premium_types as $key => $name ) { ?>
                                    <option value="<?php echo $key;?>"> <?php echo $name;?> </option>
                                <?php } ?>

                            </select>
                        </div>
                        <input type="hidden" name="pack_type" value="premium_post">
                        <div class="col-sm-12 one-line">
                            <textarea id="post_content" name="post_content" class="" placeholder="<?php _e('Description of new package','boxtheme');?>"></textarea>
                             <input type="hidden" name="ID" id="ID" value="0" />
                        </div>
                        <div class="col-sm-2 align-right one-line pull-right">
                            <button class="btn"><?php _e('Create','boxtheme');?></button>
                        </div>
                    </div>
                </form>
           </div>
        </div>
    </div>
</div>
<script type="text/template" id="json_list_package"><?php   echo json_encode($list_package); ?></script>
<script type="text/template" id="json_list_subscriptions"><?php   echo json_encode($list_subscriptions); ?></script>
<!--<script type="text/template" id="json_premium_type"><?php   //echo json_encode($premium_type); ?></script> !-->
<script type="text/html" id="tmpl-frm_edit_premium_pack">
    <form class="frm-add-package row">
        <div class="full">
            <div class="col-sm-12 one-line">            <h3><?php _e('Edit package plan','boxtheme');?></h3>            </div>
            <div class="col-sm-3 one-line">
                <input type="text" class="form-control" required name="post_title" value="{{{data.post_title}}}" placeholder="<?php _e('Name');?>">&nbsp; <i>Package name</i>
            </div>
            <div class="col-sm-3 one-line">
                <input type="text" class="form-control" required="" name="sku" placeholder="SKU" value="{{{data.sku}}}"><small>SKU</small>
            </div>
            <div class="col-sm-3 one-line">
                <input type="number" class="form-control" required="" min="1" name="price" placeholder="Price" value="{{{data.price}}}"><small>$</small>
                <input type="hidden" required="" name="pack_type"  value="premium_post">
            </div>
             <div class="col-sm-3 one-line">
                <select class="form-control" name="priority">
                    <?php foreach ($premium_types as $key => $name) { ?>
                        <option value="<?php echo $key;?>" <# if(data.priority == '<?php echo $key;?>') { #> selected <# } #> > <?php echo $name;?> </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-12 one-line">
                <textarea id="post_content" name="post_content" class="">{{{data.post_content}}}</textarea>
            </div>
            <input type="hidden" name="ID" id="ID" value="{{{data.ID}}}" />
            <div class="col-sm-10 one-line"></div>
            <div class="col-sm-2 align-right one-line">
                <button class="btn"><?php _e('Update','boxtheme');?></button>
            </div>
        </div>
    </form>
</script>