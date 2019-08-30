<?php
// refer https://support.advancedcustomfields.com/forums/topic/conditional-statements-in-frontend-acf-form/
// http://www.telegraphicsinc.com/2013/07/how-to-add-new-post-title-using-aacf_form/
//ajax form: https://support.advancedcustomfields.com/forums/topic/using-acf-form-in-ajax-call/
//wp-content\plugins\advanced-custom-fields-pro-master\includes\forms\form-front.php
//function acf_init(){


if( ! class_exists('acf_pro') )
	return ;


$option = BX_Option::get_instance();
$plugin_setting = (object)$option->get_plugins_settings();
$acfsetting = (object)$plugin_setting->acf_pro;
$project_group_id = isset( $acfsetting->project_group_id) ? $acfsetting->project_group_id : false ;

if(empty($project_group_id))
	return;


acf_register_form(array(
	'id'		=> 'new-event',
	'post_id'	=> 'new_post',
	'field_groups' => array($project_group_id),
	'new_post'	=> array(
		'post_type'		=> 'event',
		'post_status'	=> 'publish'
	),
	'form' => false,
	//'post_title'=> true,
	//'post_content'=> true,
));
//}
//add_action('init','acf_init');
global $post_args;
$post_args = array(
		//'post_id' => 0,
		'field_groups' => array($project_group_id),
		'form' => false,
		'return' => false,
	);
function show_acf_form($project){
	global $post, $post_args;
	_e('<h3> Custom Fields Addon</h3>','boxtheme');
	//acf_form_head();
	//acf_form( $post_args );
	acf_form('new-event');
	//acf_get_fields();

}
add_action('box_post_job_fields','show_acf_form');

add_action('wp_head','acf_script', 1);
function acf_script(){ ?>
	<script type="text/javascript">
		(function($){

			acf.o.post_id = 111;

		});
	</script>
	<?php
}

function show_acf_fields($project){
	global $post;

	$fields = get_field_objects($project->ID);

	if( $fields ){
		echo '<h3 class="default-label">' . __('Advances Custom Fields Pro','boxtheme').'</h3>';
		foreach( $fields as  $field_name =>$field )	{

			$value = $field['value'];
			if( empty( $value  ))
				continue;
			$field_type = $field['type']; //google_map

			$class = 'col-md-9';
			if($field_type == 'google_map')
				$class ='col-md-12';

			echo '<div class="acf-row row acf-row-'.$field_type.'">';
				echo '<label class="col-md-3 lb-meta-field">'.acf_get_field_label($field).':</label> ';
				echo '<div class="'.$class.'">';
				if( ! is_array( $field['value'] ) ){
					if( $field_type == 'date_picker'){
						//$date= date_create($field['value']);
						//echo date_format( $date, get_option( 'date_format') );
						echo $field['value'];
						//echo date( get_option('date_format'), $date );
					} else {
						echo $field['value'];
					}
				} else  if( $field_type ==  'google_map'  && !empty($value['lat']) && !empty($value['lng'])  ){  ?>
					<div class="acf-map">
						<div class="marker" data-lat="<?php echo $value['lat']; ?>" data-lng="<?php echo $value['lng']; ?>"></div>
					</div>
					<?php

				}	else if ( $field_type == 'file'){
					$f = $field['value'];
					echo '<span><i class="fa fa-paperclip primary-color" aria-hidden="true"></i>&nbsp;<a class="text-color " href="'.$f['url'].'">'.$f['filename'].' </a></span>';
				} else {
					$f = $field['value'];

					if( $field_type == 'image' ){
						echo '<img src ="'.$f['url'].'">';
					}
				}
				echo '</div>';

			echo '</div>';
		}
	}

}
add_action( 'show_acf_fields','show_acf_fields', 10 , 1);

function acf_save_post_replace( $post_id = 0, $values = null ) {

	// override $_POST
	if( $values !== null ) {
		$_POST['acf'] = $values['acf'];
	}
	// set form data
	acf_set_form_data(array(
		'post_id'	=> $post_id
	));

	// hook for 3rd party customization
	do_action('acf/save_post', $post_id);


	// return
	return true;

}
function box_update_acf_fields( $project_id, $request ){

 	acf_save_post_replace( $project_id, $request );
}
add_action( 'update_acf_fields','box_update_acf_fields', 10 , 2);


function my_acf_init() {
	global $app_api;
	$gmap_key =  (string) $app_api->gmap_key;
	acf_update_setting('google_api_key', $gmap_key);
}

add_action('acf/init', 'my_acf_init');

function scrip_map_single(){
	if( ! is_singular(PROJECT) )
		return ;
	?>
<style type="text/css">

.acf-map {
	width: 100%;
	height: 400px;
	border: #ccc solid 1px;
	margin: 20px 0;
}

/* fixes potential theme css conflict */
.acf-map img {
   max-width: inherit !important;
}

</style>
<?php
global $app_api;
	$gmap_key =  (string) $app_api->gmap_key;
	?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmap_key;?>"></script>
<script type="text/javascript">
(function($) {

/*
*  new_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function new_map( $el ) {

	// var
	var $markers = $el.find('.marker');


	// vars
	var args = {
		zoom		: 16,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};


	// create map
	var map = new google.maps.Map( $el[0], args);


	// add a markers reference
	map.markers = [];


	// add markers
	$markers.each(function(){

    	add_marker( $(this), map );

	});


	// center map
	center_map( map );


	// return
	return map;

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {

	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map
	});

	// add to array
	map.markers.push( marker );

	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});

		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {

			infowindow.open( map, marker );

		});
	}

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
*/

function center_map( map ) {

	// vars
	var bounds = new google.maps.LatLngBounds();

	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){

		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

		bounds.extend( latlng );

	});

	// only 1 marker?
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 16 );
	}
	else
	{
		// fit to bounds
		map.fitBounds( bounds );
	}

}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/
// global var
var map = null;

$(document).ready(function(){

	$('.acf-map').each(function(){

		// create map
		map = new_map( $(this) );

	});

});

})(jQuery);
</script>
<?php }
add_action('wp_footer','scrip_map_single');