<?php

function box_price($price,$echo = true){
	echo get_box_price($price);
}
function get_box_price( $price ) {
	global $box_currency;

	$price = floatval($price);
	$decimals = 2;
	$price_decimal_sep = $box_currency->price_decimal_sep;
	$price_thousand_sep = $box_currency->price_thousand_sep;

	number_format( $price, $decimals, $price_decimal_sep, $price_thousand_sep );

	$symbol = box_get_currency_symbol($box_currency->code);

	$string = $price.'<span class="currency-icon">('.$symbol.') </span>';

	return  $string;
}

/**
 * this function get the float number only withouth currency  symbol
 * This is a cool function
 * @author danng
 * @version 1.0
 * @return  [type] [description]
 */
function boxtrim_zeros( $price, $price_decimal_sep ) {
	return preg_replace( '/' . preg_quote( $price_decimal_sep, '/' ) . '0++$/', '', $price );
}
function box_get_price( $price ){
	global $box_currency;
	$decimals = 2;
	//return floatval(number_format( $price, $decimals, $box_currency->price_decimal_sep, $box_currency->price_thousand_sep ) );
	$price =  number_format( $price, $decimals, $box_currency->price_decimal_sep, $box_currency->price_thousand_sep );
	return boxtrim_zeros( $price, $box_currency->price_decimal_sep );
}

/** this function will be return float number with the symbol */

function box_get_price_format($price ){

	global $box_currency ;

	$symbol = box_get_currency_symbol($box_currency->code);
	$price = floatval($price);
	$string ='<span class="currency-icon">('.$symbol.')</span>'. $price;

	if( $box_currency->position == 'right' ){
		$string = $price. '<span class="currency-icon">('.$symbol.')</span>';
	} else if($box_currency->position == 'left_space'){

		$string = '<span class="currency-icon">('.$symbol.') </span>' . $price;
	}
	else if($box_currency->position == 'right_space'){
		$string = $price.'<span class="currency-icon"> ('.$symbol.')</span>' ;
	}

	return  $string;
}

function bx_list_start($score){ ?>
	<start class="rating-score clear block score-<?php echo $score;?>">
		<i class="fa fa-star" aria-hidden="true" title="1"></i>
    	<i class="fa fa-star" aria-hidden="true" title="2"></i>
    	<i class="fa fa-star" aria-hidden="true" title="3"></i>
    	<i class="fa fa-star" aria-hidden="true" title="4"></i>
    	<i class="fa fa-star" aria-hidden="true" title="5"></i>
	</start>
	<?php
}
if( !function_exists('box_get_static_link')):

	function box_get_static_link($page_args, $create = false){

		$slug = $page_args;
		if( is_array($page_args) ){
			$slug = $page_args['page_template'];
		}
		$name = "page-{$slug}-link";
		$link = wp_cache_get($name, 'static_link');

		if ( false !== $link ) {
			return $link;
		}
		$page = get_pages( array(
			            'meta_key' 		=> '_wp_page_template',
			            'meta_value' 	=> 'page-' . $slug . '.php',
			            'numberposts' 	=> 1,
			            'post_status' => 'publish',
			            //'hierarchical' 	=> 0,
			        ));
		$id = 0;
		if( empty($page) ){
			$args  = array(
				'post_title' => $slug,
				'post_type' => 'page',
				'post_status' => 'publish',
			);
			$id = wp_insert_post($args);
			update_post_meta($id,'_wp_page_template','page-' . $slug . '.php' );
		} else {
			$page = array_shift($page);
	        $id = $page->ID;
		}
		$link = get_permalink($id);
		wp_cache_set( $name, $link, 'static_link');
	    return $link;
	}
endif;

