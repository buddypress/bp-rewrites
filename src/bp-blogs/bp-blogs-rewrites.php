<?php
/**
 * BuddyPress Blogs Rewrites.
 *
 * @package buddypress\bp-blogs
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Blogs Directory URL.
 *
 * @since 1.0.0
 *
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_blogs_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id' => 'blogs',
		)
	);
}

/**
 * Return the Blog's creation link.
 *
 * @since 1.0.0
 *
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_blog_create_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id'       => 'blogs',
			'single_item_action' => 'create',
		)
	);
}
