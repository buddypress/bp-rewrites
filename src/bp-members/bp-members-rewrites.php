<?php
/**
 * BuddyPress Members Rewrites.
 *
 * @package buddypress\bp-members
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets nav item URL using BP Rewrites.
 *
 * @since 1.0.0
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
			'user_id'        => \bp_displayed_user_id(),
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
 * @since 1.0.0
 *
 * @param int    $user_id  The user ID.
 * @param string $username The user user_nicename.
 * @param array  $action {
 *     An array of arguments. Optional.
 *
 *     @type string $single_item_component        The component slug the action is relative to.
 *     @type string $single_item_action           The slug of the action to perform.
 *     @type array  $single_item_action_variables An array of additional informations about the action to perform.
 * }
 * @return string          The URL built for the BP Rewrites URL parser.
 */
function bp_member_rewrites_get_url( $user_id = 0, $username = '', $action = array() ) {
	if ( ! $user_id ) {
		return '';
	}

	$bp = buddypress();
	if ( ! $username ) {
		$username = bp_rewrites_get_member_slug( $user_id );
	}

	$url_params = array(
		'component_id' => 'members',
		'single_item'  => $username,
	);

	if ( $action ) {
		$url_params = array_merge( $url_params, $action );
	}

	return bp_rewrites_get_url( $url_params );
}

/**
 * Return the Members Directory URL.
 *
 * @since 1.0.0
 *
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_members_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id' => 'members',
		)
	);
}

/**
 * Return the Member Type's URL.
 *
 * @since 1.0.0
 *
 * @param object $type The Member type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_member_type_rewrites_get_url( $type = null ) {
	if ( ! isset( $type->directory_slug ) ) {
		return '';
	}

	return bp_rewrites_get_url(
		array(
			'component_id'   => 'members',
			'directory_type' => $type->directory_slug,
		)
	);
}

/**
 * Return the Signup URL.
 *
 * @since 1.0.0
 *
 * @return string The Signup URL built for the BP Rewrites URL parser.
 */
function bp_signup_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id'    => 'members',
			'member_register' => 1,
		)
	);
}

/**
 * Return the Activation URL.
 *
 * @since 1.0.0
 *
 * @param string $key The Activation Key. Optional.
 * @return string     The Activation URL built for the BP Rewrites URL parser.
 */
function bp_activation_rewrites_get_url( $key = '' ) {
	$url_params = array(
		'component_id'    => 'members',
		'member_activate' => 1,
	);

	if ( $key ) {
		$url_params['member_activate_key'] = $key;
	}

	return bp_rewrites_get_url( $url_params );
}
