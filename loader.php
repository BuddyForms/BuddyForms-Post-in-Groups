<?php

/*
Plugin Name: BuddyForms Posts in Groups
Plugin URI: http://buddyforms.com/downloads/buddyforms-posts-in-groups/
Description: BuddyForms Posts in Groups
Version: 1.0
Author: svenl77, buddyforms
Author URI: https://profiles.wordpress.org/svenl77
Licence: GPLv3
Network: false

*****************************************************************************
*
* This script is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
****************************************************************************
*/

function buddyforms_post_in_groups_load_constants() {

	define( 'BUDDYFORMS_PIG', '0.1' );

	if ( ! defined( 'BUDDYFORMS_PIG_INSTALL_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_INSTALL_PATH', dirname( __FILE__ ) . '/' );
	}

	if ( ! defined( 'BUDDYFORMS_PIG_INCLUDES_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_INCLUDES_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'includes/' );
	}

	if ( ! defined( 'BUDDYFORMS_PIG_TEMPLATE_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_TEMPLATE_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'templates/' );
	}

}

add_action( 'init', 'buddyforms_post_in_groups_load_constants' );

function buddyforms_pig_init() {

	$buddyforms_pig = get_option( 'buddyforms_pig_options' );

	require( dirname( __FILE__ ) . '/includes/functions.php' );

	if ( $buddyforms_pig['permission'] != 'disabled' ) {
		require( dirname( __FILE__ ) . '/includes/buddyforms-post-in-groups.php' );
		bp_register_group_extension( 'BuddyForms_Post_in_Groups' );
	}

}

add_action( 'bp_loaded', 'buddyforms_pig_init' );

function buddyforms_pig_admin_init() {
	require( dirname( __FILE__ ) . '/includes/admin/admin.php' );
}

add_action( 'admin_init', 'buddyforms_pig_admin_init' );

//
// Check the plugin dependencies
//
add_action('init', function(){

	// Only Check for requirements in the admin
	if(!is_admin()){
		return;
	}

	// Require TGM
	require ( dirname(__FILE__) . '/includes/resources/tgm/class-tgm-plugin-activation.php' );

	// Hook required plugins function to the tgmpa_register action
	add_action( 'tgmpa_register', function(){

		// Create the required plugins array
		$plugins = array(
			array(
				'name'              => 'BuddyPress',
				'slug'              => 'buddypress',
				'required'          => true,
			),
			array(
				'name'              => 'BuddyForms',
				'slug'              => 'buddyforms',
				'required'          => true,
			),
		);

		$config = array(
			'id'           => 'buddyforms-tgmpa',  // Unique ID for hashing notices for multiple instances of TGMPA.
			'parent_slug'  => 'plugins.php',       // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                // Show admin notices or not.
			'dismissable'  => false,               // If false, a user cannot dismiss the nag message.
			'is_automatic' => true,                // Automatically activate plugins after installation or not.
		);

		// Call the tgmpa function to register the required plugins
		tgmpa( $plugins, $config );

	} );
}, 1, 1);
