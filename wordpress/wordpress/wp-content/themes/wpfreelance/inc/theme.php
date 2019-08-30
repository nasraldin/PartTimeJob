<?php

if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Enqueue scripts and styles.
 */
function box_log($input, $file_store = ''){
	$file_store = LOG_FILE;
	//WP_CONTENT_DIR.'/ipn.log');
	if( is_array($input) || is_object( $input ) ){
		error_log( print_r($input, TRUE), 3, $file_store );
	}else {
		error_log($input . "\n" , 3, $file_store);
	}
}
function box_log_track($input, $file_store = ''){

	$file_store = WP_CONTENT_DIR.'/debug.log';
	//WP_CONTENT_DIR.'/ipn.log');
	if( is_array($input) || is_object( $input ) ){
		error_log( print_r($input, TRUE), 3, $file_store );
	}else {
		error_log($input . "\n" , 3, $file_store);
	}
}
function box_track1($contents, $file=false){
	$file = TRACK_PATH;
	//if(!$file)	$file = dirname(__FILE__).'/bplog.txt';
	file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

	if (is_array($contents))
		$contents = var_export($contents, true);
	else if (is_object($contents))
		$contents = json_encode($contents);

	file_put_contents($file, $contents."\n", FILE_APPEND);
}
function box_track($input, $file_store = ''){
	$file_store = TRACK_PATH;

	if( is_array($input) || is_object( $input ) ){
		error_log( print_r($input, TRUE), 3, $file_store );
	}else {
		error_log($input . "\n" , 3, $file_store);
	}
}

function box_header_link_in_dashboard(){
	$dashboard_link = box_get_static_link('dashboard');

	?>
<div class="full-width dashboard-nav">
	<div class="container">
		<div class="row">
			<div class="col-md-10">
				<ul class="inline">
					<li><a href="<?php echo box_get_static_link('dashboard');?>"><?php _e('Dashboard','boxtheme');?> </a></li>
					<li><a href="<?php echo box_get_static_link('my-project');?>"><?php _e('My Projects','boxtheme');?></a></li>
					<li><a href="<?php echo box_get_static_link('my-bid');;?>"><?php _e('My Bidding','boxtheme');?></a></li>
					<li><a href="<?php echo box_get_static_link('inbox');?>"><?php _e('Inbox','boxtheme');?> </a></li>
				</ul>
			</div>
			<div class="col-md-2 wrap-highlight">
				<a class="link-post-project btn-highlight" href="<?php echo box_get_static_link('post-project');?>"><?php _e('Post a Project','boxtheme');?></a>
			</div>
		</div>
	</div>
</div>
<?php }
function bx_signon($info){
	$creds 		= array();
	if(isset($info['user_pass']))
		$info['user_password'] = $info['user_pass'];

	$creds['user_login'] 	= $info['user_login'];
	$creds['user_password'] = $info['user_password'];

	$creds['remember'] 		= true;

	$response 	= array( 'success' => false, 'msg' => __('Login fail','boxtheme') );

	$user = wp_signon( $creds, false );

	if ( ! is_wp_error( $user ) ){
		$response 	= array( 'success' => true, 'msg' => __('You have logged succesful','boxtheme') );
	} else  {
		$type_error = $user->get_error_codes()[0];
		//invalid_username,empty_username,empty_password,incorrect_password
		if ( in_array($type_error, array('empty_username') ) ){
			$msg = __('The username field is empty', 'boxtheme');
		} else	if ( in_array($type_error, array('empty_password') ) ){
			$msg = __('The password field is empty', 'boxtheme');
		} else if ( in_array( $type_error, array('invalid_username') ) ){
			$msg = __('Invalid username', 'boxtheme');
		}else if ( in_array($type_error, array('incorrect_password') ) ){
			$msg = sprintf(__('The password you entered for the username %s is incorrect', 'boxtheme'), $info['user_login']);
		} else {
			$msg = strip_tags($user->get_error_message());
		}
		$response['msg'] 			= $msg;

		$response['success'] 		= false;
    }
    if( !empty( $info['redirect_url'] ) )
    	$response['redirect_url'] 	= $info['redirect_url'];
	return $response;
}
if( ! function_exists('bx_get_verify_key')):
	/**
	 * clone of the method get_password_reset_key
	 * check update then the get_password_reset_key update.
	*/
	function bx_get_verify_key( $username ) {
		global $wpdb, $wp_hasher;

		// Generate something random for a password reset key.
		$key = wp_generate_password( 20, false );

		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once( ABSPATH . WPINC . '/class-phpass.php');
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$key_saved = $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $username ) );
		if ( false === $key_saved ) {
			return new WP_Error( 'no_password_key_update', __( 'Could not save password reset key to database.','boxtheme' ) );
		}

		return $key;
	}
