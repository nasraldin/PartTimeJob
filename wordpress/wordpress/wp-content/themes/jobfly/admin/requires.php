<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bx_swap_button($group, $name, $is_active, $level = 0){
	$checked  = '';
	$level =  'level = "'.$level.'" ';
	if( $is_active ) $checked = 'checked';
	if( ! $level ) $multi = 'level = "0" ';
	echo '<input type="checkbox" class="auto-save" '.$level.' name="'.$name.'" value="'.$is_active.'" '.$checked.' data-toggle="toggle">';

}





	require_once dirname(__FILE__) . '/admin.php';
	require_once dirname(__FILE__) . '/credit.php';
	require_once dirname(__FILE__) . '/class_import.php';
	require_once dirname(__FILE__) . '/admin_ajax.php';

	if( class_exists( 'BX_Admin') )
 		new BX_Admin();
 	if( class_exists( 'BX_Credit_Setting') )
 		new BX_Credit_Setting();
?>