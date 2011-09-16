<?php
/*
Plugin Name: PCLJ Workflow State Queryable
Plugin URI: 
Description: Makes workflow state front-end queryable
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

function pclj_filter_workflow_ct( $args ) {
	$args['query_var'] = true;
	$args['rewrite'] = array('slug' => 'state');
	return $args;
}

add_filter( 'document_revisions_ct' , 'pclj_filter_workflow_ct' );
