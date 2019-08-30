 <?php
/**
 * Template Name: Page Signup
*/
 get_header();
 ?>
 	<!-- List job !-->
    <div class="container site-container">
         <h1 class="center text-center"> SIGN UP</h1>
        <div id="main_signup" class="sign-block col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 col-xs-12 col-xs-offset-0">
            <form class="frm-signup"  id="signup" >
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" id="firstName" name="first_name" placeholder="First Name">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?php _e('Last Name','boxtheme');?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="user_login" id="user_login" required placeholder="<?php _e('User Name','boxtheme');?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <input type="email" class="form-control" id="user_email" required name="user_email" placeholder="<?php _e('Your Email','boxtheme');?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <input type="password" class="form-control" id="user_pass" required name="user_pass" placeholder="<?php _e('Password','boxtheme');?>">
                    </div>
                </div>
                 <div class="form-row form-role-row">
                    <div class="form-group col-md-6 col-xs-6">
                        <label><input type="radio" name="role" value="employer" <?php echo $emp_checked;?> required><span><?php _e('Company','boxtheme');?></span></label>
                    </div>
                    <div class="form-group col-md-6 col-xs-6">
                        <label><input type="radio"  name="role" value="freelancer"  <?php echo $fre_checked;?> required><span><?php _e('Personal','boxtheme');?></span></label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn btn-success btn-block btn-submit"> <?php _e('Sign Up','boxtheme'); ?></button>
                    </div>
                </div>
                <div class="form-row">
                    <div class="hr-or"><center>or</center></div>
                    <div class="col-md-12 social-login">
                        <?php bx_social_button_signup() ?>
                    </div>
                </div>

            </form>
        </div> <!-- end sign_up !-->
    </div>
    <!-- End List Job !-->
<?php get_footer(); ?>