endif;

function box_tax_dropdown($term, $placehoder = '', $multiple = 'multiple', $required = '' ){
	?>
	<select class="form-control required chosen-select" name="skill" <?php echo $required;?>  <?php echo $multiple;?> data-placeholder="<?php echo $placehoder;?>">
       	<?php
       	$skills = get_terms(
       		array(
           		'taxonomy' =>$term,
           		'hide_empty' => false,
          	)
       	);
       if ( ! empty( $skills ) && ! is_wp_error( $skills ) ) {
            foreach ( $skills as $skill ) {

              	echo '<option  value="' . $skill->term_id . '">' . $skill->name . '</option>';
            }
        }
       ?>
	  </select>
	<?php
}
if( !function_exists('list_dealine') ) :
	function list_dealine(){
		$list = array(
		 	__('Less than 1 week','boxtheme'),
		 	__('Less than 1 month','boxtheme'),
		 	__('1 to 3 months','boxtheme'),
		 	__('3 to 6 months','boxtheme'),
		 	__('More than 6 months','boxtheme'),
		);
		return $list;
	}
endif;
/**
 * check current user click the link confirm in the email.
 * this action of register - don't relates to admin
*/
function is_account_confirmed($user_ID){ //is_verified

	return apply_filters( 'is_account_confirmed', BX_User::get_instance()->is_confirmed($user_ID) );
}
function box_auto_gernerat_profile($user_data){
	global $wpdb;
	if ( is_numeric( $user_data ) ) {
		$user_data = get_userdata($user_data);
	}
	$user_login = $user_data->user_login;
	$wpdb->update( $wpdb->users, array( 'user_status' => 1 ), array( 'user_login' => $user_login ) );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => '' ), array( 'user_login' => $user_login ) );

	box_create_a_profile($user_data);


}
if( ! function_exists('box_do_confirm_account') ):
	function box_do_confirm_account($user_data){ //is_confirmed
		global $wpdb;
		if ( is_numeric( $user_data ) ) {
			$user_data = get_userdata($user_data);
		}
		$user_login = $user_data->user_login;
		$wpdb->update( $wpdb->users, array( 'user_status' => 1 ), array( 'user_login' => $user_login ) );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => '' ), array( 'user_login' => $user_login ) );

		box_create_a_profile($user_data);
	}
