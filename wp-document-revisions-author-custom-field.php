<?php
/*
Plugin Name: Author Custom Document Field
Plugin URI: 
Description: Creates author custom taxonomy for use with WP Document Revisions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/
 	
 	 	/**
 		 * Callback to register Author metabox
 		 */
 	 	function wp_document_revisions_register_author_metabox() {
			add_meta_box( 'document_author', 'Author', 'wp_document_revisions_author_cb', 'document');
 		}
 		
 		//add action hook
	 	add_action( 'add_meta_boxes', 'wp_document_revisions_register_author_metabox' );

 		/**
 		 * Callback to store Author field
 		 */
		function wp_document_revisions_save_author( $post_id ) {
 			
 			//verify this is not an autosave
 			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      			return;
      		
      		//verify nonce
      		if ( !wp_verify_nonce( $_POST['document_author_nonce'], plugin_basename( __FILE__ ) ) )
      			return;
      		
      		//verify permissions
      		if ( !current_user_can( 'edit_post', $post_id ) )
       			return;
      		
      		//store the data 			
       		update_post_meta( $post_id, 'document_author', $_POST['document_author'], true);
 		
 		}
 		
 		add_action( 'save_post', 'wp_document_revisions_save_author', 10, 1 ); 
 	
 		/**
 		 * Callback to display Author metabox
 		 */
 		function wp_document_revisions_author_cb( ) {
 			global $post;
 			
 			//create nonce field
			wp_nonce_field( plugin_basename( __FILE__ ), 'document_author_nonce' );
		
			//output label
			echo '<label for="document_author">Author</label>:';
			
			//output Author field
			echo '<input type="text" name="document_author" id="document_author" style="margin-left:25px;"';
			echo ' value="' . get_post_meta($post->ID, 'document_author', true) . '" />';
			
 		} 		
 		
 	 	
 	
/***********************************
WP Document Revisions Custom Field
a:4:{s:4:"name";s:6:"Author";s:11:"name_plural";s:7:"Authors";s:4:"slug";s:6:"author";s:4:"type";s:4:"text";}
************************************/
 	?>