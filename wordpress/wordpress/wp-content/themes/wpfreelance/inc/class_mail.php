<?php
Class Box_Email{
	static $_instance;
	public $option;
	function __construct(){
		$this->option = BX_Option::get_instance()->get_mailing_setting();

	}
	static function get_instance(){
		if ( ! isset(self::$instance) ){
			 self::$_instance = new self();
		}
		return self::$_instance;
	}
	function get_header($option){
		//font-family: 'Roboto Condensed', sans-serif;
		//font-family: 'Roboto', sans-serif;
		//font-family: 'Raleway', sans-serif;
		//font-family: 'Open Sans', sans-serif;
		$rlt =  is_rtl() ? "rtl" : "ltr";
		$rightmargin = is_rtl() ? 'rightmargin' : 'leftmargin';
		$header = '<!DOCTYPE html>
		<html dir="'.$rlt.'">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset='.get_bloginfo( 'charset' ).'" />
				<title>'.get_bloginfo( 'name', 'display' ).'</title>
				<link href="https://fonts.googleapis.com/css?family=Raleway|Roboto|Roboto+Condensed|Open+Sans" rel="stylesheet">

				<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<defs>
					<symbol id="icon-facebook" viewBox="0 0 32 32">
					<title>facebook</title>
					<path d="M19 6h5v-6h-5c-3.86 0-7 3.14-7 7v3h-4v6h4v16h6v-16h5l1-6h-6v-3c0-0.542 0.458-1 1-1z"></path>
					</symbol>
					</defs>
				</svg>

				<style type="text/css">
					body{
						word-break: break-word;
					    color: #666;
					    font-family: Helvetica;
					    font-size: 14px;
					    line-height: 160%;
					    text-align: left;
					}
					#credit{
						font-family: "Raleway", sans-serif;
					}
					#template_header_image img{max-width: 100%; width: 350px; text-align: left; padding:15px 0;}
					img{max-width:100%;}
					#template_header_image{
						text-align: left;
						border-bottom: 1px solid #ccc;
						padding: 0 15px;
					}
					#header_wrapper{
						padding: 0 15px;
					}
					body{
						width: 100%;
						background: #ececec;
					}
					.main-body{
						width: 450px;
						margin:0 auto;
						background: #fff;

					}

					#body_content{
						padding-bottom: 35px;
					}
					a.link-skill{
						text-decoration:none;
						color:#33cc66;
					}
					a{
						text-decoration:none;

					}
					h3{ margin:0; padding:0;}
					 .connect-us a{
					  padding:0 5px;
					}
					table {
					 	border-collapse:collapse;
					}
				</style>
			</head>




			<body '.$rightmargin.'="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="#ececec">
				<div id="wrapper" dir="'.$rlt.'">
					<table border="0" cellpadding="0" class="main-body" cellspacing="0" height="100%" width="100%" bgcolor="#ececec">

						<tr>
							<td align="center" valign="top" cellpadding="0" cellspacing="0" >
							<!-- start body !-->
								<table cellpadding="0" cellspacing="0" width="600" id="template_container" bgcolor="#fff" style="border:solid 1px '.$option->main_bg.'" >
									<tr>
										<td align="left" valign="top">
											<!-- Header -->
											<table border="0" cellpadding="0" cellspacing="0" width="600" id="image_header">
												<tr>
													<td style="border-bottom:solid 1px '.$option->main_bg.';" id="image_wrapper">
														<a href="'.home_url().'">
															<img width="75%" style="display:block;margin:0 auto; padding:15px 0;"   alt="' . get_bloginfo( 'name', 'display' ) . '" src="'.$option->header_image.'">
														</a>
													</td>
												</tr>
											</table>
											<!-- End IMG Header -->
										</td>
									</tr>

									<tr>
										<td align="left" valign="top">
											<!-- Header -->
											<table border="0" cellpadding="15" cellspacing="0" width="600" id="template_header" >
												<tr>
													<td id="header_wrapper">

													</td>
												</tr>
											</table>
											<!-- End Header -->
										</td>
									</tr>
									<tr>
										<td align="center" valign="top">
											<!-- Body -->
											<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
												<tr>
													<td valign="top" id="body_content">
														<!-- Content -->
														<table border="0" cellpadding="20" cellspacing="0" width="100%">
															<tr>
																<td valign="top">
																	<div id="body_content_inner">';
		return $header;
	}
	function get_footer( $option ){
		$connect_txt = __('Connect Us','boxtheme');
		$foo_txt = wpautop( wp_kses_post( wptexturize( apply_filters( 'box_email_footer_text', get_option( 'box_email_footer_text' ) ) ) ) );

														$foo_txt = 	'</div>
																</td>
															</tr>
														</table>
														<!-- End Content -->
													</td>
												</tr>
											</table>
											<!-- End Body -->
										</td>
									</tr>
									<tr>
										<td align="center" valign="top" bgcolor="'.$option->main_bg.'">
											<!-- Footer -->
											<table border="0" cellpadding="15" cellspacing="0" width="600" id="template_footer">
												<tr>
													<td valign="top">
														<table border="0" cellpadding="0" cellspacing="0" width="100%">
															<tr>
																<td colspan="2" valign="middle" id="credit"><font color="#FFFFFF">'.$option->footer_text.'</font></td>
															</tr>
														</table>
													</td>
												</tr>

											</table>
											<!-- End Footer -->
										</td>
									</tr>

									<tr>
										<td valign="top" bgcolor="'.$option->main_bg.'">
											<table border="0" cellpadding="15" cellspacing="0" width="228px" align="left">
												<tr>
													<td colspan="2" valign="middle" id="credit"><h3 style="padding:0; margin:0;"> <font color="#FFFFFF">'.$connect_txt.'</font></h3></td>
												</tr>
											</table>
											<table border="0" class="connect-us" cellpadding="15" cellspacing="0" width="150" align="right">
												<tr>
													<td colspan="2" valign="middle" id="credit" color="#FFFFFF">';
													global $general;
													if( !isset($general) )
														$general = (object) BX_Option::get_instance()->get_group_option('general');

													$social_link = '';

													if ( !empty( $general->fb_link ) )
										    			$social_link .='<a class="gg-link"  target="_blank" href="'.esc_url($general->fb_link).'"><img src="'.get_template_directory_uri().'/img/email-fb.png" /></a></li>';

										    		if ( !empty( $general->tw_link ) )
										    			$social_link .='<a class="gg-link"  target="_blank" href="'.esc_url($general->tw_link).'"><img src="'.get_template_directory_uri().'/img/email-tw.png" /></a></li>';

										    		if ( !empty( $general->gg_link ) )
										    			$social_link .='<a class="gg-link"  target="_blank" href="'.esc_url($general->gg_link).'"><img src="'.get_template_directory_uri().'/img/email-gg.png" /></a></li>';

										    		$foo_txt.=$social_link;
										    		$foo_txt.='

													</td>
												</tr>
											</table>
										</td>
									</tr>

								</table>
							</td>
						</tr>
					</table>
				</div>
			</body>
		</html>';
		return $foo_txt;
	}
	function send_mail( $to, $subject, $message, $header  ){

		$header_mail = $this->get_header($this->option);
		$footer_mail = $this->get_footer($this->option);
		$msg = $header_mail.$message.$footer_mail;


		//add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
		return wp_mail( $to, $subject, $msg , $header);

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}
	public function get_content_type() {
		return 'text/html';
		// switch ( $this->get_email_type() ) {
		// 	case 'html' :
		// 		return 'text/html';
		// 	case 'multipart' :
		// 		return 'multipart/alternative';
		// 	default :
		// 		return 'text/plain';
		// }
	}
	function get_from_name(){

		return wp_specialchars_decode( esc_html( $this->option->from_name ), ENT_QUOTES );
	}
	public function get_from_address() {

		return sanitize_email( $this->option->from_address );
	}

}
function box_mail( $to, $subject, $message, $header = '' ) {

	return Box_Email::get_instance()->send_mail( $to, $subject, $message, $header );
}
class Box_ActMail {
	static $_instance;
	public static function get_instance(){
		if ( ! isset(self::$instance) ){
			 self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * send 2 emails. 1 to register. 1 to admin.
	**/
	function act_signup_mail( $user, $mail_to ){


		$verify_link = box_get_static_link('verify');

		$activation_key =  get_password_reset_key( $user);

		$link = add_query_arg(
			array(
				'user_login' => $user->user_login,
				'key' => $activation_key
			) ,
			$verify_link
		);

		if(box_requires_confirm() ){
			$mail = BX_Option::get_instance()->get_mail_settings('new_account_confirm');
		} else {
			$mail = BX_Option::get_instance()->get_mail_settings('new_account');
		}

		$subject = $mail->subject;
		$content = stripcslashes($mail->content);

		$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes ( $subject ) );

		$content = str_replace('#display_name', $user->display_name, $content);
		$content = str_replace('#home_url', home_url(), $content );
		$content = str_replace('#user_login', $user->user_login, $content);
		$content = str_replace('#link', esc_url($link), $content);

		// send to register.
		box_mail( $mail_to, $subject, stripslashes($content) );

		$mail = BX_Option::get_instance()->get_mail_settings('new_account_noti' );

		$admin_email = get_option( 'admin_email');

		$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes ( $mail->subject ) );
		$noti_content = str_replace('#user_login', $user->user_login, stripcslashes($mail->content));
		$noti_content = str_replace('#home_url', home_url(), $noti_content );
		$noti_content = str_replace('#blog_name', get_bloginfo('name'), $noti_content);
		$noti_content = str_replace( '#user_email', $user->user_email, $noti_content );
		//$content = str_replace('#user_', $user->user_login, stripcslashes($mail->content));

		box_mail( $admin_email, $subject, $noti_content);
	}
	function confirm_success( $user ){
		$mail = BX_Option::get_instance()->get_mail_settings('verified_success');

		$subject = $mail->subject;
		$content = stripcslashes($mail->content);

		$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes ( $subject ) );