endif;
function box_add_free_credit($user_data){
	// add default credit for new acccount.
	$number_free_credit = (int) BX_Option::get_instance()->get_group_option('opt_credit')->number_free_credit;
	if( $number_free_credit > 0 ){
		update_user_meta($user_data->ID, 'user_free_credit', $number_free_credit);
		//BX_Credit::get_instance()->increase_credit_available( $number_free_credit );
	}
	// add done.
}
function progress_after_confirm_success($user_data){
	box_create_a_profile($user_data);
	Box_ActMail::get_instance()->confirm_success( $user_data);
	do_action('box_process_after_confirm', $user_data);
}
function box_requires_confirm(){
	global $box_general;

	return ( $box_general->requires_confirm == '0' ) ? false : true;
}
function box_create_a_profile($user_data){
	box_add_free_credit($user_data);
	$role =  bx_get_user_role($user_data->ID);
	$profile_id = get_user_meta( $user_data->ID, 'profile_id', true);
	$profile_status = 'publish';
	if( in_array( $role, array(EMPLOYER,'administrator') ) || current_user_can('manage_options') )
		$profile_status = 'inactive';

	$profile_status = apply_filters('set_profile_status', $profile_status);
	$user_id = $user_data->ID;
	if( empty( $profile_id ) || ! $profile_id ){
		$args = array();
		// update lat ang long here.


		if(   box_requires_confirm() ) {
			$address = get_user_meta( $user_id, 'address', true);
			$lat_address = get_user_meta( $user_id, 'lat_address', true);
			$long_address = get_user_meta( $user_id, 'long_address', true);

		} else {
			$request = $_POST['request'];
			$address = isset($request['address']) ? $request['address'] :'';
			$lat_address = isset($request['lat_address']) ? $request['lat_address'] :'';
			$long_address = isset($request['long_address']) ? $request['long_address'] :'';
		}

		$metas	= array(
			HOUR_RATE => 0,
			RATING_SCORE => 0,
			'address' => $address,
			'lat_address' => $lat_address,
			'long_address' => $long_address,

		);
		if( BOX_VERIFICATION ){
			$metas['is_reviewed'] = 0;
		}

		$args = array(
			'post_title' 	=> $user_data->first_name . ' '.$user_data->last_name ,
			'post_type'  	=> PROFILE,
			'post_author' 	=> $user_data->ID,
			'post_status' 	=> $profile_status,
			'meta_input'	=> $metas,
		);
		$profile_id = wp_insert_post($args);
	}


	if( $profile_id ){
		update_user_meta( $user_data->ID, 'profile_id', $profile_id );
		update_geo_location($profile_id, $lat_address, $long_address); //$profile_id, $lat_address, $long_address

		$country = get_user_meta($user_id,'country', true);
		$term = get_term_by('name',$country, 'country');

		if( $term && !is_wp_error( $term ) ) {
			$term_id = (int) $term->term_id;
			$terms = array($term_id);
			wp_set_post_terms( $profile_id, $terms, 'country' );
		}
	}

}
function box_create_a_profile_later($user_data){

	$profile_status = 'publish';
	$role = bx_get_user_role($user_data);

	if( in_array( $role, array( EMPLOYER,'administrator' ) ) || current_user_can('manage_options') )
		$profile_status = 'inactive';


		// update lat ang long here.
	$user_id = $user_data->ID;
	$address = get_user_meta( $user_id, 'address', true);
	$lat_address = get_user_meta( $user_id, 'lat_address', true);
	$long_address = get_user_meta( $user_id, 'long_address', true);
	$metas	= array(
		HOUR_RATE => 0,
		RATING_SCORE => 0,
		'address' => $address,
		'lat_address' => $lat_address,
		'long_address' => $long_address,

	);
	if( BOX_VERIFICATION ){
		$metas['is_reviewed'] = 0;
	}

	$args = array(
		'post_title' 	=> $user_data->user_login ,
		'post_type'  	=> PROFILE,
		'post_author' 	=> $user_data->ID,
		'post_status' 	=> $profile_status,
		'meta_input'	=> $metas,
	);

	$profile_id = wp_insert_post($args);


	if( $profile_id ){
		update_user_meta( $user_data->ID, 'profile_id', $profile_id );
		update_geo_location($profile_id, $lat_address, $long_address); //$profile_id, $lat_address, $long_address

		$country = get_user_meta($user_id,'country', true);
		$term = get_term_by('name',$country, 'country');

		if( $term && !is_wp_error( $term ) ) {
			$term_id = (int) $term->term_id;
			$terms = array($term_id);
			wp_set_post_terms( $profile_id, $terms, 'country' );
		}
	}
	return $profile_id;

}

function is_owner_project( $project ) {
	global $user_ID;
	if( ! $user_ID )
		return false;
	if( $project->post_author == $user_ID ){
		return true;
	}
	return false;
}
function can_access_workspace($project){
	global $user_ID;

	if( is_owner_project($project) ){
		return true;
	}
	$winner_id = $project->{WINNER_ID};
	if( in_array( $user_ID, array($winner_id, $project->post_author) )) {
		return true;
	}
	if( current_user_can('manage_options') ){
		return true;
	}
	return false;
}

function current_user_can_bid($project){
	return BX_Bid::get_instance()->is_can_bid($project);
}
function is_current_user_bidded($project_id){
	$project_id = (int)$project_id;
	return BX_bid::get_instance()->has_bid_on_project($project_id);
}
function box_has_bid_on_project($project_id, $user_id){
	$project_id = (int)$project_id;
	return BX_bid::get_instance()->has_bid_on_project($project_id, $user_id);
}
function countr_of_user($profile){
}

/**
 *
 * This is a cool function
 * @author boxtheme
 * @version 1.0
 * @param   int $user_id who will receive this rating.
 * @param   string $type    emp_review ==> author of this review is employer.
 * @return  [type]          [description]
 */
