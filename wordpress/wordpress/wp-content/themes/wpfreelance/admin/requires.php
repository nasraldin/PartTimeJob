<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	function bx_swap_button( $name, $is_active, $level = 0, $text_on ='', $text_off = '' ){
		$checked  = '';
		$level =  'level = "'.$level.'" ';
		if( $is_active ) $checked = 'checked';
		if( ! $level ) $multi = 'level = "0" ';
		if(empty($text_on))
			$text_on = __('On','boxtheme');
		if(empty($text_off))
			$text_off = __('Off','boxtheme');
		echo '<input type="checkbox" class="auto-save" '.$level.' data-on="'.$text_on.'" data-off="'.$text_off.'"  name="'.$name.'" value="'.$is_active.'" '.$checked.' data-toggle="toggle">';

	}


	require_once dirname(__FILE__) . '/admin.php';
	require_once dirname(__FILE__) . '/credit.php';
	require_once dirname(__FILE__) . '/appearances.php';
	require_once dirname(__FILE__) . '/withdrawal.php';
	require_once dirname(__FILE__) . '/class_import.php';
	require_once dirname(__FILE__) . '/admin_ajax.php';
	require_once dirname(__FILE__) . '/add_tax_fields.php';


 	if( class_exists( 'BX_Credit_Setting') )
 		new BX_Credit_Setting();
 	if( class_exists( 'BX_Withdrawal') )
 		new BX_Withdrawal();
?>