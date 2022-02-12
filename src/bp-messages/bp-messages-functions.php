<?php
/**
 * BuddyPress Messages Functions.
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
 * `messages_notification_new_message()` need to use BP Rewrites to build the `message.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function messages_notification_new_message( $args = array() ) {
	if ( isset( $args['tokens']['message.url'] ) && $args['tokens']['message.url'] ) {
		$url_path  = trim( wp_parse_url( $args['tokens']['message.url'], PHP_URL_PASS ), '/' );
		$parts     = explode( '/', $url_path );
		$thread_id = array_pop( $parts );
		$action    = array_pop( $parts );
		$slug      = array_pop( $parts );
		$username  = array_pop( $parts );

		$user = get_user_by( 'slug', $username );
		if ( $user ) {
			$args['tokens']['message.url'] = esc_url_raw(
				BP\Rewrites\bp_get_message_thread_view_link(
					'',
					$thread_id,
					$user->ID
				)
			);
		}
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\messages_notification_new_message', 10, 1 );
