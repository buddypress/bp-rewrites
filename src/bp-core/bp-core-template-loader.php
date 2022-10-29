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

	// Bail if $posts_query is not the main loop and not done in BP Ajax context.
	if ( ! is_bp_doing_ajax() && ! $posts_query->is_main_query() ) {
		return;
	}

	// Bail if filters are suppressed on this query.
	if ( true === (bool) $posts_query->get( 'suppress_filters' ) ) {
		return;
	}

	// Bail if in admin and not done in BP Ajax context.
	if ( ! is_bp_doing_ajax() && is_admin() ) {
		return;
	}

	// Some Legacy Parser URL globals/filters need to be set at this time.
	$bp                        = buddypress();
	$bp->unfiltered_uri        = bp_core_get_from_uri( array( 'unfiltered_uri' ) );
	$bp->unfiltered_uri_offset = 0; // It's unclear how this is set for now. @todo review this.

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

/**
 * Resets the query to fit our permalink structure if needed.
 *
 * This is used for specific cases such as Root Member's profile or Ajax.
 *
 * @since 1.0.0
 *
 * @param string    $bp_request A specific BuddyPress request.
 * @param \WP_Query $query The WordPress query object.
 * @return true
 */
function bp_reset_query( $bp_request = '', \WP_Query $query = null ) {
	global $wp;

	// Get BuddyPress main instance.
	$bp = buddypress();

	// Back up request uri.
	$reset_server_request_uri = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$reset_server_request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	// Temporarly override it.
	if ( isset( $bp->ajax ) ) {
		$_SERVER['REQUEST_URI'] = $bp_request;

		if ( bp_has_pretty_urls() ) {
			$bp->ajax->WP->parse_request();

			// Extra step to check for root profiles.
			$member = bp_rewrites_get_member_data( $bp->ajax->WP->request );
			if ( isset( $member['object'] ) && $member['object'] ) {
				$_SERVER['REQUEST_URI'] = trailingslashit( $bp->members->root_slug ) . $bp->ajax->WP->request;

				// Reparse the request.
				$bp->ajax->WP->parse_request();
			}

			$matched_query = $bp->ajax->WP->matched_query;
		} else {
			$matched_query = wp_parse_url( $bp_request, PHP_URL_QUERY );
		}

		$query->parse_query( $matched_query );

		/*
		 * BP Parse the request in components only once per Ajax request.
		 *
		 * This should be `remove_action( 'parse_query', 'bp_parse_query', 2 );` in BP Core.
		 */
		remove_action( 'parse_query', __NAMESPACE__ . '\bp_parse_query', 2 );

	} elseif ( isset( $wp->request ) ) {
		$_SERVER['REQUEST_URI'] = str_replace( $wp->request, $bp_request, $reset_server_request_uri );

		// Reparse request.
		$wp->parse_request();

		// Reparse query.
		bp_remove_all_filters( 'parse_query' );
		$query->parse_query( $wp->query_vars );
		bp_restore_all_filters( 'parse_query' );
	}

	// Restore request uri.
	$_SERVER['REQUEST_URI'] = $reset_server_request_uri;

	// The query is reset.
	return true;
}

/**
 * Makes sure BuddyPress globals are set during Ajax requests.
 *
 * @since 1.0.0
 */
function bp_reset_ajax_query() {
	if ( ! wp_doing_ajax() ) {
		return;
	}

	// Get BuddyPress main instance.
	$bp       = buddypress();
	$bp->ajax = (object) array(
		'WP' => new \WP(),
	);

	bp_reset_query( bp_get_referer_path(), $GLOBALS['wp_query'] );
}
add_action( 'bp_admin_init', __NAMESPACE__ . '\bp_reset_ajax_query' );
