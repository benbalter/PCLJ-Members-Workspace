<?php
/*
Plugin Name: PCLJ Format Edit Flow Metadata
Description: Formats edit flow metadata displayed on front end
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

function pclj_postmeta_key_filter( $key ) {

	if ( substr( $key, 0, 3) == '_ef' )
		return $key;

	if ( substr( $key, 0, 1 ) == '_' )
		return false;
		
		return $key;

}

function pclj_postmeta_key_format( $key ) {
	
	if ( substr( $key, 0, 3) != '_ef' )
		return $key;

	$key = str_replace( '_ef_editorial_meta_text', '', $key );
	$key = str_replace( '_ef_editorial_meta_paragraph', '', $key );

	return $key;
}

function pclj_remove_postmeta_filter( ) {
	global $dcf;
	remove_filter( 'dcf_postmeta_key', array( $dcf, 'hidden_postmeta_filter'), 5 );
}

add_action( 'init', 'pclj_remove_postmeta_filter' );
add_filter( 'dcf_postmeta_key', 'pclj_postmeta_key_filter', 5 );
add_filter( 'dcf_postmeta_key', 'pclj_postmeta_key_format', 6 );
