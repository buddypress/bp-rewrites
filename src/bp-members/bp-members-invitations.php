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

	$parent_slug = bp_get_members_invitations_slug();

	// Get the main nav.
	$main_nav = $bp->members->nav->get_primary( array( 'slug' => $parent_slug ), false );

	// Set the main nav `rewrite_id` property.
	$main_nav['rewrite_id'] = sprintf( 'bp_member_%s', $parent_slug );
	$rewrite_id             = $main_nav['rewrite_id'];

	// Reset the link using BP Rewrites.
	$main_nav['link'] = bp_members_rewrites_get_nav_url(
		array(
			'rewrite_id'     => $rewrite_id,
			'item_component' => $parent_slug,
		)
	);

	$bp->members->nav->edit_nav( $main_nav, $parent_slug );

	// Get the sub nav items for this main nav.
	$sub_nav_items = $bp->members->nav->get_secondary( array( 'parent_slug' => $parent_slug ), false );

	// Loop inside it to reset the link using BP Rewrites before updating it.
	foreach ( $sub_nav_items as $sub_nav_item ) {
		$sub_nav_item['link'] = bp_members_rewrites_get_nav_url(
			array(
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $sub_nav_item['slug'],
			)
		);

		// Update the secondary nav item.
		$bp->members->nav->edit_nav( $sub_nav_item, $sub_nav_item['slug'], $parent_slug );
	}
}
add_action( 'bp_setup_nav', __NAMESPACE__ . '\bp_members_invitations_setup_nav', 11 );
