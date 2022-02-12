<?php
/**
 * BuddyPress Groups Template Tags.
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
 * `\bp_get_groups_directory_permalink()` needs to be edited so that it uses BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The Groups Directory permalink.
 * @return string     The Groups Directory permalink built using BP Rewrites.
 */
function bp_get_groups_directory_permalink( $url = '' ) {
	return bp_groups_rewrites_get_url();
}
add_filter( 'bp_get_groups_directory_permalink', __NAMESPACE__ . '\bp_get_groups_directory_permalink', 1, 1 );

/**
 * `\bp_get_group_permalink()` needs to be edited so that it uses BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string          $url   The Group permalink.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group permalink built using BP Rewrites.
 */
function bp_get_group_permalink( $url = '', $group = null ) {
	return bp_group_rewrites_get_url( $group );
}
add_filter( 'bp_get_group_permalink', __NAMESPACE__ . '\bp_get_group_permalink', 1, 2 );

/**
 * Output the group's step creation link.
 *
 * @since 1.0.0
 *
 * @param string $step The group creation step name.
 */
function bp_group_create_link( $step = '' ) {
	echo esc_url( bp_get_group_create_link( $step ) );
}

/**
 * Return the group's step creation link.
 *
 * NB: this function should be used instead of:
 * `trailingslashit( bp_get_groups_directory_permalink() . 'create' )`
 *
 * @since 1.0.0
 *
 * @param string $step The group creation step name.
 * @return string The URL of the group's step creation link.
 */
function bp_get_group_create_link( $step = '' ) {
	$link = bp_group_create_rewrites_get_url( $step );

	/**
	 * Filters the group's step creation link.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link The group's step creation link.
	 * @param string $step The group creation step name.
	 */
	return apply_filters( 'bp_get_group_create_link', $link, $step );
}

/**
 * Code to move inside `bp_get_group_create_button()` once `bp_get_group_create_link()`
 * has been merged into BP Core.
 *
 * @since 1.0.0
 *
 * @param array $button_args {
 *     Optional. An array of arguments.
 *     @see `bp_get_group_create_button()` for the full description of arguments.
 * }
 * @return array An array of arguments.
 */
function bp_get_group_create_button( $button_args = array() ) {
	$button_args['link_href'] = bp_get_group_create_link();
	return $button_args;
}
add_filter( 'bp_get_group_create_button', __NAMESPACE__ . '\bp_get_group_create_button', 1, 1 );

/**
 * Code to move inside `bp_get_group_creation_form_action()` once `bp_get_group_create_link()`
 * has been merged into BP Core.
 *
 * @since 1.0.0
 *
 * @param string $url The URL used by the step's creation form.
 * @return string     The URL used by the step's creation form.
 */
function bp_get_group_creation_form_action( $url = '' ) {
	$create_step  = bp_action_variable( 1 );
	$create_steps = buddypress()->groups->group_creation_steps;

	if ( $create_step && isset( $create_steps[ $create_step ]['rewrite_id'], $create_steps[ $create_step ]['default_slug'] ) ) {
		$create_step = bp_rewrites_get_slug( 'groups', $create_steps[ $create_step ]['rewrite_id'], $create_steps[ $create_step ]['default_slug'] );
	}

	return bp_get_group_create_link( $create_step );
}
add_filter( 'bp_get_group_creation_form_action', __NAMESPACE__ . '\bp_get_group_creation_form_action', 1, 1 );

/**
 * Code to move inside `bp_get_group_creation_previous_link()` to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL used to go to the previous creation step.
 * @return string     The same URL but BP Rewrites ready.
 */
function bp_get_group_creation_previous_link( $url = '' ) {
	$path_parts    = explode( '/', trim( wp_parse_url( $url, PHP_URL_PATH ), '/' ) );
	$previous_step = end( $path_parts );
	$create_steps  = buddypress()->groups->group_creation_steps;

	if ( isset( $create_steps[ $previous_step ]['rewrite_id'], $create_steps[ $previous_step ]['default_slug'] ) ) {
		$previous_step = bp_rewrites_get_slug( 'groups', $create_steps[ $previous_step ]['rewrite_id'], $create_steps[ $previous_step ]['default_slug'] );
	}

	return bp_get_group_create_link( $previous_step );
}
add_filter( 'bp_get_group_creation_previous_link', __NAMESPACE__ . '\bp_get_group_creation_previous_link', 1, 1 );

