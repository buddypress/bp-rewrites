<?php
/**
 * BuddyPress Messages Template Tags.
 *
 * @package buddypress\bp-messages
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_get_message_thread_view_link()` should be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url       The URL built for the BP Legacy URL parser.
 * @param int    $thread_id The ID of the thread.
 * @param int    $user_id   The ID of the user.
 * @return string           The URL built for the BP Rewrites URL parser.
 */
function bp_get_message_thread_view_link( $url = '', $thread_id = 0, $user_id = 0 ) {
	$slug       = \bp_get_messages_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	return bp_member_rewrites_get_url(
		$user_id,
		'',
		array(
			'single_item_component'        => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
			'single_item_action'           => 'view', // It shouldn't be hardcoded.
			'single_item_action_variables' => $thread_id,
		)
	);
}
add_filter( 'bp_get_message_thread_view_link', __NAMESPACE__ . '\bp_get_message_thread_view_link', 1, 3 );

/**
 * `\bp_get_send_private_message_link()` should be edited to use BP Rewrites.
 *
 * @since 1.1.0
 *
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_get_send_private_message_link() {
	$slug                 = \bp_get_messages_slug();
	$primary_rewrite_id   = sprintf( 'bp_member_%s', $slug );
	$secondary_rewrite_id = sprintf( '%1$s_%2$s', $primary_rewrite_id, 'compose' );

	$url = bp_member_rewrites_get_url(
		bp_loggedin_user_id(),
		'',
		array(
			'single_item_component' => bp_rewrites_get_slug( 'members', $primary_rewrite_id, $slug ),
			'single_item_action'    => bp_rewrites_get_slug( 'members', $secondary_rewrite_id, 'compose' ),
		)
	);

	return wp_nonce_url( add_query_arg( 'r', \bp_core_get_username( \bp_displayed_user_id() ), $url ) );
}
add_filter( 'bp_get_send_private_message_link', __NAMESPACE__ . '\bp_get_send_private_message_link', 1, 0 );
