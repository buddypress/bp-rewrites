<?php
/**
 * Activity: Single view screen.
 *
 * @package buddypress\bp-activity\screens
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This code should be moved to `\bp_activity_action_permalink_router()`.
 *
 * @since 1.0.0
 *
 * @param string               $redirect The redirection URL used for the Legacy URL Parser.
 * @param BP_Activity_Activity $activity The Activity object.
 * @return string                        The redirection URL used for the BP Rewrites URL Parser.
 */
function bp_activity_action_permalink_router( $redirect = '', $activity = null ) {
	if ( ! $redirect || ! isset( $activity->user_id ) ) {
		return $redirect;
	}

	return bp_activity_rewrites_get_redirect_url( $activity );
}
add_filter( 'bp_activity_permalink_redirect_url', __NAMESPACE__ . '\bp_activity_action_permalink_router', 1, 2 );
