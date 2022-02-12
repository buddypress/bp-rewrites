<?php
/**
 * BuddyPress xProfile Rewrites.
 *
 * @package buddypress\bp-xprofile
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Member's xProfile Edit URL.
 *
 * @since 1.0.0
 *
 * @param string $url            The URL built for the BP Legacy URL parser. Never used.
 *                               But may be passed when this function is used as a filter.
 * @param int    $field_group_id The xProfile Fields Group being edited.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_xprofile_rewrites_get_edit_url( $url = '', $field_group_id = 0 ) {
	if ( ! $field_group_id ) {
		return $url;
	}

	return bp_rewrites_get_url(
		array(
			'component_id'                 => 'members',
			'single_item'                  => bp_rewrites_get_member_slug( \bp_displayed_user_id() ),
			'single_item_component'        => bp_rewrites_get_slug( 'members', 'bp_member_profile', bp_get_profile_slug() ),
			'single_item_action'           => bp_rewrites_get_slug( 'members', 'bp_member_profile_edit', 'edit' ),
			'single_item_action_variables' => array( 'group', $field_group_id ),
		)
	);
}
