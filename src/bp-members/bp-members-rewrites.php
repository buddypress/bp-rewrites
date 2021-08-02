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
 *
 *    @type int    $user_id        The user ID.
 *    @type string $rewrite_id     The nav item rewrite ID.
 *    @type string $item_component The main nav item slug.
 *    @type string $item_action    The sub nav item slug.
 * }
 * @return string The nav item URL.
 */
function bp_members_rewrites_get_nav_url( $args = array() ) {
	$params = bp_parse_args(
		$args,
		array(
			'user_id'        => bp_displayed_user_id(),
			'rewrite_id'     => '',
			'item_component' => '',
			'item_action'    => '',
		)
	);

	$user_id = $params['user_id'];
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( ! $user_id ) {
		return '';
	}

	$username = bp_rewrites_get_member_slug( $user_id );
	if ( ! $username ) {
		return '';
	}

	$url_params = array(
		'component_id' => 'members',
		'single_item'  => $username,
	);

	if ( $params['rewrite_id'] && $params['item_component'] ) {
		$url_params['single_item_component'] = bp_rewrites_get_slug( 'members', $params['rewrite_id'], $params['item_component'] );

		if ( $params['item_action'] ) {
			$url_params['single_item_action'] = $params['item_action'];
		}
	}

	return bp_rewrites_get_url( $url_params );
}

/**
 * Return the Mmbers single item's URL.
 *
 * @since ?.0.0
 *
 * @param string $url      The URL built for the BP Legacy URL parser. Never used.
 *                         But may be passed when this function is used as a filter.
 * @param int    $user_id  The user ID.
 * @param string $username The user user_nicename.
 * @return string          The URL built for the BP Rewrites URL parser.
 */
function bp_member_rewrites_get_url( $url = '', $user_id = 0, $username = '' ) {
	if ( ! $user_id ) {
		return $url;
	}

	$bp = buddypress();
	if ( ! $username ) {
		$username = bp_rewrites_get_member_slug( $user_id );
	}

	return bp_rewrites_get_link(
		array(
			'component_id' => 'members',
			'single_item'  => $username,
		)
	);
}

/**
 * Return the Members Directory URL.
 *
 * @since ?.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser. Never used.
 *                    But may be passed when this function is used as a filter.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_members_rewrites_get_url( $url = '' ) {
	return bp_rewrites_get_link(
		array(
			'component_id' => 'members',
		)
	);
}

/**
 * Return the Member Type's URL.
 *
 * @since ?.0.0
 *
 * @param string $url  The URL built for the BP Legacy URL parser. Never used.
 *                     But may be passed when this function is used as a filter.
 * @param object $type The Member type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_member_type_rewrites_get_url( $url = '', $type = null ) {
	if ( ! isset( $type->directory_slug ) ) {
		return $url;
	}

	return bp_rewrites_get_link(
		array(
			'component_id'   => 'members',
			'directory_type' => $type->directory_slug,
		)
	);
}
