<?php
/**
 * BuddyPress Notifications Rewrites.
 *
 * @package buddypress\bp-notifications
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a Notificatons member action URL using the BP Rewrites URL parser.
 *
 * @since 1.0.0
 *
 * @param int    $user_id          The user ID of concerned by the member action.
 * @param string $action           The slug of the member action.
 * @param array  $action_variables Additional information about the member action.
 * @return string                  The Notificatons member action URL built for the BP Rewrites URL parser.
 */
function bp_notifications_rewrites_get_member_action_url( $user_id = 0, $action = '', $action_variables = array() ) {
	$slug       = bp_get_notifications_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Notificatons page of the User single item.
	$params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
	);

	if ( $action ) {
		// The action of the User single item's Notificatons page to perform.
		$params['single_item_action'] = $action;

		if ( $action_variables ) {
			// Additional information about the action to perform.
			$params['single_item_action_variables'] = $action_variables;
		}
	}

	return bp_member_rewrites_get_url( $user_id, '', $params );
}