function count_rating($user_id,$type ='emp_review'){
	global $wpdb;


	return $wpdb->get_var(
				$wpdb->prepare(
					"
					SELECT ROUND(ROUND( AVG(mt.meta_value) * 2 ) / 2,1)
					FROM $wpdb->comments cm
						INNER JOIN $wpdb->commentmeta mt
							ON cm.comment_ID = mt.comment_id
								AND cm.user_id = %s
								AND cm.comment_type = '%s'
								AND mt.meta_key = '%s'
								AND mt.meta_value > 0
					",
					$user_id, $type, RATING_SCORE
				)
	);
}
/**
 * [get_conversation_id_of_user count number message unread of a usder

 * @author boxtheme
 * @version 1.0
 * @param   integer $user_ID ID of user ID
 * @return  integer number message unread
 */

function count_bids($project_id){
	global $wpdb;
	return $wpdb->get_var( " SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'bid' AND post_parent= {$project_id}" );
}
/**
 * check current view of user is employer or freelancer - 2 values only.
*/
function get_role_active(){
	global $user_ID;

	if( $user_ID ){
		$role = get_user_meta($user_ID, 'role_activate', true);

		if( ! empty($role) )
			return $role;
	}

	$role = bx_get_user_role();

	if($role == 'administrator')
		$role = EMPLOYER;

	return $role;
}

function box_switch_role_button(){

	$role_active = get_role_active();

	if($role_active == FREELANCER){
		echo '<input type="checkbox" class="auto-save" data-on="View as Freelancer" data-off="View as Employer" data-onstyle="success" checked data-toggle="toggle" data-offstyle="danger">';
	} else {
		echo '<input type="checkbox" class="auto-save" data-on="View as Employer" data-off="View as Freelancer" data-onstyle="success" checked data-toggle="toggle" data-offstyle="danger">';
	}
	?>
	<!-- <input type="checkbox" checked data-toggle="toggle" data-on="Freelancer" data-off="Employer" data-onstyle="success" data-offstyle="danger" data-toggle="toggle"> !-->

	<?php

}


