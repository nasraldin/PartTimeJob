 <?php
/**
 * Template Name: Page Signup
*/
get_header();
$fre_checked = $emp_checked = '';;

$role = isset($_GET['role']) ? $_GET['role'] : '';
$plan = isset($_GET['plan']) ? (int) $_GET['plan'] : 0;
if( $role == 'hire' ){
    $emp_checked = 'checked';
} else  if($role == 'work') {
    $fre_checked = 'checked';
}
if($plan > 0){
    $fre_checked = 'checked';
}

global $box_general;
$singup_avatar_field = $box_general->singup_avatar_field;

?>
<?php box_map_autocomplete_script();?>
  <!-- List job !-->
    <div class="container site-container">
        <h1 class="center text-center"><?php _e('SIGN UP','boxtheme');?></h1>
        <div id="main_signup" class="sign-block col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 col-xs-12 col-xs-offset-0">
            <form class="frm-signup"  id="signup" >
                <div id="loginErrorMsg"  class="loginErrorMsg alert alert-error alert-warning hide">

                </div>
                <?php if($singup_avatar_field){ ?>
                    <div class="form-row">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-6 text-center signup-avatar">
                            <img class="avatar-img" src="<?php echo get_template_directory_uri();?>/img/avatar_login.png">
                            <div class="form-group ">
                                <div id="fileupload-container" class="file-uploader-area">
                                    <span class="btn-file-uploader">
                                        <span class="fl-icon-plus"></span>
                                        <input type="hidden" class="nonce_upload_field" name="nonce_upload_field" value="<?php echo wp_create_nonce( 'box_upload_file' ); ?>" />
                                        <span id="file-upload-button-text " class="text-color wrap-btn-upload">
                                            <input type="file" name="upload[]" id="sp-upload" multiple="" class="fileupload-input">
                                            <i class="fa fa-plus text-color" aria-hidden="true"></i> <span><?php _e('Upload Avatar','boxtheme');?></span></span>
                                        <i class='fa fa-spinner fa-spin '></i>
                                    </span>
                                    <input type="url" name="avatar_url" value="" id="avatar_url" style="opacity: 0;" required class="upload-fileset avatar_url" oninvalid="this.setCustomValidity('Please Set Your Avatar empty')" >
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" id="firstName" name="first_name" placeholder="<?php _e('First Name','boxtheme');?>">
                    </div>
                    <div class="form-group col-md-6 col-signup-lastname" >
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?php _e('Last Name','boxtheme');?>">
                    </div>
                </div>
                 <div class="form-row">
                    <div class="col-md-12">
                        <input id="autocomplete" placeholder="<?php _e('Enter your address','boxtheme');?>" name="address" onFocus="geolocate()" type="text" class="form-control" />
                        <?php box_map_field_auto();?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="user_login" id="user_login" required placeholder="<?php _e('User Name','boxtheme');?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12">
                        <input type="email" class="form-control" id="user_email" required name="user_email" placeholder="<?php _e('Email','boxtheme');?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12">
                        <input type="password" class="form-control" id="user_pass" required name="user_pass" placeholder="<?php _e('Password','boxtheme');?>">
                    </div>
                </div>
                <?php signup_nonce_fields(); ?>
                 <div class="form-row form-role-row">
                    <div class="form-group col-md-6 col-xs-6">
                        <label><input type="radio" name="role" value="employer" <?php echo $emp_checked;?> required><span><?php _e('Hire','boxtheme');?></span></label>
                    </div>
                    <div class="form-group col-md-6 col-xs-6">
                        <label><input type="radio"  name="role" value="freelancer"  <?php echo $fre_checked;?> required><span><?php _e('Work','boxtheme');?></span></label>
                    </div>
                </div>
                <?php
                box_add_captcha_field();
                $tos_link =box_get_static_link('tos');
                ?>
                <div class="form-row">
                   <div class="col-md-12 tos-row">
                        <label>
                            <span>
                                <input type="checkbox" name="tos" id="tos" class="required" required>
                            </span>
                            <?php printf(__('By signing up, you are agreeing to our <a href="%s" target="_Blank">Terms of Service and Privacy Policies</a>.','boxtheme'),$tos_link);?>
                        </label>
                    </div>
                </div>
                <input type="hidden" name="plan" value="<?php echo $plan;?>">
                <div class="form-row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn btn-success btn-block btn-submit"> <?php _e('Sign Up','boxtheme'); ?> &nbsp; <i class="fa fa-spinner fa-spin"></i></button>
                    </div>
                </div>
                <?php bx_social_button_signup() ?>
            </form>
        </div> <!-- end sign_up !-->
    </div>
    <!-- End List Job !-->

<?php
global $app_api;
$gmap_key =  $app_api->gmap_key;
?>

<script src="//maps.google.com/maps/api/js?key=<?php echo $gmap_key;?>&libraries=places&callback=initAutocomplete" type="text/javascript"></script>
 <?php if($singup_avatar_field){ ?>
    <script type="text/javascript">
    (function($){
        var nonce = $("#fileupload-container").find('.nonce_upload_field').val();
            var uploader = new plupload.Uploader({
                runtimes: 'html5,gears,flash,silverlight,browserplus,html4',
                multiple_queues: true,
                multipart: true,
                urlstream_upload: true,
                multi_selection: false,
                upload_later: false,

                browse_button : 'sp-upload', // you can pass in id...
                container: document.getElementById('fileupload-container'), // ... or DOM Element itself
                url : bx_global.ajax_url,
                filters : {
                    max_file_size : '10mb',
                    mime_types: [
                        {title : "Image files", extensions : "jpg,gif,png,jpeg"},
                    ]
                },
                multipart_params: {
                    action: 'box_upload_avatar',
                    nonce_upload_field: nonce,

                },
                init: {
                    PostInit: function() {


                    },
                    BeforeUpload: function(up, file) {
                        $(up.settings.container).addClass('uploading');
                        up.disableBrowse(true);
                    },
                    FilesAdded: function(up, files) {
                        //up.disableBrowse(true);
                    },

                    Error: function(up, err) {
                        console.log(err);
                        //document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                    },
                    FileUploaded : function(up, file, response){
                        var obj = jQuery.parseJSON(response.response);
                        if(obj.success){
                            //avatar-img
                            var src = obj.file.guid;
                            $(".avatar-img").attr('src',src);
                            $('#avatar_url').val(src);
                            $('#avatar_url').attr('value',src);
                            $('#avatar_url').removeAttr('required');

                        } else{
                            alert(obj.msg);
                        }

                        setTimeout(function(){ $(up.settings.container).removeClass('uploading'); }, 100);
                        up.disableBrowse(false);
                    }
                }
            });
            uploader.init();
            uploader.bind('FilesAdded', function(up, files) {
                //view.$el.find("i.loading").toggleClass("hide");
                up.refresh();
                up.start();
            });
    })( jQuery, window.ajaxSend );

    </script>
<?php }?>
<?php get_footer(); ?>