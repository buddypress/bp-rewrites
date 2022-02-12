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
 * `\bp_get_notifications_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url     The URL built for the BP Legacy URL parser.
 * @param int    $user_id The user ID.
 * @return string         The URL built for the BP Legacy URL parser.
 */
function bp_get_notifications_permalink( $url = '', $user_id = 0 ) {
	$slug       = bp_get_notifications_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	return bp_member_rewrites_get_url(
		$user_id,
		'',
		array(
			'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		)
	);

}
add_filter( 'bp_get_notifications_permalink', __NAMESPACE__ . '\bp_get_notifications_permalink', 1, 2 );

/**
 * `\bp_get_notifications_unread_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
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
 * @since 1.0.0
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
