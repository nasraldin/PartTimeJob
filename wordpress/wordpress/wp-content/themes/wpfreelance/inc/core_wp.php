<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_filter( 'get_site_icon_url', 'box_set_favicon', 10 ,3 );
function box_set_favicon( $url, $size, $blog_id ) {
	if( empty($url) )
		$url = get_template_directory_uri().'/ico.png';
	return $url;
}
add_action( 'wp_head', 'box_add_meta_head', 99);
function box_add_meta_head(){

	global $box_general, $app_api, $main_img;

	$gg_captcha = (object) $app_api->gg_captcha;
	$enable = (int) $gg_captcha->enable;

	if( ! empty ( $box_general->google_analytic ) ){
		echo stripslashes($box_general->google_analytic);
	}
	if( is_home() || is_front_page() ){
		$main_img = get_theme_mod('main_img',  get_template_directory_uri().'/img/banner.jpg' ); // only query in home page;
		?>
		<meta property="og:image" content="<?php echo $main_img;?>">
		<?php
	}
	if( is_singular( PROJECT ) ){
		if( have_posts() ){

			//facebook meta tag ?>
			<meta property="og:url"           content="<?php echo get_permalink();?>" />
			<meta property="og:type"          content="website" />
			<meta property="og:title"         content="<?php echo get_the_title();?>" />
			<meta property="og:description"   content="<?php echo wp_trim_words( get_the_content(), 300); ?>" />
			<!--<meta property="og:image"         content="http://www.your-domain.com/path/image.jpg" /> !-->
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.10";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<script src="https://apis.google.com/js/platform.js" async defer></script>
			<?php // wp_reset_query();
		}
	}
	//end facebook meta
	if ( $enable && ! empty ( $gg_captcha->site_key ) ) {
		$code = 'en';
		if( isset( $gg_captcha->lang_code) )
			$code = trim($gg_captcha->lang_code);
		// code: https://developers.google.com/recaptcha/docs/language
		$api_url = 'https://www.google.com/recaptcha/api.js';
		if( ! empty( $code ) )
			$api_url = $api_url.'?hl='.$code;

	 ?>
		<script src="<?php echo $api_url;?>" async defer></script><?php
	}

}
add_action('wp_footer','box_footer_script', 99);
function box_footer_script(){
	if( is_singular( PROJECT ) ){ ?>
		<script type="text/javascript">
				(function($){
					$('.popup').click(function(event) {

					    var width  = 575,
					        height = 400,
					        left   = ($(window).width()  - width)  / 2,
					        top    = ($(window).height() - height) / 2,
					        url    = this.href,
					        opts   = 'status=1' +
					                 ',width='  + width  +
					                 ',height=' + height +
					                 ',top='    + top    +
					                 ',left='   + left;

					    window.open(url, 'twitter', opts);

					    return false;
					  });
				})(jQuery);
			</script>

	<?php }
}