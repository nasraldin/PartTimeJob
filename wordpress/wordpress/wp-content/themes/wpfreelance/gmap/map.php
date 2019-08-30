<?php

function box_nearby_filter(){
	$request = $_REQUEST['request'];
	$result = get_list_freelancer_by_distance($request);
	$response = array('success' => true, 'data' => $result);
	wp_send_json( $response);
}
add_action( 'wp_ajax_nearby_filter','box_nearby_filter');
add_action('wp_ajax_nopriv_nearby_filter','box_nearby_filter');

function get_skill_html_profile($profile_id){
	$skills = get_the_terms( $profile_id, 'skill' );
	$skill_html = '';
	if ( $skills && ! is_wp_error( $skills ) ){

	  	$draught_links = array();

	  	foreach ( $skills as $term ) {
	    	//$draught_links[] = '<a href="'.get_term_link($term).'">'.$term->name.'</a>';
	    	$draught_links[] = '<span >'.$term->name.'</span>';
	     	$list_ids[] = $term->term_id;
	  }
	  $skill_html = join( ", ", $draught_links );
	}
	return $skill_html;
}



function get_list_freelancer_by_distance($request){
	$distance = $request['distance'];
	$skills = isset($request['skills']) ? $request['skills'] : '';
	$keywords = isset($request['keywords']) ? sanitize_text_field($request['keywords']) : '';
	$countries =isset($request['countries']) ? $request['countries'] : '';
	$center_lat = '51.507351';
	$center_lng = '-0.127758'; //London

	global $wpdb;
	$data = array();

	if( isset($request['lat_address']) ){
		$center_lat = $request['lat_address'];
		$center_lng = $request['long_address'];
	}
	$data['center_lat'] = $center_lat;
	$data['center_lng'] = $center_lng;

	$grouby= $where_skills = $where_countries = false;

	$sql = sprintf("SELECT p.*, ex.*, ( 3959 * acos( cos( radians('%s') ) * cos( radians( ex.lat_address ) ) * cos( radians( ex.long_address ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( ex.lat_address ) ) ) ) AS distance", $center_lat, $center_lng,  $center_lat );
	$grouby = true;
	$sql .= sprintf(" FROM $wpdb->posts p ");
	$sql.=" INNER JOIN  {$wpdb->prefix}profile_extra  ex on p.ID = ex.profile_id ";

	if( ! empty ( $skills) ) {
		$skills = join(",",$skills);

		$sql.= " LEFT JOIN $wpdb->term_relationships t ON p.ID = t.object_id ";
		$grouby = true;
		$where_skills = true;
	}
	if( ! empty ( $countries) ) {
		$countries = join(",",$countries);
		$sql.= " LEFT JOIN $wpdb->term_relationships ct ON p.ID = ct.object_id ";
		$grouby = true;
		$where_countries = true;
	}

	$where = " WHERE 1 = 1 AND p.post_type = 'profile' AND p.post_status = 'publish' ";

	if( ! empty( $keywords ) ){
		$where .= " AND  p.post_title LIKE '%".$keywords."%' OR  p.post_content LIKE '%".$keywords."%' OR  p.post_excerpt LIKE '%".$keywords."%' ";
		if($where_skills || $where_countries){
			if( $where_countries && $where_countries ){
				$where .= sprintf(" AND 	t.term_taxonomy_id IN (%s)  AND 	ct.term_taxonomy_id IN (%s)   ", $skills, $countries);
			} else if($where_countries){
				$where .= sprintf(" AND 	ct.term_taxonomy_id IN (%s) ", $countries);
			} else if($where_skills) {
				$where .= sprintf(" AND 	t.term_taxonomy_id IN (%s) ", $skills);
			}


		}
	} else {
		if( $where_countries && $where_countries ){
			$where .= sprintf(" AND 	t.term_taxonomy_id IN (%s)  AND 	ct.term_taxonomy_id IN (%s)  ", $skills, $countries);
		}  else if($where_countries){
			$where .= sprintf(" AND 	ct.term_taxonomy_id IN (%s) ", $countries);
		} else if($where_skills) {
			$where .= sprintf(" AND	t.term_taxonomy_id IN (%s) ", $skills);
		}
	}

	$sql .= $where;

	$sql.=" GROUP BY p.ID ";


	if( $distance > 0 ){
		$sql .=sprintf(" HAVING distance < %f ", $distance  );
	}

	$sql .= " ORDER BY  distance ";
	//echo $sql;
	$results = $wpdb->get_results($sql);

	$data['rows_txt'] = __( 'No freelancer found.','boxtheme');
	$data['msg'] = __('0 freelancers found.','boxtheme' );
	$data['posts'] = array();

	if( $results ){
		foreach ( $results as $post ){

			$marker = array();
			$profile_id = $post->ID;
			$profile 	= BX_Profile::get_instance()->convert($post);
			$data[$profile_id] = $marker;
			$profile->html_marker =   box_html_marker($profile);
			$data['posts'][] = $profile;

		}
		$data['rows_txt'] = sprintf(__(' %s freelancers found.','boxtheme'), count($results) );
		$data['msg'] = sprintf(__(' %s freelancers found.','boxtheme'), count($results) );

	}
	$data['slq'] = $sql;



	//echo $sql;

	return $data;
}
function get_sample_markers(){
	return array(
		308 => array('lat_address' => '-38.416097','long_address' => '-63.616672'), //argentina
		234 => array('lat_address' => '52.355518','long_address' => '-1.174320'), //England
		232 => array('lat_address' => '37.663998','long_address' => '127.978458'),//korea
		230 => array('lat_address' => '51.165691','long_address' => '10.451526'), //germany
		228 => array('lat_address' => '46.227638','long_address' => '2.213749'),//france
		226 => array('lat_address' => '36.204824','long_address' => '138.252924'), //japan
		209 => array('lat_address' => '51.507351','long_address' => '-0.127758'), //london
		206 => array('lat_address' => '-14.235004','long_address'=> '-51.925280'),//
		204 => array('lat_address' => '-22.906847','long_address' => '-43.172896'),//Brazil - Rop de janeiro
		202 => array('lat_address' => '-34.603684','long_address' => '-58.381559'), //argentina
		200 => array('lat_address' => '37.090240','long_address' => '-95.712891'), //usa
		199 => array('lat_address' => '53.480759','long_address' => '-2.242631'), //machester
		135 => array('lat_address' => '56.130366','long_address' => '-106.346771'), //canada

		67 => array('lat_address' => '-48.270148','long_address' => '-68.225799'), //argentio
		65 => array('lat_address' => '47.113870','long_address' => '-1.512423'), //nante -france
		61 => array('lat_address' => '-34.607044', 'long_address'=>'-60.593863'), //
		63 => array('lat_address' => '-17.979815','long_address' => '-62.462554'), //Bolivia

		58 => array('lat_address' => '-48.824350','long_address' => '-68.075053'), //Argentina
		56 => array('lat_address' => '51.644772','long_address' => '-1.185306'), //Oxford England

		54 => array('lat_address' => '48.135125','long_address' => '11.581980'), //gemany munich

		52 => array('lat_address' => '49.281513','long_address' => '3.466190'), //France

		50 => array('lat_address' => '-22.705755','long_address' => '-47.327906'), //decarr-france

		48 => array('lat_address' => '44.071110','long_address' => '2.235722'), //France
		46 => array('lat_address' => '52.203307','long_address' => '0.134415'), //England

	);
}
function map_remove_all_sample(){
	global $wpdb;
	$sql  ="DELETE  FROM {$wpdb->prefix}profile_extra";
	$wpdb->query($sql);
	update_option('is_inserted_markers','0');
}
function insert_markers_sample(){
	$markers = get_sample_markers();
	global $wpdb;
	$check = get_option('is_inserted_markers', true);

	if( $check !== '5'){
		foreach ($markers as $key => $marker) {
			$sql = sprintf("INSERT INTO {$wpdb->prefix}profile_extra (`extra_id`, `profile_id`, `lat_address`, `long_address`) VALUES (NULL, '%s', '%s', '%s')",$key, $marker['lat_address'],$marker['long_address'] );
			$wpdb->query($sql);
		}
		update_option('is_inserted_markers','5');
	}


}
function creata_extra_table(){

		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$tables = "	CREATE TABLE {$wpdb->prefix}profile_extra (
		  	extra_id bigint(20) NOT NULL AUTO_INCREMENT,
		  	profile_id bigint(20) NOT NULL,
		  	-- skills  longtext NOT NULL,
		  	-- categories  longtext NOT NULL,
		  	lat_address  float  NOT NULL,
		  	long_address  float NOT NULL,
		  	PRIMARY KEY  (extra_id),
		 	UNIQUE KEY extra_id (extra_id)
		) $collate";

		$is_added = get_option('is_added_extra_table', true);


		if(  $is_added !== '5'){
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$insert = dbDelta( $tables );
			if( $insert ){
				update_option( 'is_added_extra_table', '5');
			}
		}
}

