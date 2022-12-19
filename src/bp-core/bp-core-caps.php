<?php
/**
 * BuddyPress Capabilities.
 *
 * @package buddypress\bp-core
 * @since 1.6.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map community caps to built in WordPress caps.
 *
 * @since ?.0.0
 *
 * @see WP_User::has_cap() for description of the arguments passed to the
 *      'map_meta_cap' filter.
 *       args.
 *
 * @param array  $caps    See {@link WP_User::has_cap()}.
 * @param string $cap     See {@link WP_User::has_cap()}.
 * @param int    $user_id See {@link WP_User::has_cap()}.
 * @param mixed  $args    See {@link WP_User::has_cap()}.
 * @return array Actual capabilities for meta capability. See {@link WP_User::has_cap()}.
 */
function bp_map_meta_caps( $caps, $cap, $user_id, $args ) {
	if ( 'bp_read' === $cap ) {
		$caps = 'members' === bp_get_community_visibility() ? array( 'read' ) : array( 'exist' );
	}

	return $caps;
}
add_filter( 'bp_map_meta_caps', __NAMESPACE__ . '\bp_map_meta_caps', 1, 4 );
