<?php
/**
 * BP Rewrites Admin Funcions.
 *
 * @package bp-rewrites\inc
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the settings page to add a submenu to.
 *
 * @since 1.0.0
 *
 * @return string The settings page to add a submenu to.
 */
function get_settings_page() {
	// Main settings page.
	$settings_page = 'options-general.php';
	if ( bp_core_do_network_admin() ) {
		$settings_page = 'settings.php';
	}

	return $settings_page;
}

/**
 * Returns the main capability to use for admin submenus.
 *
 * @since 1.0.0
 *
 * @return string The main capability to use for admin submenus.
 */
function get_main_capability() {
	// Main capability.
	$capability = 'manage_options';
	if ( bp_core_do_network_admin() ) {
		$capability = 'manage_network_options';
	}

	return $capability;
}

/**
 * This code should be in `BP_Admin::admin_menus()`.
 *
 * @since ?.0.0
 */
function bp_admin_admin_menus() {
	$admin_page = add_submenu_page(
		get_settings_page(),
		__( 'BuddyPress URLs', 'buddypress' ),
		__( 'BuddyPress URLs', 'buddypress' ),
		get_main_capability(),
		'bp-rewrites-settings',
		__NAMESPACE__ . '\bp_core_admin_rewrites_settings'
	);

	add_action( "admin_head-{$admin_page}", '\bp_core_modify_admin_menu_highlight' );
}
add_action( bp_core_admin_hook(), __NAMESPACE__ . '\bp_admin_admin_menus', 5 );

/**
 * This code should be in `BP_Admin::admin_head()`.
 *
 * @since ?.0.0
 */
function bp_admin_admin_head() {
	remove_submenu_page( get_settings_page(), 'bp-rewrites-settings' );
}
add_action( 'bp_admin_head', __NAMESPACE__ . '\bp_admin_admin_head', 999 );

/**
 * BuddyPress doesn't need pretty permalinks anymore thanks to BP Rewrites.
 *
 * This function removes the corresponding BuddyPress Admin notice.
 *
 * @since 1.0.0
 */
function remove_pretty_permalink_admin_notice() {
	$bp                  = buddypress();
	$bp_notices          = array();
	$bp_rewrites_notices = array();

	if ( isset( $bp->admin->notices ) && $bp->admin->notices ) {
		$bp_notices = (array) $bp->admin->notices;

		foreach ( $bp_notices as $notice ) {
			if ( isset( $notice['type'], $notice['message'] ) && 'error' === $notice['type'] && preg_match( '/' . addcslashes( admin_url( 'options-permalink.php' ), '/' ) . '/', $notice['message'] ) ) {
				continue;
			}

			$bp_rewrites_notices[] = $notice;
		}

		$bp->admin->notices = $bp_rewrites_notices;
	}
}
add_action( 'admin_notices', __NAMESPACE__ . '\remove_pretty_permalink_admin_notice', 1 );
