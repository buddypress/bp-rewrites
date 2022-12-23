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

		/*
		 * Check passed arguments to eventually use the first one as the page ID or post object.
		 * Using the component's directory page will allow us to have a more granular visibility
		 * management in the future. Eg: only showing community members.
		 */
		if ( isset( $args[0]['bp_page'] ) ) {
			$post    = $args[0]['bp_page'];
			$bp_page = get_post( $post );

			// Check this BuddyPress page visibility.
			if ( $bp_page instanceof \WP_Post && in_array( $bp_page->ID, bp_core_get_directory_page_ids(), true ) && 'bp_restricted' === get_post_status( $bp_page ) ) {
				// Restrict it to members only.
				$caps = array( 'read' );
			}
		} elseif ( 'bp_restricted' === bp_get_community_visibility() ) {
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

/**
 * Checks a natively "public" BP REST request can be performed.
 *
 * @since ?.0.0
 *
 * @param true            $retval  Returned value.
 * @param WP_REST_Request $request The request sent to the API.
 * @return bool True if the user has access. False otherwise.
 */
function bp_rest_check_default_permission( $retval, $request ) {
	$path         = wp_parse_url( $request->get_route(), PHP_URL_PATH );
	$component_id = trim( str_replace( bp_rest_namespace() . '/' . bp_rest_version(), '', trim( $path, '/' ) ), '/' );
	$bp_page_id   = bp_core_get_directory_page_id( $component_id );
	$args         = array();

	if ( $bp_page_id ) {
		$args['bp_page'] = (int) $bp_page_id;
	}

	return bp_current_user_can( 'bp_read', $args );
}

/**
 * Set default permissions for the BP REST API.
 *
 * @since ?.0.0
 */
function bp_set_rest_default_permission_checks() {
	$bp = buddypress();

	if ( isset( $bp->pages ) ) {
		if ( bp_is_active( 'xprofile' ) ) {
			$bp->pages->xprofiles = (object) array(
				'visibility' => bp_get_community_visibility(),
			);
		}

		foreach ( $bp->pages as $component_id => $component_data ) {
			if ( ! isset( $component_data->visibility ) || 'bp_restricted' !== $component_data->visibility ) {
				continue;
			}

			if ( 'xprofiles' === $component_id ) {
				add_filter( 'bp_rest_xprofile_field_groups_get_items_permissions_check', __NAMESPACE__ . '\bp_rest_check_default_permission', 1, 2 );
				add_filter( 'bp_rest_xprofile_fields_get_items_permissions_check', __NAMESPACE__ . '\bp_rest_check_default_permission', 1, 2 );
			} else {
				add_filter( "bp_rest_{$component_id}_get_items_permissions_check", __NAMESPACE__ . '\bp_rest_check_default_permission', 1, 2 );
			}
		}
	}
}
add_action( 'bp_rest_api_init', __NAMESPACE__ . '\bp_set_rest_default_permission_checks', 1 );
