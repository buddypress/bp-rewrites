<?php
/**
 * Required BP Legacy edits.
 *
 * @package buddypress\bp-templates\bp-nouveau
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loop through each Primary nav item to filter its link.
 *
 * @since ?.0.0
 */
function bp_legacy_displayed_user_nav() {
	$user_nav_items = buddypress()->members->nav->get_primary();
	if ( $user_nav_items ) {
		foreach ( $user_nav_items as $user_nav_item ) {
			if ( ! isset( $user_nav_item->css_id ) || ! $user_nav_item->css_id ) {
				continue;
			}

			add_filter( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, __NAMESPACE__ . '\bp_get_displayed_user_nav', 1, 2 );
		}
	}
}
add_action( 'bp_setup_nav', __NAMESPACE__ . '\bp_legacy_displayed_user_nav', 1000 );