function box_editor_settings() {
	return apply_filters( 'box_editor_settings', array(
		'quicktags'     => true,
		'media_buttons' => false,
		'wpautop'       => true,

		//'tabindex'    =>  '2',
		'teeny'         => true,
		'tinymce'       => array(
			'height'                            => 150,
			'editor_class'                      => 'input-item',
			'autoresize_min_height'             => 150,
			'autoresize_max_height'             => 550,
			'theme_advanced_buttons1'           => 'bold,|,italic,|,underline,|,bullist,numlist,|,link,unlink,|,wp_fullscreen',
			'theme_advanced_buttons2'           => '',
			'theme_advanced_buttons3'           => '',
			'theme_advanced_statusbar_location' => 'none',
			'theme_advanced_resizing'           => true,
			'paste_auto_cleanup_on_paste'       => true,
			'setup'                             => "function(ed){
                ed.onChange.add(function(ed, l) {
                    var content = ed.getContent();
                    if(ed.isDirty() || content === '' ){
                        ed.save();
                        jQuery(ed.getElement()).blur(); // trigger change event for textarea
                    }

                });

                // We set a tabindex value to the iframe instead of the initial textarea
                ed.onInit.add(function() {
                    var editorId = ed.editorId,
                        textarea = jQuery('#'+editorId);
                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
                    textarea.attr('tabindex', null);
                });
            }"
		)
	) );
}
function box_editor_settings_front() {
	return apply_filters( 'box_editor_settings', array(
		'quicktags'     => true,
		'media_buttons' => false,
		'wpautop'       => false,

		//'tabindex'    =>  '2',
		'teeny'         => false,
		'tinymce'       => array(
			'height'                            => 235,
			'editor_class'                      => 'input-item',
			'autoresize_min_height'             => 150,
			'autoresize_max_height'             => 550,
			'theme_advanced_buttons1'           => 'bold,|,italic,|,underline,|,bullist,numlist,|,link,unlink,|,wp_fullscreen',
			'theme_advanced_buttons2'           => '',
			'theme_advanced_buttons3'           => '',
			'theme_advanced_statusbar_location' => 'none',
			'theme_advanced_resizing'           => true,
			'paste_auto_cleanup_on_paste'       => true,
			'setup'                             => "function(ed){
                ed.onChange.add(function(ed, l) {
                    var content = ed.getContent();
                    if(ed.isDirty() || content === '' ){
                        ed.save();
                        jQuery(ed.getElement()).blur(); // trigger change event for textarea
                    }

                });

                // We set a tabindex value to the iframe instead of the initial textarea
                ed.onInit.add(function() {
                    var editorId = ed.editorId,
                        textarea = jQuery('#'+editorId);
                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
                    textarea.attr('tabindex', null);
                });
            }"
		)
	) );
}
function force_default_editor() {
    return 'tinymce';
}
add_filter( 'wp_default_editor', 'force_default_editor' );
function box_get_response(  $captcha_response) {
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	global $app_api;

	$gg_captcha = (object) $app_api->gg_captcha;
	$enable = (int) $gg_captcha->enable;

	if ( !$enable || ! empty ( $gg_captcha->site_key ) ) {
		return true;
	}
	$args = array(
		'body' => array(
			'secret'   => $gg_captcha->secret_key,
			'response' => stripslashes( esc_html( $captcha_response ) ),
			'remoteip' => $remote_ip,
		),
		'sslverify' => is_ssl(),
	);
	$resp = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $args );

	$response = json_decode( wp_remote_retrieve_body( $resp ), true );

	if ( isset( $response['success'] ) && !!$response['success'] ) {

		return true;
	}
	return new WP_Error( 'gglcptch_error', __('Captcha Invalid','boxtheme') );
}
function box_add_captcha_field(){
	global $app_api;

	$gg_captcha = (object) $app_api->gg_captcha;
	$enable = (int) $gg_captcha->enable;

	if ( $enable && ! empty ( $gg_captcha->site_key ) ) { ?>
		<div class="g-recaptcha" data-sitekey="<?php echo $gg_captcha->site_key;?>"></div>
	<?php }
}
function box_social_share(){ ?>
	<ul class="social-shares">
		<li class="share-item fb-share">
			<a href="#"><center><i class="fa fa-facebook" aria-hidden="true"></i>&nbsp; <span>Share</span> </center></a>
			<div class="fb-invisi">
		  		<div class="fb-share-button " data-href="<?php the_permalink();?>" data-size="small" data-mobile-iframe="true">
		  			<a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php get_permalink();?>&amp;src=sdkpreparse"></a>
		  		</div>
	  		</div>
  		</li>
		<li class="share-item gplus-share">
			<a href="#"><center><i class="fa fa-google-plus" aria-hidden="true"></i><span> &nbsp; +1</span> </center></a>
			<div class="fb-invisi">
				<!-- <g:plusone ></g:plusone> !-->
				<g:plus action="share" data-expandTo ="top"></g:plus>
			</div>

		</li>

		<li class="share-item  tw-share">
			<a href="#"><center><i class="fa fa-twitter" aria-hidden="true"></i><span> &nbsp; Tweet</span></center></a>
			<div class="fb-invisi"><a class="twitter popup" href="http://twitter.com/share">Tweet</a></div>
		</li>

	</ul> <?php
}

 function box_upload_file($tmp_file, $author_id = 1, $post_parent = 0){

	$post_parent_id = 0;

	//$tmp_file['name'] = abc.jpg
	//var_dump($tmp_file);
	/*array(5) {
	  ["name"]=>
	  string(7) "abc.jpg"
	  ["type"]=>
	  string(10) "image/jpeg"
	  ["tmp_name"]=>
	  string(24) "D:\Xampp\tmp\php6193.tmp"
	  ["error"]=>
	  int(0)
	  ["size"]=>
	  int(220445)
	}
	*/
	//do_action( 'box_authentication_upload' );
	$allowed =  array('gif','png' ,'jpg', 'pdf','rtf','doc','docx');
	$filename = $tmp_file['name'];

	$ext = pathinfo($filename, PATHINFO_EXTENSION);

	if( ! in_array($ext,$allowed) ) {
		return false;
	}

	$post_parent_id = $post_parent;

    $upload_overrides = array( 'test_form' => false );

    require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$uploaded_file 	= wp_handle_upload( $tmp_file, $upload_overrides );

	// Get the path to the upload directory.
	$wp_upload_dir = wp_upload_dir();
    //if there was an error quit early
    if ( isset( $uploaded_file['error'] ) ) {
    	wp_send_json( array('success'=> false, 'msg' => $uploaded_file['error'] ) );
    } elseif ( isset($uploaded_file['file']) ) {
        // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
        $file_name_and_location = $uploaded_file['file'];
        // Generate a title for the image that'll be used in the media library
        $file_kb = (float)  round( $tmp_file['size']/1024, 1);
        if( $file_kb >= 1024 ){
        	$file_kb = round($tmp_file['size']/1024/1024, 0) . ' mb';
        } else{
        	$file_kb .= ' kb';
        }

        $file_title_for_media_library = sanitize_file_name($tmp_file['name']) . '('. $file_kb.')';
        $wp_upload_dir = wp_upload_dir();

        // Set up options array to add this file as an attachment
        global $user_ID;
        $attachment = array(
            'guid' => $uploaded_file['url'],
            'post_mime_type' => $uploaded_file['type'],
            'post_title' => $file_title_for_media_library,
            'post_content' => '',
            'post_status' => 'inherit',
            'post_author' => $user_ID
        );

        // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.

        $attach_id = wp_insert_attachment($attachment, $file_name_and_location, $post_parent_id);

        if( !is_wp_error($attach_id) ) {
        	$attachment['id'] = $attach_id;
        	require_once (ABSPATH . "wp-admin" . '/includes/image.php');
        	$attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);
    	    wp_update_attachment_metadata($attach_id, $attach_data);
    	}
    	return $attach_id;

	}
}
function box_get_avatar($id_or_email, $is_home = 0 ){
	$args = array(
		'width' => 170,
		'height' => 89,
	);
	if( $is_home)
	$args = array(
		'width' => 65,
		'height' => 65,
	);
	return get_avatar($id_or_email, $size = 96, $default = '', $alt = '', $args  );
}
function box_get_currency_symbol( $code = ''){

	$symbols = array('AED' => '&#x62f;.&#x625;',
		'AFN' => '&#x60b;',
		'ALL' => 'L',
		'AMD' => 'AMD',
		'ANG' => '&fnof;',
		'AOA' => 'Kz',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&fnof;',
		'AZN' => 'AZN',
		'BAM' => 'KM',
		'BBD' => '&#36;',
		'BDT' => '&#2547;&nbsp;',
		'BGN' => '&#1083;&#1074;.',
		'BHD' => '.&#x62f;.&#x628;',
		'BIF' => 'Fr',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => 'Bs.',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTC' => '&#3647;',
		'BTN' => 'Nu.',
		'BWP' => 'P',
		'BYR' => 'Br',
		'BZD' => '&#36;',
		'CAD' => '&#36;',
		'CDF' => 'Fr',
		'CHF' => '&#67;&#72;&#70;',
		'CLP' => '&#36;',
		'CNY' => '&yen;',
		'COP' => '&#36;',
		'CRC' => '&#x20a1;',
		'CUC' => '&#36;',
		'CUP' => '&#36;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => 'Fr',
		'DKK' => 'DKK',
		'DOP' => 'RD&#36;',
		'DZD' => '&#x62f;.&#x62c;',
		'EGP' => 'EGP',
		'ERN' => 'Nfk',
		'ETB' => 'Br',
		'EUR' => '&euro;',
		'FJD' => '&#36;',
		'FKP' => '&pound;',
		'GBP' => '&pound;',
		'GEL' => '&#x10da;',
		'GGP' => '&pound;',
		'GHS' => '&#x20b5;',
		'GIP' => '&pound;',
		'GMD' => 'D',
		'GNF' => 'Fr',
		'GTQ' => 'Q',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => 'L',
		'HRK' => 'Kn',
		'HTG' => 'G',
		'HUF' => '&#70;&#116;',
		'IDR' => 'Rp',
		'ILS' => '&#8362;',
		'IMP' => '&pound;',
		'INR' => '&#8377;',
		'IQD' => '&#x639;.&#x62f;',
		'IRR' => '&#xfdfc;',
		'ISK' => 'Kr.',
		'JEP' => '&pound;',
		'JMD' => '&#36;',
		'JOD' => '&#x62f;.&#x627;',
		'JPY' => '&yen;',
		'KES' => 'KSh',
		'KGS' => '&#x43b;&#x432;',
		'KHR' => '&#x17db;',
		'KMF' => 'Fr',
		'KPW' => '&#x20a9;',
		'KRW' => '&#8361;',
		'KWD' => '&#x62f;.&#x643;',
		'KYD' => '&#36;',
		'KZT' => 'KZT',
		'LAK' => '&#8365;',
		'LBP' => '&#x644;.&#x644;',
		'LKR' => '&#xdbb;&#xdd4;',
		'LRD' => '&#36;',
		'LSL' => 'L',
		'LYD' => '&#x644;.&#x62f;',
		'MAD' => '&#x62f;. &#x645;.',
		'MAD' => '&#x62f;.&#x645;.',
		'MDL' => 'L',
		'MGA' => 'Ar',
		'MKD' => '&#x434;&#x435;&#x43d;',
		'MMK' => 'Ks',
		'MNT' => '&#x20ae;',
		'MOP' => 'P',
		'MRO' => 'UM',
		'MUR' => '&#x20a8;',
		'MVR' => '.&#x783;',
		'MWK' => 'MK',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => 'MT',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => 'C&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#x631;.&#x639;.',
		'PAB' => 'B/.',
		'PEN' => 'S/.',
		'PGK' => 'K',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PRB' => '&#x440;.',
		'PYG' => '&#8370;',
		'QAR' => '&#x631;.&#x642;',
		'RMB' => '&yen;',
		'RON' => 'lei',
		'RSD' => '&#x434;&#x438;&#x43d;.',
		'RUB' => '&#8381;',
		'RWF' => 'Fr',
		'SAR' => '&#x631;.&#x633;',
		'SBD' => '&#36;',
		'SCR' => '&#x20a8;',
		'SDG' => '&#x62c;.&#x633;.',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&pound;',
		'SLL' => 'Le',
		'SOS' => 'Sh',
		'SRD' => '&#36;',
		'SSP' => '&pound;',
		'STD' => 'Db',
		'SYP' => '&#x644;.&#x633;',
		'SZL' => 'L',
		'THB' => '&#3647;',
		'TJS' => '&#x405;&#x41c;',
		'TMT' => 'm',
		'TND' => '&#x62f;.&#x62a;',
		'TOP' => 'T&#36;',
		'TRY' => '&#8378;',
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => 'Sh',
		'UAH' => '&#8372;',
		'UGX' => 'UGX',
		'USD' => '&#36;',
		'UYU' => '&#36;',
		'UZS' => 'UZS',
		'VEF' => 'Bs F',
		'VND' => '&#8363;',
		'VUV' => 'Vt',
		'WST' => 'T',
		'XAF' => 'Fr',
		'XCD' => '&#36;',
		'XOF' => 'Fr',
		'XPF' => 'Fr',
		'YER' => '&#xfdfc;',
		'ZAR' => '&#82;',
		'ZMW' => 'ZK',
	);
	if ( empty($code) ) {
		global $box_currency;
		$code = $box_currency->code;
	}

	//$currency_symbol = isset( $symbols[$code] ) ? $symbols[$code ] : '';
	return $symbols[$code];
}
function get_countries(){
	return array(
	'AF' => __( 'Afghanistan', 'woocommerce' ),
	'AX' => __( '&#197;land Islands', 'woocommerce' ),
	'AL' => __( 'Albania', 'woocommerce' ),
	'DZ' => __( 'Algeria', 'woocommerce' ),
	'AS' => __( 'American Samoa', 'woocommerce' ),
	'AD' => __( 'Andorra', 'woocommerce' ),
	'AO' => __( 'Angola', 'woocommerce' ),
	'AI' => __( 'Anguilla', 'woocommerce' ),
	'AQ' => __( 'Antarctica', 'woocommerce' ),
	'AG' => __( 'Antigua and Barbuda', 'woocommerce' ),
	'AR' => __( 'Argentina', 'woocommerce' ),
	'AM' => __( 'Armenia', 'woocommerce' ),
	'AW' => __( 'Aruba', 'woocommerce' ),
	'AU' => __( 'Australia', 'woocommerce' ),
	'AT' => __( 'Austria', 'woocommerce' ),
	'AZ' => __( 'Azerbaijan', 'woocommerce' ),
	'BS' => __( 'Bahamas', 'woocommerce' ),
	'BH' => __( 'Bahrain', 'woocommerce' ),
	'BD' => __( 'Bangladesh', 'woocommerce' ),
	'BB' => __( 'Barbados', 'woocommerce' ),
	'BY' => __( 'Belarus', 'woocommerce' ),
	'BE' => __( 'Belgium', 'woocommerce' ),
	'PW' => __( 'Belau', 'woocommerce' ),
	'BZ' => __( 'Belize', 'woocommerce' ),
	'BJ' => __( 'Benin', 'woocommerce' ),
	'BM' => __( 'Bermuda', 'woocommerce' ),
	'BT' => __( 'Bhutan', 'woocommerce' ),
	'BO' => __( 'Bolivia', 'woocommerce' ),
	'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'woocommerce' ),
	'BA' => __( 'Bosnia and Herzegovina', 'woocommerce' ),
	'BW' => __( 'Botswana', 'woocommerce' ),
	'BV' => __( 'Bouvet Island', 'woocommerce' ),
	'BR' => __( 'Brazil', 'woocommerce' ),
	'IO' => __( 'British Indian Ocean Territory', 'woocommerce' ),
	'VG' => __( 'British Virgin Islands', 'woocommerce' ),
	'BN' => __( 'Brunei', 'woocommerce' ),
	'BG' => __( 'Bulgaria', 'woocommerce' ),
	'BF' => __( 'Burkina Faso', 'woocommerce' ),
	'BI' => __( 'Burundi', 'woocommerce' ),
	'KH' => __( 'Cambodia', 'woocommerce' ),
	'CM' => __( 'Cameroon', 'woocommerce' ),
	'CA' => __( 'Canada', 'woocommerce' ),
	'CV' => __( 'Cape Verde', 'woocommerce' ),
	'KY' => __( 'Cayman Islands', 'woocommerce' ),
	'CF' => __( 'Central African Republic', 'woocommerce' ),
	'TD' => __( 'Chad', 'woocommerce' ),
	'CL' => __( 'Chile', 'woocommerce' ),
	'CN' => __( 'China', 'woocommerce' ),
	'CX' => __( 'Christmas Island', 'woocommerce' ),
	'CC' => __( 'Cocos (Keeling) Islands', 'woocommerce' ),
	'CO' => __( 'Colombia', 'woocommerce' ),
	'KM' => __( 'Comoros', 'woocommerce' ),
	'CG' => __( 'Congo (Brazzaville)', 'woocommerce' ),
	'CD' => __( 'Congo (Kinshasa)', 'woocommerce' ),
	'CK' => __( 'Cook Islands', 'woocommerce' ),
	'CR' => __( 'Costa Rica', 'woocommerce' ),
	'HR' => __( 'Croatia', 'woocommerce' ),
	'CU' => __( 'Cuba', 'woocommerce' ),
	'CW' => __( 'Cura&ccedil;ao', 'woocommerce' ),
	'CY' => __( 'Cyprus', 'woocommerce' ),
	'CZ' => __( 'Czech Republic', 'woocommerce' ),
	'DK' => __( 'Denmark', 'woocommerce' ),
	'DJ' => __( 'Djibouti', 'woocommerce' ),
	'DM' => __( 'Dominica', 'woocommerce' ),
	'DO' => __( 'Dominican Republic', 'woocommerce' ),
	'EC' => __( 'Ecuador', 'woocommerce' ),
	'EG' => __( 'Egypt', 'woocommerce' ),
	'SV' => __( 'El Salvador', 'woocommerce' ),
	'GQ' => __( 'Equatorial Guinea', 'woocommerce' ),
	'ER' => __( 'Eritrea', 'woocommerce' ),
	'EE' => __( 'Estonia', 'woocommerce' ),
	'ET' => __( 'Ethiopia', 'woocommerce' ),
	'FK' => __( 'Falkland Islands', 'woocommerce' ),
	'FO' => __( 'Faroe Islands', 'woocommerce' ),
	'FJ' => __( 'Fiji', 'woocommerce' ),
	'FI' => __( 'Finland', 'woocommerce' ),
	'FR' => __( 'France', 'woocommerce' ),
	'GF' => __( 'French Guiana', 'woocommerce' ),
	'PF' => __( 'French Polynesia', 'woocommerce' ),
	'TF' => __( 'French Southern Territories', 'woocommerce' ),
	'GA' => __( 'Gabon', 'woocommerce' ),
	'GM' => __( 'Gambia', 'woocommerce' ),
	'GE' => __( 'Georgia', 'woocommerce' ),
	'DE' => __( 'Germany', 'woocommerce' ),
	'GH' => __( 'Ghana', 'woocommerce' ),
	'GI' => __( 'Gibraltar', 'woocommerce' ),
	'GR' => __( 'Greece', 'woocommerce' ),
	'GL' => __( 'Greenland', 'woocommerce' ),
	'GD' => __( 'Grenada', 'woocommerce' ),
	'GP' => __( 'Guadeloupe', 'woocommerce' ),
	'GU' => __( 'Guam', 'woocommerce' ),
	'GT' => __( 'Guatemala', 'woocommerce' ),
	'GG' => __( 'Guernsey', 'woocommerce' ),
	'GN' => __( 'Guinea', 'woocommerce' ),
	'GW' => __( 'Guinea-Bissau', 'woocommerce' ),
	'GY' => __( 'Guyana', 'woocommerce' ),
	'HT' => __( 'Haiti', 'woocommerce' ),
	'HM' => __( 'Heard Island and McDonald Islands', 'woocommerce' ),
	'HN' => __( 'Honduras', 'woocommerce' ),
	'HK' => __( 'Hong Kong', 'woocommerce' ),
	'HU' => __( 'Hungary', 'woocommerce' ),
	'IS' => __( 'Iceland', 'woocommerce' ),
	'IN' => __( 'India', 'woocommerce' ),
	'ID' => __( 'Indonesia', 'woocommerce' ),
	'IR' => __( 'Iran', 'woocommerce' ),
	'IQ' => __( 'Iraq', 'woocommerce' ),
	'IE' => __( 'Ireland', 'woocommerce' ),
	'IM' => __( 'Isle of Man', 'woocommerce' ),
	'IL' => __( 'Israel', 'woocommerce' ),
	'IT' => __( 'Italy', 'woocommerce' ),
	'CI' => __( 'Ivory Coast', 'woocommerce' ),
	'JM' => __( 'Jamaica', 'woocommerce' ),
	'JP' => __( 'Japan', 'woocommerce' ),
	'JE' => __( 'Jersey', 'woocommerce' ),
	'JO' => __( 'Jordan', 'woocommerce' ),
	'KZ' => __( 'Kazakhstan', 'woocommerce' ),
	'KE' => __( 'Kenya', 'woocommerce' ),
	'KI' => __( 'Kiribati', 'woocommerce' ),
	'KW' => __( 'Kuwait', 'woocommerce' ),
	'KG' => __( 'Kyrgyzstan', 'woocommerce' ),
	'LA' => __( 'Laos', 'woocommerce' ),
	'LV' => __( 'Latvia', 'woocommerce' ),
	'LB' => __( 'Lebanon', 'woocommerce' ),
	'LS' => __( 'Lesotho', 'woocommerce' ),
	'LR' => __( 'Liberia', 'woocommerce' ),
	'LY' => __( 'Libya', 'woocommerce' ),
	'LI' => __( 'Liechtenstein', 'woocommerce' ),
	'LT' => __( 'Lithuania', 'woocommerce' ),
	'LU' => __( 'Luxembourg', 'woocommerce' ),
	'MO' => __( 'Macao S.A.R., China', 'woocommerce' ),
	'MK' => __( 'Macedonia', 'woocommerce' ),
	'MG' => __( 'Madagascar', 'woocommerce' ),
	'MW' => __( 'Malawi', 'woocommerce' ),
	'MY' => __( 'Malaysia', 'woocommerce' ),
	'MV' => __( 'Maldives', 'woocommerce' ),
	'ML' => __( 'Mali', 'woocommerce' ),
	'MT' => __( 'Malta', 'woocommerce' ),
	'MH' => __( 'Marshall Islands', 'woocommerce' ),
	'MQ' => __( 'Martinique', 'woocommerce' ),
	'MR' => __( 'Mauritania', 'woocommerce' ),
	'MU' => __( 'Mauritius', 'woocommerce' ),
	'YT' => __( 'Mayotte', 'woocommerce' ),
	'MX' => __( 'Mexico', 'woocommerce' ),
	'FM' => __( 'Micronesia', 'woocommerce' ),
	'MD' => __( 'Moldova', 'woocommerce' ),
	'MC' => __( 'Monaco', 'woocommerce' ),
	'MN' => __( 'Mongolia', 'woocommerce' ),
	'ME' => __( 'Montenegro', 'woocommerce' ),
	'MS' => __( 'Montserrat', 'woocommerce' ),
	'MA' => __( 'Morocco', 'woocommerce' ),
	'MZ' => __( 'Mozambique', 'woocommerce' ),
	'MM' => __( 'Myanmar', 'woocommerce' ),
	'NA' => __( 'Namibia', 'woocommerce' ),
	'NR' => __( 'Nauru', 'woocommerce' ),
	'NP' => __( 'Nepal', 'woocommerce' ),
	'NL' => __( 'Netherlands', 'woocommerce' ),
	'NC' => __( 'New Caledonia', 'woocommerce' ),
	'NZ' => __( 'New Zealand', 'woocommerce' ),
	'NI' => __( 'Nicaragua', 'woocommerce' ),
	'NE' => __( 'Niger', 'woocommerce' ),
	'NG' => __( 'Nigeria', 'woocommerce' ),
	'NU' => __( 'Niue', 'woocommerce' ),
	'NF' => __( 'Norfolk Island', 'woocommerce' ),
	'MP' => __( 'Northern Mariana Islands', 'woocommerce' ),
	'KP' => __( 'North Korea', 'woocommerce' ),
	'NO' => __( 'Norway', 'woocommerce' ),
	'OM' => __( 'Oman', 'woocommerce' ),
	'PK' => __( 'Pakistan', 'woocommerce' ),
	'PS' => __( 'Palestinian Territory', 'woocommerce' ),
	'PA' => __( 'Panama', 'woocommerce' ),
	'PG' => __( 'Papua New Guinea', 'woocommerce' ),
	'PY' => __( 'Paraguay', 'woocommerce' ),
	'PE' => __( 'Peru', 'woocommerce' ),
	'PH' => __( 'Philippines', 'woocommerce' ),
	'PN' => __( 'Pitcairn', 'woocommerce' ),
	'PL' => __( 'Poland', 'woocommerce' ),
	'PT' => __( 'Portugal', 'woocommerce' ),
	'PR' => __( 'Puerto Rico', 'woocommerce' ),
	'QA' => __( 'Qatar', 'woocommerce' ),
	'RE' => __( 'Reunion', 'woocommerce' ),
	'RO' => __( 'Romania', 'woocommerce' ),
	'RU' => __( 'Russia', 'woocommerce' ),
	'RW' => __( 'Rwanda', 'woocommerce' ),
	'BL' => __( 'Saint Barth&eacute;lemy', 'woocommerce' ),
	'SH' => __( 'Saint Helena', 'woocommerce' ),
	'KN' => __( 'Saint Kitts and Nevis', 'woocommerce' ),
	'LC' => __( 'Saint Lucia', 'woocommerce' ),
	'MF' => __( 'Saint Martin (French part)', 'woocommerce' ),
	'SX' => __( 'Saint Martin (Dutch part)', 'woocommerce' ),
	'PM' => __( 'Saint Pierre and Miquelon', 'woocommerce' ),
	'VC' => __( 'Saint Vincent and the Grenadines', 'woocommerce' ),
	'SM' => __( 'San Marino', 'woocommerce' ),
	'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'woocommerce' ),
	'SA' => __( 'Saudi Arabia', 'woocommerce' ),
	'SN' => __( 'Senegal', 'woocommerce' ),
	'RS' => __( 'Serbia', 'woocommerce' ),
	'SC' => __( 'Seychelles', 'woocommerce' ),
	'SL' => __( 'Sierra Leone', 'woocommerce' ),
	'SG' => __( 'Singapore', 'woocommerce' ),
	'SK' => __( 'Slovakia', 'woocommerce' ),
	'SI' => __( 'Slovenia', 'woocommerce' ),
	'SB' => __( 'Solomon Islands', 'woocommerce' ),
	'SO' => __( 'Somalia', 'woocommerce' ),
	'ZA' => __( 'South Africa', 'woocommerce' ),
	'GS' => __( 'South Georgia/Sandwich Islands', 'woocommerce' ),
	'KR' => __( 'South Korea', 'woocommerce' ),
	'SS' => __( 'South Sudan', 'woocommerce' ),
	'ES' => __( 'Spain', 'woocommerce' ),
	'LK' => __( 'Sri Lanka', 'woocommerce' ),
	'SD' => __( 'Sudan', 'woocommerce' ),
	'SR' => __( 'Suriname', 'woocommerce' ),
	'SJ' => __( 'Svalbard and Jan Mayen', 'woocommerce' ),
	'SZ' => __( 'Swaziland', 'woocommerce' ),
	'SE' => __( 'Sweden', 'woocommerce' ),
	'CH' => __( 'Switzerland', 'woocommerce' ),
	'SY' => __( 'Syria', 'woocommerce' ),
	'TW' => __( 'Taiwan', 'woocommerce' ),
	'TJ' => __( 'Tajikistan', 'woocommerce' ),
	'TZ' => __( 'Tanzania', 'woocommerce' ),
	'TH' => __( 'Thailand', 'woocommerce' ),
	'TL' => __( 'Timor-Leste', 'woocommerce' ),
	'TG' => __( 'Togo', 'woocommerce' ),
	'TK' => __( 'Tokelau', 'woocommerce' ),
	'TO' => __( 'Tonga', 'woocommerce' ),
	'TT' => __( 'Trinidad and Tobago', 'woocommerce' ),
	'TN' => __( 'Tunisia', 'woocommerce' ),
	'TR' => __( 'Turkey', 'woocommerce' ),
	'TM' => __( 'Turkmenistan', 'woocommerce' ),
	'TC' => __( 'Turks and Caicos Islands', 'woocommerce' ),
	'TV' => __( 'Tuvalu', 'woocommerce' ),
	'UG' => __( 'Uganda', 'woocommerce' ),
	'UA' => __( 'Ukraine', 'woocommerce' ),
	'AE' => __( 'United Arab Emirates', 'woocommerce' ),
	'GB' => __( 'United Kingdom (UK)', 'woocommerce' ),
	'US' => __( 'United States (US)', 'woocommerce' ),
	'UM' => __( 'United States (US) Minor Outlying Islands', 'woocommerce' ),
	'VI' => __( 'United States (US) Virgin Islands', 'woocommerce' ),
	'UY' => __( 'Uruguay', 'woocommerce' ),
	'UZ' => __( 'Uzbekistan', 'woocommerce' ),
	'VU' => __( 'Vanuatu', 'woocommerce' ),
	'VA' => __( 'Vatican', 'woocommerce' ),
	'VE' => __( 'Venezuela', 'woocommerce' ),
	'VN' => __( 'Vietnam', 'woocommerce' ),
	'WF' => __( 'Wallis and Futuna', 'woocommerce' ),
	'EH' => __( 'Western Sahara', 'woocommerce' ),
	'WS' => __( 'Samoa', 'woocommerce' ),
	'YE' => __( 'Yemen', 'woocommerce' ),
	'ZM' => __( 'Zambia', 'woocommerce' ),
	'ZW' => __( 'Zimbabwe', 'woocommerce' ),
);

}
function list_currency(){
	return array(
		'AED' => 'United Arab Emirates dirham (د.إ)',
		'AFN' => 'Afghan afghani (؋)',
		'ALL' => 'Albanian lek (L)',
		'AMD' => 'Armenian dram (AMD)',
		'ANG' => 'Netherlands Antillean guilder (ƒ)',
		'AOA' => 'Angolan kwanza (Kz)',
		'ARS' => 'Argentine peso ($)',
		'AUD' => 'Australian dollar ($)',
		'AWG' => 'Aruban florin (ƒ)',
		'AZN' => 'Azerbaijani manat (AZN)',
		'BAM' => 'Bosnia and Herzegovina convertible mark (KM)',
		'BBD' => 'Barbadian dollar ($)',
		'BDT' => 'Bangladeshi taka (৳&nbsp;)',
		'BGN' => 'Bulgarian lev (лв.)',
		'BHD' => 'Bahraini dinar (.د.ب)',
		'BIF' => 'Burundian franc (Fr)',
		'BMD' => 'Bermudian dollar ($)',
		'BND' => 'Brunei dollar ($)',
		'BOB' => 'Bolivian boliviano (Bs.)',
		'BRL' => 'Brazilian real (R$)',
		'BSD' => 'Bahamian dollar ($)',
		'BTC' => 'Bitcoin (฿)',
		'BTN' => 'Bhutanese ngultrum (Nu.)',
		'BWP' => 'Botswana pula (P)',
		'BYR' => 'Belarusian ruble (Br)',
		'BZD' => 'Belize dollar ($)',
		'CAD' => 'Canadian dollar ($)',
		'CDF' => 'Congolese franc (Fr)',
		'CHF' => 'Swiss franc (CHF)',
		'CLP' => 'Chilean peso ($)',
		'CNY' => 'Chinese yuan (¥)',
		'COP' => 'Colombian peso ($)',
		'CRC' => 'Costa Rican colón (₡)',
		'CUC' => 'Cuban convertible peso ($)',
		'CUP' => 'Cuban peso ($)',
		'CVE' => 'Cape Verdean escudo ($)',
		'CZK' => 'Czech koruna (Kč)',
		'DJF' => 'Djiboutian franc (Fr)',
		'DKK' => 'Danish krone (DKK)',
		'DOP' => 'Dominican peso (RD$)',
		'DZD' => 'Algerian dinar (د.ج)',
		'EGP' => 'Egyptian pound (EGP)',
		'ERN' => 'Eritrean nakfa (Nfk)',
		'ETB' => 'Ethiopian birr (Br)',
		'EUR' => 'Euro (€)',
		'FJD' => 'Fijian dollar ($)',
		'FKP' => 'Falkland Islands pound (£)',
		'GBP' => 'Pound sterling (£)',
		'GEL' => 'Georgian lari (ლ)',
		'GGP' => 'Guernsey pound (£)',
		'GHS' => 'Ghana cedi (₵)',
		'GIP' => 'Gibraltar pound (£)',
		'GMD' => 'Gambian dalasi (D)',
		'GNF' => 'Guinean franc (Fr)',
		'GTQ' => 'Guatemalan quetzal (Q)',
		'GYD' => 'Guyanese dollar ($)',
		'HKD' => 'Hong Kong dollar ($)',
		'HNL' => 'Honduran lempira (L)',
		'HRK' => 'Croatian kuna (Kn)',
		'HTG' => 'Haitian gourde (G)',
		'HUF' => 'Hungarian forint (Ft)',
		'IDR' => 'Indonesian rupiah (Rp)',
		'ILS' => 'Israeli new shekel (₪)',
		'IMP' => 'Manx pound (£)',
		'INR' => 'Indian rupee (₹)',
		'IQD' => 'Iraqi dinar (ع.د)',
		'IRR' => 'Iranian rial (﷼)',
		'ISK' => 'Icelandic króna (kr.)',
		'JEP' => 'Jersey pound (£)',
		'JMD' => 'Jamaican dollar ($)',
		'JOD' => 'Jordanian dinar (د.ا)',
		'JPY' => 'Japanese yen (¥)',
		'KES' => 'Kenyan shilling (KSh)',
		'KGS' => 'Kyrgyzstani som (сом)',
		'KHR' => 'Cambodian riel (៛)',
		'KMF' => 'Comorian franc (Fr)',
		'KPW' => 'North Korean won (₩)',
		'KRW' => 'South Korean won (₩)',
		'KWD' => 'Kuwaiti dinar (د.ك)',
		'KYD' => 'Cayman Islands dollar ($)',
		'KZT' => 'Kazakhstani tenge (KZT)',
		'LAK' => 'Lao kip (₭)',
		'LBP' => 'Lebanese pound (ل.ل)',
		'LKR' => 'Sri Lankan rupee (රු)',
		'LRD' => 'Liberian dollar ($)',
		'LSL' => 'Lesotho loti (L)',
		'LYD' => 'Libyan dinar (ل.د)',
		'MAD' => 'Moroccan dirham (د.م.)',
		'MDL' => 'Moldovan leu (L)',
		'MGA' => 'Malagasy ariary (Ar)',
		'MKD' => 'Macedonian denar (ден)',
		'MMK' => 'Burmese kyat (Ks)',
		'MNT' => 'Mongolian tögrög (₮)',
		'MOP' => 'Macanese pataca (P)',
		'MRO' => 'Mauritanian ouguiya (UM)',
		'MUR' => 'Mauritian rupee (₨)',
		'MVR' => 'Maldivian rufiyaa (.ރ)',
		'MWK' => 'Malawian kwacha (MK)',
		'MXN' => 'Mexican peso ($)',
		'MYR' => 'Malaysian ringgit (RM)',
		'MZN' => 'Mozambican metical (MT)',
		'NAD' => 'Namibian dollar ($)',
		'NGN' => 'Nigerian naira (₦)',
		'NIO' => 'Nicaraguan córdoba (C$)',
		'NOK' => 'Norwegian krone (kr)',
		'NPR' => 'Nepalese rupee (₨)',
		'NZD' => 'New Zealand dollar ($)',
		'OMR' => 'Omani rial (ر.ع.)',
		'PAB' => 'Panamanian balboa (B/.)',
		'PEN' => 'Peruvian nuevo sol (S/.)',
		'PGK' => 'Papua New Guinean kina (K)',
		'PHP' => 'Philippine peso (₱)',
		'PKR' => 'Pakistani rupee (₨)',
		'PLN' => 'Polish złoty (zł)',
		'PRB' => 'Transnistrian ruble (р.)',
		'PYG' => 'Paraguayan guaraní (₲)',
		'QAR' => 'Qatari riyal (ر.ق)',
		'RON' => 'Romanian leu (lei)',
		'RSD' => 'Serbian dinar (дин.)',
		'RUB' => 'Russian ruble (₽)',
		'RWF' => 'Rwandan franc (Fr)',
		'SAR' => 'Saudi riyal (ر.س)',
		'SBD' => 'Solomon Islands dollar ($)',
		'SCR' => 'Seychellois rupee (₨)',
		'SDG' => 'Sudanese pound (ج.س.)',
		'SEK' => 'Swedish krona (kr)',
		'SGD' => 'Singapore dollar ($)',
		'SHP' => 'Saint Helena pound (£)',
		'SLL' => 'Sierra Leonean leone (Le)',
		'SOS' => 'Somali shilling (Sh)',
		'SRD' => 'Surinamese dollar ($)',
		'SSP' => 'South Sudanese pound (£)',
		'STD' => 'São Tomé and Príncipe dobra (Db)',
		'SYP' => 'Syrian pound (ل.س)',
		'SZL' => 'Swazi lilangeni (L)',
		'THB' => 'Thai baht (฿)',
		'TJS' => 'Tajikistani somoni (ЅМ)',
		'TMT' => 'Turkmenistan manat (m)',
		'TND' => 'Tunisian dinar (د.ت)',
		'TOP' => 'Tongan paʻanga (T$)',
		'TRY' => 'Turkish lira (₺)',
		'TTD' => 'Trinidad and Tobago dollar ($)',
		'TWD' => 'New Taiwan dollar (NT$)',
		'TZS' => 'Tanzanian shilling (Sh)',
		'UAH' => 'Ukrainian hryvnia (₴)',
		'UGX' => 'Ugandan shilling (UGX)',
		'USD' => 'United States dollar ($)',
		'UYU' => 'Uruguayan peso ($)',
		'UZS' => 'Uzbekistani som (UZS)',
		'VEF' => 'Venezuelan bolívar (Bs F)',
		'VND' => 'Vietnamese đồng (₫)',
		'VUV' => 'Vanuatu vatu (Vt)',
		'WST' => 'Samoan tālā (T)',
		'XAF' => 'Central African CFA franc (Fr)',
		'XCD' => 'East Caribbean dollar ($)',
		'XOF' => 'West African CFA franc (Fr)',
		'XPF' => 'CFP franc (Fr)',
		'YER' => 'Yemeni rial (﷼)',
		'ZAR' => 'South African rand (R)',
		'ZMW' => 'Zambian kwacha (ZK)',
	);
}
add_filter('getimagesize_mimes_to_exts','bx_allow_upload_extend');
function bx_allow_upload_extend($args){

	$add = array('doc|docx' => 'application/msword' ,
		'pdf' 			=> 'application/pdf',
		'zip' 			=> 'multipart/x-zip',
		'mp3|m4a|m4b'  => 'audio/mpeg',
		'wav'          => 'audio/wav',
		'wma'          => 'audio/x-ms-wma',
		'wmv'          => 'video/x-ms-wmv',
		'wmx'          => 'video/x-ms-wmx',
		'wm'           => 'video/x-ms-wm',
		'avi'          => 'video/avi',
		'divx'         => 'video/divx',
		'flv'          => 'video/x-flv',
		'mov|qt'       => 'video/quicktime',
		'mpeg|mpg|mpe' => 'video/mpeg',
		'mp4|m4v'      => 'video/mp4'
		);
	return array_merge($args, $add);
}
function bo_list_paymentgateways($label = ''){ ?>
	<div class="form-group list-paymentgateways">
		<h3  class="col-sm-12 col-form-label"><span class="bg-color">2</span><?php echo $label;?></h3>
		<?php
			global $has_payment;
			$has_payment= 0;
		    $option = BX_Option::get_instance();
		    $payment = $option->get_group_option('payment');

		    $paypal = array();
			$paypal_enable = 0;
		    $paypal = (object)$payment->paypal;
		    if( isset($paypal->enable) ){
		    	$paypal_enable = $paypal->enable;

		    	if( empty( $paypal->email )){
		    		$paypal_enable  = 0;
		    	}
		    }

		    $cash = (object)$payment->cash;
				$cash_enable =   0;
				if( isset($cash->enable) ){
					$cash_enable = $cash->enable;
				}


		    if( $paypal_enable ) { 	$has_payment= 1;
		 		?>
			    <div class="col-sm-12  gateway-payment  record-line"">
			    	<div class="col-sm-9">
			    		<img src="<?php echo get_theme_file_uri('img/PayPal.jpg');?>" width="200">
			    		<p> <?php _e('You will checkout via paypal','boxtheme');?> </p>

			    	</div>
			    	<div class="col-sm-3 align-right">
			    		<label>
			    		<input type="radio" class="required radio radio-gateway-item"  name="_gateway" required value="paypal">
			    		<span class=" no-radius btn align-right btn-select " ><span class="default"><?php _e('Select','boxtheme');?></span><span class="activate"><?php _e('Selected','boxtheme');?></span></span>
			    		</label>
			    	</div>
			    	<div class="full f-left"></div>
			    </div>
		   	<?php } ?>

			<?php if( $cash_enable ){  $has_payment = 1;?>
			    <div class="col-sm-12  gateway-payment record-line">
			    	<div class="col-sm-9">
			    		<img src="<?php echo get_template_directory_uri().'/img/cash.png';?>" height="69">
			    		<p><?php _e('You will checkout via cash method','boxtheme');?> </p>
			    	</div>
			    	<div class="col-sm-3 align-right">
				    	<label>
				    		<input type="radio" class="required radio radio-gateway-item" name="_gateway" required value="cash">
				    		<span class=" no-radius btn align-right btn-select " ><span class="default"><?php _e('Select','boxtheme');?></span><span class="activate"><?php _e('Selected','boxtheme');?></span></span>
				    	</label>
			    	</div>
			    	<div class="full f-left"></div>
			    </div>
			    <input type="radio" class="required radio radio-gateway-item" name="_gateway" id="free" required value="free">
			<?php } ?>
			<?php do_action('insert_payment_gateway') ; ?>
		<?php if( ! $has_payment){ ?>
			<?php _e('The is not any payment gateways','boxtheme');?>
		<?php } ?>
		</div> <!-- end list payment !--> <?php
}
function box_get_premium_types(){
	$list = array(
		0 => "Select Priority",
		3 => 'Featured',
		5  => 'Urgent',
	);
	return $list;
}