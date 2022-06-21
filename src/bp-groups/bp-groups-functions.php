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
 * Adds backward compatibility when `\groups_get_current_group()` is called too early.
 *
 * @since 1.0.0
 *
 * @param false|BP_Groups_Group $current_group False if the current group is not set yet. The current Group object otherwise.
 * @return null|BP_Groups_Group                Null if the current group is not set yet. The current Group object otherwise.
 */
function groups_get_current_group( $current_group = false ) {
	if ( ! isset( $current_group->id ) ) {
		$current_group = _was_called_too_early( 'groups_get_current_group()', array( 'current_group' ) );
	}

	return $current_group;
}
add_filter( 'groups_get_current_group', __NAMESPACE__ . '\groups_get_current_group', 1, 1 );

/**
 * Adds backward compatibility when `bp_get_current_group_directory_type()` is called too early.
 *
 * @since 1.0.0
 *
 * @param string $current_directory_type Empty string if the current group type is not set yet. The group type otherwise.
 * @return null|string           Null if the current group type is not set yet. The group type name otherwise.
 */
function bp_get_current_group_directory_type( $current_directory_type = '' ) {
	if ( ! $current_directory_type ) {
		$current_directory_type = _was_called_too_early( 'bp_get_current_group_directory_type()', array( 'current_directory_type' ) );
	}

	return $current_directory_type;
}
add_filter( 'bp_get_current_group_directory_type', __NAMESPACE__ . '\bp_get_current_group_directory_type', 1, 1 );

/**
 * Returns the Group restricted views.
 *
 * @since 1.0.0
 *
 * @return array The list of the Group restricted views.
 */
function bp_get_group_restricted_views() {
	return array(
		'bp_group_create'      => array(
			'rewrite_id' => 'bp_group_create',
			'slug'       => 'create',
			'name'       => _x( 'Create Group root slug', 'Group create restricted rewrite id', 'bp-rewrites' ),
			'context'    => 'create',
		),
		'bp_group_create_step' => array(
			'rewrite_id' => 'bp_group_create_step',
			'slug'       => 'step',
			'name'       => _x( 'Create step slug', 'Group create restricted rewrite id', 'bp-rewrites' ),
			'context'    => 'create',
		),
	);
}

/**
 * Returns all registered Group Extension views.
 *
 * @since 1.0.0
 *
 * @param string $context The display context. Required. Defaults to `read`.
 * @return array          The list of registered Group Extension views.
 */
function bp_get_group_extension_views( $context = 'read' ) {
	$bp = buddypress();

	$group_extension_views = array(
		'create' => array(),
		'manage' => array(),
		'read'   => array(),
	);

	if ( $bp->groups->group_extensions ) {
		foreach ( $bp->groups->group_extensions as $extension_views ) {
			if ( ! is_array( $extension_views ) ) {
				continue;
			}

			foreach ( $extension_views as $ctext => $extension_view ) {
				$group_extension_views[ $ctext ] = array_merge( $group_extension_views[ $ctext ], $extension_view );
			}
		}
	}

	if ( ! array_filter( $group_extension_views ) || ! isset( $group_extension_views[ $context ] ) ) {
		return array();
	}

	return $group_extension_views[ $context ];
}

/**
 * Returns all potential Group views.
 *
 * @since 1.0.0
 *
 * @param string $context The display context. Required. Defaults to `read`.
 * @return array          The list of potential Group views.
 */
