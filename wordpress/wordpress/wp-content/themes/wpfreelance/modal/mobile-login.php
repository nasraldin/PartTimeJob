<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content no-radius">
            <div class="modal-body">
                <h6><?php _e('Member Login','boxtheme');?></h6>
                <form class="form-material sign-in  mt20" id="login-form" method="post">
                    <div id="login-error" class="alert alert-danger error"></div>
                    <div class="block">
                        <input type="text" class="form-control no-radius" name="user_login" placeholder="<?php _e('Email / Username','boxtheme');?>" required>
                    </div>
                    <div class="block mt10">
                        <input type="password" class="form-control no-radius" name="user_password" placeholder="<?php _e('Password','boxtheme');?>" required>
                    </div>
                    <?php wp_nonce_field( 'bx_login', 'nonce_login_field' ); ?>

                    <button id="login-submit" type="submit" class="btn btn-block btn-lg btn-go mt30 no-radius"><?php _e('Login','boxtheme');?></button>
                    <div class="loading-item" id="login-loading" style="display: none;"><div class="loadinghdo"></div></div>
                    <div class="login-via block">
                        <span class="f-left"><a onclick="show_forgot()" title="Forgot password?"><?php _e('Forgot password?','boxtheme');?></a></span>
                        <span class="f-right"><a class="link-signup"  href="<?php echo box_get_static_link('signup');?>" title="<?php _e('New Member','boxtheme');?>"><?php _e('New Member','boxtheme');?></a></span>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Close','boxtheme');?></button>
            </div>
        </div>
    </div>
</div>