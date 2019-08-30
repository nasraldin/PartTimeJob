<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$pending_post = false;
$google_analytic = $copyright = $tw_link = $fb_link = $gg_link = '' ;
$group_option = "general";
$option = BX_Option::get_instance();
$general = $option->get_general_option();

$requires_confirm = box_requires_confirm();

?>

<div id="<?php echo $group_option;?>" class="main-group">
	<div class="sub-section " id="<?php echo $group_option;?>">
		<h2 class="section-title"><?php _e('Main options','boxtheme');?> </h2>
		<div class="full sub-item" id="pending_post" >
			<div class="col-md-3"><h3><?php _e('Pending jobs','boxtheme');?></h3></div>
			<div class="col-md-9">
				<div class="row">
						<?php bx_swap_button('pending_post', $general->pending_post, $multipe = false);?><br /><span><?php _e('if enable this option, all job only appearances in the site after admin manually approve it.','boxtheme');?></span>
					<div class="box-tooltip"> <strong>(?)</strong>
	 			 		<span class="tooltiptext">This option is not available if admin post job.</span>
					</div>
				</div>
			</div>
		</div>

		<div class="full" id="google_analytic">
			<div class="col-md-3"><h3><?php _e('Google Analytics Script','boxtheme');?></h3></div>
			 <div class="col-md-9 no-padding">
			 	<div class="no-label field-item">
			 		<textarea class="auto-save" level="0" name="google_analytic"><?php echo stripslashes($general->google_analytic);?></textarea>
			 	</div>
			 </div>
		</div>

		<div class="full">
			<div class="col-md-3"><h3><?php _e('Copyright text','boxtheme');?></h3></div> <div class="col-md-9  no-padding">
				<div class="no-label field-item">
					<textarea class="form-control auto-save" level="0"  name="copyright" ><?php echo stripslashes($general->copyright);?> </textarea>
				</div>
			</div>

		</div>
		<div class="full">
			<div class="col-md-3"><h3><?php _e('Social Links','boxtheme');?></h3><span><?php _e('List social link in the footer','boxtheme');?></span></div>
			<div class="col-md-9">

				<div class="form-group row">
					<div class="field-item">
						<label for="example-text-input" class=" field-label"><?php _e('Facebook Link','boxtheme');?></label>
						<input class="form-control auto-save" type="text" value="<?php echo $general->fb_link;?>"  level="0" name="fb_link" id="fb_link">
					</div>
				</div>

				<div class="form-group row">
					<div class="field-item">
						<label for="example-text-input" class="field-label"><?php _e('Twitter link','boxtheme');?></label>
						<input class="form-control auto-save" type="text" name="tw_link"  level="0"  value="<?php echo $general->tw_link;?>" id="tw_link">
					</div>
				</div>

				<div class="form-group row">
					<div class="field-item">
						<label for="example-text-input" class="field-label"><?php _e('Google Plus link','boxtheme');?></label>
						<input class="form-control auto-save" type="text" name="gg_link" level="0"  value="<?php echo $general->gg_link;?>" id="gg_link">
					</div>
			</div>
		</div>
	</div>
</div>
<?php
$group_option = "general";
$section = 'app_api';
$item1  = 'facebook';

$app_id = $app_secret = '';

$app_api = (OBJECT) BX_Option::get_instance()->get_app_api_option($general);
$facebook = (object) $app_api->facebook;
$google = (object) $app_api->google;
$gmap_key =  (string) $app_api->gmap_key;

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
  						<div class="field-item">
				    		<label for="app_id">APP ID</label>
				    		<input type="text" value="<?php echo $facebook->app_id;?>" level='2' class="form-control auto-save" name="app_id" id="app_id" aria-describedby="app_id" placeholder="Enter APP ID">
				    	</div>
				    </div>
			    	<span class="text-muted">Go to this <a  target="_blank" href="https://developers.facebook.com/apps/">link</a> and create new app then set the API for this section.</span>

			    </div>
		    	<div class="full field-item swich-field">
		    		<div class="form-group toggle-line"> <?php bx_swap_button( 'enable', $facebook->enable, 2 );?>   </div>
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
	  					<div class="field-item">
			    			<label for="client_id"><?php _e('Client ID','boxtheme');?></label>
			    			<input type="text" class="form-control auto-save" value="<?php echo $google->client_id;?>" level="2" name="client_id" id="client_id" aria-describedby="client_id" placeholder="Client ID">
			    		</div>
			    	</div>
			    	<span class="text-muted">Go to this <a  target="_blank" href="https://console.developers.google.com/apis/"> link </a> and create new api then get/set api for this section</span>
		    	</div>
		    	<div class="form-group toggle-line field-item swich-field">
		    		<?php bx_swap_button('enable', $google->enable, 2);?>
		    	</div>


	    	</div>
	  	</div>
	</div>
