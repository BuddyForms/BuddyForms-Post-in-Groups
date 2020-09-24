<?php
/*
 * Plugin Name: BuddyForms Posts in Groups
 * Plugin URI: http://buddyforms.com/downloads/buddyforms-posts-in-groups/
 * Description: BuddyForms Posts in Groups
 * Version: 1.0.3
 * Author: svenl77, buddyforms, gfirem
 * Author URI: https://profiles.wordpress.org/svenl77
 * Licence: GPLv3
 * Network: false
 * Text Domain: buddyforms-post-in-groups
 * Domain Path: /languages
 *
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

	define( 'BUDDYFORMS_PIG', '1.0.3' );

	if ( ! defined( 'BUDDYFORMS_PIG_INSTALL_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_INSTALL_PATH', dirname( __FILE__ ) . '/' );
	}

	if ( ! defined( 'BUDDYFORMS_PIG_INCLUDES_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_INCLUDES_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'includes/' );
	}

	if ( ! defined( 'BUDDYFORMS_PIG_TEMPLATE_PATH' ) ) {
		define( 'BUDDYFORMS_PIG_TEMPLATE_PATH', BUDDYFORMS_PIG_INSTALL_PATH . 'templates/' );
	}
	add_action( 'plugins_loaded', 'buddyforms_post_in_groups_load_plugin_textdomain' );
}

/**
 * Load the textdomain for the plugin
 */
function buddyforms_post_in_groups_load_plugin_textdomain() {
	load_plugin_textdomain( 'buddyforms-post-in-groups', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'buddyforms_post_in_groups_load_constants' );

function buddyforms_pig_init() {

	$buddyforms_pig = get_option( 'buddyforms_pig_options' );

	if ( $buddyforms_pig['permission'] != 'disabled' ) {
		require( dirname( __FILE__ ) . '/includes/buddyforms-post-in-groups.php' );
		$BuddyForms_Post_in_Groups = new BuddyForms_Post_in_Groups();
		add_action( 'bp_actions', array( &$BuddyForms_Post_in_Groups, '_register' ), 8 );
		add_action( 'admin_init', array( &$BuddyForms_Post_in_Groups, '_register' ) );
	}

}

add_action( 'bp_init', 'buddyforms_pig_init', 12 );

function buddyforms_pig_admin_init() {
	require( dirname( __FILE__ ) . '/includes/admin/admin.php' );
	require( dirname( __FILE__ ) . '/includes/admin/metabox-select-group.php' );
}

add_action( 'admin_init', 'buddyforms_pig_admin_init' );

//
// Check the plugin dependencies
//
add_action( 'init', function () {

	// Only Check for requirements in the admin
	if ( ! is_admin() ) {
		return;
	}

	// Require TGM
	require( dirname( __FILE__ ) . '/includes/resources/tgm/class-tgm-plugin-activation.php' );

	// Hook required plugins function to the tgmpa_register action
	add_action( 'tgmpa_register', function () {

		// Create the required plugins array
		$plugins['buddypress'] = array(
			'name'     => 'BuddyPress',
			'slug'     => 'buddypress',
			'required' => true,
		);


		if ( ! defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
			$plugins['buddyforms'] = array(
				'name'     => 'BuddyForms',
				'slug'     => 'buddyforms',
				'required' => true,
			);
		}

		$config = array(
			'id'           => 'buddyforms-tgmpa',
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'parent_slug'  => 'plugins.php',
			// Parent menu slug.
			'capability'   => 'manage_options',
			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,
			// Show admin notices or not.
			'dismissable'  => false,
			// If false, a user cannot dismiss the nag message.
			'is_automatic' => true,
			// Automatically activate plugins after installation or not.
		);

		// Call the tgmpa function to register the required plugins
		tgmpa( $plugins, $config );

	} );
}, 1, 1 );

if ( ! function_exists( 'bf_bp_post_in_groups_fs' ) ) {
	// Create a helper function for easy SDK access.
	function bf_bp_post_in_groups_fs() {
		global $bf_bp_post_in_groups_fs;

		if ( ! isset( $bf_bp_post_in_groups_fs ) ) {
			// Include Freemius SDK.
			if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php' ) ) {
				// Try to load SDK from parent plugin folder.
				require_once dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php';
			} else {
				if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php' ) ) {
					// Try to load SDK from premium parent plugin folder.
					require_once dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php';
				}
			}

			$bf_bp_post_in_groups_fs = fs_dynamic_init( array(
				'id'               => '5160',
				'slug'             => 'bf-bp-post-in-groups',
				'type'             => 'plugin',
				'public_key'       => 'pk_ef7d9b4a7a9af078f32386bdcebe7',
				'is_premium'       => true,
				'is_premium_only'  => true,
				'has_paid_plans'   => true,
				'is_org_compliant' => false,
				'parent'           => array(
					'id'         => '391',
					'slug'       => 'buddyforms',
					'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
					'name'       => 'BuddyForms',
				),
				'menu'             => array(
					'first-path' => 'plugins.php',
					'support'    => false,
				)
			) );
		}

		return $bf_bp_post_in_groups_fs;
	}
}

function bf_bp_post_in_groups_fs_is_parent_active_and_loaded() {
	// Check if the parent's init SDK method exists.
	return function_exists( 'buddyforms_core_fs' );
}

function bf_bp_post_in_groups_fs_is_parent_active() {
	$active_plugins = get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
		$active_plugins         = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
	}

	foreach ( $active_plugins as $basename ) {
		if ( 0 === strpos( $basename, 'buddyforms/' ) ||
		     0 === strpos( $basename, 'buddyforms-premium/' )
		) {
			return true;
		}
	}

	return false;
}

function bf_bp_post_in_groups_fs_init() {
	if ( bf_bp_post_in_groups_fs_is_parent_active_and_loaded() ) {
		// Init Freemius.
		bf_bp_post_in_groups_fs();


		// Signal that the add-on's SDK was initiated.
		do_action( 'bf_bp_post_in_groups_fs_loaded' );

		require_once( dirname( __FILE__ ) . '/includes/functions.php' );
		require_once( dirname( __FILE__ ) . '/includes/form-elements.php' );


	} else {
		// Parent is inactive, add your error handling here.
	}
}

if ( bf_bp_post_in_groups_fs_is_parent_active_and_loaded() ) {
	// If parent already included, init add-on.
	bf_bp_post_in_groups_fs_init();
} else if ( bf_bp_post_in_groups_fs_is_parent_active() ) {
	// Init add-on only after the parent is loaded.
	add_action( 'buddyforms_core_fs_loaded', 'bf_bp_post_in_groups_fs_init' );
} else {
	// Even though the parent is not activated, execute add-on for activation / uninstall hooks.
	bf_bp_post_in_groups_fs_init();
}