function bp_get_group_views( $context = 'read' ) {
	$views = array(
		'create' => array(
			'group-details'     => array(
				'rewrite_id' => 'bp_group_create_group_details',
				'slug'       => 'group-details',
				'name'       => _x( 'Details', 'Group create view', 'bp-rewrites' ),
				'position'   => 0,
			),
			'group-settings'    => array(
				'rewrite_id' => 'bp_group_create_group_settings',
				'slug'       => 'group-settings',
				'name'       => _x( 'Settings', 'Group create view', 'bp-rewrites' ),
				'position'   => 10,
			),
			'group-avatar'      => array(
				'rewrite_id' => 'bp_group_create_group_avatar',
				'slug'       => 'group-avatar',
				'name'       => _x( 'Photo', 'Group create view', 'bp-rewrites' ),
				'position'   => 20,
			),
			'group-cover-image' => array(
				'rewrite_id' => 'bp_group_create_group_cover_image',
				'slug'       => 'group-cover-image',
				'name'       => _x( 'Cover Image', 'Group create view', 'bp-rewrites' ),
				'position'   => 25,
			),
			'group-invites'     => array(
				'rewrite_id' => 'bp_group_create_group_invites',
				'slug'       => 'group-invites',
				'name'       => _x( 'Invites', 'Group create view', 'bp-rewrites' ),
				'position'   => 30,
			),
		),
		'read'   => array(
			'home'               => array(
				'rewrite_id'      => 'bp_group_read_home',
				'slug'            => 'home',
				'name'            => _x( 'Home', 'Group read view', 'bp-rewrites' ),
				'screen_function' => 'groups_screen_group_home',
				'position'        => 10,
				'item_css_id'     => 'home',
			),
			'request-membership' => array(
				'rewrite_id'      => 'bp_group_read_request_membership',
				'slug'            => 'request-membership',
				'name'            => _x( 'Request Membership', 'Group read view', 'bp-rewrites' ),
				'screen_function' => 'groups_screen_group_request_membership',
				'position'        => 30,
			),
			'members'            => array(
				'rewrite_id'      => 'bp_group_read_members',
				'slug'            => 'members',
				/* translators: %s: total member count */
				'name'            => _x( 'Members %s', 'Group read view', 'bp-rewrites' ),
				'screen_function' => 'groups_screen_group_members',
				'position'        => 60,
				'user_has_access' => false,
				'no_access_url'   => '',
				'item_css_id'     => 'members',
			),
			'send-invites'       => array(
				'rewrite_id'      => 'bp_group_read_send_invites',
				'slug'            => 'send-invites',
				'name'            => _x( 'Send Invites', 'Group read view', 'bp-rewrites' ),
				'screen_function' => 'groups_screen_group_invite',
				'position'        => 70,
				'user_has_access' => false,
				'no_access_url'   => '',
				'item_css_id'     => 'invite',
			),
			'admin'              => array(
				'rewrite_id'      => 'bp_group_read_admin',
				'slug'            => 'admin',
				'name'            => _x( 'Manage', 'Group read view', 'bp-rewrites' ),
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
				'slug'              => 'edit-details',
				'name'              => _x( 'Details', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 0,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-settings'      => array(
				'rewrite_id'        => 'bp_group_manage_group_settings',
				'slug'              => 'group-settings',
				'name'              => _x( 'Settings', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 10,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-avatar'        => array(
				'rewrite_id'        => 'bp_group_manage_group_avatar',
				'slug'              => 'group-avatar',
				'name'              => _x( 'Photo', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 20,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'group-cover-image'   => array(
				'rewrite_id'        => 'bp_group_manage_group_cover_image',
				'slug'              => 'group-cover-image',
				'name'              => _x( 'Cover Image', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 25,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'manage-members'      => array(
				'rewrite_id'        => 'bp_group_manage_manage_members',
				'slug'              => 'manage-members',
				'name'              => _x( 'Members', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 30,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'membership-requests' => array(
				'rewrite_id'        => 'bp_group_manage_membership_requests',
				'slug'              => 'membership-requests',
				'name'              => _x( 'Requests', 'Group manage view', 'bp-rewrites' ),
				'screen_function'   => 'groups_screen_group_admin',
				'position'          => 40,
				'user_has_access'   => false,
				'show_in_admin_bar' => true,
			),
			'delete-group'        => array(
				'rewrite_id'        => 'bp_group_manage_delete_group',
				'slug'              => 'delete-group',
				'name'              => _x( 'Delete', 'Group manage view', 'bp-rewrites' ),
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

	$context_views         = array();
	$custom_views          = apply_filters( 'bp_get_group_custom_' . $context . '_views', $context_views );
	$group_extension_views = bp_get_group_extension_views( $context );

	if ( $group_extension_views ) {
		$custom_views = array_merge( $custom_views, $group_extension_views );
	}

	if ( $custom_views && ! wp_is_numeric_array( $custom_views ) ) {
		// The view key (used as default slug) and `rewrite_id` prop need to be unique.
		$valid_custom_views   = array_diff_key( $custom_views, $views[ $context ] );
		$existing_rewrite_ids = array_column( $views[ $context ], 'rewrite_id' );
		$existing_rewrite_ids = array_merge(
			$existing_rewrite_ids,
			// BP Group Reserved rewrite IDs.
			array_keys( bp_get_group_restricted_views() )
		);

		foreach ( $valid_custom_views as $key_view => $view ) {
			if ( ! isset( $view['rewrite_id'] ) || ! in_array( $view['rewrite_id'], $existing_rewrite_ids, true ) ) {
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
