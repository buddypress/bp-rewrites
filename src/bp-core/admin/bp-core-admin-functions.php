<?php
/**
 * BuddyPress Common Admin Functions.
 *
 * @package buddypress\bp-core\admin
 * @since 2.3.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This code should be in `\bp_core_get_admin_settings_tabs()`
 *
 * @since 1.0.0
 *
 * @param array $tabs Tabs list.
 * @return array      Edited tabs list.
 */
function bp_core_get_admin_settings_tabs( $tabs = array() ) {
	$tabs['1'] = array(
		'id'   => 'bp-rewrites-settings',
		'href' => bp_get_admin_url( add_query_arg( array( 'page' => 'bp-rewrites-settings' ), 'admin.php' ) ),
		'name' => __( 'URLs', 'bp-rewrites' ),
	);

	return $tabs;
}
add_filter( 'bp_core_get_admin_settings_tabs', __NAMESPACE__ . '\bp_core_get_admin_settings_tabs', 10, 2 );

/**
 * Include the BP Rewrites tab to the Admin tabs needing specific inline styles.
 *
 * @since 1.0.0
 *
 * @param array $submenu_pages The BP_Admin submenu pages passed by reference.
 */
function bp_admin_submenu_pages( &$submenu_pages = array() ) {
	$submenu_pages['settings']['bp-rewrites-settings'] = get_plugin_page_hookname( 'bp-rewrites-settings', get_settings_page() );
}
add_action( 'bp_admin_submenu_pages', __NAMESPACE__ . '\bp_admin_submenu_pages', 10, 1 );
