<?php
/*
Plugin Name: PCLJ Title Tweak
Plugin URI: 
Description: Appends Author's name to titles when displayed on front end
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

function pclj_add_author_to_title( $title, $postID ) {

	$wpdr = Document_Revisions::$instance;
	
	if ( !$wpdr->verify_post_type( $postID ) )
		return $title;

	$author = get_post_meta( $postID, 'document_author', true );
	
	if ( !$author || strlen ( $author ) == 0 )
		return $title;
		
	$last = substr( $author, strrpos( $author, ' ' ) );
	
	return $last . ' - ' . $title;
	
}

add_filter( 'the_title', 'pclj_add_author_to_title', 10, 2 );