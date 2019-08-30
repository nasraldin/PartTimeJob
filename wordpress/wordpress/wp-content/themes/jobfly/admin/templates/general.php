<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$pending_post = false;
$google_analytic = $copyright = $tw_link = $fb_link = $gg_link = '' ;
$group_option = "general";
$option = BX_Option::get_instance();
$general = $option->get_general_option();

// echo '<pre>';
// var_dump($general);
// echo '</pre>';
?>

<div id="<?php echo $group_option;?>" class="main-group">
	<div class="sub-section " id="<?php echo $group_option;?>">
		<h2 class="section-title"><?php _e('Main options','boxtheme');?> </h2>
		<div class="full sub-item" id="pending_post" >
			<div class="col-md-3"><h3><?php _e('Pending jobs','boxtheme');?></h3></div>
			<div class="col-md-9"><?php bx_swap_button($group_option,'pending_post', $general->pending_post, $multipe = false);?><br /><span><?php _e('if enable this option, all job only appearances in the site after admin manually approve it.','boxtheme');?></span></div>

		</div>
		<div class="full" id="google_analytic">
			<div class="col-md-3"><h3><?php _e('Google Analytics Script','boxtheme');?></h3></div> <div class="col-md-9 no-padding"><textarea class="auto-save" level="0" name="google_analytic"><?php echo stripslashes($general->google_analytic);?></textarea></div>
		</div>
		<div class="full">
			<div class="col-md-3"><h3><?php _e('Copyright text','boxtheme');?></h3></div> <div class="col-md-9  no-padding"><textarea class="form-control auto-save" level="0"  name="copyright" ><?php echo stripslashes($general->copyright);?> </textarea></div>
		</div>
		<div class="full">
			<div class="col-md-3"><h3><?php _e('Social Links','boxtheme');?></h3><span><?php _e('List social link in the footer','boxtheme');?></span></div>
			<div class="col-md-9">

				<div class="form-group row">
					<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Facebook link','boxtheme');?></label>
					<input class="form-control auto-save" type="text" value="<?php echo $general->fb_link;?>"  level="0" name="fb_link" id="fb_link">
				</div>

				<div class="form-group row">
					<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Twitter link','boxtheme');?></label>
					<input class="form-control auto-save" type="text" name="tw_link"  level="0"  value="<?php echo $general->tw_link;?>" id="tw_link">
				</div>

				<div class="form-group row">
					<label for="example-text-input" class="col-md-4 col-form-label"><?php _e('Google Plus link','boxtheme');?></label>
					<input class="form-control auto-save" type="text" name="gg_link" level="0"  value="<?php echo $general->gg_link;?>" id="gg_link">
			</div>
		</div>
	</div>
</div>
<?php
$group_option = "general";
$section = 'app_api';
$item1  = 'facebook';
$item2  = 'google';
$app_id = $app_secret = '';

$app_api = (OBJECT) BX_Option::get_instance()->get_app_api_option($general);
$facebook = (object) $app_api->facebook;
$google = (object) $app_api->google;

?>
<h2><?php _e('Social Login','boxtheme');?></h2>
<div id="<?php echo $group_option;?>" class="main-group">
<div class="sub-section" id="<?php echo $section;?>">
		<div class="sub-item" id="<?php echo $item1;?>">
	  	<div class="form-group row">
  			<div class="col-md-3"><h3> Facebook Login API </h3></div>
  			<div class="col-md-9 form-group">
  				<div class="full">
  					<div class="full">
				    	<label for="app_id">APP ID</label>
				    	<input type="text" value="<?php echo $facebook->app_id;?>" level='2' class="form-control auto-save" name="app_id" id="app_id" aria-describedby="app_id" placeholder="Enter APP ID">
				    </div>
			    	<span class="text-muted">Go to this <a  target="_blank" href="https://developers.facebook.com/apps/">link</a> and create new app then set the API for this section.</span>

			    </div>
		    	<div class="full">
		    		<div class="form-group toggle-line"> <?php bx_swap_button( $group_option, 'enable', $facebook->enable, 2 );?>   </div>
		    	</div>
		    </div>
	    </div>

	</div>
	<div class="sub-item" id="google">
	  	<div class="form-group row">
	  		<div class="col-md-3"><h3> Google Login API </h3></div>
	  		<div class="col-md-9 ">
	  			<div class="full">
	  				<div class="full">
			    	<label for="client_id"><?php _e('Client ID','boxtheme');?></label>
			    	<input type="text" class="form-control auto-save" value="<?php echo $google->client_id;?>" level="2" name="client_id" id="client_id" aria-describedby="client_id" placeholder="Client ID">
			    	</div>
			    	<span class="text-muted">Go to this <a  target="_blank" href="https://console.developers.google.com/projectselector/apis/library?pli=1">link</a> and create new api and set api for this section</span>
		    	</div>
		    	<div class="form-group toggle-line">
		    		<?php bx_swap_button($group_option,'enable', $google->enable, 2);?>

		    	</div>
		    	<div class="form-group toggle-line"><span class="text-muted clear"> Enable or disable this option</span></div>
	    	</div>
	  	</div>
	</div>
</div>
<?php
$gg_captcha = (object) $app_api->gg_captcha;
$site_key = $secret_key = "";
if( isset( $gg_captcha->site_key)){
	$site_key =  $gg_captcha->site_key;
}
if( isset( $gg_captcha->secret_key)){
	$secret_key =  $gg_captcha->secret_key;
}
$gg_enable = 0;
if( isset( $gg_captcha->enable)){
	$gg_enable =  $gg_captcha->enable;
}
$item3  = 'gg_captcha';
?>
<h2><?php _e('Google Captcha','boxtheme');?></h2>
<div id="<?php echo $group_option;?>" class="main-group">
<div class="sub-section" id="<?php echo $section;?>">
	<div class="sub-item" id="<?php echo $item3;?>">
	  	<div class="form-group row">
  			<div class="col-md-3"><h3><?php _e('Settings','boxtheme');?></h3></div>
  			<div class="col-md-9 form-group">
  				<div class="form-group">
			    	<label for="app_id"><?php _e('reCaptcha Site Key','boxtheme');?></label>
			    	<input type="text" value="<?php echo $site_key;?>" class="form-control auto-save" level="2" name="site_key" id="site_key" aria-describedby="site_key" placeholder="<?php _e('reCaptcha Site Key','boxtheme');?>">
		    	</div>
		    	<div class="form-group">
		    		<label for="app_id"><?php _e('reCaptcha Secret Key','boxtheme');?></label>
		    		<input type="text" value="<?php echo $secret_key;?>" class="form-control auto-save" level="2"  name="secret_key" id="secret_key" aria-describedby="secret_key" placeholder="<?php _e('reCaptcha Secret Key','boxtheme');?>">
		    	</div>
		    	<div class="form-group">
		    		<div class="form-group toggle-line">  	<?php bx_swap_button($group_option, 'enable', $gg_enable, 2);?>   </div>
		    		<div class="form-group toggle-line"><span><?php _e('Enable this to help your website security more and safe. Add captcha code in login form and in register form - <a target="_blank" href="https://www.google.com/recaptcha/admin#list" target="_blank" rel="nofollow">get key</a>','boxtheme');?> </span> </div>
		    	</div>
		    </div>
	    </div>
	</div>
</div>