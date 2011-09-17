<?php
/*
Plugin Name: PCLJ Current Issue
Plugin URI: 
Description: Tells the system what the current issue is so that it can direct users to the proper pipeline
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

/**
 * Regsiter with WP's Settings API
 */
function pclj_settings_api_init() {
	add_settings_section( 'pclj_issue_settings', 'Issue Settings', 'pclj_issue_settings_cb', 'pclj' );
	add_settings_field( 'pclj_current_issue', 'Current Issue', 'pclj_current_issue_cb', 'pclj', 'pclj_issue_settings' );
	register_setting( 'pclj_issue_settings', 'pclj_current_issue', 'pclj_sanitize_current_issue' );
}

add_action( 'admin_init', 'pclj_settings_api_init' );

/** 
 * Sanitizes current issue on save, forces it to be a #
 */
function pclj_sanitize_current_issue( $issue ) {
	return (int) $issue;
}

/**
 * Callback to be run at the top of our settings section
 */
function pclj_issue_settings_cb( ) {
	echo "<p><em>Selecting the current issue below lets the workspace know what folks are working on and can then direct them to the proper place acordingly.</em></p>";
}

/**
 * Callback to be run for the current isseu field
 */
function pclj_current_issue_cb( ) { ?>
	<select name="pclj_current_issue" id="pclj_current_issue">
		<option></option>
		<?php $issues = get_terms( 'document_issue', 'hide_empty=0'); 
		foreach ( $issues as $issue ) { ?>
		<option value="<?php echo $issue->term_id; ?>" <?php selected( $issue->term_id, get_option( 'pclj_current_issue' ) ); ?>><?php echo $issue->name; ?></option>
		<?php } ?>
	</select>
<?php	
}

/**
 * Add the menu as a submenu of the Articles page
 */
function pclj_add_menu() {
	add_submenu_page( 'edit.php?post_type=document', 'PCLJ Settings', 'Settings', 'manage_options', 'pclj_settings', 'pclj_settings_page_cb' );	
}

add_action( 'admin_menu', 'pclj_add_menu' );

/**
 * Callback to build the settigns page
 */
function pclj_settings_page_cb() { ?>
	<div class="wrap">
	<h2>Members' Workspace Settings</h2>
	<br />
	<form action="options.php" method="post">
	<?php settings_fields('pclj_issue_settings'); ?>
	<?php do_settings_sections('pclj'); ?>
	<br />
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form></div>
<?php
}

/**
 * Shortcode to retrieve the current issue
 * Can be called alone [current_issue] to return the name of the current issue
 * Can be called as [current_issue format=slug] to get the slug (used for links)
 */
function pclj_current_issue_shortcode( $atts ) {

	extract( shortcode_atts( array( 
		'format' => 'name', //options are name or slug
	), $atts ) );
	
	$issueID = get_option( 'pclj_current_issue' );
	$issue = get_term( $issueID, 'document_issue' );

	if ( !$issue )
		return false;
		
	if ( $format == 'name' || $format == 'slug' )
		return $issue->$format; 
}

add_shortcode( 'current_issue', 'pclj_current_issue_shortcode' );