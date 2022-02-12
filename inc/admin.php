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
 * Enqueues styles and scripts for the BP URLs settings tab.
 *
 * @since 1.0.0
 */
function admin_load() {
	$bpr = bp_rewrites();

	wp_enqueue_style( 'site-health' );
	wp_add_inline_style(
		'site-health',
		'#bp-admin-rewrites-form .form-table { border: none; padding: 0; }
		#bp-admin-rewrites-form .bp-nav-slug { margin-left: 2em; display: inline-block; vertical-align: middle; }
		.site-health-issues-wrapper:first-of-type { margin-top: 0; }
		.site-health-issues-wrapper .health-check-accordion { border-bottom: none; }
		.site-health-issues-wrapper .health-check-accordion:last-of-type { border-bottom: 1px solid #c3c4c7; }'
	);

	wp_enqueue_script(
		'bp-rewrites-ui',
		$bpr->url . '/src/bp-core/admin/js/rewrites-ui.js',
		array(),
		$bpr->version,
		true
	);
}

/**
 * This code should be in `BP_Admin::admin_menus()`.
 *
 * @since ?.0.0
 */
function bp_admin_admin_menus() {
	$admin_page = add_submenu_page(
		get_settings_page(),
		__( 'BuddyPress URLs', 'bp-rewrites' ),
		__( 'BuddyPress URLs', 'bp-rewrites' ),
		get_main_capability(),
		'bp-rewrites-settings',
		__NAMESPACE__ . '\bp_core_admin_rewrites_settings'
	);

	add_action( "admin_head-{$admin_page}", '\bp_core_modify_admin_menu_highlight' );
	add_action( "load-{$admin_page}", __NAMESPACE__ . '\admin_load' );
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
