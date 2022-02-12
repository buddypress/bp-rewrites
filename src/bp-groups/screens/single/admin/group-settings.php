<?php
/**
 * Group: Settings screen.
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
 * The redirection should be set this way in `\groups_screen_group_admin_settings()`.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_settings() {
	bp_core_redirect( bp_group_admin_rewrites_get_form_url( \groups_get_current_group(), 'group-settings', 'bp_group_manage_group_settings' ) );
}
add_action( 'groups_group_settings_edited', __NAMESPACE__ . '\groups_screen_group_admin_settings', 1 );
