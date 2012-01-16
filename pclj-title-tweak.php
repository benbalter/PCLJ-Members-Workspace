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

	$author = get_post_meta( $postID, '_ef_editorial_meta_text_author', true );
	
	if ( !$author || strlen ( $author ) == 0 )
		return $title;
		
	$last = substr( $author, strrpos( $author, ' ' ) );
	
	if ( pclj_get_type( $postID ) == 'note' )
		return $last . ' (Note) - ' . $title;	
	
	return $last . ' - ' . $title;
	
}

add_filter( 'the_title', 'pclj_add_author_to_title', 10, 2 );

/**
 * Gets the type from the type taxonomy
 * @param int $postID the post ID
 * @returns bool|string the type, false on failure
 */
function pclj_get_type( $postID ) {
    $type = wp_get_post_terms( $postID, 'document_type' );	

    if ( !$type )
    	return false;
    	
    return $type[0]->slug;
}