<?php
/**
 * BuddyPress Member Functions.
 *
 * @package buddypress\bp-members
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_core_get_user_domain()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $domain        Domain for the user.
 * @param int    $user_id       ID of the user.
 * @param string $user_nicename User nicename of the passed user.
 * @return string               User Domain built for the BP Rewrites URL parser.
 */
function bp_core_get_user_domain( $domain = '', $user_id = 0, $user_nicename = '' ) {
	return bp_member_rewrites_get_url( $domain, $user_id, $user_nicename );
}
add_filter( 'bp_core_get_user_domain', __NAMESPACE__ . '\bp_core_get_user_domain', 1, 3 );
