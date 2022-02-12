<?php
/**
 * Group: Admin screen.
 *
 * @package buddypress\bp-groups\screens\single
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This code should move inside `\groups_screen_group_admin()`.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin() {
	if ( ! bp_is_groups_component() || ! bp_is_current_action( 'admin' ) ) {
		return false;
	}

	if ( \bp_action_variables() ) {
		return false;
	}

	bp_core_redirect( bp_group_admin_rewrites_get_form_url( \groups_get_current_group(), 'edit-details', 'bp_group_manage_edit_details' ) );
}
