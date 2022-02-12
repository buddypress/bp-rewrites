<?php
/**
 * BuddyPress xProfile Functions.
 *
 * @package buddypress\bp-xprofile
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooking to `bp_setup_globals` is too early, the displayed user is not set yet.
 *
 * @since 1.0.0
 */
function xprofile_override_user_fullnames() {
	remove_action( 'bp_setup_globals', 'xprofile_override_user_fullnames', 100 );
	add_action( 'bp_parse_query', 'xprofile_override_user_fullnames', 100 );
}
add_action( 'bp_setup_globals', __NAMESPACE__ . '\xprofile_override_user_fullnames', 1, 1 );