		$content = str_replace('#display_name', $user->display_name, $content);
		$content = str_replace('#blog_name', get_bloginfo('name'), $content);
		$content = str_replace('#user_login', $user->user_login, $content);
		$content = str_replace('#user_email', $user->user_email, $content);
		$content = str_replace('#home_url', home_url(), $content );


		return box_mail( $user->user_email, $subject, stripslashes($content) );
	}
	function send_reconfirm_email( $current_user ){

		$activation_key = get_password_reset_key($current_user);
		$link = box_get_static_link('verify');
		$link = add_query_arg( array( 'user_login' => $current_user->user_login ,  'key' => $activation_key) , $link );
		if ( ! is_wp_error( $activation_key ) ){
			$subject = sprintf( __('Re-confirmation email from %s','boxtheme'), get_bloginfo('name') );
			$message = sprintf( __( 'Hello %s,<p>This is new confirmation email from %s.</p>Kindly click <a href="%s">here</a> to active your account.<p>Regards,','boxtheme'), $current_user->display_name, get_bloginfo('name'), $link );
			return box_mail( $current_user->user_email, $subject, $message );
		}
		return $activation_key;
	}


	function mail_reset_password( $user){
		//$mail = BX_Option::get_instance()->get_mail_settings('new_account');
		$activation_key =  get_password_reset_key( $user);
		$link = box_get_static_link('reset-pass');
		$link = add_query_arg( array('user_login' => $user->user_login,  'key' => $activation_key) , $link );


		$mail =BX_Option::get_instance()->get_mail_settings('reset_password');
		$subject = str_replace('#blog_name', get_bloginfo('name'), stripslashes ($mail->subject) );

		$content = str_replace('#user_login', $user->display_name, stripcslashes($mail->content));

		$content = str_replace('#display_name', $user->display_name, $content);
		$content = str_replace('#blog_name', get_bloginfo('name'), $content);
		$content = str_replace('#home_url', home_url(), $content);
		$content = str_replace('#reset_link', esc_url($link), $content);


		box_mail( $user->user_email, $subject, stripslashes($content) );
	}
	/**
	 * Send an email to owner project let he know has new bidding in his project.
	 **/
	function has_new_bid($project){


		$mail = BX_Option::get_instance()->get_mail_settings('new_bidding');

		$content = str_replace("#project_link", get_permalink( $project->ID), stripcslashes($mail->content));
		$content = str_replace("#project_name", $project->post_title, $content);

		$author = get_userdata( $project->post_author );

		$content = str_replace("#display_name", $author->display_name, $content);

		box_mail( $author->user_email, $mail->subject, $content );

	}

	/**
	 * send an email to freelancer when employer create a conversion with this freelancer
	**/
	function has_new_conversation($freelancer_id, $employer, $project){

		$mail = BX_Option::get_instance()->get_mail_settings('new_converstaion');
		$freelancer = get_userdata($freelancer_id);

		$subject =  $mail->subject;

		$content = str_replace("#project_name", $project->post_title, stripcslashes($mail->content));
		$content = str_replace("#project_link", get_permalink( $project->ID), $content);
		$content = str_replace("#inbox_link", box_get_static_link('inbox'), $content);

		$content = str_replace("#employer_name", $employer->display_name, $content);

		$content = str_replace("#display_name", $freelancer->display_name, $content);

		box_mail( $freelancer->user_email, $subject, $content );

	}
	function assign_job( $freelancer_id, $project_id ){
		$mail = BX_Option::get_instance()->get_mail_settings('assign_job');
		$subject =  $mail->subject;
		$project = get_post($project_id);
		$content = str_replace("#project_name", $project->post_title, stripcslashes($mail->content));

		$project_link = get_permalink($project_id);
		$project_link = add_query_arg('workspace','1', $project_link);
		$content = str_replace("#project_link", $project_link, $content);

		$freelancer = get_userdata( $freelancer_id );
		$content = str_replace("#display_name",$freelancer->user_login , $content);

		box_mail( $freelancer->user_email, $subject, $content );

	}
	function subscriber_match_skill($project_id, $header, $admin_email){
		$project = get_post($project_id);
		$mail = BX_Option::get_instance()->get_mail_settings('subscriber_skill');
		$subject =  $mail->subject;
		$terms = get_the_terms( $project, 'skill' );
		$skill_html = '';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
			  	$skill_html.='<a class="link-skill  link-skill" href="' . get_term_link($term).'">' . $term->name . '</a>, ';
			}

		}

		$employer = get_userdata($project->post_author);
		$content = str_replace("#project_name", $project->post_title, stripcslashes($mail->content));
		$content = str_replace("#project_link", get_permalink( $project->ID), $content);
		$content = str_replace("#author_name", $employer->display_name, $content);
		$content = str_replace("#skill_list", $skill_html, $content);
		box_mail( $admin_email, $subject, $content, $header);
	}

	function send_job_to_mail( $receiver_email, $subject, $message, $project_id ){
		$mail = BX_Option::get_instance()->get_mail_settings('send_job_to_mail');

		$project= get_post($project_id);

		$content = str_replace("#project_name", $project->post_title, stripcslashes($mail->content));
		$content = str_replace('#blog_name', get_bloginfo('name'), $content );
		$content = str_replace("#project_link", get_permalink( $project_id), $content);
		$content = str_replace('#message', $message, $content );


		return box_mail( $receiver_email, $subject, $content);
	}

}
class Box_Mail_Hook{
	function __construct(){
		add_action( 'after_create_pending_order_via_cash',array($this,'mail_detail_cash'), 10 ,2 );
		add_action( 'after_approve_cash_deposit',array($this,'mail_approve_cash_deposit'), 10 ,2 );

	}
	function get_settingt($name){
		return BX_Option::get_instance()->get_mail_settings($name);
	}
	function mail_detail_cash($order_id, $order_args){
		global $box_currency, $symbol;
		$buyer =  wp_get_current_user();
		$mail = $this->get_settingt('cash_order');
		$content = stripcslashes($mail->content);


		$content = str_replace('#blog_name', get_bloginfo('name'), $content );
		$content = str_replace('#display_name', $buyer->display_name, $content);
		$content = str_replace("#order_id", $order_id, $content);
		$content = str_replace('#amount', "({$symbol})".$order_args->meta_input['amount'].($box_currency->code), $content );

		$subject = __('Your cash order','boxtheme');
		return box_mail( $buyer->user_email, $subject, $content);
	}
	function mail_approve_cash_deposit($order){
		global $box_currency, $symbol;
		$buyer =  get_userdata($order->payer_id);
		$mail = $this->get_settingt('cash_approve');

		$content = stripcslashes($mail->content);
		$content = str_replace('#blog_name', get_bloginfo('name'), $content );
		$content = str_replace('#display_name', $buyer->display_name, $content);
		$content = str_replace("#order_id", $order->ID, $content);
		$content = str_replace('#amount', "({$symbol})".$order->amount.($box_currency->code), $content );

		$subject = __('Your cash is approved.','boxtheme');
		return box_mail( $buyer->user_email, $subject, $content);
	}
}
new Box_Mail_Hook();