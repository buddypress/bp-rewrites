<?php
/**
 * BuddyPress Filters.
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `bp_core_activation_signup_blog_notification()` need to use BP Rewrites to build the `activate-site.url` Email tokens argument.
 *
 * @since ?.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function bp_core_activation_signup_blog_notification( $args = array() ) {
	if ( isset( $args['tokens']['activate-site.url'], $args['tokens']['key_blog'] ) && $args['tokens']['activate-site.url'] && $args['tokens']['key_blog'] ) {
		$args['tokens']['activate-site.url'] = esc_url_raw( bp_activation_rewrites_get_url( $args['tokens']['key_blog'] ) );
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\bp_core_activation_signup_blog_notification', 10, 1 );
