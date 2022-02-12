<?php
/**
 * BuddyPress Friends Functions.
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
 * `friends_notification_new_request()` need to use BP Rewrites to build the `friend-requests.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function friends_notification_new_request( $args = array() ) {
	if ( isset( $args['tokens']['friend-requests.url'], $args['tokens']['friend.id'] ) && $args['tokens']['friend-requests.url'] && $args['tokens']['friend.id'] ) {
		$args['tokens']['friend-requests.url'] = esc_url_raw(
			bp_friends_rewrites_get_member_action_url(
				$args['tokens']['friend.id'],
				'requests' // Should it be hardcoded?
			)
		);
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\friends_notification_new_request', 10, 1 );
