<?php
/*
Plugin Name: Simple PW Ads
Plugin URI: http://interruptedreality.com/plugins/simple-pw-ads/
Description: Adds easy ways to insert Project Wonderful ads into your site.
Version: 3.0.3
Author: Big Bagel
Author URI: http://interruptedreality.com
License: GPL2
	
Copyright 2011  Eric B.  (email : bigbagel@interruptedreality.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

require_once( plugin_dir_path( __FILE__ ) . 'inc/public.php' );
if ( class_exists( 'simple_pw_ads' ) ) {
	global $spw_ads_instance;
	$spw_ads_instance = new simple_pw_ads();

	if ( isset( $spw_ads_instance ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'inc/widget.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'inc/template_tags.php' );
	
		if ( is_admin() ) {
			require_once( plugin_dir_path( __FILE__ ) . 'inc/admin.php' );
			global $spw_admin_instance;
			$spw_admin_instance = new simple_pw_admin();
		}
	}
}