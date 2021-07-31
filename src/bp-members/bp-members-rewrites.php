<?php
/**
 * BuddyPress Members Rewrites.
 *
 * @package buddypress\bp-members
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets nav item URL using BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param array $args {
 *    An array of arguments.
 *    @see `bp_core_create_nav_link()` for the full list of arguments.
 * }
 * @return string The nav item URL.
 */
function bp_members_rewrites_get_nav_url( $args = array() ) {
	// Set default URL.
	$url = '';

	$user_id = bp_displayed_user_id();
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	$username = bp_rewrites_get_member_slug( $user_id );
	if ( ! $username ) {
		return $url;
	}

	$url_params = array(
		'component_id' => 'members',
		'single_item'  => $username,
	);

	// This is a secondary nav.
	if ( isset( $args['parent_slug'], $args['slug'] ) && $args['parent_slug'] && $args['slug'] ) {
		$parent_nav = buddypress()->members->nav->get_primary( array( 'slug' => $args['parent_slug'] ), false );
		if ( ! $parent_nav ) {
			return $url;
		}

		$parent_nav = reset( $parent_nav );
		if ( ! isset( $parent_nav->rewrite_id ) ) {
			return $url;
		}

		$url_params['single_item_component'] = bp_rewrites_get_slug( 'members', $parent_nav->rewrite_id, $args['parent_slug'] );
		$url_params['single_item_action']    = $args['slug'];

		// This is a primary nav.
	} elseif ( isset( $args['rewrite_id'], $args['slug'] ) && $args['rewrite_id'] && $args['slug'] ) {
		$url_params['single_item_component'] = bp_rewrites_get_slug( 'members', $args['rewrite_id'], $args['slug'] );

		// This is not a BP Nav.
	} else {
		return $url;
	}

	return bp_rewrites_get_url( $url_params );
}

/**
 * Sets admin nav item URL using BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param array $args {
 *      An array of arguments.
 *
 *      @type string $href       The BP Legacy URI parser navigation link.
 *      @type string $rewrite_id The component rewrite ID.
 * }
 * @return string The admin nav item URL.
 */
function bp_members_rewrites_get_admin_nav_url( $args = array() ) {
	$bp       = buddypress();
	$url      = '';
	$username = bp_rewrites_get_member_slug( bp_loggedin_user_id() );
	if ( ! $username ) {
		return $url;
	}

	if ( ! isset( $args['href'] ) || ! isset( $args['rewrite_id'] ) ) {
		return $url;
	}

	// Init URL parameters.
	$url_params = array(
		'component_id' => 'members',
		'single_item'  => $username,
	);

	// Set URL parts.
	$url_parts      = array_filter( explode( '/', rtrim( wp_parse_url( $args['href'], PHP_URL_PATH ), '/' ) ) );
	$url_parts      = array_values( array_diff( $url_parts, $url_params ) );
	$item_component = array_shift( $url_parts );

	if ( ! $item_component ) {
		return $url;
	}

	// Get the component's slug.
	$url_params['single_item_component'] = bp_rewrites_get_slug( 'members', $args['rewrite_id'], $item_component );

	// Next part is item action.
	if ( $url_parts ) {
		$item_action                      = array_shift( $url_parts );
		$url_params['single_item_action'] = $item_action;
	}

	// Next parts are action variables.
	if ( $url_parts ) {
		$url_params['single_item_action_variables'] = $url_parts;
	}

	return bp_rewrites_get_url( $url_params );
}
