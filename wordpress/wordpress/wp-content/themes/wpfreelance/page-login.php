<?php
/**
 *	Template Name: Page Login
 */
?>
<?php get_header(); ?>

	<div class="container site-container">
		<h1 class="center text-center page-title"><?php the_post(); the_title();?></h1>
        <div id="loginbox" style="margin-top:15px; min-height: 40em;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                <div style="padding-top:25px" class="panel-body" >
                    <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                	<?php
                		$warning = array();
                		$email = isset($_GET['email'])? $_GET['email'] : '';
                		$redirect_url = '';
                		if( ! empty( $_GET['redirect'] ) ){
                			$redirect_url   = esc_url($_GET['redirect']);
                		} else {
                			$redirect_url = box_get_login_redirect_url();
                		}
                	?>
                    <div class="row">
						<div class="col-xs-12">
						  	<div class="well" id="login_section">
								<div class="input-group full center row-login-avatar">
			                		<img class="avatar" src="<?php echo get_template_directory_uri().'/img/avatar_login.png';?>" />
			                	</div>

						      	<form id="loginform" class="loginform"  method="POST"  >
						      		<div id="loginErrorMsg"  class="loginErrorMsg alert alert-error alert-warning hide"></div>
						          	<div class="form-group">
						              	<input type="text" class="form-control required" required id="login-username" name="user_login" value="<?php echo $email;?>" title="<?php _e('Enter you username','boxtheme');?>" placeholder="<?php _e('Username or Email','boxtheme');?>">
						          	</div>
						          	<div class="form-group">
						              	<input type="password" class="form-control required" required id="password" required name="user_password" value=""  title="<?php _e('Enter your password','boxtheme');?>" placeholder="<?php _e('Password','boxtheme');?>">
						          	</div>

						          	<div class="checkbox"><label><input type="checkbox" name="remember" id="remember"><?php _e('Remember login','boxtheme');?>  </label></div>
					           		<?php
				                        if( ! empty( $redirect_url ) ){
				                            echo '<input type ="hidden" name="redirect_url" value ="'.$redirect_url.'" />';
				                        }
				                        wp_nonce_field( 'bx_login', 'nonce_login_field' );
			                    	?>
			                    	<!-- <div class="g-recaptcha1" data-sitekey="6LfaagYTAAAAAINBy-JHFEOOBU115cNMa5cSSv77" data-size="normal"></div> -->

			                    	<?php // box_add_captcha_field(); ?>

						          	<button type="submit" class="btn btn-success btn-block btn-submit"><?php _e('Log In','boxtheme');?> <i class="fa fa-spinner fa-spin"></i></button>

						          	<div class="forgotLink">
						          		<a href="#" class="show_fpf"><?php _e('Forgot password?','boxtheme');?></a> &nbsp; &nbsp; &nbsp; &nbsp;
						          		<a href="<?php echo box_get_static_link('signup');?>"><?php _e('Register new account?','boxtheme');?></a>
			                        </div>
			                    	<div class="no-padding-bottom no-margin-bottom form-group social-login"><?php bx_social_button_signup();?></div>
						      	</form>

						  	</div>
						  	<div class="well hide forgetpass" id="reset_pass_section">
						  		<form class="fre-reset-pw " id="resetPass">
						      		<h2><?php _e('Reset your password','boxtheme');?></h2>
						      		<div id="loginErrorMsg" class="loginErrorMsg alert alert-error alert-warning hide"><?php _e('Wrong username or password','boxtheme');?></div>
						      		<div class="form-group">
						              	<input type="email" class="form-control required" required="" id="login-email" name="email" value="" title="<?php _e('Enter you email','boxtheme');?>" placeholder="<?php _e('Enter you email','boxtheme');?>">
						              	<input type="hidden" name="method" value="send_request">

						              	<?php  wp_nonce_field( 'box_resetpass', 'nonce_resetpass_field' ); ?>
						          	</div>
						          	<div class="form-group">
						          		<button type="submit" class="btn btn-success btn-block " >
			                                <?php _e('Send','boxtheme');?>
			                            </button>
			                    	</div>
			                    	<!-- Please check your mailbox for instructions to reset your password. -->
						      	</form>
						  	</div>
						</div>
					</div>
                </div> <!-- panel-body !-->
            </div>
        </div>
	</div>


