<?php
	if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		exit( 'Do not access this file directly.' );
	}

	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit();
	}

	global $wpdb;
	
	/* Remove the database entry/entries that holds all saved ads */
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		$blog_ids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs" ) );
		foreach( $blog_ids as $the_id ) {
			delete_blog_option( $the_id, 'saved_spw_ads' );
		}
	} else {
		delete_option( 'saved_spw_ads' );
	}
?>