if ( ! function_exists('box_account_dropdow_menu') ) {
	function box_account_dropdow_menu(){

		global $role; global $user_ID, $wpdb;
		$current_user = wp_get_current_user();
		$messages = $notifies = array();

		//$number_new_notify = (int) get_user_meta( $user_ID, 'number_new_notify', true ); //has_new_notify
		$sql = $wpdb->prepare(
				"	SELECT msg.*, count( case msg.msg_unread = '1' when 1 then 1 else null end) as count_unread
					FROM {$wpdb->prefix}box_messages msg
					WHERE msg.receiver_id = %d AND( msg.msg_type ='notify' OR (msg.msg_type = 'message' ) )
					group by (case when msg.msg_type='message' then msg.cvs_id end) DESC
					ORDER BY msg.msg_date  DESC LIMIT 8 ",
					$user_ID
			);

		$list_noti  = $wpdb->get_results($sql);
		global $count_unread;
		$count_unread = 0;
		if( $list_noti){

			foreach ( $list_noti as $noti ) {
				$notifies[] = $noti;
				if( $noti->count_unread  ){
					$count_unread = $noti->count_unread + $count_unread;
				}
			}
		}

		$msg_class= 'empty-msg';
		if( $count_unread > 0 )
			$msg_class = "has-msg-unread";	?>
		<ul class="account-dropdown">
			<li class="profile-account dropdown">

				<a rel="nofollow" class="dropdown-toggle account-name" data-toggle="dropdown" href="#"><div class="head-avatar"><?php echo get_avatar($user_ID);?></div><span class="username"><?php echo $current_user->display_name;?></span> <span class="caret"></span>
				<span class="hide <?php echo $msg_class;?>"><?php echo $count_unread;?></span>
				</a>
				<ul class="dropdown-menu account-link" >
					<li class="toggleRoleViewer">
						<?php   box_switch_role_button();?>
					</li>
					<li> <i class="fa fa-th-list" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('dashboard');?>"><?php _e('Dashboard','boxtheme');?></a></li>
					<?php global $escrow; if($escrow->active == 'credit' || !isset( $escrow->active ) ){?>
					<li><i class="fa fa-credit-card" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('my-credit');?>"><?php _e('My Credit','boxtheme');?></a></li>				<?php } ?>
					<li> <i class="fa fa-user-circle-o" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('my-profile');?>"><?php _e('My Profile','boxtheme');?></a></li>
					<li> <i class="fa fa-sign-out" aria-hidden="true"></i>  <a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e('Logout','boxtheme');?></a></li>
				</ul>
			</li>
			<li class="icon-bell first-sub no-padding-left pull-left" id="toggle-msg">
				<div class="dropdown">
				  	<span class="dropdown-toggle <?php if ( $count_unread)  echo 'toggle-msg';?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell  " aria-hidden="true"></i></span> <?php
				  	echo '<ul class=" dropdown-menu ul-notification">';
				  	$unread = 0;
					if( !empty ( $notifies) ) {

						foreach ($notifies as $noti) {
							$class ="noti-read";
							if( $noti->msg_unread == 1) { $unread ++; $class="noti-unread"; }
							$date = date_create( $noti->msg_date );
							$date = date_format($date,"m/d/Y");

							Box_Notify::get_instance()->render_noti_item($noti);


						}

					} else { ?>
						<p class="empty-noty"><?php _e('There is no new notification','boxtheme');?></p>
					<?php }
					echo '</ul>';
					$class = 'notify-number ';
					$number_unread = '';
					if( $count_unread > 0 ){
						$class .= 'notify-acti';
						$number_unread = $count_unread;
					}
					echo '<span class="'.$class.'">'.$count_unread.'</span>'; ?>

				</div>
			</li>
		</ul>
	<?php }
}

if( ! function_exists( 'box_social_link' ) ){
	function box_social_link( $general ){ ?>
		<ul class="social-link">
    		<?php
    		if ( !empty( $general->gg_link ) )
    			echo '<li><a class="gg-link"  target="_blank" href="'.esc_url($general->gg_link).'"><span></span></a></li>';
    		if ( !empty( $general->tw_link ) )
    			echo '<li><a class="tw-link" target="_blank"  href="'.esc_url($general->tw_link).'"><span></span></a></li>';
    		if ( !empty( $general->fb_link ) )
    			echo '<li><a class="fb-link"  target="_blank" href="'.esc_url($general->fb_link).'"><span></span></a></li>'; ?>
    	</ul> <?php
	}
}
function heading_project_info($project, $is_workspace){ ?>
	<div class="full heading">
		<div class ="col-md-2 no-padding-right"><?php printf(__('Status: %s','boxtheme'),$project->post_status); ?></div>
      	<div class="col-md-3"><?php printf(__('Post date: %s','boxtheme'),get_the_date() );?></div>
      	<div class="col-md-3"><?php printf(__("Fixed Price: %s",'boxtheme'),box_get_price_format($project->_budget) ); ?> </div>
      	<div class="col-md-4"> 	<?php
      	global $can_access_workspace;

      	if( $project->post_status != 'publish' && $can_access_workspace ) {
      		step_process($is_workspace);
      	} else {
      		box_social_share();
      	} ?>
      	</div>

	</div> <!-- full !-->
	<?php
}
function step_process( $is_workspace ){
		global $project, $can_access_workspace, $winner_id, $is_dispute;
		$class = $detail_section = $dispute_section = '';
		if( $is_workspace ){
			$class ='current-section';
		} else if( $is_dispute) {
			$dispute_section = 'current-section';
		} else {
			$detail_section = 'current-section';
		}

		if( $can_access_workspace && in_array( $project->post_status, array('awarded','complete','dispute','finish','disputing', 'disputed','archived') ) ) { ?>
	    	<ul class="job-process-heading">
	    		<?php if( $is_workspace){ ?>
					<li class="<?php echo $detail_section;?>"><a href="<?php echo get_permalink();?>"> <span class="glyphicon glyphicon-list"></span> <?php _e('Job Detail','boxtheme');?></a></li>
				<?php } ?>
				<li class=" text-center <?php echo $class;?>"><a href="?workspace=1"> <span class="glyphicon glyphicon-saved"></span> <?php _e('Workspace','boxtheme');?></a>	</li>
				<li class="text-right <?php echo $dispute_section;?>"><a href="?dispute=1"> <span class="glyphicon glyphicon-saved"></span> <?php _e('Dispute','boxtheme');?></a>	</li>
	    	</ul> <?php
	    }
	}
	function box_project_status($status){
		$args = array(
			'publish' => __('Open','boxtheme'),
			'pending' => __('Pending','boxtheme'),
			'draft' => __('Draft','boxtheme'),
			'awarded' => __('Working','boxtheme'),
			'complete' => __('Complete','boxtheme'),
			'archived' => __('Archived','boxtheme'),
			'disputing' => __('Disputing','boxtheme'),
			'resolved' => __('Resolved','boxtheme'),
			'inherit' => '',

		);
		if( isset($args[$status]) )
			return $args[$status];
		return '';
	}
	/**
	 * check role of current user can view bid info or not.
	 * Default: bid author, owner project or admin can see this.
	 *@return boolean true or false
	*/
	function user_can_see_bid_info( $bid, $project, $user_id = 0 ) {
		if( ! $user_id ){
			global $user_ID;
			$user_id = $user_ID;
		}
		if ( $bid->post_author == $user_id || $project->post_author == $user_ID || is_current_box_administrator() ){
			return true;
		}
		return false;

	}
	function box_list_categories( $args = array( 'style' => 1, 'hide_empty' => 0) ){
		$fargs = wp_parse_args( $args, array('style' => 1,'hide_empty' => 0) );
		$terms = get_terms( 'project_cat', array(
	    	'orderby'    => 'name',
	    	'hide_empty' => $fargs['hide_empty'],
			)
		);
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			if($args['style'] == 1){
				echo '<ul class="none-style list-cats list-cats-1">';
				foreach ($terms as $key => $term) {
					$term_link = get_term_link( $term );
					echo '<li class="col-md-3"><a class="primary-color" href="'. esc_url( $term_link ) .'">'.$term->name.'('.$term->count.')</a></li>';							}
				echo '</ul>';
			} else if($args['style'] == 2) {

				$alphas=range("A","Z");
				$text = array();
				foreach($alphas as $char){
					$text[$char] = array();
				}

			    foreach ( $terms as $term ) {
			        $first_letter = strtoupper(substr( trim($term->name) , 0, 1) );
			        if( isset($text[$first_letter]) )
			        	array_push($text[$first_letter], $term);
			    }

			    foreach ($text as $key => $terms) {

					if( !empty($terms) ){
						echo '<div class="col-md-3"><ul class="none-style list-cats list-cat-2"><li class="cat-label"><label class="h5">'.$key.'</label></li>';
						foreach ($terms as $term) {
							$term_link = get_term_link( $term );
							echo '<li><a class="primary-color" href="'. esc_url( $term_link ) .'">'. $term->name .'</a></li>';
						}
						echo '</ul></div>';
					}
				}
			}

		} else {
			_e('List categories is empty','boxtheme');
		}
	}