/**
 * `\bp_get_group_admin_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string          $url   The Group admin form action URL built for the BP Legacy URL parser.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group admin form action URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_form_action( $url = '', $group = null ) {
	$action     = bp_action_variable( 0 );
	$rewrite_id = '';

	if ( $action ) {
		$manage_views = bp_get_group_views( 'manage' );

		if ( isset( $manage_views[ $action ]['rewrite_id'] ) ) {
			$rewrite_id = $manage_views[ $action ]['rewrite_id'];
		}
	}

	return bp_group_admin_rewrites_get_form_url( $group, $action, $rewrite_id );
}
add_filter( 'bp_group_admin_form_action', __NAMESPACE__ . '\bp_group_admin_form_action', 1, 2 );

/**
 * `\bp_group_form_action()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string          $url   The Group form action URL built for the BP Legacy URL parser.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group form action URL built for the BP Rewrites URL parser.
 */
function bp_group_form_action( $url = '', $group = null ) {
	$action = \bp_current_action();

	if ( ! $action ) {
		return $url;
	}

	$views = bp_get_group_views( 'read' );
	if ( isset( $views[ $action ]['rewrite_id'] ) ) {
		$action = bp_rewrites_get_slug( 'groups', $views[ $action ]['rewrite_id'], $action );
	}

	return bp_group_rewrites_get_action_url( $action, $group );
}
add_filter( 'bp_group_form_action', __NAMESPACE__ . '\bp_group_form_action', 1, 2 );

/**
 * `\bp_get_groups_action_link()` needs to be edited to use BP Rewrites.
 *
 * NB: This function seems to be only used by the single Group Admin Bar menu.
 *
 * @since 1.0.0
 *
 * @param string $url        URL for a group component action built for the BP Legacy URL parser.
 * @param string $action     Action being taken for the group.
 * @param array  $query_args Query arguments being passed.
 * @param bool   $nonce      Whether or not to add a nonce.
 * @return string            URL for a group component action built for the BP Rewrites URL parser.
 */
function bp_get_groups_action_link( $url = '', $action = '', $query_args = array(), $nonce = false ) {
	$action_variables = explode( '/', $action );
	$action           = array_shift( $action_variables );

	$views = bp_get_group_views( 'read' );
	if ( isset( $views[ $action ]['rewrite_id'] ) ) {
		$view   = $views[ $action ];
		$action = bp_rewrites_get_slug( 'groups', $view['rewrite_id'], $action );

		if ( isset( $action_variables[0] ) && 'admin' === $view['slug'] ) {
			$first_action_variable = $action_variables[0];
			$manage_views          = bp_get_group_views( 'manage' );

			if ( isset( $manage_views[ $first_action_variable ]['rewrite_id'] ) ) {
				$action_variables[0] = bp_rewrites_get_slug( 'groups', $manage_views[ $first_action_variable ]['rewrite_id'], $first_action_variable );
			}

			$action .= '/' . implode( '/', $action_variables );
		}
	}

	return bp_group_rewrites_get_action_url( $action, null, $query_args, $nonce );
}
add_filter( 'bp_get_groups_action_link', __NAMESPACE__ . '\bp_get_groups_action_link', 1, 4 );

/**
 * `\bp_get_group_accept_invite_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string          $url   The Group action URL built for the BP Legacy URL parser.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The Group action URL built for the BP Rewrites URL parser.
 */
