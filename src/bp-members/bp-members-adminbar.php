<?php
/**
 * BuddyPress Members Toolbar.
 *
 * @package buddypress\bp-members
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code to use inside `\bp_members_admin_bar_add_invitations_menu()`.
 *
 * @since 1.0.0
 */
function bp_members_admin_bar_add_invitations_menu() {
	global $wp_admin_bar;

	// Bail if this is an ajax request.
	if ( wp_doing_ajax() ) {
		return;
	}

	if ( bp_current_user_can( 'bp_members_invitations_view_screens' ) ) {
		$user_id     = bp_loggedin_user_id();
		$parent_slug = bp_get_members_invitations_slug();
		$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );

		$admin_nav = array(
			0 => array(
				'id'     => 'my-account-invitations',
				'parent' => buddypress()->my_account_menu_id,
				'title'  => __( 'Invitations', 'bp-rewrites' ),
				'href'   => bp_members_rewrites_get_nav_url(
					array(
						'user_id'        => $user_id,
						'rewrite_id'     => $rewrite_id,
						'item_component' => $parent_slug,
					)
				),
			),
			1 => array(),
			2 => array(
				'id'     => 'my-account-invitations-invitations-list',
				'parent' => 'my-account-invitations',
				'title'  => __( 'Pending Invites', 'bp-rewrites' ),
				'href'   => bp_members_rewrites_get_nav_url(
					array(
						'user_id'        => $user_id,
						'rewrite_id'     => $rewrite_id,
						'item_component' => $parent_slug,
						'item_action'    => bp_rewrites_get_slug( 'members', 'bp_member_invitations_list_invites', 'list-invites' ),
					)
				),
			),
		);

		if ( bp_current_user_can( 'bp_members_invitations_view_send_screen' ) ) {
			$admin_nav[1] = array(
				'id'     => 'my-account-invitations-invitations-send',
				'parent' => 'my-account-invitations',
				'title'  => __( 'Send Invites', 'bp-rewrites' ),
				'href'   => bp_members_rewrites_get_nav_url(
					array(
						'user_id'        => $user_id,
						'rewrite_id'     => $rewrite_id,
						'item_component' => $parent_slug,
						'item_action'    => bp_rewrites_get_slug( 'members', 'bp_member_invitations_send_invites', 'send-invites' ),
					)
				),
			);
		}

		foreach ( array_filter( $admin_nav ) as $admin_nav_item ) {
			$args = array_merge(
				$admin_nav_item,
				array(
					'meta' => array(
						'class' => 'ab-sub-secondary',
					),
				)
			);

			$wp_admin_bar->add_node( $args );
		}
	}
}
add_action( 'bp_setup_admin_bar', __NAMESPACE__ . '\bp_members_admin_bar_add_invitations_menu', 90 );
