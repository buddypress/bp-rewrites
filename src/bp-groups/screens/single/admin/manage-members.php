<?php
/**
 * Group: Manage Members screen.
 *
 * @package buddypress\bp-groups\screens\single\admin
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The redirection should be set this way in `\groups_screen_group_admin_manage_members()`.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_manage_members() {
	bp_core_redirect( bp_group_admin_rewrites_get_form_url( \groups_get_current_group(), 'manage-members', 'bp_group_manage_manage_members' ) );
}
add_action( 'groups_removed_member', __NAMESPACE__ . '\groups_screen_group_admin_manage_members', 1 );
add_action( 'groups_unbanned_member', __NAMESPACE__ . '\groups_screen_group_admin_manage_members', 1 );
add_action( 'groups_banned_member', __NAMESPACE__ . '\groups_screen_group_admin_manage_members', 1 );
add_action( 'groups_demoted_member', __NAMESPACE__ . '\groups_screen_group_admin_manage_members', 1 );
add_action( 'groups_promoted_member', __NAMESPACE__ . '\groups_screen_group_admin_manage_members', 1 );