</div>
<div id="<?php echo $group_option;?>" class="main-group">
	<div class="sub-section" id="<?php echo $section;?>">

		<div class="sub-item" id="gmap_key">
		  	<div class="form-group row">
		  		<div class="col-md-3"><h3> Google Map API </h3></div>
		  		<div class="col-md-9 ">
	  					<div class="field-item">
			    			<label for="client_id" class="field-label"><?php _e('Google Map API KEY','boxtheme');?></label>
			    			<input type="text" class="form-control auto-save" value="<?php echo $gmap_key;?>" level="1" name="gmap_key" id="gmap_key" aria-describedby="Gmap Api Key" placeholder="Gmap API Key">
			    		</div>
			    		<span class="text-muted">Go to this <a  target="_blank" href="https://console.cloud.google.com/home/dashboard/">link</a> and create new api and set api for this section</span>

		    	</div>
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
// echo '<pre>';
// var_dump($gg_captcha);
// echo '</pre>';
$gg_enable =  0;
$lang_code ='en';
if( isset( $gg_captcha->enable)){
	$gg_enable =  $gg_captcha->enable;
}
if( isset( $gg_captcha->lang_code)){
	$lang_code =  $gg_captcha->lang_code;
}
$item3  = 'gg_captcha';
?>
<h2><?php _e('Google Captcha','boxtheme');?></h2>
<div id="<?php echo $group_option;?>" class="main-group">
<div class="sub-section" id="<?php echo $section;?>">
	<div class="sub-item" id="<?php echo $item3;?>">
	  	<div class="form-group row">
  			<div class="col-md-3"><h3><?php _e('Settings','boxtheme');?></h3></div>
  			<div class="col-md-9 ">
  				<div class="field-item">
			    	<label for="app_id"><?php _e('Site Key','boxtheme');?></label>
			    	<input type="text" value="<?php echo $site_key;?>" class="form-control auto-save" level="2" name="site_key" id="site_key" aria-describedby="site_key" placeholder="<?php _e('reCaptcha Site Key','boxtheme');?>">
		    	</div>
		    	<div class="field-item">
		    		<label for="app_id" class="field-label"><?php _e('Secret Key','boxtheme');?></label>
		    		<input type="text" value="<?php echo $secret_key;?>" class="form-control auto-save" level="2"  name="secret_key" id="secret_key" aria-describedby="secret_key" placeholder="<?php _e('reCaptcha Secret Key','boxtheme');?>">
		    	</div>
		    	<div class="field-item switch-field">
		    		<div class=" full"><?php bx_swap_button( 'enable', $gg_enable, 2);?></div>
		    		<div class="form-group toggle-line"><span><?php _e('Enable this to help your website security more and safe. Add captcha code in login form and in register form - <a target="_blank" href="https://www.google.com/recaptcha/admin#list" target="_blank" rel="nofollow">get key</a>','boxtheme');?> </span>
		    		</div>
		    	</div>
		    	<div class="full">
	  				<div class="full">
	  					<div class="field-item">
			    			<label for="client_id"><?php _e('Language codes','boxtheme');?></label>
			    			<input type="text" class="form-control auto-save" value="<?php echo $lang_code;?>" level="2" name="lang_code" id="lang_code" aria-describedby="lang_code" placeholder="Language codes">
			    		</div>
			    	</div>
			    	<span class="text-muted">Go to this <a  target="_blank" href="https://developers.google.com/recaptcha/docs/language"> link </a> to get a lagnuage code and set it here.</span>
		    	</div>
		    </div>
	    </div>
	</div>
</div>