<?php
	global $user_ID;

	$user_data = get_userdata($user_ID );

	$country_id  = get_user_meta( $user_ID, 'location', true );
	$txt_country = 'Unset';
	$ucountry = get_term( $country_id, 'country' );

	if( ! is_wp_error($ucountry ) && ! empty( $ucountry ) ){
		$txt_country = $ucountry->name;
	}

	$country_select = '';
	$countries = get_countries();

?>
<div id="profile" class="col-md-12 edit-profile-section edit-em-profile">
	<form id="update_profile" class="frm-overview-emp">
		<div class="form-group "><div class="col-md-3 text-center"> <h2><?php _e('Your Profile','boxtheme');?></h2></div></div>
	    <div class="form-group ">
	    	<div class="col-md-3 update-avatar">
	    		<?php
	    		$url = get_user_meta($user_ID,'avatar_url', true);

	    		if ( ! empty($url ) ){
	    			echo '<img class="avatar" src=" '.$url.'" />';
	    		}else {
	    			echo get_avatar($user_ID);
	    		}
	    		?>
	    	</div>
	      	<div class="col-md-9 col-sm-12">
	      		<div class="col-sm-12"><span class="btn-edit btn-edit-default btn-emp-edit"> Edit</span></div>
	      		<div class="full">
		      		<div class="form-group hide">
		      			<label class="col-md-2"><?php _e('First name','boxtheme');?>: </label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $user_data->first_name;?></span> </div>
		      				<div class="line default-hidden">
			      				<input class="form-control" type="text" required name="first_name" value="<?php  echo $user_data->first_name;?>"  placeholder="<?php _e('First Name','boxtheme');?> " id="first-text-input">
			      			</div>
		      			</div>
		      		</div>
		      		<div class="form-group hide">
		      			<label class="col-md-3"><?php _e('Last name','boxtheme');?></label>
			      		<div class="col-md-9">
			      			<div class="line default-show"><span><?php echo $user_data->last_name;?></span></div>
		      				<div class="line default-hidden">
			      				<input class="form-control " type="text" required name="last_name" value="<?php  echo $user_data->last_name;?>"  placeholder="<?php _e('Last Name','boxtheme');?> " id="last-text-input">
			      			</div>
			      		</div>
		      		</div>

		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Company Name','boxtheme');?></label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $user_data->display_name ;?></span></div>
		      				<div class="line default-hidden">
			      				<input class="form-control " type="text" required name="display_name" value="<?php  echo $user_data->display_name;?>"  placeholder="<?php _e('Display name','boxtheme');?> " id="display-text-input">
			      			</div>
			      		</div>
		      		</div>

		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Email','boxtheme');?></label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $user_data->user_email ;?></span></div>
		      				<div class="line default-hidden">
			      				<input class="form-control " type="text" disabled required name="user_email" value="<?php  echo $user_data->user_email;?>"  placeholder="<?php _e('Your email','boxtheme');?> " id="user_email-text-input">
			      			</div>
			      		</div>
		      		</div>
		      		<?php
		      		$umeta = get_user_meta( $user_ID,  'umeta', true);

		      		$overview = $description = $company_type = $range =$country ='' ;
		      		if( isset( $umeta['overview']) )
		      			$overview = $umeta['overview'];


		      		if( isset( $umeta['description']) )
		      			$description = trim($umeta['description']);

		      		if( isset( $umeta['company_type']) ){
		      			$types = get_company_types();
		      			if( isset( $types[ $umeta['company_type'] ]) )
		      				$company_type = trim($types[$umeta['company_type']]);
		      		}
		      		$ranges = get_company_ranges();
		      		$range_key = 0;
		      		if( isset( $umeta['range']) ){
		      			$range_key = $umeta['range'];
		      			if( isset( $ranges[ $umeta['range'] ]) ){
		      				$range = trim($ranges[$umeta['range']]);
		      			}
		      		}
		      		$country_key = 0;
		      		if( isset( $umeta['country']) ){
		      			$country_key = $umeta['country'];
		      			if( isset( $countries[ $umeta['country'] ]) ){

		      				$country = trim($countries[$umeta['country']]);
		      			}
		      		}

				   if ( ! empty( $countries ) || ! is_wp_error( $countries ) ){
				      	$country_select.= '<select name="country" id="country" class="chosen-select form-control umeta" data-placeholder="Choose a country" >';
				      	$country_select.= '<option value="">Select your country</option>';
				      	foreach ( $countries as $key => $name ) {

				        	$country_select .= '<option value="'.$key .'" '. selected($country , $name, false) .' >' . $name . '</option>';
				      	}
				      	$country_select.= '</select>';
				   } else {
				   	$country_select == __('List country is empty','boxtheme');
				   }

		      		?>
		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Short des company','boxtheme');?></label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $description ;?></span></div>
		      				<div class="line default-hidden">
			      				<textarea class="form-control umeta" type="text"  required name="description" value="<?php  echo $description;?>"  placeholder="<?php _e('Description company','boxtheme');?> " id="user_description-text-input"><?php echo $description;?></textarea>
			      			</div>
			      		</div>
		      		</div>
		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Overview company','boxtheme');?></label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $overview ;?></span></div>
		      				<div class="line default-hidden">
			      				<textarea class="form-control umeta " type="text"  required name="overview" value="<?php  echo $overview;?>"  placeholder="<?php _e('Overview company','boxtheme');?> " id="overview-text-input"><?php echo $overview;?></textarea>
			      			</div>
			      		</div>
		      		</div>

		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Company type','boxtheme');?></label>
		      			<div class="col-md-9">

		      				<div class="line default-show"><span><?php echo $company_type;?> </span></div>
		      				<div class="line default-hidden">
			      				<select class="form-control umeta " name="company_type">
			      					<option value="product" <?php selected($company_type,'product');?> > Product</option>
			      					<option value="outsouce" <?php selected($company_type,'outsouce');?> > Outsouce</option>
			      				</select>
			      			</div>
			      		</div>
		      		</div>

		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Company range','boxtheme');?></label>
		      			<div class="col-md-9">

		      				<div class="line default-show"><span><?php echo $range;?> </span></div>
		      				<div class="line default-hidden">
			      				<select class="form-control umeta " name="range">
			      				<?php
			      				 foreach($ranges as $key=>$lable){ ?>
			      				 	<option value="<?php echo $key;?>" <?php selected($range,$range_key);?> ><?php echo $lable;?></option>
			      				 	<?php } ?>
			      				</select>
			      			</div>
			      		</div>
		      		</div>

		      		<div class="form-group ">
		      			<label class="col-md-3"><?php _e('Country','boxtheme');?></label>
		      			<div class="col-md-9">
		      				<div class="line default-show"><span><?php echo $country; ?></span></div>
		      				<div class="line default-hidden">
			      				<?php echo $country_select;?>
			      			</div>
			      		</div>
		      		</div>
		      		<input type="hidden" name="is_emp" value="1">
		      	</div>
		      	<div class="is-edit full">
					<div class="form-group">
				      	<div class="offset-sm-10 col-sm-12 pull-right align-right no-padding-right">
				        	<button type="submit" class="btn btn-primary update hide "> &nbsp; <?php _e('Save','boxtheme');?> &nbsp;</button>
				      	</div>
				    </div>
		      	</div>
	      	</div>

	    </div>
	</form>
</div> <!-- end left !-->
<style type="text/css">
	.default-hidden{
		display: none;
	}
	.is-edit .default-hidden{
		display: block;
	}
	.default-show{
		width: 100%;
		display: block;
	}
	.is-edit .default-show{
		display: none;
	}
	.1frm-overview-emp .col-md-9{
		height: 30px;
		overflow: hidden;
		display: block;
		float: left;
	}
	.edit-em-profile .form-group{
		min-height: 23px;
	}
</style>