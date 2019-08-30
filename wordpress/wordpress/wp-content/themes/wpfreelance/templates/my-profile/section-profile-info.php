<?php

	global $user_ID;
	$register_address = get_user_meta($user_ID,'address', true);
	$profile_id 	= get_user_meta($user_ID,'profile_id', true);
    if( ! empty( $register_address ) ){
    	update_post_meta($profile_id,'address',$register_address);
    	update_user_meta($user_ID,'address','');
    	$user_lat = get_user_meta($user_ID,'lat', true);
    	update_post_meta($profile_id,'lat', $user_lat);
    	$user_lng = get_user_meta($user_ID,'lng', true);
    	update_post_meta($profile_id,'lng', $user_lng);
    }

	$profile 		= BX_Profile::get_instance()->convert($profile_id);
	$user_data = get_userdata($user_ID );
	$is_subscriber = $profile->is_subscriber;


   	$txt_country = $slug = $skill_val = $country_select = $phone_number = $address ='';

   	$country_slug = box_get_country_args()->slug;

   	$pcountry = get_the_terms( $profile_id, $country_slug );
   	if( !empty($pcountry) ){
    	$txt_country =  $pcountry[0]->name;
      	$slug = $pcountry[0]->slug;
   	}

   	$countries = get_terms( $country_slug, array(
    	'hide_empty' => false,
    	// 'orderby'    => 'count',
	    // 'order' => 'DESC',
    	)
   	);

   if ( ! empty( $countries ) || ! is_wp_error( $countries ) ){
      	$country_select.= '<select name="'.$country_slug.'" id="country" class="chosen-select form-control" data-placeholder="'.__('Choose a country','boxtheme').'" >';
      		$country_select.='<option value = "0">'.__('Select Country','boxtheme').'<option>';
      	foreach ( $countries as $country ) {
        	$country_select .= '<option value="'.$country->slug.'" '. selected($country->slug, $slug, false) .' >' . $country->name . '</option>';
      	}
      	$country_select.= '</select>';
   } else {
   	$country_select == __('List country is empty','boxtheme');
   }


   	$list_ids = array();
   	$skills = get_the_terms( $profile_id, 'skill' );

   	if ( $skills && ! is_wp_error( $skills ) ){

      	$draught_links = array();

      	foreach ( $skills as $term ) {
        	$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
         	$list_ids[] = $term->term_id;
      	}
      	$skill_val = join( ", ", $draught_links );
   	}

   	$skills = get_terms( 'skill', array(
    	'hide_empty' => false));
   	$skill_list = '';

   	if ( ! empty( $skills ) && ! is_wp_error( $skills ) ){

    	$skill_list .=  '<select name="skill" multiple  id="skill" class="chosen-select form-control" data-placeholder="'.__('Select your skills','boxtheme').'" >';
      	foreach ( $skills as $skill ) {
        	$selected = "";
         	if( in_array($skill->term_id, $list_ids) ){
            	$selected = ' selected ';
         	}
        	$skill_list .= '<option '.$selected.' value="'.$skill->slug.'" >' . $skill->name . '</option>';
      	}
      $skill_list.='</select>';
   }

   ?>
   	<div class="edit-profile-section profile-info-section col-md-12 clear edit-fre-freelancer.php" >
   		<div class="col-md-12 clear">
   				<h2> <?php _e('Profile info','boxtheme');?></h2>
			<form id="update_profile_meta" class="update-profile row-section">
				<span class="btn-edit btn-edit-second"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php _e('Edit','boxtheme');?></span>
				<div class="form-group row hide">
				 	<label for="country" class="col-sm-3 col-form-label"><?php _e('Email','boxtheme');?></label>
				 	<div class="col-sm-9">
				    <span class="visible-default"><?php echo $user_data->user_email ;?></span>
				    <div class="invisible-default">
				       <input type="text"  class="update form-control" value="<?php echo $user_data->user_email;?>" name="user_email">
				    </div>
				 </div>
				</div>

				<div class="form-group row">
				 	<label for="country" class="col-sm-3 col-xs-4 col-form-label"><?php _e('Hour rate','boxtheme');?></label>
				 	<div class="col-sm-9 col-xs-8">
					    <span class="visible-default"><?php echo  $profile->hour_rate ;?></span>
					    <div class="invisible-default">
					       <input type="number" min="1"  class="update form-control" value="<?php echo $profile->hour_rate;?>" name="hour_rate">
					    </div>
					</div>
				</div>
				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-xs-4 col-form-label"><?php _e('Phone','boxtheme');?></label>
				 <div class="col-sm-9 col-xs-8">
				    <span class="visible-default"><?php echo  $profile->phone_number ;?></span>
				    <div class="invisible-default">
				       <input type="text" class="update form-control " value="<?php echo $profile->phone_number;?>" name="phone_number">
				    </div>
				 </div>
				</div>
				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-xs-4 col-form-label"><?php _e('Address','boxtheme');?></label>
				 <div class="col-sm-9 col-xs-8 ">
				    <span class="visible-default"><?php echo $profile->address ;?></span>
				    <?php
				    global $user_ID;
				    $lat_address = $long_address = '';
				    $geo_info = box_get_geo_info($profile_id);
				    if( $geo_info ){
				    	$lat_address =  $geo_info->lat_address;
				    	$long_address = $geo_info->long_address;
				    }

				    ?>

                        <input type="hidden" name="lat_address" id="lat_address" value="<?php echo $lat_address;?>">
                        <input type="hidden" name="long_address" id="long_address" value="<?php echo $lat_address;?>">

				    <div class="invisible-default">
				        <input  id="autocomplete" type="text" class="update form-control" value="<?php echo $profile->address;?>" onFocus="geolocate()" name="address">
				    </div>
				 </div>
				</div>


				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-xs-4 col-form-label"><?php echo box_get_country_args()->label['singular_name'];?></label>
				 <div class="col-sm-9 set-relative col-xs-8">
				    <span class="visible-default"><?php echo !empty($txt_country) ? $txt_country : __('Unset','boxtheme');?></span>
				    <div class="chosen-edit-wrap">
				       <?php echo $country_select;?>
				    </div>
				 </div>
				</div>
				<div class="form-group row">
					 <label for="country" class="col-sm-3 col-form-label"><?php _e('Your Skills','boxtheme');?></label>
					 <div class="col-sm-9 set-relative">
					    <span class="visible-default"><?php echo  $skill_val ;?></span>
					    <div class="chosen-edit-wrap">  <?php echo $skill_list;?></div>
					 </div>
					 <input type="hidden" name="ID" value="<?php echo $profile_id;?>" >
				</div>
				<div class="form-group row">
				 	<label for="country" class="col-sm-3 col-xs-4 col-form-label"></label>
				 	<div class="col-sm-9 col-xs-8">
					    <div class="invisible-default">
					       <label for="is_subscriber"><input type="checkbox" value="1" <?php checked($is_subscriber,1); ?> class="update " style="float: left;" value="" name="is_subscriber" id = "is_subscriber"> &nbsp;
					       <span><?php _e('Receive new email alert.','boxtheme');?> </span>
					   </label>
					    </div>
					</div>
				</div>
				<?php
				global $escrow;

				if( $escrow->active == 'paypal_adaptive'){
					$paypal_email = get_user_meta($user_ID, 'paypal_email', true);
					?>
				<div class="form-group row">
					<label for="country" class="col-sm-3 col-form-label"><?php _e('PayPal Email','boxtheme');?></label>
					<div class="col-sm-9">
						<span class="visible-default"><?php echo $paypal_email ;?></span>
						<div class="invisible-default">
						    <input type="email" class="update form-control" value="<?php echo $paypal_email;?>" name="paypal_email">
						</div>
					</div>
				</div>
				<?php } ?>

				<div class="form-group row invisible-default">
				 <div class="offset-sm-10 col-sm-12 align-right">
				   <button type="submit" class="btn btn-primary"> &nbsp; <?php _e('Save','boxtheme');?> &nbsp;</button>
				 </div>
				</div>

			</form>
		</div>
	</div>


