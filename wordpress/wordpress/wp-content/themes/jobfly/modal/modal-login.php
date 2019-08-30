<div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header login_modal_header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        		<h2 class="modal-title" id="myModalLabel"><?php _e('Login to Your Account','boxtheme');?></h2>
      		</div>
      		<div class="modal-body login-modal">
      			<div id='social-icons-conatainer'>
	        		<div class='modal-login-form'>
	        			<form  id="login-form" class="sign-in" method="post">
		        			<div class="form-group">
			              		<input type="text" id="username" name="user_login" placeholder="<?php _e('Username or Email','boxtheme');?>" value="" class="form-control login-field">
			              		<i class="fa fa-user login-field-icon"></i>
			            	</div>

			            	<div class="form-group">
			            	  	<input type="password" id="login-pass" name="user_password" placeholder="<?php _e('Password','boxtheme');?>" value="" class="form-control login-field">
			              		<i class="fa fa-lock login-field-icon"></i>
			            	</div>
			            	 <?php wp_nonce_field( 'bx_login', 'nonce_login_field' ); ?>
			            	<button type="submit" class="btn btn-success modal-login-btn"><?php _e('Login','boxtheme');?></button>
			            	<a href="#" class="login-link text-center"><?php _e('Lost your password?','boxtheme');?></a>
			            </form>
	        		</div>

	        		<div class='modal-login-social'>
	        			<div class="divider-line">
		        			<span class="line"></span>
		        			<div id='center-line'> <?php _e('OR','boxtheme');?> </div>
		        		</div>
	        			<?php bx_social_button_signup();?>
	        		</div>

	        	</div>
        		<div class="clearfix"></div>

        		<div class="form-group modal-register-btn">
        			<span href="#" class="login-link text-center">You don't have an account? <a href="<?php echo box_get_static_link('signup');?>"><?php _e('Register','boxtheme');?></a></span>
        		</div>
      		</div>
    	</div>
  	</div>
</div>