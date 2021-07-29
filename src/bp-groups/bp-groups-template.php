<?php
/**
 * BuddyPress Groups Template Tags.
 *
 * @package buddypress\bp-groups
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the group's step creation link.
 *
 * @since ?.0.0
 *
 * @param string $step The group creation step name.
 */
function bp_group_create_link( $step = '' ) {
	echo esc_url( bp_get_group_create_link( $step ) );
}

/**
 * Return the group's step creation link.
 *
 * @since ?.0.0
 *
 * @param string $step The group creation step name.
 * @return string The URL of the group's step creation link.
 */
function bp_get_group_create_link( $step = '' ) {
	$link = trailingslashit( bp_get_groups_directory_permalink() . 'create' );

	if ( $step ) {
		$link = trailingslashit( $link . 'step/' . $step );
	}

	/**
	 * Filters the group's step creation link.
	 *
	 * @since ?.0.0
	 *
	 * @param string $link The group's step creation link.
	 * @param string $step The group creation step name.
	 */
	return apply_filters( 'bp_get_group_create_link', $link, $step );
}

/**
 * Code to move inside `bp_get_group_create_button()` once `bp_get_group_create_link()`
 * has been merged into BP Core.
 *
 * @since ?.0.0
 *
 * @param array $button_args {
 *     Optional. An array of arguments.
 *     @see `bp_get_group_create_button()` for the full description of arguments.
 * }
 * @return array An array of arguments.
 */
function bp_get_group_create_button( $button_args = array() ) {
	$button_args['link_href'] = bp_get_group_create_link();
	return $button_args;
}
add_filter( 'bp_get_group_create_button', __NAMESPACE__ . '\bp_get_group_create_button', 1, 1 );

/**
 * Code to move inside `bp_get_group_creation_form_action()` once `bp_get_group_create_link()`
 * has been merged into BP Core.
 *
 * @since ?.0.0
 *
 * @param string $url The URL used by the step's creation form.
 * @return string     The URL used by the step's creation form.
 */
function bp_get_group_creation_form_action( $url = '' ) {
	return bp_get_group_create_link( bp_action_variable( 1 ) );
}
add_filter( 'bp_get_group_creation_form_action', __NAMESPACE__ . '\bp_get_group_creation_form_action', 1, 1 );
