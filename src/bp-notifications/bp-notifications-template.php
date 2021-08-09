<?php
/**
 * BuddyPress Notifications Template functions.
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
 * `\bp_get_notifications_unread_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url     The URL built for the BP Legacy URL parser.
 * @param int    $user_id The user ID.
 * @return string         The URL built for the BP Legacy URL parser.
 */
function bp_get_notifications_unread_permalink( $url = '', $user_id = 0 ) {
	// Should `unread` be hardcoded?
	return bp_notifications_rewrites_get_member_action_url( $user_id, 'unread' );
}
add_filter( 'bp_get_notifications_unread_permalink', __NAMESPACE__ . '\bp_get_notifications_unread_permalink', 1, 2 );

/**
 * `\bp_get_notifications_read_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url     The URL built for the BP Legacy URL parser.
 * @param int    $user_id The user ID.
 * @return string         The URL built for the BP Legacy URL parser.
 */
function bp_get_notifications_read_permalink( $url = '', $user_id = 0 ) {
	// Should `unread` be hardcoded?
	return bp_notifications_rewrites_get_member_action_url( $user_id, 'read' );
}
add_filter( 'bp_get_notifications_read_permalink', __NAMESPACE__ . '\bp_get_notifications_read_permalink', 1, 2 );
