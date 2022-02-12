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
 * @since 1.0.0
 *
 * @param string $domain        Domain for the user.
 * @param int    $user_id       ID of the user.
 * @param string $user_nicename User nicename of the passed user.
 * @return string               User Domain built for the BP Rewrites URL parser.
 */
function bp_core_get_user_domain( $domain = '', $user_id = 0, $user_nicename = '' ) {
	return bp_member_rewrites_get_url( $user_id, $user_nicename );
}
add_filter( 'bp_core_get_user_domain', __NAMESPACE__ . '\bp_core_get_user_domain', 1, 3 );

/**
 * Adds backward compatibility when `\bp_get_current_member_type()` is called too early.
 *
 * @since 1.0.0
 *
 * @param string $current_member_type The current member type being displayed into the Members directory.
 * @return null|string                Null if the current member type is not set yet. The current member type otherwise.
 */
function bp_get_current_member_type( $current_member_type = '' ) {
	if ( ! $current_member_type ) {
		$current_member_type = _was_called_too_early( 'bp_get_current_member_type()', array( 'current_member_type' ) );
	}

	return $current_member_type;
}
add_filter( 'bp_get_current_member_type', __NAMESPACE__ . '\bp_get_current_member_type', 1, 1 );

/**
 * `bp_core_signup_send_validation_email()` as well as `\bp_core_activation_signup_blog_notification()` need to use BP Rewrites
 * to build the `activate.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function bp_core_signup_send_validation_email( $args = array() ) {
	if ( isset( $args['tokens']['activate.url'], $args['tokens']['key'] ) && $args['tokens']['activate.url'] && $args['tokens']['key'] ) {
		$args['tokens']['activate.url'] = esc_url_raw( bp_activation_rewrites_get_url( $args['tokens']['key'] ) );
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\bp_core_signup_send_validation_email', 10, 1 );
