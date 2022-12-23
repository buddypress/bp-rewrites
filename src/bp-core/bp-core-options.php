<?php
/**
 * BuddyPress Options.
 *
 * @package buddypress\bp-core
 * @since 1.6.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the default BuddyPress options and their values.
 *
 * @since 1.6.0
 * @since ?.0.0 Introduce the `_bp_community_visibility` option.
 *
 * @param array $options Default BuddyPress options.
 * @return array Default BuddyPress options.
 */
function bp_get_default_options( $options = array() ) {
	return array_merge(
		$options,
		array(
			// The community area default visibility.
			'_bp_community_visibility' => 'publish',
		)
	);
}
add_filter( 'bp_get_default_options', __NAMESPACE__ . '\bp_get_default_options', 1, 1 );

/**
 * Get the community area visibility.
 *
 * @since ?.0.0
 *
 * @param string $default Optional. Fallback value if not found in the database.
 *                        Default: 'publish'.
 * @return string The visibility of the community area. Possible values are `anyone` or `members`.
 */
function bp_get_community_visibility( $default = 'publish' ) {

	/**
	 * Filters the current community area visibility.
	 *
	 * @since ?.0.0
	 *
	 * @param string $value The current community area visibility.
	 */
	return apply_filters( 'bp_get_community_visibility', bp_get_option( '_bp_community_visibility', $default ) );
}
