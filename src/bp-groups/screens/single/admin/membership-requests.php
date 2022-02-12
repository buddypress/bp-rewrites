<?php
/**
 * Group: membership requests Admin screen.
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
 * The redirection should be set this way in `\groups_screen_group_admin_requests()`.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_requests() {
	bp_core_redirect( bp_group_admin_rewrites_get_form_url( \groups_get_current_group(), 'membership-requests', 'bp_group_manage_membership_requests' ) );
}
add_action( 'groups_group_request_managed', __NAMESPACE__ . '\groups_screen_group_admin_requests', 1 );
