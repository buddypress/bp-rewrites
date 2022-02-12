<?php
/**
 * BuddyPress Blogs Template Tags.
 *
 * @package buddypress\bp-blogs
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the create blog link.
 *
 * @since 1.0.0
 */
function bp_blog_create_link() {
	echo esc_url( bp_get_blog_create_link() );
}

/**
 * Return the create blog link.
 *
 * NB : This function should be used instead of:
 * `trailingslashit( bp_get_blogs_directory_permalink() . 'create' )`
 *
 * @since 1.0.0
 *
 * @return string The URL of the create blog link.
 */
function bp_get_blog_create_link() {
	$link = bp_blog_create_rewrites_get_url();

	/**
	 * Filters the create blog link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Permalink URL for the create blog link.
	 */
	return apply_filters( 'bp_get_blog_create_link', $link );
}

/**
 * Code to move inside `bp_get_blog_create_button()` once `bp_get_blog_create_link()`
 * has been merged into BP Core.
 *
 * @since 1.0.0
 *
 * @param array $button_args {
 *     Optional. An array of arguments.
 *     @see `bp_get_blog_create_button()` for the full description of arguments.
 * }
 * @return array An array of arguments.
 */
function bp_get_blog_create_button( $button_args = array() ) {
	$button_args['link_href'] = bp_get_blog_create_link();
	return $button_args;
}
add_filter( 'bp_get_blog_create_button', __NAMESPACE__ . '\bp_get_blog_create_button', 1, 1 );

/**
 * `bp_get_blogs_directory_permalink()` needs to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_get_blogs_directory_permalink( $url = '' ) {
	return bp_blogs_rewrites_get_url();
}
add_action( 'bp_get_blogs_directory_permalink', __NAMESPACE__ . '\bp_get_blogs_directory_permalink', 1, 1 );
