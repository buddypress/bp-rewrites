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
 * @param string          $url   The URL built for the BP Legacy URL parser. Never used.
 *                               But may be passed when this function is used as a filter.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_rewrites_get_url( $url = '', $group = null ) {
	if ( ! isset( $group->id ) || ! $group->id ) {
		return $url;
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
 * @param string $url The URL built for the BP Legacy URL parser. Never used.
 *                    But may be passed when this function is used as a filter.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_groups_rewrites_get_url( $url = '' ) {
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
 * @param string $url  The URL built for the BP Legacy URL parser. Never used.
 *                     But may be passed when this function is used as a filter.
 * @param object $type The Group type object.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_group_type_rewrites_get_url( $url = '', $type = null ) {
	if ( ! isset( $type->directory_slug ) ) {
		return $url;
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
 * @param string $url        The URL built for the BP Legacy URL parser. Never used.
 *                           But may be passed when this function is used as a filter.
 * @param string $action     The component action.
 * @param string $query_args The query arguments to add to the URL.
 * @param bool   $nonce      The nonce to append to the URL.
 * @return string            The URL built for the BP Rewrites URL parser.
 */
function bp_group_rewrites_get_action_url( $url = '', $action = '', $query_args = array(), $nonce = false ) {
	if ( ! $action ) {
		return $url;
	}

	$group = groups_get_current_group();
	if ( ! isset( $group->slug ) ) {
		return $url;
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
 * Return the Group's Admin URL.
 *
 * @since ?.0.0
 *
 * @param string          $url   The URL built for the BP Legacy URL parser. Never used.
 *                               But may be passed when this function is used as a filter.
 * @param BP_Groups_Group $group The Group object.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_rewrites_get_url( $url = '', $group = null ) {
	if ( ! isset( $group->slug ) ) {
		return $url;
	}

	return bp_rewrites_get_url(
		array(
			'component_id'       => 'groups',
			'single_item'        => $group->slug,
			'single_item_action' => 'admin',
		)
	);
}

/**
 * Return the Group's Admin form URL.
 *
 * @since ?.0.0
 *
 * @param string          $url   The URL built for the BP Legacy URL parser. Never used.
 *                               But may be passed when this function is used as a filter.
 * @param BP_Groups_Group $group The Group object.
 * @param string          $page  The Group Admin page to reach.
 * @return string                The URL built for the BP Rewrites URL parser.
 */
function bp_group_admin_rewrites_get_form_url( $url = '', $group = null, $page = '' ) {
	if ( ! isset( $group->slug ) ) {
		return $url;
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
 * @param string $url  The URL built for the BP Legacy URL parser. Never used.
 *                     But may be passed when this function is used as a filter.
 * @param string $step The group creation step name.
 * @return string      The URL built for the BP Rewrites URL parser.
 */
function bp_group_create_rewrites_get_url( $url = '', $step = '' ) {
	$url_params = array(
		'component_id'       => 'groups',
		'create_single_item' => 1,
	);

	if ( $step ) {
		$url_params['create_single_item_variables'] = array( 'step', $step );
	}

	return bp_rewrites_get_url( $url_params );
}
