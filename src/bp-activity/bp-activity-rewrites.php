<?php
/**
 * BuddyPress Activity Rewrites.
 *
 * @package buddypress\bp-activity
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the Activity Directory URL.
 *
 * @since 1.0.0
 *
 * @return string The Activity Directory URL built for the BP Rewrites URL parser.
 */
function bp_activities_rewrites_get_url() {
	return bp_rewrites_get_url(
		array(
			'component_id' => 'activity',
		)
	);
}

/**
 * Returns the Activity single item URL.
 *
 * @since 1.0.0
 *
 * @param BP_Activity_Activity $activity The Activity Object.
 * @return string                        The Activity single item URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_url( $activity = null ) {
	if ( ! isset( $activity->primary_link ) ) {
		return '';
	}

	$url_params = array(
		'component_id'                 => 'activity',
		'single_item_action'           => 'p',
		'single_item_action_variables' => array( $activity->id ),
	);

	if ( 'activity_comment' === $activity->type ) {
		$url_params['single_item_action_variables'] = array( $activity->item_id );
	}

	$url = bp_rewrites_get_url( $url_params );

	if ( 'activity_comment' === $activity->type ) {
		$url .= '#acomment-' . $activity->id;
	}

	return $url;
}

/**
 * Returns the Activity Post Form URL.
 *
 * @since 1.0.0
 *
 * @return string The Activity Post Form URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_post_form_action() {
	return bp_rewrites_get_url(
		array(
			'component_id'       => 'activity',
			'single_item_action' => 'post',
		)
	);
}

/**
 * Returns the Activity Comment Form URL.
 *
 * @since 1.0.0
 *
 * @return string The Activity Comment Form URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_comment_form_action() {
	return bp_rewrites_get_url(
		array(
			'component_id'       => 'activity',
			'single_item_action' => 'reply',
		)
	);
}

/**
 * Returns the Activity Comment URL.
 *
 * @since 1.0.0
 *
 * @param array  $query_vars  Additional query vars to add to the Activity comment URL.
 * @param string $anchor      Anchor to add to the Activity comment URL.
 * @return string             The Activity Comment URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_comment_url( $query_vars = array(), $anchor = '' ) {
	if ( bp_is_activity_directory() ) {
		$url = bp_activities_rewrites_get_url();

	} elseif ( isset( $GLOBALS['activities_template']->activity->id ) ) {
		$url = bp_rewrites_get_url(
			array(
				'component_id'                 => 'activity',
				'single_item_action'           => 'p',
				'single_item_action_variables' => array( $GLOBALS['activities_template']->activity->id ),
			)
		);
	}

	if ( $query_vars ) {
		$url = add_query_arg( $query_vars, $url );
	}

	if ( $anchor ) {
		$url .= '#' . $anchor;
	}

	return $url;
}

/**
 * Returns the Activity Favorite Action URL.
 *
 * @since 1.0.0
 *
 * @param int $activity_id The activity ID. Optional.
 * @return string          The Activity Favorite Action URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_favorite_url( $activity_id = 0 ) {
	if ( ! $activity_id && isset( $GLOBALS['activities_template']->activity->id ) ) {
		$activity_id = $GLOBALS['activities_template']->activity->id;
	}

	$url = bp_rewrites_get_url(
		array(
			'component_id'                 => 'activity',
			'single_item_action'           => 'favorite',
			'single_item_action_variables' => array( $activity_id ),
		)
	);

	return wp_nonce_url( $url, 'mark_favorite' );
}

/**
 * Returns the Activity Unfavorite Action URL.
 *
 * @since 1.0.0
 *
 * @param int $activity_id The activity ID. Optional.
 * @return string          The Activity Unfavorite Action URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_unfavorite_url( $activity_id = 0 ) {
	if ( ! $activity_id && isset( $GLOBALS['activities_template']->activity->id ) ) {
		$activity_id = $GLOBALS['activities_template']->activity->id;
	}

	$url = bp_rewrites_get_url(
		array(
			'component_id'                 => 'activity',
			'single_item_action'           => 'unfavorite',
			'single_item_action_variables' => array( $activity_id ),
		)
	);

	return wp_nonce_url( $url, 'unmark_favorite' );
}

/**
 * Returns the Activity Delete Action URL.
 *
 * @since 1.0.0
 *
 * @param int   $activity_id The activity ID. Optional.
 * @param array $query_vars  The additional query vars to add to the delete URL.
 * @return string            The Activity Delete Action URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_delete_url( $activity_id = 0, $query_vars = array() ) {
	if ( ! $activity_id && isset( $GLOBALS['activities_template']->activity->id ) ) {
		$activity_id = $GLOBALS['activities_template']->activity->id;
	}

	$url = bp_rewrites_get_url(
		array(
			'component_id'                 => 'activity',
			'single_item_action'           => 'delete',
			'single_item_action_variables' => array( $activity_id ),
		)
	);

	if ( $query_vars ) {
		$url = add_query_arg( $query_vars, $url );
	}

	return $url;
}

/**
 * Returns the Activity Sitewide Feed URL.
 *
 * @since 1.0.0
 *
 * @return string The Activity Sitewide Feed URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_sitewide_feed_url() {
	return bp_rewrites_get_url(
		array(
			'component_id'       => 'activity',
			'single_item_action' => 'feed',
		)
	);
}

/**
 * Returns the User's Activity Feed URL.
 *
 * @since 1.0.0
 *
 * @param int $user_id The activity ID. Optional. Defaults to the displayed user ID.
 * @return string      The User's Activity Feed URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_member_rss_url( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = \bp_displayed_user_id();
	}

	$slug       = bp_get_activity_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	$url_params = array(
		'component_id'          => 'members',
		'single_item'           => bp_rewrites_get_member_slug( $user_id ),
		'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
	);

	if ( bp_is_user_activity() ) {
		$url_params['single_item_action_variables'] = 'feed';

		if ( bp_is_user_friends_activity() ) {
			$url_params['single_item_action'] = bp_get_friends_slug();

		} elseif ( bp_is_user_groups_activity() ) {
			$url_params['single_item_action'] = bp_get_groups_slug();

		} elseif ( 'favorites' === \bp_current_action() ) {
			$url_params['single_item_action'] = 'favorites';

		} elseif ( 'mentions' === \bp_current_action() && bp_activity_do_mentions() ) {
			$url_params['single_item_action'] = 'mentions';

		} else {
			$url_params['single_item_action'] = 'feed';
			unset( $url_params['single_item_action_variables'] );
		}
	}

	return bp_rewrites_get_url( $url_params );
}

/**
 * Returns the Activity redirect URL.
 *
 * @since 1.0.0
 *
 * @param BP_Activity_Activity $activity The Activity Object.
 * @return string                        The Activity redirect URL built for the BP Rewrites URL parser.
 */
