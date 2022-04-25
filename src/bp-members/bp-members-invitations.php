<?php
/**
 * BuddyPress Membersip Invitations.
 *
 * @package buddypress\bp-members
 * @since 8.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code to use inside `\bp_members_invitations_setup_nav()`.
 *
 * @since 1.0.0
 */
function bp_members_invitations_setup_nav() {
	$bp = buddypress();

	// In this case a new navigation is created for Members invitations.
	if ( ! bp_get_members_invitations_allowed() ) {
		return;
	}

	// Get the main nav.
	$main_nav = $bp->members->nav->get_primary( array( 'slug' => bp_get_members_invitations_slug() ), false );
	if ( ! $main_nav ) {
		return;
	}

	$main_nav = reset( $main_nav );

	if ( isset( $main_nav['slug'] ) ) {
		$slug = $main_nav['slug'];

		// Set the main nav `rewrite_id` property.
		$main_nav['rewrite_id'] = sprintf( 'bp_member_%s', $slug );
		$rewrite_id             = $main_nav['rewrite_id'];

		// Reset the link using BP Rewrites.
		$main_nav['link'] = bp_members_rewrites_get_nav_url(
			array(
				'rewrite_id'     => $rewrite_id,
				'item_component' => $slug,
			)
		);

		// Update the primary nav item.
		$bp->members->nav->edit_nav( $main_nav, $slug );

		// Update the secondary nav items.
		reset_secondary_nav( $slug, $rewrite_id );
	}
}
add_action( 'bp_setup_nav', __NAMESPACE__ . '\bp_members_invitations_setup_nav', 11 );
