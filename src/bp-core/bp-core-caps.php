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
 * @since 1.6.0
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
		// Allowed to anyone by default.
		$caps    = array( 'exist' );
		$bp_page = null;

		// Check passed arguments to eventually use the first one as the page ID or post object.
		if ( isset( $args[0]['bp_page'] ) ) {
			$post    = $args[0]['bp_page'];
			$bp_page = get_post( $post );
		}

		// Check this BuddyPress page visibility.
		if ( $bp_page instanceof \WP_Post && in_array( $bp_page->ID, bp_core_get_directory_page_ids(), true ) && 'bp_restricted' === get_post_status( $bp_page ) ) {
			// Restrict it to members only.
			$caps = array( 'read' );
		}
	}

	return $caps;
}
add_filter( 'bp_map_meta_caps', __NAMESPACE__ . '\bp_map_meta_caps', 1, 4 );

/**
 * Rewrite the `bp_get_caps_for_role` to include the `bp_read` cap for all WP roles.
 *
 * @since 1.6.0
 *
 * @param array  $caps Capabilities for the role.
 * @param string $role The role for which you're loading caps.
 * @return array Capabilities for the role.
 */
function bp_get_caps_for_role( $caps = array(), $role = '' ) {
	if ( ! $role ) {
		return $caps;
	}

	$roles = array_keys( bp_get_current_blog_roles() );

	if ( in_array( $role, $roles, true ) ) {
		$caps[] = 'bp_read';
	}

	return $caps;
}
add_filter( 'bp_get_caps_for_role', __NAMESPACE__ . '\bp_get_caps_for_role', 1, 4 );