<?php get_footer();?>
<script type="text/javascript">
    (function($){

        $("#loginform").submit(function(event){
           // event.preventDefault();
            var form    = $(event.currentTarget);
            var send    = {};
            form.find( 'input' ).each( function() {
                var key     = $(this).attr('name');
                send[key]   = $(this).val();
            });
            var captcha = '';

            if (typeof grecaptcha != "undefined") {
            	//captcha = grecaptcha.getResponse();
            }

          	$.ajax({
                emulateJSON: true,
                url : bx_global.ajax_url,
                data: {
                        action: 'bx_login',
                        request: send,
                        captcha: captcha,

                },
                beforeSend  : function(event){
                	form.attr('disabled', 'disabled');
                	//$(".btn-submit").
                	form.find(".btn-submit").addClass("loading");
                },
                success : function(res){
                	form.find(".btn-submit").removeClass("loading");
                    if ( res.success ){
                        if( res.redirect_url ){
                            window.location.href = res.redirect_url;
                        } else {
                            window.location.href= bx_global.home_url;
                        }
                    } else {
                    	$(".loginErrorMsg").html(res.msg);
                    	$(".loginErrorMsg").removeClass("hide");
                    	//if( bx_global.enable_capthca ){
	        				//grecaptcha.reset();
	        			//}
                    }
                }
            });
            return false;
        });
        $(".forgotLink .show_fpf").click(function(event){
        	var _this = $(event.currentTarget);
        	$("#login_section").hide();
        	$("#reset_pass_section").removeClass('hide');
        });

        $("#resetPass").submit(function(event){
            event.preventDefault();
            var form    = $(event.currentTarget);
            var send    = {};
            form.find( 'input' ).each( function() {
                var key     = $(this).attr('name');
                send[key]   = $(this).val();
            });

           $.ajax({
                emulateJSON: true,
                url : bx_global.ajax_url,
                data: {
                        action: 'bx_resetpass',
                        request: send,
                },
                beforeSend  : function(event){
                	form.attr('disabled', 'disabled');
                	//$(".btn-submit").
                	form.find(".btn-submit").addClass("loading");
                },
                success : function(res){
                	form.find(".btn-submit").removeClass("loading");
                    if ( res.success ){
                    	$(".forgetpass").html(res.msg);
                    } else {
                    	$(".loginErrorMsg").html(res.msg);
                    	$(".loginErrorMsg").removeClass("hide");
                    }
                }
            });
            return false;
        });
    })(jQuery);

</script>

<style type="text/css">
	.row-login-avatar{
		margin-bottom: 25px;
	}
	.form-control{
		-webkit-box-shadow: 0;
	    box-shadow: none !important;
	    -webkit-transition:none;
	    height: 39px;
	    border-radius: 3px;
	}
	.form-group:focus{
		box-shadow: none !important;
	}
	img.avatar{
		border: 1px solid #d4d9dc;
		width: 100px;
		margin: 0px 5px 0;
		border-radius: 50%;
	}
	.well{
		background:transparent;
		border: 0;
		box-shadow: none;
		margin-bottom: 0;
		padding-bottom: 0;
	}
	.loginSignUpSeparator {
	    border-top: 1px solid #cbd2d6;
	    position: relative;
	    margin: 25px 0 10px;
	    text-align: center;
	}
	.loginSignUpSeparator .textInSeparator {
	    background-color: #fff;
	    padding: 0 .5em;
	    position: relative;
	    color: #999;
	    top: -.7em;
	}
	.forgotLink {
	    margin: 20px 0 20px;
	    text-align: center;
	    border-bottom: 0;
	}
	.forgotLink a{
		color:#626060;
	}
	.btn-success.btn-signup,
	.btn-success:hover.btn-signup{
		background-color: #ccc;
		border-color: #ccc;
		padding-top: 9px;
	}
	.loginform .btn,.forgetpass .btn{
		height: 39px;
		border-radius: 3px;
	}
	.forgetpass .btn{
		float: left;
		margin-top: 10px;
		margin-bottom: 15px;
	}
	#reset_pass_section{
		padding-top: 0p
	}
	.fre-reset-pw {
		padding-bottom: 20px;
		overflow: hidden;
	}
	.fre-reset-pw h2{
		padding-bottom: 15px;
		margin-top: 0;
	}
</style>