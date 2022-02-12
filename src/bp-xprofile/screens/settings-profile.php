<?php
/**
 * XProfile: User's "Settings > Profile Visibility" screen handler
 *
 * @package buddypress\bp-xprofile\screens
 *
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * This code should be in `\bp_xprofile_action_settings()`.
 *
 * @since 1.0.0
 */
function bp_xprofile_action_settings() {
	$bp = buddypress();

	$parent_slug = bp_get_settings_slug();
	$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
	$redirect    = bp_members_rewrites_get_nav_url(
		array(
			'rewrite_id'     => $rewrite_id,
			'item_component' => $parent_slug,
			'item_action'    => 'profile',
		)
	);

	// Redirect to Profile Visibility Settings screen.
	bp_core_redirect( $redirect );
}
add_action( 'bp_xprofile_settings_after_save', __NAMESPACE__ . '\bp_xprofile_action_settings' );
