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
