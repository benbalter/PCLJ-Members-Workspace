<?php
/*
Plugin Name: PCLJ Notifications 
Description: Provides notifications to senior editors and up when document state changes
Version: 1.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
*/

class PCLJ_Notifications {

	function __construct( ) {
		add_action( 'change_document_workflow_state', array( &$this, 'change_notification'), 10, 2); 
	}
	
	/**
	 * Hook Callback
	 * @param int $postID the ID of the post being changed
	 * @param string state the slug of the state we're changing to
	 */
	function change_notification( $postID, $state ) {
		
		$users = array();
		
		//send different notifications to different users...
		switch( $state ) {
			case 'eic-signoff':
				$users = $this->get_users_by_role( 'eic' );
			break;
			case 'sme-review':
				$users = $this->get_users_by_role( 'sme' );		
			break;
			case 'senior-review':
			
				$type = $this->get_type( $postID );
				if ( !$type )
					return;
					
				if ( $type == 'article' )		
					$users = $this->get_users_by_role( 'sae' );
				else
					$users = $this->get_users_by_role( 'sne' );
					
			break;
		}
		
		if ( empty( $users ) )
			return;
			
		$this->send_notifications( $users, $postID, $state );
		
	} 
	
	/**
	 * Gets the type from the type taxonomy
	 * @param int $postID the post ID
	 * @returns bool|string the type, false on failure
	 */
	function get_type( $postID ) {
		$type = wp_get_post_terms( $postID, 'document_type' );	
	
		if ( !$type )
			return false;
			
		return $type[0]->slug;
	}
	
	/**
	 * Formats and sends the notification
	 * @param array $users array of users to notify
	 * @param int $postID the doc id
	 * @param string $state the slug of the state being transitioned to
	 */
	function send_notifications( $users, $postID, $state ) {
		
		$current_user = wp_get_current_user();
		$post = get_post( $postID );
		$state = get_term_by( 'slug', $state, 'workflow_state' );
		
		$subject = '[PCLJ] ' . $post->post_title . ' has been marked ' . $state->name;
		$message = 'The document ' . $post->post_title . ' has been marked ' . $state->name . ' by ' . $current_user->display_name . '.' . "\n\n";
		$message .= 'View: ' . get_permalink( $postID ) . "\n";
		$message .= 'Edit: ' . admin_url( 'post.php?post=' . $postID . '&action=edit' );

		foreach ( $users as $user ) {

			//don't send if they were the one who took the action
			if ( $user->ID == $current_user->ID )
				return;
			
			wp_mail( $user->user_email, $subject, $message );
				
		}
		
	}

	/**
	 * Gets all users with a given role
	 * @param string $role the role
	 * @returns array of user objects
	 */
	function get_users_by_role( $role ) {
		$wp_user_search = new WP_User_Query( array( 'role' => $role, 'fields' => 'all' ) );
		return $wp_user_search->get_results();
	}
}

new PCLJ_Notifications;