function box_map_autocomplete_script(){ ?>
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
        console.log('fillInAddress in theme.php line 674');
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();
        document.getElementById('lat_address').value = lat;
        document.getElementById('long_address').value = lng;

        window.search_args.lat_address =lat;
        window.search_args.long_address =lng;
        console.log('window.search_args');
        console.log(window.search_args);
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
      function success(position){
      	console.log('geolocate success - theme.php');
      	var lat = lng = '';
        var geolocation = {
              	lat: position.coords.latitude,
              	lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
            window.search_args.lat_address =lat;
       		window.search_args.long_address =lng;
      }
      function map_error(err) {
        console.warn(`ERROR(${err.code}): ${err.message}`);

      }

      function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success,map_error);

        }
      }

    </script>
    <?php }

    function box_map_field_auto(){?>
    <input type="hidden" name="lat_address" id="lat_address">
    <input type="hidden" name="long_address" id="long_address">
    <input type="hidden" name="short_name" id="short_name">
    <input type="hidden" name="long_name" id="long_name">
    <input type="hidden" name="locality" id="locality">
    <input type="hidden" name="route" id="route">
    <input type="hidden" name="postal_code" id="postal_code">

    <input type="hidden" name="street_number" id="street_number">
    <input type="hidden" name="administrative_area_level_1" id="administrative_area_level_1">
    <input type="hidden" name="country" id="country">
  <?php }

if( ! function_exists('box_get_number_free_bid_in_a_month')){
	function box_get_number_free_bid_in_a_month(){
		global $box_general;
		return (int) $box_general->number_bid_free;
	}
}

