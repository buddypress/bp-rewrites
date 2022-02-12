<?php
/**
 * BuddyPress Messages Notifications.
 *
 * @package buddypress\bp-messages
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make sure multiple message notifications are using BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string|array $content           Depending on format, an HTML link to new requests profile tab or array with link and text.
 * @param int          $total_items       The total number of messaging-related notifications waiting for the user.
 * @return array|string                   The Notification content built for the BP Rewrites URL parser.
 */
function bp_messages_format_multiple_message_notification( $content = '', $total_items = 0 ) {
	if ( 1 === $total_items ) {
		return $content;
	}

	$slug       = bp_get_messages_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Activity page of the User single item.
	$url_params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		'single_item_action'    => 'inbox', // It shouldn't be hardcoded.
	);

	$link = bp_member_rewrites_get_url( bp_loggedin_user_id(), '', $url_params );

	if ( is_array( $content ) ) {
		$content['link'] = $link;
	} else {
		$content = preg_replace( '/href=\"(.*?)\">/', sprintf( 'href="%s">', $link ), $content );
	}

	return $content;
}

/**
 * `\messages_format_notifications()` needs to use BP Rewrite to build URLs.
 *
 * This function is hooked to `bp_init` and register the above filter.
 *
 * @since 1.0.0
 */
function messages_format_notifications() {
	add_filter( 'bp_messages_multiple_new_message_string_notification', __NAMESPACE__ . '\bp_messages_format_multiple_message_notification', 1, 2 );
	add_filter( 'bp_messages_multiple_new_message_array_notification', __NAMESPACE__ . '\bp_messages_format_multiple_message_notification', 1, 2 );
}
add_action( 'bp_init', __NAMESPACE__ . '\messages_format_notifications', 50 );
