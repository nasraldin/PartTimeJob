<?php
/**
 *	Template Name: Warning Verify accoun
 */
?>
<?php get_header(); ?>
<?php
	global $wpdb;
	$verify_key = isset($_GET['key']) ? wp_unslash($_GET['key']) :'';
	$user_login = isset($_GET['user_login']) ? wp_unslash($_GET['user_login']) :'';
	$user_id 	= 0;
?>
<div class="full-width">
	<div class="container site-container">
		<div class="site-content" id="content" >
			<div class="col-md-12  text-justify mt50" style="min-height: 450px; padding-top: 100px;">
				<div id="verify_content">
				<?php
				global $user_ID;
				if( !empty( $verify_key) && !empty( $user_login) ) {

					$user = check_password_reset_key( $verify_key, $user_login ); // return userdata if match

					if ( ! $user || is_wp_error( $user ) ) {
						if ( $user && $user->get_error_code() === 'expired_key' ) {
							_e('Key is expired', 'boxtheme');
						} else {
							echo $user->get_error_message();
						}
					} else {

						$wpdb->update( $wpdb->users, array( 'user_status' => 1 ), array( 'user_login' => $user_login ) );
						$wpdb->update( $wpdb->users, array( 'user_activation_key' => '' ), array( 'user_login' => $user_login ) );

						$redirect_link = '';
						$user_id = $user->ID;
						$role =  bx_get_user_role($user->ID);

						// add default credit for new acccount.
						$default_credit = (int) BX_Option::get_instance()->get_group_option('opt_credit')->number_credit_default;
						if( $default_credit > 0 )
							BX_Credit::get_instance()->increase_credit_available( $default_credit );
						// add done.

						if ( $role == FREELANCER ) {

							$redirect_link =  box_get_static_link('my-profile');
							// save status 1 as verified of this user.
							$args = array(
								'post_title' 	=> $user->first_name . ' '.$user->last_name ,
								'post_type'  	=> PROFILE,
								'post_author' 	=> $user_id,
								'post_status' 	=> 'publish',
								'meta_input'	=> array(
									HOUR_RATE => 0,
									RATING_SCORE => 0,
									)
							);
							$profile_id = wp_insert_post($args);
							update_user_meta( $user_id, 'profile_id', $profile_id );
						} else {
							$redirect_link = home_url();
						}
						Box_ActMail::get_instance()->verified_success( $current_user , $redirect_link);
						?>
						<form name="redirect">
							<center>
								<?php _e('Your account is verified. You are redirecting to home page.','boxtheme'); ?>
								<form>
								<input type="hidden" size="3" readonly="true" name="redirect2">
							</center>
						</form>
						<script>
							var targetURL= "<?php echo $redirect_link; ?>";
							var countdownfrom=2
							var currentsecond=document.redirect.redirect2.value=countdownfrom+1
							function countredirect(){
								if (currentsecond!=1){
									currentsecond-=1
									document.redirect.redirect2.value=currentsecond
								}else{
									window.location=targetURL
									return
								}
								setTimeout("countredirect()",1000)
							}
							countredirect()
						</script>
						<?php

					}
				} else if( is_user_logged_in() ) {
					$user 	= wp_get_current_user(); ?>
					<h2 class="primary-font"><?php _e('Verify your account to access website','boxtheme');?></h2>
					<div class="col-md-12 mt50">
						<?php printf (__('We\'ve sent an email to your address: <strong>%s</strong><br /> Please check your email and click on the link provided to verify your account.','boxtheme'), $user->user_email) ; ?>
						<p class="show-btn"><?php _e('If you did not receive that email. You can click <a href="#" class="btn-resend"> here</a> to resend a new email','boxtheme');?>
						<input type="hidden" id="nonce_new_email" value="<?php echo wp_create_nonce('new_confirm_email');?>" name="nonce_new_email">

					</div><?php
				} ?>
				</div> <!-- #verify_content !-->
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var h_footer = $("footer#main-footer ").css("height"),
			h_header = $("#full_header").css('height');
			var body = $( window ).height();

			var h_content = $("#content").css('height');
			var h_expected = parseInt(body)- parseInt(h_header) - parseInt(h_footer);

			if( h_expected > parseInt(h_content) ) {
				h_expected = h_expected - 38;
				$("#content").css('height', h_expected );
			}
			$(".btn-resend").click(function(event){

				var _this = $(event.currentTarget);
				if( _this.hasClass('disable') ){
					return false;
				}
				_this.addClass('disable');

				var nonce = $("#nonce_new_email").val();
				var data = {action:'send_new_confirm_email',nonce: nonce};

				var success = function(response){
					console.log(response);
					$(".show-btn").hide();
				}
				window.ajaxSend.Custom(data, success);

			})
		});
	})(jQuery);
</script>
<style type="text/css">
	.btn-resend{
		text-decoration: underline;
	}
</style>
<?php get_footer();?>