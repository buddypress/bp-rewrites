<?php
/**
 * BuddyPress xProfile Template Tags.
 *
 * @package buddypress\bp-xprofile
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code to move inside `bp_get_the_profile_group_edit_form_action()`.
 *
 * @since 1.0.0
 *
 * @global BP_XProfile_Group $group The Field Group object.
 * @param string $url The Member's xProfile Edit URL.
 * @return string     The Member's xProfile Edit URL built using BP Rewrites.
 */
function bp_get_the_profile_group_edit_form_action( $url = '' ) {
	global $group;

	return bp_xprofile_rewrites_get_edit_url( '', $group->id );
}
add_filter( 'bp_get_the_profile_group_edit_form_action', __NAMESPACE__ . '\bp_get_the_profile_group_edit_form_action', 1, 1 );

/**
 * Return the XProfile group tabs.
 *
 * @since 1.0.0
 *
 * @param array  $tabs       Array of tabs to display.
 * @param array  $groups     Array of profile groups.
 * @param string $group_name Name of the current group displayed.
 * @return array             Array of tabs to display.
 */
function bp_get_profile_group_tabs( $tabs = array(), $groups = array(), $group_name = '' ) {
	$profile_group_tabs = array();

	if ( is_array( $groups ) && count( $groups ) ) {
		foreach ( $groups as $group ) {
			$selected = '';
			if ( $group_name === $group->name ) {
				$selected = ' class="current"';
			}

			// Skip if group has no fields.
			if ( empty( $group->fields ) ) {
				continue;
			}

			// Build the profile field group link, using the BP Rewrites API.
			$link = bp_xprofile_rewrites_get_edit_url( '', $group->id );

			// Add tab to end of tabs array.
			$profile_group_tabs[] = sprintf(
				'<li %1$s><a href="%2$s">%3$s</a></li>',
				$selected,
				esc_url( $link ),
				esc_html( apply_filters( 'bp_get_the_profile_group_name', $group->name ) )
			);
		}
	}

	if ( $profile_group_tabs ) {
		return $profile_group_tabs;
	}

	return $tabs;
}
add_filter( 'xprofile_filter_profile_group_tabs', __NAMESPACE__ . '\bp_get_profile_group_tabs', 1, 3 );
