<?php
global $role;
?>
<form id="signup" class="frm-signup">
	<div class="row" id="heading_signup">
	    <div class="col-md-12 align-center">
	        <h1><?php _e('SIGN UP','boxtheme');?></h1>
	    <h3><?php _e('Create your freelancer account in seconds and start bidding for a job now.','boxtheme');?></h3>
	    </div>
	</div>
	<div class="row">
	    <div class="form-group col-md-6">
	        <input class="form-control" type="text" name="first_name"  placeholder="<?php _e('First name','boxtheme');?> " id="example-text-input">
	    </div>
	    <div class="col-md-6 form-group">
	        <input class="form-control" type="text" name="last_name"   placeholder="<?php _e('Last name','boxtheme');?> " id="example-text-input">
	    </div>
	</div>

	<div class="row">
	    <div class="form-group col-md-12">
	        <input class="form-control"  name="user_login" required   placeholder="<?php _e('User name','boxtheme');?> " type="text"  >
	    </div>
	</div>
	<div class="row">
	    <div class="form-group col-md-12">
	        <input class="form-control"  name="user_email" required  type="email" placeholder="<?php _e('Your email','boxtheme');?> " >
	    </div>
	</div>
	<div class="row">
	    <div class="form-group col-md-12">
	        <input class="form-control"  name="user_pass" required  type="password" placeholder="<?php _e('Password','boxtheme');?> ">
	    </div>
	    <?php signup_nonce_fields(); ?>
	</div>


	<div class="form-group row">
		<div class="col-md-12">
		    <button class="btn btn-primary btn-xlarge" type="submit">SIGN UP</button>
		</div>

	</div>

</form>