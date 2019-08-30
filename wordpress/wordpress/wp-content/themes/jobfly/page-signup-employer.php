 <?php
/**
 * Template Name: Page Employer Signup
*/
?>
<?php get_header(); ?>
<div class="full-width fw-min-height">
	<div class="container">
	    <div class="row m-lg-top m-xlg-bottom">
	        <div class="col-md-12 m-lg-bottom page-auth pb50">
	        <?php if( ! is_user_logged_in() ) { ?>
				<form id="signup" class="frm-signup">
					<div class="row" id="heading_signup">
					    <div class="col-md-12 align-center">
					        <h1><?php _e('SIGN UP','boxtheme');?></h1>
					    	<h3><?php _e('Create a employer account in seconds and post a job right now','boxtheme');?></h3>
					    </div>
					</div>
					<div class="row">
					    <div class="form-group col-md-12">
					        <input class="form-control" type="text" name="first_name"  placeholder="<?php _e('First Name','boxtheme');?> " id="example-text-input">
					    </div>
					    <div class="col-md-12 form-group">
					        <input class="form-control" type="text" name="last_name"   placeholder="<?php _e('Last Name','boxtheme');?> " id="example-text-input">
					    </div>
					</div>

					<div class="row">
					    <div class="form-group col-md-12">
					        <input class="form-control"  name="user_login" required   placeholder="<?php _e('User Name','boxtheme');?> " type="text"  >
					    </div>
					</div>
					<div class="row phone-number hide">
					    <div class="form-group col-md-12">
					        <input class="form-control"  name="phone_number" value="9999999" required  type="text" placeholder="<?php _e('Your Phone Number','boxtheme');?> " >
					    </div>
					</div>
					<div class="row">
					    <div class="form-group col-md-12">
					        <input class="form-control"  name="user_email" required  type="email" placeholder="<?php _e('Your Email','boxtheme');?> " >
					    </div>
					</div>
					<div class="row">
					    <div class="form-group col-md-12">
					        <input class="form-control"  name="user_pass" required  type="password" placeholder="<?php _e('Password','boxtheme');?> ">
					    </div>
					</div>
					<input type="hidden" name="role" value="<?php echo EMPLOYER;?>">
					<div id="loginErrorMsg" class="alert alert-error alert-warning hide"></div>
					<?php signup_nonce_fields(); ?>
					<div class="row">
					 	<div class="form-group col-md-12">
	                          <label class="lb-checkbox"><input type="checkbox" required   name="agree"> <?php printf(__('Yes, I understand and agree <a href="%s" target="_blank">All Terms of Service</a>','boxtheme'),home_url('terms'));?>.</label>
	                    </div>
	                </div>
					<div class="form-group row">
						<div class="col-md-12">	<button class="btn btn-action btn-xlarge" type="submit"><?php _e('SIGN UP','boxtheme');?></button> </div>
					</div>

				</form>
			<?php } else { ?>
			<?php _e(' This page only available for visitor','boxtheme');?>
			<?php } ?>
	        </div>
	    </div>
	</div>
</div>
<script type="text/javascript">

	(function($){
		$("input:radio").click(function(event){
			var button = $(event.currentTarget).attr('id');
			var strclass = "."+button+"";
			$(".tab-signup").hide();
			$(strclass).toggle();
		});
	})( jQuery );

</script>
<style type="text/css">
	.alert{
		margin-bottom: 10px;
		padding: 10px;
	}
</style>
<?php get_footer(); ?>