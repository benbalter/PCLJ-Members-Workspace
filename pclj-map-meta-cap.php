<?php
/*
Plugin Name: PCLJ Capabilities Tweaks
Plugin URI: 
Description: Fixes anoyances caused by users not having edit_post permissions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

/**
 * Map read_private_document to read_private_any
 */
function pclj_allow_read_private( $caps, $cap ) {

	if ( 'read_private_anys' == $cap ) {
		$caps[] = 'read_private_documents';
		unset( $caps[ array_search( 'read_private_anys', $caps ) ] );
	}

	return $caps;
}

add_filter( 'map_meta_cap', 'pclj_allow_read_private', 10, 2);

/**
 * Because WordPress looks to edit_post by default to determine if a user can upload,
 * map that check to edit_document
 * 
 * NOTE: there are no posts on this server, so no post_type check is necessary
 *
 */
function pclj_allow_upload( ) {
	global $wp_post_types;
	$wp_post_types['attachment']->cap->edit_post = 'edit_documents';
}

add_action( 'wp_loaded', 'pclj_allow_upload' );

function pclj_edit_flow_user_groups_workaround( $query ) {
	global $wpdb;
	
	if ( !isset( $query->query_vars['meta_key'] ) )
		return $query;

	if ( $query->query_vars['meta_key'] != $wpdb->prefix . 'user_level' )
		return $query;
	
	unset( $query->query_vars['meta_key'] );
	unset( $query->query_vars['meta_value'] );
	unset( $query->query_vars['meta_compare'] );
	unset( $query->query_vars['who'] );
	
	$where = "({$wpdb->usermeta}.meta_key = '{$wpdb->prefix}user_level' AND CAST({$wpdb->usermeta}.meta_value AS CHAR) != '0')";
	
	$query->query_where = str_replace( $where, '1=1', $query->query_where );
	
	$query->query_fields = 'DISTINCT ' . $query->query_fields;
		
	return $query;
	
}

add_filter( 'pre_user_query', 'pclj_edit_flow_user_groups_workaround', 10 );
