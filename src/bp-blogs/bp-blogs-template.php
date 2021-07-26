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
 * @since ?.0.0
 */
function bp_blog_create_link() {
	echo esc_url( bp_get_blog_create_link() );
}

/**
 * Return the create blog link.
 *
 * @since ?.0.0
 *
 * @return string The URL of the create blog link.
 */
function bp_get_blog_create_link() {

	/**
	 * Filters the blog create blog link.
	 *
	 * @since ?.0.0
	 *
	 * @param string $value Permalink URL for the create blog link.
	 */
	return apply_filters( 'bp_get_blog_create_link', trailingslashit( bp_get_root_domain() . '/' . bp_get_blogs_root_slug() ) . 'create/' );
}
