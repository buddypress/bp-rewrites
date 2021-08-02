<?php
/**
 * BuddyPress Blogs Rewrites.
 *
 * @package buddypress\bp-blogs
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Blogs Directory URL.
 *
 * @since ?.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser. Never used.
 *                    But may be passed when this function is used as a filter.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_blogs_rewrites_get_url( $url = '' ) {
	return bp_rewrites_get_url(
		array(
			'component_id' => 'blogs',
		)
	);
}

/**
 * Return the Blog's creation link.
 *
 * @since ?.0.0
 *
 * @param string $url  The URL built for the BP Legacy URL parser. Never used.
 *                     But may be passed when this function is used as a filter.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_blog_create_rewrites_get_url( $url = '' ) {
	return bp_rewrites_get_url(
		array(
			'component_id'       => 'blogs',
			'single_item_action' => 'create'
		)
	);
}
