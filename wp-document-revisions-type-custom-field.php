<?php
/*
Plugin Name: Type Custom Document Field
Plugin URI: 
Description: Creates type custom taxonomy for use with WP Document Revisions
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/
	
	/**
 	 * Callback to register Type custom document taxonomy
 	 */
		function wp_document_revisions_register_type_ct() {
	
			$labels = array(
			  'name' => _x( 'Types', 'taxonomy general name' ),
			  'singular_name' => _x( 'Type', 'taxonomy singular name' ),
			  'search_items' =>  __( 'Search Types' ),
			  'all_items' => __( 'All Types' ),
			  'parent_item' => __( 'Parent Type' ),
			  'parent_item_colon' => __( 'Parent Type:' ),
			  'edit_item' => __( 'Edit Type' ), 
			  'update_item' => __( 'Update Type' ),
			  'add_new_item' => __( 'Add New Type' ),
			  'new_item_name' => __( 'New Type Name' ),
			  'menu_name' => __( 'Type' ),
			); 	
			
			register_taxonomy('document_type',array('document'), array(
			  'hierarchical' => true,
			  'labels' => $labels,
			  'show_ui' => true,
			  'public' => true,
			  'rewrite' => array( 'slug' => 'type' ),
			));	
		  
		}
	  
	 //add action hook
	add_action( 'init', 'wp_document_revisions_register_type_ct');
	
 		
	/**
 	 * Callback to register Type metabox
 	 */
 	function wp_document_revisions_register_type_metabox() {
		
		//remove default metabox 		
 		remove_meta_box( 'document_typediv', 'document', 'side');

		//add custom metabox
 		add_meta_box( 'document_type', 'Type', 'wp_document_revisions_type_cb', 'document', 'side');
 	
 	}
 	
 	//add action hook
 	add_action( 'add_meta_boxes', 'wp_document_revisions_register_type_metabox' );
 	
/**
 * Generates the Type taxonomy radio inputs 
 * @params object $post the post object WP passes
 * @params object $box the meta box object WP passes (with our arg stuffed in there)
 */
function wp_document_revisions_type_cb( $post ) {
	
	//get the taxonomy and labels		
	$taxonomy = get_taxonomy( 'document_type' );
	
	//grab an array of all terms within our custom taxonomy, including empty terms
	$terms = get_terms( 'document_type', array( 'hide_empty' => false ) );

	//garb the current selected term where applicable so we can select it
	$current = wp_get_object_terms( $post->ID, 'document_type' );
	
	//loop through the terms
	foreach ($terms as $term) {
		
		//build the radio box with the term_id as its value
		echo '<input type="radio" name="document_type" value="'.$term->term_id.'" id="'.$term->slug.'"';
		
		//if the post is already in this taxonomy, select it
		if ( isset( $current[0]->term_id ) )
			checked( $term->term_id, $current[0]->term_id );
		
		//build the label
		echo '> <label for="'.$term->slug.'">' . $term->name . '</label><br />'. "\r\n";
	}
		echo '<input type="radio" name="document_type" value="" id="none" ';
		checked( empty($current[0]->term_id) );
		echo '/> <label for="none">None</label><br />'. "\r\n"; 
		?>		
		<a href="#" id="add_document_type_toggle">+ <?php echo $taxonomy->labels->add_new_item; ?></a>
		<div id="add_document_type_div" style="display:none">
			<label for="new_document_type"><?php echo $taxonomy->labels->singular_name; ?>:</label> 
			<input type="text" name="new_document_type" id="new_document_type" /><br />
			<input type="button" value="Add New" id="add_document_type_button" />
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="document_type-ajax-loading" style="display:none;" alt="" />
		</div>
		<script>
			jQuery(document).ready(function($){
				$('#add_document_type_toggle').click(function(event){
					$('#add_document_type_div').toggle();
					event.preventDefault();
				});
				$('#add_document_type_button').click(function() {
					$('#document_type-ajax-loading').show();
					$.post('admin-ajax.php?action=add_document_type', $('#new_document_type, #new_document_type_location, #_ajax_nonce-add-document_type, #post_ID').serialize(), function(data) { 
						$('#document_type .inside').html(data); 
						});
				});
			});
		</script>
	<?php
	//nonce is a funny word
	wp_nonce_field( 'add_document_type', '_ajax_nonce-add-document_type' );
	wp_nonce_field( 'document_type', 'document_type_nonce'); 
}

/**
 * Processes AJAX request to add new Type terms
 * @since 1.2
 */
function wp_document_revisions_type_ajax_add() {
	
	//pull the taxonomy from the action query var
	$type = substr($_GET['action'],4);
	
	//pull up the taxonomy details
	$taxonomy = get_taxonomy($type);
	
	//check the nonce
	check_ajax_referer( $_GET['action'] , '_ajax_nonce-add-document_type' );
	
	//check user capabilities
	if ( !current_user_can( $taxonomy->cap->edit_terms ) )
		die('-1');

	//insert term
	$term = wp_insert_term( $_POST['new_document_type'], 'document_type' );
	
  	//get updated post to send to taxonomy box
	$post = get_post( $_POST['post_ID'] );
	
	//return the HTML of the updated metabox back to the user so they can use the new term
	wp_document_revisions_type_cb( $post );
	exit();
}

add_action('wp_ajax_add_document_type', 'wp_document_revisions_type_ajax_add');

		/**
 		 * Callback to store Type custom taxonomy
 		 */
		function wp_document_revisions_save_type( $post_id ) {
 			
 			//verify this is not an autosave
 			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      			return;
      		
      		//verify nonce
      		if ( !isset( $_POST['document_type_nonce'] ) ||
      			 !wp_verify_nonce( $_POST['document_type_nonce'], 'document_type' ) )
      			return;
      		
      		//verify permissions
      		if ( !current_user_can( 'edit_post', $post_id ) )
       			return;
      		
      		//associate taxonomy with parent, not revision
			if ( wp_is_post_revision( $post_id ) )
				$post_id = wp_is_post_revision( $post_id );
      		
      		//store the data 			
       		wp_set_post_terms( $post_id,  $_POST['document_type'], 'document_type', false);
 		
 		}

 		add_action( 'save_post', 'wp_document_revisions_save_type', 10, 1 ); 

 	
 	
/***********************************
WP Document Revisions Custom Field
a:4:{s:4:"name";s:4:"Type";s:11:"name_plural";s:5:"Types";s:4:"slug";s:4:"type";s:4:"type";s:9:"exclusive";}
************************************/
 	?>