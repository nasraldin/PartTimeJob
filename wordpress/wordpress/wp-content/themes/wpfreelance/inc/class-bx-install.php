<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class BX_Install{


	private static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$t = dbDelta( self::get_schema() );
		$flag_name = $wpdb->prefix.'_installed';
		update_option($flag_name, '1084');
		return $t;
	}
	public static function install() {
		global $wpdb;
		$flag_name = $wpdb->prefix.'_installed';

		if( (int) get_option( $flag_name, false ) !== '1084' ){
			return 	self::create_tables();
		}
		return false;
	}

	private static function get_schema() {

		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		$max_index_length = 191;

		$tables = "
			CREATE TABLE {$wpdb->prefix}box_messages (
			  	ID bigint(20) NOT NULL AUTO_INCREMENT,
			  	cvs_id bigint(20) NOT NULL,
			  	sender_id  bigint(20)  NULL,
			  	receiver_id  bigint(20) NOT NULL,
			  	msg_status  char(15)  NULL,
			  	msg_type  char(15)  NULL, /* message or notify*/
			  	msg_unread  bigint(20) NULL,
			  	msg_content longtext NOT NULL,
			  	msg_date datetime NULL default null,
			  	time_gmt bigint(20) NOT NULL,
			  	attach_ids varchar(100)  NULL,
			  	PRIMARY KEY  (ID),
			 	 	UNIQUE KEY ID (ID)
				) $collate;
			CREATE TABLE {$wpdb->prefix}box_conversations (
			  	ID bigint(20) NOT NULL AUTO_INCREMENT,
			  	cvs_author  bigint(20) NOT NULL,
			  	project_id  bigint(20) NOT NULL,
			  	receiver_id  bigint(20) NOT NULL,
			  	cvs_content longtext NOT NULL,
			 	cvs_status char(15) NOT NULL,
			 	msg_unread bigint(20) NULL,
			 	date_created datetime NULL default null,
			 	date_modify datetime NULL default null,
			  	PRIMARY KEY  (ID),
			  	UNIQUE KEY ID (ID)
			) $collate;";


		return $tables;
	}
}
//BX_Install::install();