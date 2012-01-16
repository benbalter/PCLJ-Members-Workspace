<?php
/*
Plugin Name: Don't use edit flow
Description: Tells WP Document Revisions to use its own custom taxonomy, rather than edit flow's statuses
Author: Benjamin Balter
Version: 1.0
Author URI: http://ben.balter.com/
*/

add_filter( 'document_revisions_use_edit_flow', '__return_false' ); 