function box_create_table_extra(){
	//drop_profile_extra_table();
	creata_extra_table();
	//change_column_name();
}
function drop_profile_extra_table($table_name = 'profile_extra'){

    global $wpdb;
    $table_name_prepared = $wpdb->prefix . $table_name;
    $the_removal_query = "DROP TABLE IF EXISTS {$table_name_prepared}";

    $wpdb->query( $the_removal_query );
    update_option('is_inserted_markers', 0 );
    update_option('is_added_extra_table', 0 );


}
function change_column_name(){
	global $wpdb;
 	//$sql = "ALTER TABLE {$wpdb->prefix}profile_extra RENAME COLUMN lat_geo to lat_address";
 	$sql1 = "ALTER TABLE {$wpdb->prefix}profile_extra   CHANGE COLUMN lat_geo lat_address   float (10,7)";
 	$sql2 = "ALTER TABLE {$wpdb->prefix}profile_extra   CHANGE COLUMN lng_geo long_address   float (10,7)";

 	//$sql2 = "ALTER TABLE {$wpdb->prefix}profile_extra RENAME COLUMN 'lng_geo' TO 'long_address' ";
 	$wpdb->query($sql1);
 	$wpdb->query($sql2);
}
add_action('after_setup_theme','box_create_table_extra');

