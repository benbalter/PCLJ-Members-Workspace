<?php
/*
Plugin Name: PCLJ Remove Unused Menus
Plugin URI: 
Description: Remove unused menus from admin screens
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

// Inspirtation: http://hungred.com/how-to/remove-wordpress-admin-menu-affecting-wordpress-core-system/

function pclj_remove_menus () {
	
	global $menu;
	
	//menus to remove
	$restricted = array( __('Posts'), __('Media'), __('Dashboard') );
	
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}

add_action('admin_menu', 'pclj_remove_menus');