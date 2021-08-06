<?php
/**
 * BuddyPress Members Template Tags.
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
 * `\bp_get_members_directory_permalink()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url The Members directory permalink built for the BP Legacy URL parser.
 * @return string     The Members directory permalink built for the BP Rewrites URL parser.
 */
function bp_get_members_directory_permalink( $url = '' ) {
	return bp_members_rewrites_get_url( $url );
}
add_filter( 'bp_get_members_directory_permalink', __NAMESPACE__ . '\bp_get_members_directory_permalink', 1, 1 );

/**
 * `\bp_get_displayed_user_nav()` needs to be edited to stop modifying the nav item links.
 *
 * @since ?.0.0
 *
 * @param string $output          Nav Item HTML output.
 * @param object $nav_item_object The nav item object.
 * @return string                 Nav Item HTML output.
 */
function bp_get_displayed_user_nav( $output = '', $nav_item_object = null ) {
	if ( isset( $nav_item_object->link ) && $nav_item_object->link ) {
		$output = preg_replace( '/href=\"(.*?)\">/', sprintf( 'href="%s">', $nav_item_object->link ), $output );
	}

	return $output;
}
