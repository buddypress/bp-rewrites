<?php
/**
 * BuddyPress Members Filters.
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
 * `\bp_members_edit_profile_url()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $profile_link The BuddyPress Edit Profile URL.
 * @param string $url          The WP profile edit URL.
 * @param int    $user_id      ID of the user.
 * @return string              The BuddyPress Edit Profile URL built with BP Rewrites.
 */
function bp_members_edit_profile_url( $profile_link = '', $url = '', $user_id = 0 ) {
	if ( is_admin() || ! bp_is_active( 'xprofile' ) ) {
		return $profile_link;
	}

	$slug       = bp_get_profile_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	return bp_member_rewrites_get_url(
		$user_id,
		'',
		array(
			'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
			'single_item_action'    => 'edit',
		)
	);
}
add_filter( 'bp_members_edit_profile_url', __NAMESPACE__ . '\bp_members_edit_profile_url', 1, 3 );
