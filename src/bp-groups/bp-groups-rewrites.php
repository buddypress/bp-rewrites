<?php
/**
 * BuddyPress Groups Rewrites.
 *
 * @package buddypress\bp-groups
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Groups single item's URL.
 *
 * @since ?.0.0
 *
 * @param BP_Groups_Group $group The Group object.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_rewrites_get_url( $group = null ) {
	if ( ! isset( $group->id ) || ! $group->id ) {
		return '';
	}

	return bp_rewrites_get_url(
		array(
			'component_id' => 'groups',
			'single_item'  => bp_get_group_slug( $group ),
		)
	);
}

/**
 * Return the Groups Directory URL.
 *
 * @since ?.0.0
 *
 * @return string The URL built for the BP Rewrites URL parser.
 */
function bp_groups_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id' => 'groups',
		)
	);
}

/**
 * Return the Group Type's URL.
 *
 * @since ?.0.0
 *
 * @param object $type The Group type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_group_type_rewrites_get_url( $type = null ) {
	if ( ! isset( $type->directory_slug ) ) {
		return '';
	}

	return bp_rewrites_get_url(
		array(
			'component_id'   => 'groups',
			'directory_type' => $type->directory_slug,
		)
	);
}

/**
 * Returns an URL for a group component action.
 *
 * @since ?.0.0
 *
 * @param string          $action     The component action.
 * @param BP_Groups_Group $group      The Group object.
 * @param string          $query_args The query arguments to add to the URL.
 * @param bool            $nonce      The nonce to append to the URL.
 * @return string                     The URL built for the BP Rewrites URL parser.
 */
function bp_group_rewrites_get_action_url( $action = '', $group = null, $query_args = array(), $nonce = false ) {
	if ( ! $action ) {
		return '';
	}

	if ( ! $group ) {
		$group = groups_get_current_group();
	}

	if ( ! isset( $group->slug ) ) {
		return '';
	}

	$single_item_action_variables = explode( '/', rtrim( $action, '/' ) );
	$single_item_action           = array_shift( $single_item_action_variables );

	$url = bp_rewrites_get_url(
		array(
			'component_id'                 => 'groups',
			'single_item'                  => $group->slug,
			'single_item_action'           => $single_item_action,
			'single_item_action_variables' => $single_item_action_variables,
		)
	);

	if ( $query_args && is_array( $query_args ) ) {
		$url = add_query_arg( $query_args, $url );
	}

	if ( true === $nonce ) {
		$url = wp_nonce_url( $url );
	} elseif ( is_string( $nonce ) ) {
		$url = wp_nonce_url( $url, $nonce );
	}

	return $url;
}

/**
 * Return the Group's Nav Item URL.
 *
 * @since ?.0.0
 *
 * @param BP_Groups_Group $group The Group object.
 * @param string          $slug  Group Nav Item slug.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_nav_rewrites_get_url( $group = null, $slug = '' ) {
	if ( ! isset( $group->slug ) || ! $slug ) {
		return '';
	}

	return bp_rewrites_get_url(
		array(
			'component_id'       => 'groups',
			'single_item'        => $group->slug,
			'single_item_action' => $slug,
		)
	);
}

/**
 * Return the Group's Admin URL.
 *
 * @since ?.0.0
 *
 * @param BP_Groups_Group $group The Group object.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_rewrites_get_url( $group = null ) {
	return bp_group_nav_rewrites_get_url( $group, 'admin' );
}

/**
 * Return the Group's Admin form URL.
 *
 * @since ?.0.0
 *
 * @param BP_Groups_Group $group The Group object.
 * @param string          $page  The Group Admin page to reach.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_rewrites_get_form_url( $group = null, $page = '' ) {
	if ( ! isset( $group->slug ) ) {
		return '';
	}

	if ( ! $page ) {
		$page = bp_action_variable( 0 );
	}

	return bp_rewrites_get_url(
		array(
			'component_id'                 => 'groups',
			'single_item'                  => $group->slug,
			'single_item_action'           => 'admin',
			'single_item_action_variables' => array( $page ),
		)
	);
}

/**
 * Return the group's step creation link.
 *
 * @since ?.0.0
 *
 * @param string $step The group creation step name.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_group_create_rewrites_get_url( $step = '' ) {
	$url_params = array(
		'component_id'       => 'groups',
		'create_single_item' => 1,
	);

	if ( $step ) {
		$url_params['create_single_item_variables'] = array( 'step', $step );
	}

	return bp_rewrites_get_url( $url_params );
}

/**
 * Returns a Group member action URL using the BP Rewrites URL parser.
 *
 * @since ?.0.0
 *
 * @param int    $user_id          The user ID of concerned by the member action.
 * @param string $action           The slug of the member action.
 * @param array  $action_variables Additional information about the member action.
 * @return string                  The Group member action URL built for the BP Rewrites URL parser.
 */
function bp_groups_rewrites_get_member_action_url( $user_id = 0, $action = '', $action_variables = array() ) {
	$slug       = bp_get_groups_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	// The Groups page of the User single item.
	$params = array(
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
	);

	if ( $action ) {
		// The action of the User single item's Groups page to perform.
		$params['single_item_action'] = $action;

		if ( $action_variables ) {
			// Additional information about the action to perform.
			$params['single_item_action_variables'] = $action_variables;
		}
	}

	return bp_member_rewrites_get_url( $user_id, '', $params );
}
