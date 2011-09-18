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
 	 * Callback to register ABA Editor custom document taxonomy
 	 */
		function wp_document_revisions_register_aba_editor_ct() {
	
			$labels = array(
			  'name' => _x( 'ABA Editors', 'taxonomy general name' ),
			  'singular_name' => _x( 'ABA Editor', 'taxonomy singular name' ),
			  'search_items' =>  __( 'Search ABA Editors' ),
			  'all_items' => __( 'All ABA Editors' ),
			  'parent_item' => __( 'Parent ABA Editor' ),
			  'parent_item_colon' => __( 'Parent ABA Editor:' ),
			  'edit_item' => __( 'Edit ABA Editor' ), 
			  'update_item' => __( 'Update ABA Editor' ),
			  'add_new_item' => __( 'Add New ABA Editor' ),
			  'new_item_name' => __( 'New ABA Editor Name' ),
			  'menu_name' => __( 'ABA Editor' ),
			); 	
			
			register_taxonomy('document_aba_editor',array('document'), array(
			  'hierarchical' => true,
			  'labels' => $labels,
			  'show_ui' => true,
			  'public' => false,
			  'rewrite' =>false,
			));	
		  
		}
	  
	 //add action hook
	add_action( 'init', 'wp_document_revisions_register_aba_editor_ct');
	
 		
	/**
 	 * Callback to register ABA Editor metabox
 	 */
 	function wp_document_revisions_register_aba_editor_metabox() {
 	
		//remove default metabox 		
 		remove_meta_box( 'document_aba_editordiv', 'document', 'side');

		if ( !current_user_can( 'edit_article_metadata' ) )
			return;
		

		//add custom metabox
 		add_meta_box( 'document_aba_editor', 'ABA Editor', 'wp_document_revisions_aba_editor_cb', 'document', 'side');
 	
 	}
 	
 	//add action hook
 	add_action( 'add_meta_boxes', 'wp_document_revisions_register_aba_editor_metabox' );
 	
/**
 * Generates the ABA Editor taxonomy radio inputs 
 * @params object $post the post object WP passes
 * @params object $box the meta box object WP passes (with our arg stuffed in there)
 */
function wp_document_revisions_aba_editor_cb( $post ) {
	
	//get the taxonomy and labels		
	$taxonomy = get_taxonomy( 'document_aba_editor' );
	
	//grab an array of all terms within our custom taxonomy, including empty terms
	$terms = get_terms( 'document_aba_editor', array( 'hide_empty' => false ) );

	//garb the current selected term where applicable so we can select it
	$current = wp_get_object_terms( $post->ID, 'document_aba_editor' );
	
	//loop through the terms
	foreach ($terms as $term) {
		
		//build the radio box with the term_id as its value
		echo '<input type="radio" name="document_aba_editor" value="'.$term->term_id.'" id="'.$term->slug.'"';
		
		//if the post is already in this taxonomy, select it
		if ( isset( $current[0]->term_id ) )
			checked( $term->term_id, $current[0]->term_id );
		
		//build the label
		echo '> <label for="'.$term->slug.'">' . $term->name . '</label><br />'. "\r\n";
	}
		echo '<input type="radio" name="document_aba_editor" value="" id="none" ';
		checked( empty($current[0]->term_id) );
		echo '/> <label for="none">None</label><br />'. "\r\n"; 
		?>		
		<a href="#" id="add_document_aba_editor_toggle">+ <?php echo $taxonomy->labels->add_new_item; ?></a>
		<div id="add_document_aba_editor_div" style="display:none">
			<label for="new_document_aba_editor"><?php echo $taxonomy->labels->singular_name; ?>:</label> 
			<input type="text" name="new_document_aba_editor" id="new_document_aba_editor" /><br />
			<input type="button" value="Add New" id="add_document_aba_editor_button" />
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="document_aba_editor-ajax-loading" style="display:none;" alt="" />
		</div>
		<script>
			jQuery(document).ready(function($){
				$('#add_document_aba_editor_toggle').click(function(event){
					$('#add_document_aba_editor_div').toggle();
					event.preventDefault();
				});
				$('#add_document_aba_editor_button').click(function() {
					$('#document_aba_editor-ajax-loading').show();
					$.post('admin-ajax.php?action=add_document_aba_editor', $('#new_document_aba_editor, #new_document_aba_editor_location, #_ajax_nonce-add-document_aba_editor, #post_ID').serialize(), function(data) { 
						$('#document_aba_editor .inside').html(data); 
						});
				});
			});
		</script>
	<?php
	//nonce is a funny word
	wp_nonce_field( 'add_document_aba_editor', '_ajax_nonce-add-document_aba_editor' );
	wp_nonce_field( 'document_aba_editor', 'document_aba_editor_nonce'); 
}

/**
 * Processes AJAX request to add new ABA Editor terms
 * @since 1.2
 */
function wp_document_revisions_aba_editor_ajax_add() {

	if ( !current_user_can( 'edit_article_metadata' ) )
		return;	
	
	//pull the taxonomy from the action query var
	$type = substr($_GET['action'],4);
	
	//pull up the taxonomy details
	$taxonomy = get_taxonomy($type);
	
	//check the nonce
	check_ajax_referer( $_GET['action'] , '_ajax_nonce-add-document_aba_editor' );
	
	//check user capabilities
	if ( !current_user_can( $taxonomy->cap->edit_terms ) )
		die('-1');

	//insert term
	$term = wp_insert_term( $_POST['new_document_aba_editor'], 'document_aba_editor' );
	
  	//get updated post to send to taxonomy box
	$post = get_post( $_POST['post_ID'] );
	
	//return the HTML of the updated metabox back to the user so they can use the new term
	wp_document_revisions_aba_editor_cb( $post );
	exit();
}

add_action('wp_ajax_add_document_aba_editor', 'wp_document_revisions_aba_editor_ajax_add');

		/**
 		 * Callback to store ABA Editor custom taxonomy
 		 */
		function wp_document_revisions_save_aba_editor( $post_id ) {
 			
 			//verify this is not an autosave
 			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      			return;
      		
      		//verify nonce
      		if ( !wp_verify_nonce( $_POST['document_aba_editor_nonce'], 'document_aba_editor' ) )
      			return;
      		
      		//verify permissions
      		if ( !current_user_can( 'edit_post', $post_id ) )
       			return;
       			
			if ( !current_user_can( 'edit_article_metadata' ) )
				return;
      		
      		//associate taxonomy with parent, not revision
			if ( wp_is_post_revision( $post_id ) )
				$post_id = wp_is_post_revision( $post_id );
      		
      		//store the data 			
       		wp_set_post_terms( $post_id,  $_POST['document_aba_editor'], 'document_aba_editor', false);
 		
 		}

 		add_action( 'save_post', 'wp_document_revisions_save_aba_editor', 10, 1 ); 