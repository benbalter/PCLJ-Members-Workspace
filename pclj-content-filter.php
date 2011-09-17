<?php
/*
Plugin Name: PCLJ Content Filter
Plugin URI: 
Description: Displays document metadata on front end
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

add_filter( 'the_content', 'pclj_the_content_filter' );

function pclj_the_content_filter( $content ) {
	global $post;
	
	$wpdr = Document_Revisions::$instance;
	
	if ( !$wpdr->verify_post_type() )
		return $content;

	$content = "";
	$abstract = get_post_meta( $post->ID, 'document_abstract', true );
	$latest = $wpdr->get_latest_revision( $post->ID );
	$last_modified = human_time_diff( strtotime( $latest->post_modified_gmt ) ) . " ago by " . get_the_author_meta( 'display_name', $latest->post_author );
	
	$aba_editor = get_post_meta( $post->ID, 'document_aba_editor', true );
	if ( $aba_editor ) {
		$aba_editor = get_user_by( 'slug', $aba_editor );
		$aba_editor = $aba_editor->display_name;
	}

	$metas = array( 'Author' =>  get_post_meta( $post->ID, 'document_author', true ), 
					'Issue' => pclj_get_exclusive_term( $post->ID, 'document_issue' ),
					'Current Workflow State' => pclj_get_exclusive_term( $post->ID, 'workflow_state' ),
					'Student Editor' => pclj_get_exclusive_term( $post->ID, 'document_editor' ),
					'ABA Editor' => $aba_editor,
					'Last Modified' => $last_modified,
					);
	
	$content .= '<p>';
	foreach ( $metas as $label => $value ) {
		if ( $value )
			$content .= "<strong>$label:</strong> " . esc_html( $value ) . "<br />";
	}
	$content .= '</p>';
	
	if ( $abstract ) 
		$content .= "<p>" . nl2br( esc_html( $abstract ) ) . "</p>";
		
	return $content;
}

function pclj_get_exclusive_term( $postID, $taxonomy ) {
	
	$terms = wp_get_post_terms( $postID, $taxonomy );
		
	if ( sizeof( $terms ) == 0)
		return false;
		

	return $terms[0]->name;
	
}