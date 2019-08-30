<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function check_permission(){
	if( ! current_user_can( 'manage_options' ) )
		return new WP_Error( 'insert_fail',  $project_id->get_error_message() );
	return true;
}
class BX_ajax_backend{
	static $instance;
	static function get_instance(){
		  if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
	}
	function init(){

		add_action( 'wp_ajax_del-post',array( __CLASS__, 'del_post' ) );
		add_action( 'wp_ajax_admin_approve', array( __CLASS__, 'admin_approve' ) );
		add_action( 'wp_ajax_save_mail_setup',array( __CLASS__, 'save_email' ) );
		add_action( 'wp_ajax_install_sample',array( $this, 'install_sample' ) );


	}

	static function save_option(){

		if( ! self::check_permission() ){
			wp_send_json( array('success' => false, 'msg' => 'Security declince') );
			die();
		}

		$request= $_REQUEST['request'];
		$name = $request['name'];
		$group = $request['group'];
		$value = $request['value'];
		$item =  isset( $request['item'] ) ? $request['item'] : 0;
		$section = isset($request['section']) ? $request['section']: '';
		$level = isset($request['level']) ? $request['level']: 0;

		$option = BX_Option::get_instance();

		$option->set_option( $group, $section, $item, $name, $value, $level);
		wp_send_json(array('success' => true, 'msg' => 'save done'));
	}
	static function create_package() {
		if( ! self::check_permission() ){
			wp_send_json( array('success' => false, 'msg' => 'Security declince') );
			die();
		}
		$request = $_REQUEST['request'];
		$id = isset($request['ID']) ? $request['ID'] : 0;
		$post_title = isset($request['post_title']) ? $request['post_title'] : 'Package name';
		$type = isset($request['type']) ? $request['type'] :'buy_credit';

		$args = array(
			'post_title' => $post_title,
			'post_content' => $request['post_content'],
			'post_type' => '_package',
			'post_status' =>'publish',
			'meta_input' => array(
				'sku' => $request['sku'],
				'price' => $request['price'],
				'type' => $type,
				),
		);

		if( $id ){
			$args['ID'] = $id;
			wp_update_post($args );
		} else {
			wp_insert_post($args);
		}
		wp_send_json( array(
			'success' => true,
			'msg' => 'DONE'
			)
		);
	}
	static function del_post(){
		if( ! self::check_permission() ){
			wp_send_json( array('success' => false, 'msg' => 'Security declince') );
			die();
		}

		$request= $_REQUEST['request'];
		$id = $request['id'];
		wp_delete_post($id,true);
		wp_send_json( array(
			'success' => true,
			'msg' => 'DONE'
			)
		);
	}
	static function check_permission(){
		if( current_user_can('manage_options' ) ){
			return true;
		}
		return false;
	}
	static function admin_approve(){
		if( ! self::check_permission() ){
			wp_send_json( array('success' => false, 'msg' => 'Security declince') );
			die();
		}

		$request= $_REQUEST['request'];
		$order_id = $request['order_id'];
		$type = $_REQUEST['type'];

		$credit = BX_Credit::get_instance()->$type($order_id); // approve_withdraw_act , approve_deposit_credit
		if( is_wp_error( $credit ) ){
			wp_send_json(array('success'=> false,'msg' => $credit->get_error_message() ) );
		}
		wp_send_json(array('success'=> tre,'msg' => 'Update OK') );
	}
	static function save_email(){
		$response = array('success' => true, 'msg' =>'done');
		$request = $_REQUEST['request'];
		$subject = $request['subject'];
		$content = $request['content'];
		$key = $request['key'];
		$new_value = array(
			'subject' => $subject,
			'content' => $content,
		);


		$option = BX_Option::get_instance();
		$list = $option->list_email();

		$default_content = $option->get_default_mail_content($key);
		$new_value = wp_parse_args($new_value, $default_content );
		$list[$key] = $new_value;
		$option->set_mails($list);


		wp_send_json( $response );

	}
	function install_sample(){
		$file = get_template_directory().'/sampledata/sampledata.xml.txt';
		$import = new BOX_Import();
		$result = $import->import($file);
		update_option( 'install_sample', 1);
		$response = array(
			'success'=>true,
			'msg'=> 'DONE',
		);

		wp_send_json( $response );
	}

}
BX_ajax_backend::get_instance()->init();
?>