<?php
//var_dump('123');
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

function box_excerpt( $full_content, $maxchar, $end = "..." ) {

	$full_content = wp_strip_all_tags($full_content);
	if( empty($full_content) )
		return '';
  	if ( strlen( $full_content ) > $maxchar || $full_content == '' ) {
        $words = preg_split('/\s/', $full_content);
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output) + strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            }
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    }
    else {
        $output = $full_content;
    }
    return $output;
}
function the_requirement(){
	echo get_the_excerpt();
}


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
add_action('wp_mail_failed','show_mail_fail');
function show_mail_fail($wp_error){
	//var_dump($wp_error);
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
function is_account_verified($user_ID){ //is_verified
	return true; //hack code;
	//return BX_User::get_instance()->is_verified($user_ID);
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

function box_account_dropdow_menu(){ global $role; global $user_ID; $current_user = wp_get_current_user();

	global $user_ID, $wpdb;

	$messages = $notifies = array();

	$number_new_notify = (int) get_user_meta( $user_ID, 'number_new_notify', true ); //has_new_notify

	$msg_class= 'empty-msg';
	if( $number_new_notify > 0 )
		$msg_class = "has-msg-unread"

?>
	<ul class="account-dropdown">
		<li class="profile-account dropdown ">

			<a rel="nofollow" class="dropdown-toggle account-name" data-toggle="dropdown" href="#"><div class="head-avatar"><?php echo get_avatar($user_ID);?></div><span class="username"><?php echo $current_user->display_name;?></span> <span class="caret"></span>
			<span class="hide <?php echo $msg_class;?>"><?php echo $number_new_notify;?></span>
			</a>
			<ul class="dropdown-menu account-link" >
				<?php if($role == FREELANCER){ ?>
					<li> <i class="fa fa-th-list" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('dashboard');?>"><?php _e('My Job','boxtheme');?></a></li>
				<?php } else { ?>
					<li> <i class="fa fa-th-list" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('dashboard');?>"><?php _e('My Jobs','boxtheme');?></a></li>
				<?php }  ?>
				<li> <i class="fa fa-user-circle-o" aria-hidden="true"></i> <a href="<?php echo box_get_static_link('my-profile');?>"><?php _e('My Profile','boxtheme');?></a></li>
				<li> <i class="fa fa-sign-out" aria-hidden="true"></i>  <a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e('Logout','boxtheme');?></a></li>
			</ul>
		</li>
		<li class="icon-bell first-sub no-padding-left pull-left" id="toggle-msg">
			<div class="dropdown">
			  	<span class="dropdown-toggle <?php if ( $number_new_notify)  echo 'toggle-msg';?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell  " aria-hidden="true"></i></span> <?php
			  	echo '<ul class=" dropdown-menu ul-notification">';
			  	$unread = 0;
				if( !empty ( $notifies) ) {

					foreach ($notifies as $noti) {
						$class ="noti-read";
						if( $noti->msg_unread == 1) { $unread ++; $class="noti-unread"; }
						$date = date_create( $noti->msg_date );
						$date = date_format($date,"m/d/Y");	?>

						<li class="dropdown-item <?php echo $class;?>">
							<div class="left-noti"><a href="#"><?php echo get_avatar( $noti->sender_id ); ?></a></div>
							<div class='right-noti'>
								<a href="<?php echo esc_url($noti->msg_link);?>"><?php echo stripslashes($noti->msg_content);?></a>
								<?php echo '<small class="mdate">'.$date.'</small>'; ?>
							</div>
							<span class="btn-del-noti" title="<?php _e('Remove','boxtheme');?>" rel="<?php echo $noti->ID;?>" href="#"><i class="fa fa-times primary-color" aria-hidden="true"></i></span>
						</li> <?php
					}

				} else { ?>
					<p class="empty-noty"><?php _e('There is no new notification','boxtheme');?></p>
				<?php }
				echo '</ul>';
				if( $unread )
					echo '<span class="notify-acti">'.$unread.'</span>'; ?>
			</div>
		</li>
	</ul>
<?php }

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

		if( $can_access_workspace && in_array( $project->post_status, array('awarded','done','dispute','finish','disputing', 'disputed','archived') ) ) { ?>
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
			'done' => __('Done','boxtheme'),
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
		if ( $bid->post_author == $user_id || $project->post_author == $user_ID || current_user_can( 'administrator' ) ){
			return true;
		}
		return false;

	}


function get_job_location($job_id){
	$location = get_the_terms( $job_id, 'location' );

	if( !is_wp_error( $location ) && !empty($location) ){
		return $location[0]->name;
	}
	return '';
}

function show_job_location($job_id){

	$locations = get_the_terms( $job_id, 'location' );
	if( !is_wp_error( $locations ) && !empty($locations) ){

		$parent = $sub = $locations[0];
		$check = false;

		if( isset($locations[1] ) ) {
			$sub = $locations[1];
			$check = true;
		}

		if( $locations[0]->parent != 0 ){
			$parent = $locations[1];
			$sub = $locations[0];

		}

		?>
	<div class="address" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
		<meta content="VN" itemprop=" addressCountry ">
		<div class="text" itemprop="addressLocality">		<?php echo $parent->name; ?>	</div>
		<div class="text other-address"> <?php if($check) echo $sub->name;?> 	</div>
	</div> <?php
	}

}
function get_years(){?>
	<div>
	<label for="year-started" class="offscreen ng-binding">started Year</label>
		<select id="year-started" class="year ng-pristine ng-invalid ng-invalid-required ng-valid-monthpicker-beyond-lower-limit ng-valid-monthpicker-beyond-upper-limit" ng-model="year" data-automation="year-started-field" ng-change="changeDate()" ng-disabled="ngDisabled" ng-required="ngRequired" sk-error-message="" name="year-started" aria-describedby="year-started-error" required="required">
			<option disabled="" value="">Year</option><!-- ngRepeat: year in years -->
			<?php
			for($i = 2017; $i > 1920; $i--){ ?>
				<option ng-repeat="year in years" ng-value="<?php echo $i;?>" class="ng-binding ng-scope" value="<?php echo $i;?>"><?php echo $i;?></option><!-- end ngRepeat: year in years -->
			 <?php } ?>


		</select>
		<i class="icon-chevron-down"></i>
	</div> <?php
}
function get_company_types(){
	return array(
		'product' => __('Product','boxtheme'),
		'outsouce' => __('Outsouce','boxtheme'),
	);
}
function get_company_ranges(){
	return array(
		'range1' => __('< 10','boxtheme'),
		'range2' => __('10-30','boxtheme'),
		'range3' => __('300 +','boxtheme'),
		'range4' => __('1000 +','boxtheme'),
	);
}
function box_processing_apply($request, $job){
	// echo '<pre>';
	// var_dump($request);
	// echo '</pre>';
	//https://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data
	global $user_ID;
	$user_email = sanitize_email($request['user_email']);
	$first_name = isset($request['first_name']) ? sanitize_text_field($request['first_name']) :'';
	$last_name = isset($request['last_name']) ? sanitize_text_field($request['last_name']) :'';
	$phone_number = isset($request['phone_number']) ? sanitize_text_field($request['phone_number']) :'';
	$job_titile = isset($request['job_titile']) ? sanitize_text_field($request['job_titile']) :'';
	$date_start = isset($request['date_start']) ? sanitize_text_field($request['date_start']) :'';


	$first_name = isset($request['first_name']) ? sanitize_text_field($request['first_name']) :'';
	$cvlt  = isset($request['chooseCoverLetterOpt']) ? $request['chooseCoverLetterOpt'] : 0;

	$cover_letter_text= '';
	$attach_id = $resume_id = 0;
	if( $cvlt == 2){
		// access text are.
		$cover_letter_text = isset( $request['cover_letter_text'] ) ? sanitize_text_field($request['cover_letter_text']) :'';

	} else {
		$cvlt_file = isset($_FILES['cover_letter_file']) ? $_FILES['cover_letter_file'] : 0;

		if( isset($cvlt_file['size']) && $cvlt_file['size'] > 1  ){

			$attach_id = 	box_upload_file( $cvlt_file, $user_ID);
		}

	}
	$chooseResume = isset($request['chooseResume']) ? sanitize_text_field($request['chooseResume']) :'';
	if( ! $chooseResume == 3){
		$resume_file = isset($_FILES['resume_file']) ? $_FILES['resume_file'] : 0;
		$resume_id = 	box_upload_file( $resume_file, $user_ID);
	} else {
		// without resume.
	}
	$company = get_userdata($job->post_author);

	$to 	= $company->data->user_email;
	$headers = 'From: My Name <myname@mydomain.com>' . "\r\n";
	$subject = 'Has new application';
	$content = 'Yay! Your job has new application';

	$info = '';
	$info.= '<p><strong>Email:</strong> '.$user_email.'<p>';
	$info.= '<p><strong>Job title:</strong> '.$job_titile.'<p>';

	if( !empty($date_start))
	$info.= '<p><strong> Date start:</strong> '.$date_start.'<p>';

	if( !empty($phone_number))
		$info.= '<p><strong> Phone number:</strong> '.$phone_number.'<p>';

	if( ! empty( $cover_letter_text) ){
		$info.= '<p><strong>Cover letter</strong>: '.$cover_letter_text.'<p>';

	}

	$content.=$info;
	$attachment = array();
	if( $attach_id ){
		$attachment[] = get_attached_file($attach_id);
	}
	if( $resume_id ){
		$attachment[] = get_attached_file($resume_id);
	}

	box_mail($to, $subject, $content, $attachment);

	$headers = 'From: My Name <myname@mydomain.com>' . "\r\n";
	$subject = 'Your application is submitted';
	$content = 'Yay! Your application is submitted successful';
	$content.=$info;
	box_mail($user_email, $subject, $content );
	$applied = get_user_meta($user_ID,'job_applied', true);

	if( ! is_array( $applied ) || !$applied ) $applied = array();

	array_push($applied, $job->ID);

	update_user_meta( $user_ID, 'job_applied',$applied );

	return true;
}
function is_applied($job, $user_id = 0){
	global $user_ID;
	if( ! $user_id )
		$user_id = $user_ID;

	$job_applied = get_user_meta($user_id,'job_applied', true);


	if( ! is_array($job_applied)|| ! $job_applied )
		return false;
	if( in_array( $job->ID, $job_applied ) ){
		return true;
	}
	return false;
}