function box_get_number_bidded_this_moth($user_id = 0){
	if($user_id == 0){
		global $user_ID;
		$user_id = $user_ID;
	}
	$month = box_get_current_month();
	$option_name = "number_bidded_of_".$month;
	$number_bidded =  (int)get_user_meta($user_id, $option_name, true);
	return $number_bidded;
}
function box_update_number_bidded_this_moth($user_id = 0){
	if($user_id == 0){
		global $user_ID;
		$user_id = $user_ID;
	}
	$month = box_get_current_month();
	$option_name = "number_bidded_of_".$month;
	$number_bidded =  (int)get_user_meta($user_id, $option_name, true);
	$new_number = $number_bidded + 1;
	update_user_meta($user_id,$option_name, $new_number);
}
function submit_project_localize(){
	return apply_filters('localize_submit_form', array(
		'required' => box_get_required_fields(),
		'messages' => array(
			'required' => __('This field is required.','boxtheme'),
			'_budget' => __('Budget field is required.','boxtheme'),
			'post_title' => __('Project title field is required.','boxtheme'),
			'skill' => __('Please select a skill.','boxtheme'),
			'project_cat' => __('Please select a category.','boxtheme'),

		),
	));
}
function box_get_required_fields(){
	return apply_filters('project_required_fields', array('post_title','_budget','skill','post_content'));
}
function box_admin_act_buttons($profile){
	global $profile;
	$profile_id = $profile->ID;
	$status = $profile->post_status;

	if ( is_current_box_administrator() ){ ?>
		<div class="row">
			<div class="bg-section float-right admin-act">
				<?php

				if($status == 'inactive'){ ?>
				<p class="text-right"><?php _e('This account is inactive','boxtheme');?><button class="toggleActiv btn activate float-right" value="<?php echo $profile_id;?>"><i class="fa fa-check"></i> <?php _e('Activate this account ?','boxtheme');?> </button> </p>
				<?php } else if( $status == 'publish' ){
					if( BOX_VERIFICATION ){
						$is_reviewed = get_post_meta( $profile_id, 'is_reviewed', true );

						//$is_admin_reviewed empt => default. 0 => not review. 1=> review.
						if( ! $is_reviewed ){ ?>
							<button  rel="review" class="btn togleReview   float-right" value="<?php echo $profile_id;?>"> <i class="fa fa-eye-slash"></i> <?php _e('Mark as verified ?','boxtheme');?></button>
							<?php
						} else { ?>
							<button rel ="unreview" class="btn togleReview approved float-right" value="<?php echo $profile_id;?>"> <i class="fa fa-eye-slash"></i> <?php _e('Mark as unverified ?','boxtheme');?></button>
							<?php
						}
					}

					?>
				<p class="text-right"><?php _e('This account is active','boxtheme');?> <button class="toggleActiv btn deactivate float-right" value="<?php echo $profile_id;?>"> <i class="fa fa-eye-slash"></i> <?php _e('Deactivate this account?','boxtheme');?></button>
					<?php

				} ?>
				</p>
			</div>
		</div>
		<?php
	}
}
if( ! function_exists('do_after_verify_account') ):
	function do_after_verify_account($profile_id){

	}
endif;

if( ! function_exists('do_after_unverify_account')):
	function do_after_unverify_account($profile_id){

	}
endif;

function box_bid_buton($post){

	$apply_link = 	add_query_arg( 'apply','1' ,  get_the_permalink($post->ID) );

	$back_url = add_query_arg(  'redirect', $apply_link , box_get_static_link('login') );
	if( is_user_logged_in() ){
		echo '<a href="?apply=1#bid_form" class ="btn-bid-now pull-right" > &nbsp; ' . __('Bid Now','boxtheme') . ' &nbsp; &nbsp;  <i class="fa fa-angle-right" aria-hidden="true"></i> </a>';
	} else {
		echo '<a class ="btn-login text-right pull-right" href ="'.esc_url($back_url).'"> &nbsp; '.__('Bid Now','boxtheme') . ' &nbsp; &nbsp;  <i class="fa fa-angle-right" aria-hidden="true"></i> </a>';
	}
}

