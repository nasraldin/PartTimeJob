<?php

	global $user_ID;
	$profile_id 	= get_user_meta($user_ID,'profile_id', true);
	$profile 		= BX_Profile::get_instance()->convert($profile_id);
	$user_data = get_userdata($user_ID );

   	$txt_country = $slug = $skill_val = $country_select = $phone_number = $address ='';
   	$pcountry = get_the_terms( $profile_id, 'country' );
   	if( !empty($pcountry) ){
    	$txt_country =  $pcountry[0]->name;
      	$slug = $pcountry[0]->slug;
   	}

   	$countries = get_terms( 'country', array(
    	'hide_empty' => false)
   	);

   if ( ! empty( $countries ) || ! is_wp_error( $countries ) ){
      	$country_select.= '<select name="country" id="country" class="chosen-select form-control" data-placeholder="Choose a country" >';
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

    	$skill_list .=  '<select name="skill" multiple  id="skill" class="chosen-select form-control" data-placeholder="Selec your skills" >';
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
   <div class="col-md-12 clear hide">
      <div class="video block">

         <span href="#" class="btn-edit btn-edit-video"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</span>
         <h2> Video </h2>
         <?php
         $video_id = get_post_meta($profile_id, 'video_id', true);

         if( !empty($video_id) ){ ?>
            <div class="video-container">
            <iframe width="635" height="315" src="https://www.youtube-nocookie.com/embed/<?php echo $video_id;?>?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
            </div>

         <?php } ?>
         <form class="update-one-meta">
            <!-- <img src="<?php echo get_stylesheet_directory_uri().'/img/youtube.png';?>" /> -->
            <div class="form-group row">
               <label class="col-sm-3 col-form-label">Set youtube video ID</label>
               <div class="col-sm-9">
                  <input type="text" class="update form-control" name="video_id" value="<?php echo $video_id;?>" placeholder="<?php _e('Set your youtube video ID here','boxtheme');?>">
               </div>
            </div>
            <input type="hidden" name="ID" value="<?php echo $profile_id;?>" >

            <div class="form-group row">
                  <label class="col-sm-3 col-form-label">&nbsp;</label>
                  <div class="col-sm-9 align-right">
                     <button type="submit" class="btn btn-primary"><?php _e('Save','boxtheme');?></button>
                  </div>
            </div>
         </form>
      </div>
   </div>

   	<div class="edit-profile-section profile-info-section col-md-12 clear" >
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
				 	<label for="country" class="col-sm-3 col-form-label"><?php _e('Hour rate','boxtheme');?></label>
				 	<div class="col-sm-9">
				    <span class="visible-default"><?php echo  $profile->hour_rate ;?></span>
				    <div class="invisible-default">
				       <input type="number" min="1"  class="update form-control" value="<?php echo $profile->hour_rate;?>" name="hour_rate">
				    </div>
				 </div>
				</div>
				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-form-label"><?php _e('Phone','boxtheme');?></label>
				 <div class="col-sm-9">
				    <span class="visible-default"><?php echo  $profile->phone_number ;?></span>
				    <div class="invisible-default">
				       <input type="text" class="update form-control " value="<?php echo $profile->phone_number;?>" name="phone_number">
				    </div>
				 </div>
				</div>
				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-form-label"><?php _e('Address','boxtheme');?></label>
				 <div class="col-sm-9">
				    <span class="visible-default"><?php echo $profile->address ;?></span>
				    <div class="invisible-default">
				        <input type="text" class="update form-control" value="<?php echo $profile->address;?>" name="address">
				    </div>
				 </div>
				</div>


				<div class="form-group row">
				 <label for="country" class="col-sm-3 col-xs-12 col-form-label"><?php _e('Country','boxtheme');?></label>
				 <div class="col-sm-9 set-relative">
				    <span class="visible-default"><?php echo !empty($txt_country) ? $txt_country : __('Unset','boxtheme');?></span>
				    <div class="chosen-edit-wrap">
				       <?php echo $country_select;?>
				    </div>
				 </div>
				</div>
				<div class="form-group row">
					 <label for="country" class="col-sm-3 col-form-label"><?php _e('Skill','boxtheme');?></label>
					 <div class="col-sm-9 set-relative">
					    <span class="visible-default"><?php echo  $skill_val ;?></span>
					    <div class="chosen-edit-wrap">  <?php echo $skill_list;?></div>
					 </div>
					 <input type="hidden" name="ID" value="<?php echo $profile_id;?>" >
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


<div class="col-md-12 center frame-add-port edit-profile-section portfolio-section">
	<div class="full">
		<button class="btn btn-show-portfolio-modal"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; <?php _e('Add portfolio','boxtheme');?></button>
	</div>

	<div class="row-section col-md-12" id="list_portfolio">
		<!-- portfolio !-->

		<?php
		global $user_ID, $list_portfolio;
		$args = array(
			'post_type' 	=> 'portfolio',
			'author' 		=> $user_ID,
		);
		$result =  new WP_Query($args);
		$list_portfolio = array();
		if( $result->have_posts() ){
			while ($result->have_posts()) {

				$result->the_post();
				$post->feature_image = get_the_post_thumbnail_url($post->ID, 'full');
				$post->thumbnail_id = get_post_thumbnail_id($post->ID);
				$list_portfolio[$post->ID] = $post;
				echo '<div class="col-md-4 port-item" id="'.$post->ID.'">';
					the_post_thumbnail('full' );
					echo '<div class="btns-act"><span class="btn-sub-act btn-edit-port "><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>';
					echo '<span class="btn-sub-act btn-del-port" ><i class="fa fa-times" aria-hidden="true"></i></span></div>';
				echo '</div>';
			}
			wp_reset_query();
		} else {
			_e('There is no any portfolio yet','boxtheme');
		}
		?>

	</div>
	<!-- end portfolio !-->
</div>