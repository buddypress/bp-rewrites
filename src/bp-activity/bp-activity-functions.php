<?php
/**
 * BuddyPress Activity Functions.
 *
 * @package buddypress\bp-activity
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `bp_activity_get_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string               $url      The Activity single item URL built for the Legacy URL parser.
 * @param BP_Activity_Activity $activity The activity object.
 * @return string                        The Activity single item URL built for the BP Rewrites URL parser.
 */
function bp_activity_get_permalink( $url = '', $activity = null ) {
	return bp_activity_rewrites_get_url( $activity );
}
add_filter( 'bp_activity_get_permalink', __NAMESPACE__ . '\bp_activity_get_permalink', 1, 2 );
