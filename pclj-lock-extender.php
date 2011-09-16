<?php
/*
Plugin Name: PCLJ Lock Extender
Plugin URI: 
Description: Extends the default 2 minutes file lock buffer (how long a file is checked out if nothing is heard) to 5 minutes
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

function pclj_extend_file_lock( $original ) {

	$wpdr = Document_Revisions::$instance;
	
	if ( !$wpdr->verify_post_type() )
		return $original;
		
	// 5 min * 60 seconds = 300 seconds
	return 300;
}
	
add_filter( 'wp_check_post_lock_window', 'pclj_extend_file_lock' );