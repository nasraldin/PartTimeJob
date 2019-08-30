<?php
/**
 *  Template Name: Map Template
 */
get_header(); ?>
<?php box_map_autocomplete_script();?>

      <div class="left-search-bar col-md-4 no-padding-right">
        <div class="job_listings">
          <form class="job_filters">
            <div class="col-md-12"><center><h1><?php _e('Search a freelancer nearby location','boxtheme');?> </h1></center> <br /></div>
            <div class="search_jobs form-row">

              <div class="search_keywords col-md-7">
                <input type="text" name="keywords" class="form-control" id="search_keywords" placeholder="<?php _e('Keyword','boxtheme');?>" value="">
              </div>


              <div class="geo_address col-md-5 no-padding-left">
                <input type="text" class="form-control required" required  name="geo_address" id="autocomplete"  onFocus="geolocate()"  placeholder="<?php _e('Address ...','boxtheme');?>" value="" ><i class="fa fa-map-marker" aria-hidden="true"></i>

                <?php box_map_field_auto();?>
              <i class="locate-me"></i></div>

            </div>
            <div class="search_jobs form-row">
              <div class="search_keywords col-md-12">
                <?php box_tax_dropdown('skill', __('Enter skills','boxtheme') );?>
              </div>

            </div>

            <div class="search-radius-wrapper in-use full" id="wrap_range">
              <div class="search-radius-label full">
                <div class="col-md-12"><input type="text" name="distance" id="range_02"></div>
                <div class="col-md-12"><label><?php _e('Radius:','boxtheme');?></label> <label style="float:right"> <?php _e('< 1000 Miles','boxtheme');?> </label></div>
              </div>

            </div>
            <div class="col-md-12 " style="text-align: center; "><button type="submit" class="search-btn" id="update_results"><?php _e('Search','boxtheme');?></button></div>
          </form>
          <div class="result_filter">
            <div class="col-md-12">
                <?php
                  //map_remove_all_sample();
                  //insert_markers_sample();
                  global $wpdb;
                  $sql = " SELECT p.*, ex.* FROM {$wpdb->prefix}posts p  LEFT JOIN {$wpdb->prefix}profile_extra ex on ex.profile_id = p.ID WHERE 1 = 1 AND ex.lat_address IS NOT NULL AND  p.post_type = 'profile' AND post_status = 'publish'  GROUP BY p.ID ";
                  $markers = array();
                  $results = $wpdb->get_results($sql);
                  $text_result = __( 'No freelancer found.','boxtheme');

                  if( $results ){
                    $text_result = sprintf(__(' %s freelancers found.','boxtheme'), $wpdb->num_rows );
                    foreach ($results as $post) {

                        $profile  = BX_Profile::get_instance()->convert($post);
                        // $skill_html = get_skill_html_profile($profile->ID);
                        // $professional_title = get_post_meta($profile->ID, 'professional_title', true);

                        $marker['html_marker'] = box_html_marker($profile);

                        $marker['lat_address'] = $profile->lat_address;
                        $marker['long_address'] = $profile->long_address;
                        $marker['title'] = $profile->profile_name;
                        $markers[] = $marker;
                    }
                  }
                  unset($results);
                  wp_reset_query();

                ?>
              <p id="text_result"><?php echo $text_result;?></p>
            </div>
          </div>
        </div>
      </div>  <!-- en left search bar !-->
    <div  class="col-md-8" id="bmap" ></div>

     <?php get_template_part( 'modal/mobile', 'login' ); ?>
    <?php wp_footer();?>
</body>

<?php
global $app_api;
$gmap_key =  (string) $app_api->gmap_key;
?>
<script src="//maps.google.com/maps/api/js?key=<?php echo $gmap_key;?>&libraries=places&callback=initAutocomplete" type="text/javascript"></script>
<script type="text/javascript" src="https://googlemaps.github.io/js-marker-clusterer/src/markerclusterer.js"></script>
<script type="text/template" id="json_list_profile"><?php echo json_encode($markers); ?></script>

<style type="text/css">
    .user-marker{
      display: block;
      width: 359px; overflow: hidden;
      padding: 10px 0;
      font-family: 'Raleway', sans-serif;
    }
    .user-marker .marker-avatar img{
      max-width: 100px;
      height: auto;
      border:1px solid #f1f1f1;
      border-radius: 50%;
    }
  .user-marker .half-left {
      width: 100px;
      float: left;

  }
    .user-marker .half-right{
      width: 259px;
      float: left;
      padding-left: 15px;
    }
    .user-marker h2,.user-marker h3{
      margin:0; padding: 0;     white-space: nowrap; text-overflow: ellipsis;
      font-weight: bold;
    }


    .user-marker h2{
      font-size: 16px;
    }
    .user-marker h3{
      font-size: 15px;
      padding-top: 5px;
    }
    .user-marker .profile-title a{
      display: block;
      padding: 5px 0;
    }
    .mk-skils{
      margin-top: 10px;
      font-size: 14px;
    }
    .job_filters{
      clear: both;
      display: block;
      width: 100%;
      float: left;
    }
    .job_filters .form-control{
      margin-bottom: 25px;
      height: 42px;
      border-radius: 5px;
      width: 100%;

    }
    body .chosen-container-multi .chosen-choices{
      min-height: 39px; border-radius: 5px;

    }
    .chosen-container{
      width: 100% !important;
    }
    .search-btn {
      background: rgb(30, 159, 173);
      margin-top: 40px;
      width: 100%;
      height: 50px;
      border: none;
      border-radius: 30px;
      color: #FFF;
      text-align: center;
      box-shadow: 0 0 20px 0 #b0d6f4;
      text-transform: uppercase;
  }
  .irs-line{
    background: #eee;
  }
  .irs-bar-edge{
    background: rgb(30, 159, 173);
  }
  .result_filter {
      border-top: 1px solid #ccc;
      margin-top: 38px;
      padding-top: 40px;
      width: 100%;
      float: left;
      clear: both;
  }
  body{
    background: #fff !important;
  }
  </style>