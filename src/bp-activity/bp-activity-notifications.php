<?php
/**
 * BuddyPress Activty Notifications.
 *
 * @package buddypress\bp-activity
 * @since 1.2.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make sure @mentions notifications are using BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param array|string $content     HTML Output or Array holding the content and permalink for the interaction notification.
 * @param string       $url         The permalink for the interaction.
 * @param int          $total_items How many items being notified about.
 * @param int          $activity_id ID of the activity item being formatted.
 * @param int          $user_id     ID of the user who inited the interaction.
 * @return array|string             The Notification content built for the BP Rewrites URL parser.
 */
function bp_activity_format_at_mentions_notification( $content = '', $url = '', $total_items = 0, $activity_id = 0, $user_id = 0 ) {
	$slug       = bp_get_activity_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Activity page of the User single item.
	$url_params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		'single_item_action'    => 'mentions', // It shouldn't be hardcoded.
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
 * `\bp_activity_format_notifications()` needs to use BP Rewrite to build URLs.
 *
 * This function is hooked to `bp_init` and register the above filter.
 *
 * @since 1.0.0
 */
function bp_activity_format_notifications() {
	add_filter( 'bp_activity_single_at_mentions_notification', __NAMESPACE__ . '\bp_activity_format_at_mentions_notification', 1, 5 );
	add_filter( 'bp_activity_multiple_at_mentions_notification', __NAMESPACE__ . '\bp_activity_format_at_mentions_notification', 1, 5 );
}
add_action( 'bp_init', __NAMESPACE__ . '\bp_activity_format_notifications', 50 );
