<?php
/**
 * BuddyPress Activity Template Functions.
 *
 * @package buddypress\bp-activity
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_get_activity_directory_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_directory_permalink( $url = '' ) {
	return bp_activities_rewrites_get_url();
}
add_filter( 'bp_get_activity_directory_permalink', __NAMESPACE__ . '\bp_get_activity_directory_permalink', 1, 1 );

/**
 * `\bp_get_sitewide_activity_feed_link()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_sitewide_activity_feed_link( $url = '' ) {
	return bp_activity_rewrites_get_sitewide_feed_url();
}
add_filter( 'bp_get_sitewide_activity_feed_link', __NAMESPACE__ . '\bp_get_sitewide_activity_feed_link', 1, 1 );
