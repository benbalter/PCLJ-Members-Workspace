<?php
/*
Plugin Name: PCLJ Force Login
Plugin URI: 
Description: Locks down site to non-logged in users
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

add_action( 'get_header', 'pclj_lock_down' );

function pclj_lock_down() {
	
	if ( !current_user_can( 'read' ) ) {
		wp_redirect( home_url( add_query_arg( 'redirect_to', 
urlencode( stripslashes( $_SERVER['REQUEST_URI'] ) ), 'wp-login.php' ) ) );
		exit();
	}	

}