<?php
/**
 * BuddyPress Groups Filters.
 *
 * @package buddypress\bp-groups
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_groups_user_can_filter` should wait the `bp_groups_parse_query` hook has fired before
 * trying to get the current group ID.
 *
 * @since 1.0.0
 *
 * @param bool   $retval     Whether or not the current user has the capability.
 * @param int    $user_id    The user ID.
 * @param string $capability The capability being checked for.
 * @param int    $site_id    Site ID. Defaults to the BP root blog.
 * @param array  $args       Array of extra arguments passed.
 *
 * @return bool
 */
function bp_groups_user_can_filter( $retval, $user_id, $capability, $site_id, $args ) {
	$group_caps = array(
		'groups_join_group',
		'groups_request_membership',
		'groups_send_invitation',
		'groups_receive_invitation',
		'groups_access_group',
		'groups_see_group',
	);

	if ( ! in_array( $capability, $group_caps, true ) ) {
		return $retval;
	}

	if ( ! empty( $args['group_id'] ) || did_action( 'bp_groups_parse_query' ) ) {
		$retval = \bp_groups_user_can_filter( $retval, $user_id, $capability, $site_id, $args );
	}

	return $retval;
}

/**
 * `\bp_groups_user_can_filter` can happen before the Groups component is fully set.
 *
 * @since 1.0.0
 */
function do_it_right_groups_user_can_filter() {
	remove_filter( 'bp_user_can', 'bp_groups_user_can_filter', 10 );
	add_filter( 'bp_user_can', __NAMESPACE__ . '\bp_groups_user_can_filter', 10, 5 );
}
add_action( 'bp_init', __NAMESPACE__ . '\do_it_right_groups_user_can_filter', 1 );
