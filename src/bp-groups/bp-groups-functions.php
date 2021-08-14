<?php
/**
 * BuddyPress Groups Functions.
 *
 * @package buddypress\bp-groups
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns all potential Group views.
 *
 * @since ?.0.0
 *
 * @param string $context The display context. Required. Defaults to `read`.
 * @return array the list of potential Group views.
 */
function bp_get_group_views( $context = 'read' ) {
	$views = array(
		'create' => array(
			'group-details'     => array(
				'rewrite_id' => 'bp_group_create_group_details',
				'name'       => _x( 'Details', 'Group create screen view', 'buddypress' ),
				'position'   => 0,
			),
			'group-settings'    => array(
				'rewrite_id' => 'bp_group_create_group_settings',
				'name'       => _x( 'Settings', 'Group create screen view', 'buddypress' ),
				'position'   => 10,
			),
			'group-avatar'      => array(
				'rewrite_id' => 'bp_group_create_group_avatar',
				'name'       => _x( 'Photo', 'Group create screen view', 'buddypress' ),
				'position'   => 20,
			),
			'group-cover-image' => array(
				'rewrite_id' => 'bp_group_create_cover_image',
				'name'       => _x( 'Cover Image', 'Group create screen view', 'buddypress' ),
				'position'   => 25,
			),
			'group-invites'     => array(
				'rewrite_id' => 'bp_group_create_group_invites',
				'name'       => _x( 'Invites', 'Group create screen view', 'buddypress' ),
				'position'   => 30,
			),
		),
		'read'   => array(
			'home'               => array(
				'rewrite_id'      => 'bp_group_read_home',
				'name'            => _x( 'Home', 'Group screen view', 'buddypress' ),
				'screen_function' => 'groups_screen_group_home',
				'position'        => 10,
				'item_css_id'     => 'home',
			),
			'request-membership' => array(
				'rewrite_id'      => 'bp_group_read_request_membership',
				'name'            => _x( 'Request Membership', 'Group screen view', 'buddypress' ),
				'screen_function' => 'groups_screen_group_request_membership',
				'position'        => 30,
			),
			'members'            => array(
				'rewrite_id'      => 'bp_group_read_members',
				/* translators: %s: total member count */
				'name'            => _x( 'Members %s', 'Group screen view', 'buddypress' ),
				'screen_function' => 'groups_screen_group_members',
				'position'        => 60,
				'user_has_access' => false,
				'no_access_url'   => '',
				'item_css_id'     => 'members',
			),
			'send-invites'       => array(
				'rewrite_id'      => 'bp_group_read_send_invites',
				'name'            => _x( 'Send Invites', 'Group screen view', 'buddypress' ),
				'screen_function' => 'groups_screen_group_invite',
				'position'        => 70,
				'user_has_access' => false,
				'no_access_url'   => '',
				'item_css_id'     => 'invite',
			),
			'admin'              => array(
				'rewrite_id'      => 'bp_group_read_admin',
				'name'            => _x( 'Manage', 'Group screen view', 'buddypress' ),
				'screen_function' => 'groups_screen_group_admin',
				'position'        => 1000,
				'user_has_access' => false,
				'no_access_url'   => '',
				'item_css_id'     => 'admin',
			),
		),
		'manage' => array(
			'edit-details'        => array(
				'rewrite_id'        => 'bp_group_manage_edit_details',
				'name'              => _x( 'Details', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 0,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-settings'      => array(
				'rewrite_id'        => 'bp_group_manage_group_settings',
				'name'              => _x( 'Settings', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 10,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-avatar'        => array(
				'rewrite_id'        => 'bp_group_manage_group_avatar',
				'name'              => _x( 'Photo', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 20,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-cover-image'   => array(
				'rewrite_id'        => 'bp_group_manage_group_cover_image',
				'name'              => _x( 'Photo', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 25,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'manage-members'      => array(
				'rewrite_id'        => 'bp_group_manage_manage_members',
				'name'              => _x( 'Members', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 30,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'membership-requests' => array(
				'rewrite_id'        => 'bp_group_manage_membership_requests',
				'name'              => _x( 'Members', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 40,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'delete-group'        => array(
				'rewrite_id'        => 'bp_group_manage_delete_group',
				'name'              => _x( 'Delete', 'Group manage screen view', 'buddypress' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 1000,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
		),
	);

	if ( ! isset( $views[ $context ] ) ) {
		return array();
	}

	$context_views = array();
	$custom_views  = apply_filters( 'bp_get_group_custom_' . $context . '_views', $context_views );

	if ( $custom_views && ! wp_is_numeric_array( $custom_views ) ) {
		// The view key (used as default slug) and `rewrite_id` prop need to be unique.
		$valid_custom_views   = array_diff_key( $custom_views, $views[ $context ] );
		$existing_rewrite_ids = array_column( $views[ $context ], 'rewrite_id' );

		foreach ( $valid_custom_views as $key_view => $view ) {
			if ( ! in_array( $view['rewrite_id'], $existing_rewrite_ids, true ) ) {
				continue;
			}

			unset( $valid_custom_views[ $key_view ] );
		}

		$context_views = array_merge( $views[ $context ], $valid_custom_views );
	} else {
		$context_views = $views[ $context ];
	}

	return $context_views;
}
