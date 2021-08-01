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