function box_post_project_button(){ ?>
	<a href="<?php echo box_get_static_link('post-project'); ?>" class ="btn-bid-now " > &nbsp;  <?php  _e('Post new job','boxtheme');?> &nbsp; &nbsp;  <i class="fa fa-angle-right" aria-hidden="true"></i> </a>
<?php }
function show_sidebar_project_buttons($project, $bidding){
	global $user_ID;
	$role_active = get_role_active();

	if( $role_active == EMPLOYER ) {
		echo '<li>';
			box_post_project_button();
		echo '</li>';

	} else if($role_active == FREELANCER ){
		if( $user_ID != $project->post_author && !$bidding && $project->post_status == 'publish' ){
			echo '<li>';
			box_bid_buton($project);
			echo '</li>';
		}
		//box_post_project_button();
	}


}
function box_allow_directly_message(){
	global $box_general;
	return (int) $box_general->direct_message;
}
function has_directly_message($receiver_id){
	$receiver_id = absint($receiver_id);
	global $wpdb, $user_ID;

	$c_id = $wpdb->get_row( "SELECT c.ID FROM {$wpdb->prefix}box_conversations c WHERE project_id = 0 AND ( receiver_id = {$receiver_id} AND cvs_author = $user_ID || receiver_id = {$user_ID} AND cvs_author = $receiver_id) ", ARRAY_N );

	if ( null !== $c_id ) {
	  // do something with the link
	  return (int) $c_id[0];
	}
	return false;
}
function map_in_archive(){
	global $box_general;
	return $box_general->map_in_archive;
}
if( ! function_exists('box_manuall_approve') ) {
	function box_manuall_approve(){
		return false;
	}
}
if( ! function_exists('fre_a_cat_item') ) {
	function fre_a_cat_item($cat){
		$term_link = esc_url(get_term_link($cat));
		$thumbnail_id = get_term_meta($cat->term_id,'cat_thumbnail_id', true);
		$thumbnail_url =  wp_get_attachment_url ( $thumbnail_id );
		if( !$thumbnail_url ){
			$thumbnail_url =BOX_IMG_URL.'/no-cat-thumbnail.png';
		}
		?>
		<li class="col-md-3 col-xs-6 cat-item">
			<div class="wrap">
				<div class="header-cat">
					<div class="cat-thumbnail">
						<a href="<?php echo $term_link;?>" class="cat-link">
							<img src="<?php echo $thumbnail_url;?>" alt="<?php echo $cat->name;?>" title="<?php echo $cat->name;?>">
						</a>
					</div>
					<div class="cat-name">
						<a href="<?php echo $term_link;?>"><h3><?php echo $cat->name;?></h3></a>
					</div>
				</div>
				<div class="des-cat">
					<?php echo term_description( $cat->term_id ); // echo $on_draught;?>
				</div>

			</div>
		</li>

		<?php
	}
}

function box_create_deposit_draft_order($gateway, $amount){
	$obj_gateway = new \stdClass;
	switch ($gateway) {
		case 'paypal':
			$obj_gateway = Box_PayPal::get_instance();
			break;
		case 'payfast':
			$obj_gateway = Box_PayFast::get_instance();
			break;
		case 'cash':
			$obj_gateway = BX_Cash::get_instance();
			break;
		default:
			$obj_gateway = apply_filters('box_order_object_checkout', $obj_gateway, $gateway);
			break;
	}

	$order_id =  $obj_gateway->create_deposit_draft_order( $amount); // return id or form generate
	do_action( 'create_pending_order', $gateway, $order_id );
	$response = $obj_gateway->get_redirect_response($order_id , $amount);

	return $response;
}
function box_checkout_get_redirect($gateway, $order){
	$respond = array(
		'msg' => 'Check done',
		'success'=> true,
		'redirect_url' => false,
	);
	switch ($gateway) {
		case 'paypal':
		case 'cash':
			$respond['redirect_url'] = $order;
			break;
		case 'payfast':
			$respond['patch_form'] = $order;
			break;
		default:
			$respond = apply_filters('box_checkout_get_redirect',$respond, $gateway, $order);
			break;
	}
	return $respond;
}
function box_get_country_args(){
	$label = array(
		'name'                       => __( 'Countries',  'boxtheme' ),
		'singular_name'              => __( 'Country',  'boxtheme' ),
		'search_items'               => __( 'Search Countries', 'boxtheme' ),
		'popular_items'              => __( 'Popular Countries', 'boxtheme' ),
		'all_items'                  => __( 'All Countries', 'boxtheme' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Country', 'boxtheme' ),
		'update_item'                => __( 'Update Country', 'boxtheme' ),
		'add_new_item'               => __( 'Add New Country', 'boxtheme' ),
		'new_item_name'              => __( 'New Country Name', 'boxtheme' ),
		'separate_items_with_commas' => __( 'Separate Countries with commas', 'boxtheme' ),
		'add_or_remove_items'        => __( 'Add or remove Countries', 'boxtheme' ),
		'choose_from_most_used'      => __( 'Choose from the most used Countries', 'boxtheme' ),
		'not_found'                  => __( 'No Countries found.', 'boxtheme' ),
		'menu_name'                  => __( 'Countries', 'boxtheme' ),

	);
	$result['label'] = $label;
	$result['slug'] = 'country';
	return apply_filters('tax_country_args',(object) $result);
}
function get_first_gateway_id(){
	$gateways = box_get_list_payment();
	if( !empty($gateways)){
		return $gateways[1]['id'];
	}
	return 0;
}