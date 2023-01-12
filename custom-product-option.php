<?php

/**
 * Plugin Name: Custom Product Option
 * Description: Add & remove Multiple Image uplode in Product Single Page and Cart Page.
 * Author: TRooBound
 * Version: 0.0.1
 */

if ( ! class_exists( 'cpo' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-cpo.php';
}

function cpo_plugin_create_tables() {
    /*global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'smfen_bookings';
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		service_id text NOT NULL,
		service_name text NOT NULL,
		preduration text NOT NULL,
		servicestart text NOT NULL,
		serviceend text NOT NULL,
		postduration text NOT NULL,
		booking_date text NOT NULL,
		employee_index text NOT NULL,
		price text NOT NULL,
		firstname text NOT NULL,
		lastname text NOT NULL,
		address_line1 text NOT NULL,
		address_line2 text NOT NULL,
		zipcode text NOT NULL,
		phone text NOT NULL,
		email text NOT NULL,
		date text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );*/
}
register_activation_hook( __FILE__, 'cpo_plugin_create_tables' );

