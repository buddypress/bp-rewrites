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
 * `\bp_get_groups_directory_permalink()` needs to be edited so that it uses BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string $url The Groups Directory permalink.
 * @return string     The Groups Directory permalink built using BP Rewrites.
 */
function bp_get_groups_directory_permalink( $url = '' ) {
	return bp_groups_rewrites_get_url( $url );
}
add_filter( 'bp_get_groups_directory_permalink', __NAMESPACE__ . '\bp_get_groups_directory_permalink', 1, 1 );

/**
 * `\bp_get_group_permalink()` needs to be edited so that it uses BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string          $url   The Group permalink.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group permalink built using BP Rewrites.
 */
function bp_get_group_permalink( $url = '', $group = null ) {
	return bp_group_rewrites_get_url( $url, $group );
}
add_filter( 'bp_get_group_permalink', __NAMESPACE__ . '\bp_get_group_permalink', 1, 2 );

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
 * NB: this function should be used instead of:
 * `trailingslashit( bp_get_groups_directory_permalink() . 'create' )`
 *
 * @since ?.0.0
 *
 * @param string $step The group creation step name.
 * @return string The URL of the group's step creation link.
 */
function bp_get_group_create_link( $step = '' ) {
	$link = bp_group_create_rewrites_get_url( '', $step );

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

/**
 * `\bp_get_group_admin_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string          $url   The Group admin form action URL built for the BP Legacy URL parser.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group admin form action URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_form_action( $url = '', $group = null ) {
	return bp_group_admin_rewrites_get_form_url( $url, $group );
}
add_filter( 'bp_group_admin_form_action', __NAMESPACE__ . '\bp_group_admin_form_action', 1, 2 );

/**
 * `\bp_group_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since ?.0.0
 *
 * @param string          $url   The Group form action URL built for the BP Legacy URL parser.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group form action URL built for the BP Rewrites URL parser.
 */
function bp_group_form_action( $url = '', $group = null ) {
	$action = bp_current_action();

	if ( ! $action ) {
		return $url;
	}

	return bp_group_rewrites_get_action_url( $url, $action, $group );
}
add_filter( 'bp_group_form_action', __NAMESPACE__ . '\bp_group_form_action', 1, 2 );
