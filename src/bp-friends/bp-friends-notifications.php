<?php
/**
 * BuddyPress Friends Notifications.
 *
 * @package buddypress\bp-friends
 * @since 1.2.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make sure accepted friendship notifications are using BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string|array $content           Depending on format, an HTML link to new requests profile tab or array with link and text.
 * @param int          $total_items       The total number of messaging-related notifications waiting for the user.
 * @param int          $item_id           The primary item ID.
 * @param int          $secondary_item_id The secondary item ID.
 * @return array|string                   The Notification content built for the BP Rewrites URL parser.
 */
function bp_friends_format_friendship_accepted_notification( $content = '', $total_items = 0, $item_id = 0, $secondary_item_id = 0 ) {
	$slug       = bp_get_friends_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Activity page of the User single item.
	$url_params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		'single_item_action'    => 'my-friends', // It shouldn't be hardcoded.
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
 * Make sure friendship requests notifications are using BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string|array $content           Depending on format, an HTML link to new requests profile tab or array with link and text.
 * @param int          $total_items       The total number of messaging-related notifications waiting for the user.
 * @param int          $item_id           The primary item ID.
 * @param int          $secondary_item_id The secondary item ID.
 * @return array|string                   The Notification content built for the BP Rewrites URL parser.
 */
function bp_friends_format_friendship_request_notification( $content = '', $total_items = 0, $item_id = 0, $secondary_item_id = 0 ) {
	$slug       = bp_get_friends_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Activity page of the User single item.
	$url_params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		'single_item_action'    => 'requests', // It shouldn't be hardcoded.
	);

	$link = add_query_arg( 'new', 1, bp_member_rewrites_get_url( bp_loggedin_user_id(), '', $url_params ) );

	if ( is_array( $content ) ) {
		$content['link'] = $link;
	} else {
		$content = preg_replace( '/href=\"(.*?)\">/', sprintf( 'href="%s">', $link ), $content );
	}

	return $content;
}

/**
 * `\friends_format_notifications()` needs to use BP Rewrite to build URLs.
 *
 * This function is hooked to `bp_init` and register the above filter.
 *
 * @since 1.0.0
 */
function friends_format_notifications() {
	foreach ( array( 'friendship_accepted', 'friendship_request' ) as $action ) {
		add_filter( 'bp_friends_single_' . $action . '_notification', __NAMESPACE__ . '\bp_friends_format_' . $action . '_notification', 1, 4 );
		add_filter( 'bp_friends_multiple_' . $action . '_notification', __NAMESPACE__ . '\bp_friends_format_' . $action . '_notification', 1, 4 );
	}
}
add_action( 'bp_init', __NAMESPACE__ . '\friends_format_notifications', 50 );
