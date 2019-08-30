<?php
/**
 *  Template Name: Profile of current user.
 */
?>
<?php get_header(); ?>

  <div class="container site-container">
    <div class="row site-content" id="content" >
        <div class="col-md-12">
        <?php
        global $current_user, $profile, $profile_id, $user_ID, $current_user,$skills;
        $role = bx_get_user_role();
        $profile_id = (int) get_user_meta($user_ID,'profile_id', true);
        $current_user = wp_get_current_user();

        if( empty($profile_id) ){
        $profile_id = box_create_a_profile_later($current_user);
        }
        if( $profile_id ){

            $chekc_p = get_post($profile_id);
            if( ! $chekc_p || is_wp_error( $chekc_p ) ){
                $profile_id = box_create_a_profile_later($current_user);
            }

            $profile = BX_Profile::get_instance()->convert($profile_id);
            ?>

            <ul class="box-tab nav nav-tabs hide">
                <li class="active" ><a   href="#panel1" role="tab"><?php _e('My Profile','boxtheme');?></a></li>
                <li><a  href="#panel2" role="tab"><?php _e('My Subscriber','boxtheme');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="  tab-pane  fade in active" id="panel1" role="tabpanel">
                    <?php
                    get_template_part( 'templates/my-profile/section', 'about-me' );
                    get_template_part( 'templates/my-profile/section', 'profile-info' );
                    get_template_part( 'templates/my-profile/section', 'portfolio' );
                    ?>
                </div>

                <div class="tab-pane fade" id="panel2" role="panel2">
                    <?php get_template_part( 'templates/profile/edit', 'fre-subscriber-form' ); ?>
                </div>
            </div> <?php
        }

        ?>
      </div>

    </div>
  </div>
<?php
  /**
   * Add modal and json for js query.
  */
  get_template_part( 'templates/my-profile/section', 'footer' );
?>
<?php get_footer();?>
<script>
  var placeSearch, autocomplete;
  var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
  };

  function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
        {types: ['geocode']});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);
  }

  function fillInAddress() {
    console.log('fillInAddress');

    // Get the place details from the autocomplete object.
    var place = autocomplete.getPlace();

    var lat = place.geometry.location.lat();
    var lng = place.geometry.location.lng();
    document.getElementById('lat_address').value = lat;
    document.getElementById('long_address').value = lng;


    for (var component in componentForm) {
      document.getElementById(component).value = '';
      document.getElementById(component).disabled = false;
    }

    // Get each component of the address from the place details
    // and fill the corresponding field on the form.
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById(addressType).value = val;
      }
    }
  }

  // Bias the autocomplete object to the user's geographical location,
  // as supplied by the browser's 'navigator.geolocation' object.
  function geolocate() {
    console.log('geolocate');
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var geolocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
          center: geolocation,
          radius: position.coords.accuracy
        });
        autocomplete.setBounds(circle.getBounds());
      });
    }
  }

</script>
<?php

global $app_api;
$gmap_key =  (string) $app_api->gmap_key;
?>
?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmap_key;?>&libraries=places&callback=initAutocomplete" async defer></script>