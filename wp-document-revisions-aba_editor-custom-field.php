<?php
/*
Plugin Name: ABA Editor Custom Document Field
Plugin URI: 
Description: Creates aba editor custom taxonomy for use with WP Document Revisions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/
 	
/**
 * Callback to register ABA Editor metabox
 */
function wp_document_revisions_register_aba_editor_metabox() {
    add_meta_box( 'document_aba_editor', 'ABA Editor', 'wp_document_revisions_aba_editor_cb', 'document');
}

//add action hook
add_action( 'add_meta_boxes', 'wp_document_revisions_register_aba_editor_metabox' );
 
/**
 * Callback to store ABA Editor field
 */
function  wp_document_revisions_save_aba_editor( $post_id ) {
    //verify this is not an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
  			return;
  		
  	//verify nonce
  	if ( !wp_verify_nonce( $_POST['document_aba_editor_nonce'], plugin_basename( __FILE__ ) ) )
  			return;
  		
  	//verify permissions
  	if ( !current_user_can( 'edit_post', $post_id ) )
   		return;
  		
  	//store the data 			
   	update_post_meta( $post_id, 'document_aba_editor', $_POST['document_aba_editor'], true);
}

add_action( 'save_post', 'wp_document_revisions_save_aba_editor', 10, 1 ); 

/**
 * Callback to display ABA Editor metabox
 */
function wp_document_revisions_aba_editor_cb( $post) {
    global $wpdb;
    
    //output nonce field
    wp_nonce_field( plugin_basename( __FILE__ ), 'document_aba_editor_nonce' );
    
    //get list of authors
    $authors = pclj_get_users_by_role( 'aba_member' );
    
    //output label
    echo '<label for="document_aba_editor">ABA Editor</label>:';
    
    //output ABA Editor dropdown
    echo '<select name="document_aba_editor" id="document_aba_editor" style="margin-left: 25px;">';
    echo '<option></option>';
    foreach ( $authors as $author )
    	echo '<option value="' . $author->user_nicename . '" ' . selected( $author->user_nicename, get_post_meta( $post->ID, 'document_aba_editor' , true ) ) . ' >' .  $author->display_name . '</option>';
    echo '</select>';
}

/**
 * Gets all users with a given role
 * @param string $role the role
 * @returns array of user objects
 */
function pclj_get_users_by_role( $role ) {
    $wp_user_search = new WP_User_Query( array( 'role' => $role, 'fields' => 'all' ) );
    return $wp_user_search->get_results();
}