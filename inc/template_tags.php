<?php

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

/** 
* Template tag to show a project wonderful ad.
*
* Echos the result of spw_ad_func().
*
* @since 1.0.0
* @package simple_pw_ads
*
* @param	str	$ad	The Project Wonderful ad number with underscores.
*/
if ( ! function_exists( 'spw_insert_ad' ) ) {
	function spw_insert_ad( $ad ) {
		echo $spw_ads_instance->spw_ad_func( $ad );
	}
}