function bp_get_group_accept_invite_link( $url = '', $group = null ) {
	return wp_nonce_url(
		bp_groups_rewrites_get_member_action_url(
			bp_loggedin_user_id(),
			'invites', // Should it be hardcoded?
			array( 'accept', $group->id ) // Should "accept" be hardcoded?
		),
		'groups_accept_invite'
	);
}
add_filter( 'bp_get_group_accept_invite_link', __NAMESPACE__ . '\bp_get_group_accept_invite_link', 1, 2 );

/**
 * `\bp_get_group_accept_invite_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param array           $args {
 *    An array of arguments.
 *    @see `BP_Button` for the full arguments description.
 * }
 * @param BP_Groups_Group $group The Group object.
 * @return string                The `BP_Button` arguments for the Group button to output.
 */
function bp_get_group_join_button( $args = array(), $group = null ) {
	if ( ! is_array( $args ) || ! isset( $group->status ) ) {
		return $args;
	}

	if ( ! $group->is_member ) {
		if ( 'public' === $group->status ) {
			$args['link_href'] = bp_group_rewrites_get_action_url(
				'join', // Should it be hardcoded?
				$group,
				array(),
				'groups_join_group'
			);
		} elseif ( ! $group->is_invited && ! $group->is_pending ) {
			$args['link_href'] = bp_group_rewrites_get_action_url(
				bp_rewrites_get_slug( 'groups', 'bp_group_read_request_membership', 'request-membership' ),
				$group,
				array(),
				'groups_request_membership'
			);
		}
	} else {
		$args['link_href'] = bp_group_rewrites_get_action_url(
			'leave-group', // Should it be hardcoded?
			$group,
			array(),
			'groups_leave_group'
		);
	}

	return $args;
}
add_filter( 'bp_get_group_join_button', __NAMESPACE__ . '\bp_get_group_join_button', 1, 2 );

/**
 * `\bp_get_group_request_accept_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL used by the step's creation form.
 * @return string     The URL used by the step's creation form.
 */
function bp_get_group_request_accept_link( $url = '' ) {
	if ( ! isset( $GLOBALS['requests_template']->request->user_id ) ) {
		return $url;
	}

	// Get potential customized slugs.
	$action  = bp_rewrites_get_slug( 'groups', 'bp_group_read_admin', 'admin' );
	$action .= '/' . bp_rewrites_get_slug( 'groups', 'bp_group_manage_membership_requests', 'membership-requests' );

	return bp_group_rewrites_get_action_url(
		$action,
		\groups_get_current_group(),
		array(
			'user_id' => $GLOBALS['requests_template']->request->user_id,
			'action'  => 'accept',
		),
		'groups_accept_membership_request'
	);
}
add_filter( 'bp_get_group_request_accept_link', __NAMESPACE__ . '\bp_get_group_request_accept_link', 1, 1 );

/**
 * `\bp_get_group_request_reject_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL used by the step's creation form.
 * @return string     The URL used by the step's creation form.
 */
function bp_get_group_request_reject_link( $url = '' ) {
	if ( ! isset( $GLOBALS['requests_template']->request->user_id ) ) {
		return $url;
	}

	// Get potential customized slugs.
	$action  = bp_rewrites_get_slug( 'groups', 'bp_group_read_admin', 'admin' );
	$action .= '/' . bp_rewrites_get_slug( 'groups', 'bp_group_manage_membership_requests', 'membership-requests' );

	return bp_group_rewrites_get_action_url(
		$action,
		\groups_get_current_group(),
		array(
			'user_id' => $GLOBALS['requests_template']->request->user_id,
			'action'  => 'reject',
		),
		'groups_reject_membership_request'
	);
}
add_filter( 'bp_get_group_request_reject_link', __NAMESPACE__ . '\bp_get_group_request_reject_link', 1, 1 );

/**
 * `\bp_get_group_type_directory_permalink()` should use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url  The URL built for the BP Legacy URL parser.
 * @param object $type The Group type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_get_group_type_directory_permalink( $url = '', $type = null ) {
	return bp_group_type_rewrites_get_url( $type );
}
add_filter( 'bp_get_group_type_directory_permalink', __NAMESPACE__ . '\bp_get_group_type_directory_permalink', 1, 2 );
