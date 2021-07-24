<?php
/**
 * BuddyPress Catch URI functions.
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code to move in `\bp_core_load_template()`.
 *
 * @since ?.0.0
 */
function bp_core_load_template() {
	if ( 'bp_core_pre_load_template' === current_action() || ( 'bp_setup_theme_compat' === current_action() && is_buddypress() ) ) {
		global $wp_query;

		// BuddyPress is not home!
		$wp_query->is_home = false;
	}
}
add_action( 'bp_core_pre_load_template', __NAMESPACE__ . '\bp_core_load_template' );
add_action( 'bp_setup_theme_compat', __NAMESPACE__ . '\bp_core_load_template' );
