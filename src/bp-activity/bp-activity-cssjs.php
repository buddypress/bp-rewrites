<?php
/**
 * Activity component CSS/JS
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
 * Enqueues the Heartbeat script if needed.
 *
 * @since 1.0.0
 */
function bp_activity_heartbeat_enqueue_script() {
	if ( bp_activity_do_heartbeat() ) {
		wp_enqueue_script( 'heartbeat' );
	}
}
add_action( 'bp_actions', __NAMESPACE__ . '\bp_activity_heartbeat_enqueue_script' );
