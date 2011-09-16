<?php
/*
Plugin Name: PCLJ Recently Revised Widget
Plugin URI: https://github.com/benbalter/WP-Document-Revisions-Code-Cookbook
Description: Widget to display recently revised documents
Version: 1.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

class pclj_recently_revised_documents extends WP_Widget {

	function __construct() {
		parent::WP_Widget( 'pclj_recently_revised_documents', $name = 'Recently Revised Documents' );
		add_action( 'widgets_init', create_function( '', 'return register_widget("pclj_recently_revised_documents");' ) );
	}
	
	function widget( $args, $instance ) {
		
		$wpdr = Document_Revisions::$instance;
		
		extract( $args );
 		
 		echo $before_widget; 
 		
		echo $before_title . 'Recently Revised Documents' . $after_title;	
		
		$query = array( 
				'post_type' => 'document',
				'orderby' => 'modified',
				'order' => 'DESC',
				'numberposts' => '5',
				'post_status' => array( 'private', 'publish', 'draft' ),
		);
		
		$documents = get_posts( $query );

		echo "<ul>\n";
		foreach ( $documents as $document ) {
		 
			//use our function to get post data to correct WP's author bug
			$revision = $wpdr->get_latest_revision( $document->ID );

		?>
			<li><a href="<?php echo get_edit_post_link( $revision->ID ); ?>"><?php echo $revision->post_title; ?></a><br />
			<?php echo human_time_diff( strtotime( $revision->post_modified_gmt ) ); ?> ago by <?php echo  get_the_author_meta( 'display_name', $revision->post_author ); ?>
			</li>
		<?php }
		
		echo "</ul>\n";
		
		echo $after_widget;
		
	}

}

new pclj_recently_revised_documents;

