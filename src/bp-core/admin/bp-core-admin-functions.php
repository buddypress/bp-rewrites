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
 * This code should be in `\bp_core_get_admin_tabs()`
 *
 * @since ?.0.0
 *
 * @param array  $tabs    Tab data.
 * @param string $context The context of use for the tabs.
 * @return array          Tab data.
 */
function bp_core_get_admin_tabs( $tabs = array(), $context = '' ) {
	if ( 'settings' === $context ) {
		$tabs['1'] = array(
			'href' => bp_get_admin_url( add_query_arg( array( 'page' => 'bp-rewrites-settings' ), 'admin.php' ) ),
			'name' => __( 'URLs', 'buddypress' ),
		);
	}

	return $tabs;
}
add_filter( 'bp_core_get_admin_tabs', __NAMESPACE__ . '\bp_core_get_admin_tabs', 10, 2 );
