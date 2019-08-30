<?php
/**
 *	Template Name: Reset Password
 */
$key = isset($_GET['key']) ? $_GET['key'] : '';
$user_login = isset($_GET['user_login']) ? $_GET['user_login'] : '';

?>
<?php get_header(); ?>
<div class="full-width ">
	<div class="container page-nosidebar site-container">
        <div id="loginbox" style="margin-top:15px; min-height: 40em;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="panel panel-info" >
                <div style="padding-top:25px" class="panel-body" >
                    <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                    <div class="row">
						<div class="col-xs-12">
						  	<div class="well" id="login_section">
						      	<h1 class="margin-b20 margin-t30 span7"><?php _e('Choose your new password','boxtheme');?></h1>

							    <div id="loginErrorMsg" class="alert alert-error alert-warning" style="display: none;"></div>
							    <form class="span7" id="reset_password_form" method="POST">

							        <input type="hidden" id="token" name="token" value="<?php echo $key;?>">

							        <input type="hidden" id="username" name="username" value="<?php echo $user_login;?>">
							        <input type="hidden" id="method" name="method" value="setpass">

							        <div class="form-group">
							            <label for="new_password"><?php _e('New Password:','boxtheme');?></label>
							            <div>
							                <input type="password"  class="required form-control"   required  name="new_password" id="new_password">
							                <i class="icon-16-green-tick" style="display:none"></i>
							                <span class="help-inline small" style="display:block"></span>
							                <span id="passwd_hint_id" class="hint" style="display:block"></span>
							            </div>
							        </div>
							        <div class="form-group">
							            <label for="confirm_password"><?php _e('Confirm Password:','boxtheme');?></label>
							            <div>
							                <input type="password" class="required form-control" required name="confirm_password" id="confirm_password">
							                <i class="icon-16-green-tick" style="display:none"></i>
							                <span class="help-inline small" style="display:block"></span>

							            </div>
							        </div>
							        <div class="form-group ">
							            <div id="submit-controls">
							                <input type="submit" id="submit-btn" class="btn btn-large btn-info pull-right" value="Submit" style="border-radius: 3px;">
							                <img id="ajax-loader" alt="Freelancer Loading..." src="https://cdn3.f-cdn.com/img/ajax-loader.gif?v=62d3d0c60d4c33ef23dcefb9bc63e3a2&amp;m=6" style="display: none;">
							            </div>
							        </div>
							    </form>

						  	</div>

						</div>
					</div>
                </div> <!-- panel-body !-->
            </div>
        </div>
	</div>
</div>

<?php get_footer();?>
<script type="text/javascript">
    (function($){
       	$("#reset_password_form").submit(function(event){

	        var form    = $(event.currentTarget);
	        var send    = {};
	        form.find( 'input' ).each( function() {
	            var key     = $(this).attr('name');
	            send[key]   = $(this).val();
	        });

	        if( send['new_password'] != send['confirm_password'] ){
	        	alert('Passowrd not match');
	        	return false;
	        }
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
	                	$("#login_section").html('Your password is updated. Please relogin to access your account');
	                } else {
	                	$("#loginErrorMsg").html(res.msg);
	                	$("#loginErrorMsg").show();
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
	#loginErrorMsg{
		padding: 10px 0;
		margin: 0;
		font-size: 12px;
		text-indent: 15px;
		margin-bottom: 10px;
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
	    margin: 0px 0 20px;
	    text-align: center;
	    border-bottom: 0;
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
		font-weight: bold;
	}
	.forgetpass .btn{
		float: left;
		margin-top: 10px;
		margin-bottom: 15px;
	}
	.fre-reset-pw h2{
		padding-bottom: 15px;
		margin-top: 0;
	}
</style>