function update_geo_location($profile_id, $lat_address, $long_address){

	global $wpdb;
	$profile = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}profile_extra WHERE profile_id = ".$profile_id );
	$sql= '';

	if( ! $profile ){
		//$sql =      sprintf("INSERT INTO {$wpdb->prefix}profile_extra (`extra_id`, `profile_id`, `lat_address`, `long_address`) VALUES ( NULL, '%s', '%s', '%s')",$profile_id, $lat_address,$long_address );
		$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}profile_extra (`extra_id`, `profile_id`, `lat_address`, `long_address`) VALUES ( NULL, '%s', '%s', '%s')",  $profile_id, $lat_address, $long_address) ;

	} else {

		$sql  = $wpdb->prepare("UPDATE `{$wpdb->prefix}profile_extra` SET `lat_address` = '%s', `long_address` = '%s' WHERE `{$wpdb->prefix}profile_extra`.`extra_id` = %d", $lat_address, $long_address, $profile->extra_id);;

	}


	$result = $wpdb->query( $sql);

}
function box_get_geo_info($profile_id){
	global $wpdb;
	return  $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}profile_extra WHERE profile_id = ".$profile_id );

}
function box_html_marker($profile){
	return '<div class="user-marker"><div class="marker-avatar half-left">'.get_avatar($profile->post_author).'</div><div class="half-right half"><h3 class="profile-title"><a href="'.get_permalink($profile->ID).'">'.$profile->profile_name.'</a></h3><span class="professional-title item professional-title primary-color">'.$profile->professional_title.'</span><div class="full mk-skils">'.$profile->skill_text.'</div></div>';
}