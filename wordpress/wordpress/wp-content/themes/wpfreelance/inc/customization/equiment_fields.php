<?php

function ove_box_convert_profile($profile){
	//$porfile->equipment = get_post_meta($porfile->ID,'equipment', true);
	//$profile->professional_title = $profile->post_excerpt;
	$post_excerpt = $profile->post_excerpt;
	$profile->equipment = '';
	$part = explode("[____]", $post_excerpt);

	if( isset($part[1]) ){
		$profile->professional_title = $part[0];
	 	$profile->equipment = $part[1];
	}

	return $profile;

}
add_filter('box_convert_profile', 'ove_box_convert_profile', 99);
/**
 * use in author page.
*/
function box_show_equipment($profile){
	$equipment= $profile->equipment;
	if(! empty($equipment) ){
		echo '<div class="full" style="padding:15px 0;">';
		echo '<h3> Equipment</h3>';
		echo nl2br($equipment);
		echo '</div>';
	}

}
function box_edit_equiment_field($profile){
	?>
	<div class="form-group ">
    	<div class="static visible-default equipment-field-edit"> <?php if( !empty($profile->equipment) ){echo $profile->equipment; } ?></div>
    	<textarea class="update hide form-control" name="equipment" cols="50" rows="6" placeholder="<?php _e("List equipment here","boxtheme");?>"><?php  echo $profile->equipment; ?></textarea>
    </div>
    <?php
}
function cs_save_equiment_fields($profile_id){
	$request = $_POST['request'];
	$equipment = isset($request['equipment']) ? $request['equipment'] : '';
	if($equipment){
		//update_post_meta($profile_id,'equipment', $equiment);
		//$post = get_post($profile_id);
		$professional_title = $request['post_excerpt'];

		$temp = $professional_title .= '[____]'. $equipment;

		$t= wp_update_post( array('ID' => $profile_id,'post_excerpt' => $temp) );

	}

}
add_action('after_update_profile','cs_save_equiment_fields',9999999);
?>