function bp_activity_rewrites_get_redirect_url( $activity = null ) {
	if ( ! isset( $activity->user_id ) ) {
		return '';
	}

	// This shouldn't happen so often!
	if ( bp_is_active( 'groups' ) && 'groups' === $activity->component && ! $activity->user_id ) {
		$group = groups_get_group( $activity->item_id );

		$url_params = array(
			'component_id'                 => 'groups',
			'single_item'                  => bp_get_group_slug( $group ),
			'single_item_action'           => bp_get_activity_slug(),
			'single_item_action_variables' => array( $activity->id ),
		);
	} else {
		$url_params = array(
			'component_id'          => 'members',
			'single_item'           => bp_rewrites_get_member_slug( $activity->user_id ),
			'single_item_component' => bp_rewrites_get_slug( 'members', 'bp_member_activity', bp_get_activity_slug() ),
			'single_item_action'    => $activity->id,
		);
	}

	$redirect = bp_rewrites_get_url( $url_params );

	// If set, add the original query string back onto the redirect URL.
	if ( isset( $_SERVER['QUERY_STRING'] ) ) {
		$query_vars = array();
		wp_parse_str( wp_unslash( $_SERVER['QUERY_STRING'] ), $query_vars ); // phpcs:ignore
		$exclude_vars = array_intersect_key( $query_vars, array_flip( buddypress()->activity->rewrite_ids ) );
		$query_vars   = array_diff_key( $query_vars, $exclude_vars );

		if ( $query_vars ) {
			$redirect = add_query_arg( urlencode_deep( $query_vars ), $redirect );
		}
	}

	return $redirect;
}
