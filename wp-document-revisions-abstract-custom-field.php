<?php
/*
Plugin Name: Abstract Custom Document Field
Plugin URI: 
Description: Creates abstract custom taxonomy for use with WP Document Revisions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/
 	
/**
 * Callback to register Abstract metabox
 */
function wp_document_revisions_register_abstract_metabox() {

	if ( !current_user_can( 'edit_article_metadata' ) )
		return;
	
    add_meta_box( 'document_abstract', 'Abstract', 'wp_document_revisions_abstract_cb', 'document');
}

//add action hook
add_action( 'add_meta_boxes', 'wp_document_revisions_register_abstract_metabox' );

/**
 * Callback to store Abstract field
 */
function wp_document_revisions_save_abstract( $post_id ) {
    
    //verify this is not an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    	return;
 	
 	//perms
   	if ( !current_user_can( 'edit_article_metadata' ) )
		return;
    
    //verify nonce
    if ( !wp_verify_nonce( $_POST['document_abstract_nonce'], plugin_basename( __FILE__ ) ) )
    	return;
    
    //verify permissions
    if ( !current_user_can( 'edit_post', $post_id ) )
		return;
    
    //store the data 			
	update_post_meta( $post_id, 'document_abstract', esc_html( $_POST['document_abstract'] ), true);

}

add_action( 'save_post', 'wp_document_revisions_save_abstract', 10, 1 ); 

/**
 * Callback to display Abstract metabox
 */
function wp_document_revisions_abstract_cb( ) {
    global $post;
    
    //create nonce field
    wp_nonce_field( plugin_basename( __FILE__ ), 'document_abstract_nonce' );

    //output label
    echo '<label for="document_abstract" class="screen-reader-text">Abstract</label>';
    
    //output Abstract field
    echo '<textarea name="document_abstract" id="document_abstract" rows="1" cols="40" style="height:150px;width:98%">';
    echo get_post_meta($post->ID, 'document_abstract', true) . '</textarea>';
    
} 