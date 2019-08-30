
<div class="full search-adv">
	<div class="block full hidden-md-up">
		<h2 class="sidebar-title "> <?php _e('Advance Filters','boxtheme');?>
	</div>
	<div class="block full">
		<?php if ( map_in_archive() ) { ?>
		<div class="geo_address full">
            <input type="text" class="form-control required" required  name="geo_address" id="autocomplete"  onFocus="geolocate()"  placeholder="<?php _e('Address ...','boxtheme');?>" value="" ><i class="fa fa-map-marker" aria-hidden="true"></i>

                <?php box_map_field_auto();?>
              <i class="locate-me"></i>
        </div>
    	<?php } ?>

		<h3 class="block-title "> <?php _e('Skills','boxtheme');?>  <i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></span></h3>
		<select class="form-control required chosen-select" name="skill" required  multiple data-placeholder="<?php _e('Enter skills','boxtheme');?> ">
	       	<?php
	       	$skills = get_terms(
	       		array(
	           		'taxonomy' => 'skill',
	           		'hide_empty' => false,
	          	)
	       	);
	       if ( ! empty( $skills ) && ! is_wp_error( $skills ) ) {
	       		$i = 1;
	            foreach ( $skills as $skill ) {
	              	echo '<option  value="' . $skill->term_id . '" alt="'.$i.'">' . $skill->name . '</option>';
	              	$i++;
	            }
	        }
	       ?>
	    </select>
	    <div id="selected_html"></div>
	</div>
	<div class="block full">
		<h3 class="block-title toggle-check"><?php echo box_get_country_args()->label['name'];?><i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></h3>
		<ul class="list-checkbox ul-cats">
			<?php
				$countries = get_terms( array(
	                'taxonomy' => box_get_country_args()->slug,
	                'hide_empty' => true,
	                'orderby'    => 'count',
	                'order' => 'DESC',
	            	)
				);
	            if ( ! empty( $countries ) && ! is_wp_error( $countries ) ){
	                foreach ( $countries as $key=>$country ) {

	                   echo '<li><label class="skil-item"> ' . $country->name . '<span class="term-count-profile">('.$country->count.')</span> <input type="checkbox" name="country" class="search_country" alt="'.$key.'"  value="' . $country->term_id . '"> <i class="fa fa-check primary-color" aria-hidden="true"></i></label></li>';
	                }
	            } else { ?>
	            <li> <?php _e('The is not any locations','boxtheme');?></li>
	            <?php }
	     	?>
	    </ul>
	</div>
	<?php if( map_in_archive() ) { ?>
	<div class="search-radius-wrapper in-use full">
      	<div class="search-radius-label full " id="wrap_range">
	        <input type="text" name="distance" class="radius-filter" id="range">
	        <span><?php _e('Radius:','boxtheme');?></label> <span style="float:right"> <?php _e('< 1000 Miles','boxtheme');?> </span>

      </div>

    </div>
<?php } ?>

	<div class="block full hide">
      <label i18n-id="2518b9c5418254bf1a86c2baf1799761" i18n-msg="Specific Location">Specific Location</label>
      <div class="filter-item-group-location">
        <input type="text" id="specific-location" placeholder="Add Location" class="search-input" i18n-placeholder-id="a46340dfde3d21b81a706650aacab998" i18n-placeholder-msg="Add Location" i18n-id="" autocomplete="off">
        <button id="specific-location-geocode" class="btn" type="button" aria-label="Use Current Location">
          <span class="Icon Icon--small"><fl-icon name="ui-pin"><svg class="Icon-image" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"></path></svg></fl-icon></span>
        </button>
      </div>

	</div>

	<div class="block full hide">
		<h3 class="block-title"> <?php _e('Skills','boxtheme');?>  <i class="toggle-check fa fa-sort-desc pull-right" aria-hidden="true"></i></span></h3>

	 	<ul class="list-checkbox ul-skills">

			<?php
				$skills = get_terms( array(
	                'taxonomy' => 'skill',
	                'hide_empty' => true,
	            ) );
	            if ( ! empty( $skills ) && ! is_wp_error( $skills ) ){
	                foreach ( $skills as $key=>$skill ) {
	                   	echo '<li><label class="skil-item"> <input type="checkbox" name="skill" class="search_skill" alt="'.$key.'" value="' . $skill->term_id . '">' . $skill->name . '<i class="fa fa-check primary-color" aria-hidden="true"></i></label></li>';
	                }
	             }
	         ?>
	 	</ul>
	</div>
	<input type="hidden" name="post_type" id="post_type" value="profile">
	<div class="disable-search"></div>

</div> <!-- end search adv !-->
<button class="btn btn-adv full mobile-only no-radius"> <?php _e('Advance Filter','boxtheme');?></button>

