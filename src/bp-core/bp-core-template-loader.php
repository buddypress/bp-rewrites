<?php
/**
 * BuddyPress Catch URI functions.
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
 * Override completely `\bp_parse_query`.
 *
 * Add checks for BuddyPress conditions to 'parse_query' action.
 *
 * @since 1.7.0
 *
 * @param \WP_Query $posts_query WP_Query object.
 */
function bp_parse_query( $posts_query ) {
	$bp_is_doing_ajax = isset( buddypress()->ajax->WP );

	// Bail if $posts_query is not the main loop and not done in BP Ajax context.
	if ( ! $bp_is_doing_ajax && ! $posts_query->is_main_query() ) {
		return;
	}

	// Bail if filters are suppressed on this query.
	if ( true === (bool) $posts_query->get( 'suppress_filters' ) ) {
		return;
	}

	// Bail if in admin and not done in BP Ajax context.
	if ( ! $bp_is_doing_ajax && is_admin() ) {
		return;
	}

	/**
	 * Fires at the end of the bp_parse_query function.
	 *
	 * Allow BuddyPress components to parse the main query.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Query $posts_query WP_Query instance. Passed by reference.
	 */
	do_action_ref_array( 'bp_parse_query', array( &$posts_query ) );
}
remove_action( 'parse_query', 'bp_parse_query', 2 );
add_action( 'parse_query', __NAMESPACE__ . '\bp_parse_query', 2 );
