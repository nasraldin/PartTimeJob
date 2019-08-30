<?php

function box_switch_logo(){
	return apply_filters('box_switch_logo',false);
}

function box_logo(){
	$html_logo 	= get_custom_logo();
	$white_logo = $decator = '';
	$indecate_white_logo = 'has_not_white_logo';
	if( box_switch_logo() ){
		$indecate_white_logo = 'has_white_logo';
		$white_logo = '<img class="logo style-svg white-logo" src ="'.get_stylesheet_directory_uri().'/white-logo.png"/>';
	}

	$default_logo = '<img class="main-logo logo style-svg" alt="'.get_bloginfo( 'name','display' ).'" src="'.	get_template_directory_uri().'/img/logo.png'.'" />';


	if( ! empty( $html_logo ) ){ echo $html_logo; } else { ?>
		<a itemprop="logo"  class="logo <?php echo $indecate_white_logo;?>" title="<?php echo get_bloginfo( 'name','display' ); ?>"  href="<?php echo home_url();?>"> <?php echo $white_logo; echo $default_logo; ?>	</a> <?php
	}
}
function box_price($price,$echo = true){
	//echo get_box_price($price);
	echo box_get_price_format($price);
}
function get_box_price( $price ) {
	global $box_currency;

	$price = floatval($price);
	$decimals = 2;
	$price_decimal_sep = $box_currency->price_decimal_sep;
	$price_thousand_sep = $box_currency->price_thousand_sep;

	number_format( $price, $decimals, $price_decimal_sep, $price_thousand_sep );

	$symbol = box_get_currency_symbol($box_currency->code);

	$string = $price.'<span class="currency-icon">('.$symbol.')</span>';

	return  $string;
}
if( ! function_exists('is_current_box_administrator') ){
	function is_current_box_administrator(){
		return current_user_can( 'administrator' ) ;
	}
}



/**
 * this function get the float number only withouth currency  symbol
 * This is a cool function
 * @author boxtheme
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
	//$price = floatval($price);
	//$price = box_get_price($price);
	$string ='<span class="currency-icon">('.$symbol.')</span>'. $price;

	if( $box_currency->position == 'right' ){
		$string = $price.'<span class="currency-icon">('.$symbol.')</span>';
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

	function box_get_static_link($slug){

		global $box_slugs;

		if( isset( $box_slugs[$slug] ) ) {
			$item = (object) $box_slugs[$slug];

			if( ! empty ( $item->ID ) )
			 	return get_permalink( $item->ID );
		}
	    return home_url();
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
function box_get_response(  $captcha_response) {
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	global $app_api;

	$gg_captcha = (object) $app_api->gg_captcha;
	$enable = (int) $gg_captcha->enable;

	if ( ! $enable ||  empty ( $gg_captcha->site_key ) || empty( $gg_captcha->secret_key ) ) {
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
	return new WP_Error( 'gglcptch_error', __('Captcha Invalid.','boxtheme') );
}
function box_add_captcha_field(){
	global $app_api;

	$gg_captcha = (object) $app_api->gg_captcha;
	$enable = (int) $gg_captcha->enable;

	if ( $enable && ! empty ( $gg_captcha->site_key ) && !empty($gg_captcha->secret_key) ) { ?>
		<div class="form-row">
			<div class="g-recaptcha form-group col-md-12" data-sitekey="<?php echo $gg_captcha->site_key;?>"></div>
		</div>
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
				<g:plusone align="right"></g:plusone>
			</div>
		</li>

		<li class="share-item  tw-share">
			<a href="#"><center><i class="fa fa-twitter" aria-hidden="true"></i><span> &nbsp; Tweet</span></center></a>
			<div class="fb-invisi"><a class="twitter popup" href="http://twitter.com/share">Tweet</a></div>
		</li>
		<li class="share-item sendmail">
  			<a href="#" id="send_email"><i class="fa fa-envelope" aria-hidden="true"></i> </a>
  		</li>
	</ul> <?php
}
function mark_as_premium_post($order){
	$pack_id = get_post_meta( $order->ID, 'pack_id', true);
	$priority = get_post_meta( $pack_id, 'priority', true );
	$project_id = get_post_meta( $order->ID, 'pay_premium_post', true  );
	update_post_meta( $project_id, 'priority', $priority);
}
if( ! function_exists('box_get_login_redirect_url') ){
	function box_get_login_redirect_url(){
		return home_url();
	}
}

// if( ! function_exists('box_edit_social_link')){
// 	function box_edit_social_link($profile){

// 	}
// }
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
function is_box_freelancer($user_id){
	$role = bx_get_user_role( $user_id );
	if($role == FREELANCER)
		return true;
	$profile_id = (int) get_user_meta( $user_id, 'profile_id', true );
	if( $profile_id > 0 )
		return $profile_id;
	return false;
	// if ( in_array ( $role, array('administrator', EMPLOYER) ) )
	// 	return false;
	// return true;

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
			$paypal_enable = $stripe_enable = 0;
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

			$stripe = (object)$payment->stripe;
			$stripe_enable = 0;
			if( isset($stripe->enable) ){
		    	$stripe_enable = $stripe->enable;
		    	$publishable_key = 'live_publishable_key';
		    	if( empty( $stripe->publishable_key )){
		    		$stripe_enable  = 0;
		    	}
		    }
		    $service = array();
 			//html_paypal_item($service);
		    if( $paypal_enable ) { 	$has_payment= 1; ?>
			    <div class="col-sm-12  gateway-payment  record-line">
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

		   	<?php if( $stripe_enable ) { 	$has_payment= 1; ?>
			    <div class="col-sm-12  gateway-payment  record-line">
			    	<div class="col-sm-9">
			    		<img src="<?php echo get_theme_file_uri('img/stripe.png');?>" width="200">
			    		<p> <?php _e('You will checkout via stripe','boxtheme');?> </p>

			    	</div>
			    	<div class="col-sm-3 align-right">
			    		<label>
			    		<input type="radio" class="required radio radio-gateway-item"  name="_gateway" required value="paypal">
			    		<span class=" no-radius btn align-right btn-select " ><span class="default"><?php _e('Select','boxtheme');?></span><span class="activate stripe-button"><?php _e('Selected','boxtheme');?></span></span>
			    		</label>
			    	</div>
			    	<div class="full f-left"></div>
			    	<div class="full">
			    		<?php // stripe_button();?>
			    	</div>

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
		//0 => "Select Priority",
		3 => __('Featured','boxtheme'),
		5  => __('Urgent','boxtheme'),
	);
	return $list;
}
function box_get_list_payment(){
	$gateways = array();
	$list_payment = apply_filters('add_payment_gateway',$gateways);
	ksort($list_payment);
	return $list_payment;
}
function box_gateway_settings($config){
    $list_payment = box_get_list_payment();

    echo '<ul id="sortable">';
    foreach ($list_payment as $position=>$payment) {
    	echo '<li class="ui-state-default" id="item_'.$payment['id'].'"  key="'.$payment['id'].'" rel="'.$payment['id'].'">';
        do_action('add_payment_setting_'.$payment['id'], $config);
        echo '</li>';
    }
    echo '</ul>';
}