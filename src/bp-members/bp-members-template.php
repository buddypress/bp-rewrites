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
 * @since 1.0.0
 *
 * @param string $url The Members directory permalink built for the BP Legacy URL parser.
 * @return string     The Members directory permalink built for the BP Rewrites URL parser.
 */
function bp_get_members_directory_permalink( $url = '' ) {
	return bp_members_rewrites_get_url();
}
add_filter( 'bp_get_members_directory_permalink', __NAMESPACE__ . '\bp_get_members_directory_permalink', 1, 1 );

/**
 * `\bp_get_displayed_user_nav()` needs to be edited to stop modifying the nav item links.
 *
 * @since 1.0.0
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

/**
 * `\bp_get_signup_page()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The Signup URL built for the BP Legacy URL parser.
 * @return string     The Signup URL built for the BP Rewrites URL parser.
 */
function bp_get_signup_page( $url = '' ) {
	if ( ! bp_has_custom_signup_page() ) {
		return $url;
	}

	return bp_signup_rewrites_get_url();
}
add_filter( 'bp_get_signup_page', __NAMESPACE__ . '\bp_get_signup_page', 1, 1 );

/**
 * `\bp_get_activation_page()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The Activation URL built for the BP Legacy URL parser.
 * @return string     The Activation URL built for the BP Rewrites URL parser.
 */
function bp_get_activation_page( $url = '' ) {
	if ( ! bp_has_custom_activation_page() ) {
		return $url;
	}

	return bp_activation_rewrites_get_url();
}
add_filter( 'bp_get_activation_page', __NAMESPACE__ . '\bp_get_activation_page', 1, 1 );

/**
 * `\bp_get_member_type_directory_permalink()` should use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url  The URL built for the BP Legacy URL parser.
 * @param object $type The Member type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_get_member_type_directory_permalink( $url = '', $type = null ) {
	return bp_member_type_rewrites_get_url( $type );
}
add_filter( 'bp_get_member_type_directory_permalink', __NAMESPACE__ . '\bp_get_member_type_directory_permalink', 1, 2 );
