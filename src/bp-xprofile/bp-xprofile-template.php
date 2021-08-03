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
 * @since ?.0.0
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
