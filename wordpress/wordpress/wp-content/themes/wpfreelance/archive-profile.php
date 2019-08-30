<?php
/**
 * @key:archive-profile.php
 */
get_header();

global $box_general;
$class = "col-md-9";
$wrap_class = '';
if($box_general->one_column){
	$wrap_class ='one-column-layout';
}
?>
<?php box_map_autocomplete_script();?>

<div class="container site-container <?php echo $wrap_class;?>">
	<div class="row" id="content" >
		<div class="set-bg full">
			<?php if($box_general->one_column) {
				$class = "col-md-12";
				?>
			<?php } else { ?>
			<div class="col-md-3 sidebar sidebar-search set-bg box-shadown" id="sidebar">
				<?php get_template_part( 'sidebar/archive', 'profiles' ); ?>
			</div>

			<?php } ?>
			<div class="<?php echo $class;?> no-padding-right" id="right_column">
				<div class="full set-bg box-shadown">
					<div class="col-md-12" id = "search_line">

							<form action="<?php echo get_post_type_archive_link(PROFILE);?>" class="full frm-search">
								<div class="input-group full">
							       <input type="text" name="s" id="keyword"  required placeholder="<?php _e('Search...','boxtheme');?>" value="<?php echo get_search_query();?>" class="form-control required" />
							       <div class="input-group-btn">
							           <button class="btn btn-info primary-bg"> <i class="fa fa-search" aria-hidden="true"></i>  </button>
							       </div>
							   </div>
							</form>

						<div class="full hide" id="count_results">
							<h5> &nbsp;<?php printf( __('There are %s profiles.','boxtheme'), $wp_query->found_posts )?>	</h5>
						</div>
					</div>
					<div class="col-md-12 count-result count-result-static"><div class="full"><h5><?php printf(__('There are %s profiles.','boxtheme'),$wp_query->found_posts);?></h5></div><div></div></div>
					<?php if( map_in_archive() ){?>
						<div  class="full" id="bmap" style="height: 350px;"></div>
					<?php }?>

					<div class="list-project" id="ajax_result">
						<?php
							global $wp_query;
							$markers = array();
							if( have_posts() ): ?>

								<?php

								$all_marker_args = array(
									'post_type' => 'profile',
									'post_status' => 'publish',
									'posts_per_page' => -1,
								);
								$all_marker = new WP_Query($all_marker_args);
								$skill_html = '';
								while ($all_marker->have_posts() ) {
									$all_marker->the_post();
									$profile 	= BX_Profile::get_instance()->convert($post);
									$marker = array();
									$lat_address = get_post_meta($profile->ID,'lat_address', true);

									$skills = get_the_terms( $profile->ID, 'skill' );
									if ( $skills && ! is_wp_error( $skills ) ){

									  	$draught_links = array();

									  	foreach ( $skills as $term ) {
									    	//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
									    	$draught_links[] = '<span >'.$term->name.'</span>';
									     	$list_ids[] = $term->term_id;
									  }
									  $skill_html = join( ",", $draught_links );
									}

									if( ! empty($lat_address) ){
										$marker['html_marker'] = '<div class="user-marker"><div class="marker-avatar half-left">'.get_avatar($post->post_author).'</div><div class="half-right half"><h2><a href="'.get_permalink($profile->ID).'">'.$post->post_title.'</a></h2><h3>'.$profile->professional_title.'</div><div class="full">'.$skill_html.'</div>';
										$marker['lat_address'] = $lat_address;
										$marker['long_address'] = get_post_meta($post->ID,'long_address', true);
										$markers[] = $marker;
									}
								}
								wp_reset_query();


								while( have_posts() ):the_post();
									global $post;
									$profile 	= BX_Profile::get_instance()->convert($post);

									$skills = get_the_terms( $post->ID, 'skill' );
									if ( $skills && ! is_wp_error( $skills ) ){

									  	$draught_links = array();

									  	foreach ( $skills as $term ) {
									    	//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
									    	$draught_links[] = '<span >'.$term->name.'</span>';
									     	$list_ids[] = $term->term_id;
									  }
									  $skill_html = join( ",", $draught_links );
									}
									if($box_general->one_column)
										get_template_part( 'templates/profile', 'loop-one-column' );
									else
										get_template_part( 'templates/profile', 'loop' );

								endwhile;
								bx_pagenate();
							endif;
							wp_reset_query();
						?>
					</div>
				</div>
			</div>
		</div><!-- .set bg !-->
	</div> <!-- .row !-->
</div>

<?php
global $app_api;
$gmap_key =  (string) $app_api->gmap_key;
if( map_in_archive() ){
?>
<script src="//maps.google.com/maps/api/js?key=<?php echo $gmap_key;?>&libraries=places&callback=initAutocomplete" type="text/javascript"></script>
<script type="text/javascript" src="https://googlemaps.github.io/js-marker-clusterer/src/markerclusterer.js"></script>
<script type="text/template" id="json_list_profile"><?php echo json_encode($markers); ?></script>
<?php } ?>


<script type="text/html" id="tmpl-search-record">
	<div class="archive-profile-item profile-item-{{{data.ID}}}">
		<div class="full archive-full">
			<div class="col-md-2 no-padding col-xs-3 col-avatar">
				<a class="avatar" href = "{{{data.author_link}}}">{{{data.avatar}}}</a>
			</div>
			<div class="col-md-10 align-left  col-xs-9 res-content res-second-line no-padding-right">

				<h3 class="profile-title no-margin col-xs-12">
					<a class="" href = "{{{data.author_link}}}">{{{data.profile_name}}}</a>
				</h3>
				<span class="inline second-line col-md-12 col-xs-12">
					<span class="item professional-title primary-color">{{{data.professional_title}}}</span>
				</span>

				<span class="inline list-info col-md-12 no-padding-right no-padding-left">
					<span class=" item hour-rate col-md-3  no-padding-left"><i class="fa fa-clock-o " aria-hidden="true"></i><span class="txt-rate">{{{data.hour_rate_text}}}</span></span>
					<span class=" item eared-txt col-md-3 col-xs-4 text-center"><?php the_box_icon('earning');?> {{{data.earned_txt}}} </span>
					<span class=" item country-profile col-md-3 col-xs-4 text-center"> <i class="fa fa-map-marker" aria-hidden="true"></i> {{{data.country}}} </span>
					<span class="item profile-rating col-md-3 col-xs-4 no-padding-right text-right hidden-xs">
						<start class="rating-score {{{data.score_class}}}">
							<i class="fa fa-star" aria-hidden="true"></i>
							<i class="fa fa-star" aria-hidden="true"></i>
							<i class="fa fa-star" aria-hidden="true"></i>
							<i class="fa fa-star" aria-hidden="true"></i>
							<i class="fa fa-star" aria-hidden="true"></i>
						</start>
					</span>
				</span>
			</div>
			<div class="col-md-10 align-left  col-xs-12 res-content no-padding-right">
				<span class="overview-profile clear col-xs-12">{{{data.short_des}}}</span>
				<small class="clear skills">{{{data.skill_text}}}</small>
			</div>
		</div>
	</div>

</script>
<script type="text/javascript">
	(function($){
		var h_right = $("#right_column").css('height'),
			h_left = $("#sidebar").css('height');
			$(".list-project").css('min-height',h_left);
		if( parseInt(h_left) > parseInt(h_right) ){

			$(".list-project").css('height',h_left);
		}

	})(jQuery);
</script>
<?php get_footer(); ?>
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
      min-height: 33px; border-radius: 5px;

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
  </style>