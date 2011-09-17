<?php
/*
Plugin Name: Issue Custom Document Field
Plugin URI: 
Description: Creates issue custom taxonomy for use with WP Document Revisions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/
	
	/**
 	 * Callback to register Issue custom document taxonomy
 	 */
		function wp_document_revisions_register_issue_ct() {
	
			$labels = array(
			  'name' => _x( 'Issues', 'taxonomy general name' ),
			  'singular_name' => _x( 'Issue', 'taxonomy singular name' ),
			  'search_items' =>  __( 'Search Issues' ),
			  'all_items' => __( 'All Issues' ),
			  'parent_item' => __( 'Parent Issue' ),
			  'parent_item_colon' => __( 'Parent Issue:' ),
			  'edit_item' => __( 'Edit Issue' ), 
			  'update_item' => __( 'Update Issue' ),
			  'add_new_item' => __( 'Add New Issue' ),
			  'new_item_name' => __( 'New Issue Name' ),
			  'menu_name' => __( 'Issue' ),
			); 	
			
			register_taxonomy('document_issue',array('document'), array(
			  'hierarchical' => true,
			  'labels' => $labels,
			  'show_ui' => true,
			  'public' => true,
			  'rewrite' => array( 'slug' => 'issue' ),
			  'capabilities' => array( 
				'manage_terms' => 'manage_categories',
				'edit_terms' => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_documents',
				),
			));	
					  
		}
	  
	 //add action hook
	add_action( 'init', 'wp_document_revisions_register_issue_ct');
	
 	 	
 	
/***********************************
WP Document Revisions Custom Field
a:4:{s:4:"name";s:5:"Issue";s:11:"name_plural";s:6:"Issues";s:4:"slug";s:5:"issue";s:4:"type";s:12:"hierarchical";}
************************************/
 	?>