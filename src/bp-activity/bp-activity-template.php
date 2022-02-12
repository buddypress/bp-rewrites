<?php
/**
 * BuddyPress Activity Template Functions.
 *
 * @package buddypress\bp-activity
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_get_activity_directory_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_directory_permalink( $url = '' ) {
	return bp_activities_rewrites_get_url();
}
add_filter( 'bp_get_activity_directory_permalink', __NAMESPACE__ . '\bp_get_activity_directory_permalink', 1, 1 );

/**
 * `\bp_get_sitewide_activity_feed_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_sitewide_activity_feed_link( $url = '' ) {
	return bp_activity_rewrites_get_sitewide_feed_url();
}
add_filter( 'bp_get_sitewide_activity_feed_link', __NAMESPACE__ . '\bp_get_sitewide_activity_feed_link', 1, 1 );

/**
 * `\bp_get_activity_post_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_post_form_action( $url = '' ) {
	return bp_activity_rewrites_get_post_form_action();
}
add_filter( 'bp_get_activity_post_form_action', __NAMESPACE__ . '\bp_get_activity_post_form_action', 1, 1 );

/**
 * `\bp_get_activity_comment_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_comment_form_action( $url = '' ) {
	return bp_activity_rewrites_get_comment_form_action();
}
add_filter( 'bp_get_activity_comment_form_action', __NAMESPACE__ . '\bp_get_activity_comment_form_action', 1, 1 );

/**
 * `\bp_get_activity_comment_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_comment_link( $url = '' ) {
	// Get URL query vars.
	$query_vars = array();
	wp_parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $query_vars );

	// Get URL anchor.
	$anchor = wp_parse_url( $url, PHP_URL_FRAGMENT );

	return bp_activity_rewrites_get_comment_url( $query_vars, $anchor );
}
add_filter( 'bp_get_activity_comment_link', __NAMESPACE__ . '\bp_get_activity_comment_link', 1, 1 );

/**
 * `\bp_get_activity_favorite_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_favorite_link( $url = '' ) {
	return bp_activity_rewrites_get_favorite_url();
}
add_filter( 'bp_get_activity_favorite_link', __NAMESPACE__ . '\bp_get_activity_favorite_link', 1, 1 );

/**
 * `\bp_get_activity_unfavorite_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_unfavorite_link( $url = '' ) {
	return bp_activity_rewrites_get_unfavorite_url();
}
add_filter( 'bp_get_activity_unfavorite_link', __NAMESPACE__ . '\bp_get_activity_unfavorite_link', 1, 1 );

/**
 * `\bp_get_activity_delete_url()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_activity_delete_url( $url = '' ) {
	// Get query vars.
	$query_vars = array();
	wp_parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $query_vars );

	return bp_activity_rewrites_get_delete_url( 0, $query_vars );
}
add_filter( 'bp_get_activity_delete_url', __NAMESPACE__ . '\bp_get_activity_delete_url', 1, 1 );
