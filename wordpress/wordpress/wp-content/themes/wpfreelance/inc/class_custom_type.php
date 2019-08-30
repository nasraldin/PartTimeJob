<?php

/**
 * this class will be extend by message or notify class.
 */
Class Box_Custom_Type{
	public $type;
	static protected $instance;
	public $table;
	function __construct(){
		global $wpdb;
		$this->table = $wpdb->prefix."box_messages";
	}
	static function get_instance(){
		if (null === static::$instance) {
        	static::$instance = new static();
    	}
    	return static::$instance;
	}

}