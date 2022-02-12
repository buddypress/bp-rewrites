<?php
/**
 * BuddyPress Friends Template Tags.
 *
 * @package buddypress\bp-friends
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Friendship buttons need to use BP Rewrites to set up their links.
 *
 * NB: the `\bp_get_add_friend_button` filter should pass the User ID.
 *
 * @since 1.0.0
 *
 * @param array $args {
 *    An array of arguments.
 *    @see `BP_Button` for the full arguments description.
 * }
 *
 * @return array The `BP_Button` arguments for the frienship button to output.
 */
function bp_get_add_friend_button( $args = array() ) {
	$user_id = 0;

	// phpcs:disable WordPress.Security.NonceVerification
	if ( wp_doing_ajax() && isset( $_POST['item_id'] ) ) {
		$user_id = absint( wp_unslash( $_POST['item_id'] ) );
		// phpcs:enable WordPress.Security.NonceVerification
	} elseif ( bp_is_members_directory() ) {
		$user_id = bp_get_member_user_id();
	} elseif ( bp_is_group_members() ) {
		$user_id = bp_get_group_member_id();
	} else {
		$user_id = bp_get_potential_friend_id();
	}

	$is_friend = bp_is_friend( $user_id );

	switch ( $is_friend ) {
		case 'pending':
			$args['link_href'] = wp_nonce_url(
				bp_friends_rewrites_get_member_action_url(
					bp_loggedin_user_id(),
					'requests', // Should it be hardcoded?
					array( 'cancel', $user_id ) // Should "cancel" be hardcoded?
				),
				'friends_withdraw_friendship'
			);
			break;

		case 'awaiting_response':
			$args['link_href'] = bp_friends_rewrites_get_member_action_url(
				bp_loggedin_user_id(),
				'requests' // Should it be hardcoded?
			);
			break;

		case 'is_friend':
			$args['link_href'] = wp_nonce_url(
				bp_friends_rewrites_get_member_action_url(
					bp_loggedin_user_id(),
					'remove-friend', // Should it be hardcoded?
					array( $user_id )
				),
				'friends_remove_friend'
			);
			break;

		default:
			$args['link_href'] = wp_nonce_url(
				bp_friends_rewrites_get_member_action_url(
					bp_loggedin_user_id(),
					'add-friend', // Should it be hardcoded?
					array( $user_id )
				),
				'friends_add_friend'
			);
			break;
	}

	return $args;
}
add_filter( 'bp_get_add_friend_button', __NAMESPACE__ . '\bp_get_add_friend_button', 1, 1 );

/**
 * `\bp_get_friend_accept_request_link()` should be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url           The URL built for the BP Legacy URL parser.
 * @param int    $friendship_id The ID of the friendship.
 * @return string               The URL built for the BP Rewrites URL parser.
 */
function bp_get_friend_accept_request_link( $url = '', $friendship_id = 0 ) {
	return wp_nonce_url(
		bp_friends_rewrites_get_member_action_url(
			bp_loggedin_user_id(),
			'requests', // Should it be hardcoded?
			array( 'accept', $friendship_id ) // Should "accept" be hardcoded?
		),
		'friends_accept_friendship'
	);
}
add_filter( 'bp_get_friend_accept_request_link', __NAMESPACE__ . '\bp_get_friend_accept_request_link', 1, 2 );

/**
 * `\bp_get_friend_reject_request_link()` should be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url           The URL built for the BP Legacy URL parser.
 * @param int    $friendship_id The ID of the friendship.
 * @return string               The URL built for the BP Rewrites URL parser.
 */
function bp_get_friend_reject_request_link( $url = '', $friendship_id = 0 ) {
	return wp_nonce_url(
		bp_friends_rewrites_get_member_action_url(
			bp_loggedin_user_id(),
			'requests', // Should it be hardcoded?
			array( 'reject', $friendship_id ) // Should "reject" be hardcoded?
		),
		'friends_reject_friendship'
	);
}
add_filter( 'bp_get_friend_reject_request_link', __NAMESPACE__ . '\bp_get_friend_reject_request_link', 1, 2 );
