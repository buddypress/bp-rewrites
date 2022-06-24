<?php
/**
 * Required bbPress edits.
 *
 * @package buddypress\bp-plugins\bbpress
 * @since 1.3.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Let's override the way bbPress does to what it should.
remove_action( 'bp_include', 'bbp_setup_buddypress', 10 );

/**
 * Override the main bbPress's BP Component class.
 *
 * @since 1.3.0
 */
function bbp_setup_buddypress() {
	$bp = null;

	if ( function_exists( 'buddypress' ) ) {
		$bp = buddypress();
	}

	// Bail if in maintenance mode.
	if ( ! $bp || $bp->maintenance_mode ) {
		return;
	}

	// Include the BuddyPress Component.
	require_once bbpress()->includes_dir . 'extend/buddypress/loader.php';
	require_once plugin_dir_path( __FILE__ ) . 'bbpress/class-forums-component.php';

	// Instantiate BuddyPress for bbPress and for BP Rewrites!
	bbpress()->extend->buddypress = new Forums_Component();
}
add_action( 'bp_include', __NAMESPACE__ . '\bbp_setup_buddypress', 10 );

/**
 * Remove a problematic hook happening too early.
 *
 * @since 1.3.0
 */
function bbp_remove_hooks() {
	remove_filter( 'bbp_get_user_id', 'bbp_filter_user_id', 10, 3 );
}
add_action( 'bbp_buddypress_loaded', __NAMESPACE__ . '\bbp_remove_hooks', 1, 1 );

/**
 * Restore the problematic hook so that it happens at a better time.
 *
 * @since 1.3.0
 */
function bbp_restore_hooks() {
	add_filter( 'bbp_get_user_id', 'bbp_filter_user_id', 10, 3 );
}
add_action( 'bp_parse_query', __NAMESPACE__ . '\bbp_restore_hooks', 1, 1 );

/**
 * Should the forum tab be displayed for the current group?
 *
 * @since 1.3.0
 *
 * @return bool True to display the "Forum" tab. False otherwise.
 */
function bbp_show_group_tab() {
	$retval = false;

	if ( did_action( 'bp_parse_query' ) ) {
		$group_id = (int) bp_get_current_group_id();

		if ( ! $group_id && bp_get_new_group_id() ) {
			$group_id = (int) bp_get_new_group_id();
		}

		$retval = bp_get_new_group_enable_forum() || groups_get_groupmeta( $group_id, 'forum_id' );
	}

	return (bool) $retval;
}

/**
 * Returns the Group Forum slug.
 *
 * @since 1.3.0
 *
 * @return string The Group Forum slug.
 */
function bbp_get_group_forum_slug() {
	$views = bp_get_group_views( 'read' );
	$slug  = 'forum';

	if ( isset( $views[ $slug ]['rewrite_id'] ) ) {
		$view = $views[ $slug ];
		$slug = bp_rewrites_get_slug( 'groups', $view['rewrite_id'], $slug );
	}

	return $slug;
}

/**
 * Returns the Group Admin Forum slug.
 *
 * @since 1.3.0
 *
 * @return string The Group Admin Forum slug.
 */
function bbp_get_group_admin_forum_slug() {
	$views = bp_get_group_views( 'manage' );
	$slug  = 'forum';

	if ( isset( $views[ $slug ]['rewrite_id'] ) ) {
		$view = $views[ $slug ];
		$slug = bp_rewrites_get_slug( 'groups', $view['rewrite_id'], $slug );
	}

	return $slug;
}

/**
 * Make sure Activity format callback is available for groups.
 *
 * @since 1.3.0
 */
function bbp_register_activity_actions() {
	$bp = buddypress();

	// Group Forum topic.
	bp_activity_set_action(
		$bp->groups->id,
		'bbp_topic_create',
		esc_html__( 'New Group forum topic', 'bp-rewrites' ),
		'bbp_format_activity_action_new_topic',
		esc_html__( 'Topics', 'bp-rewrites' ),
		array( 'group' )
	);

	// Group Forum reply.
	bp_activity_set_action(
		$bp->groups->id,
		'bbp_reply_create',
		esc_html__( 'New Group forum reply', 'bp-rewrites' ),
		'bbp_format_activity_action_new_reply',
		esc_html__( 'Replies', 'bp-rewrites' ),
		array( 'group' )
	);
}
add_action( 'bp_register_activity_actions', __NAMESPACE__ . '\bbp_register_activity_actions', 100 );

/**
 * Filters the action type being set for the Sitewide forum topics and replies.
 *
 * @since 1.3.0
 *
 * @param array  $args         Array of arguments for action type being set.
 * @param string $component_id The name of the component.
 * @return array Array of arguments for action type being set.
 */
function bbp_activity_set_action( $args = array(), $component_id = '' ) {
	if ( 'bbpress' === $component_id ) {
		$args['context'] = array_diff( $args['context'], array( 'group' ) );
	}

	return $args;
}
add_filter( 'bp_activity_set_action', __NAMESPACE__ . '\bbp_activity_set_action', 10, 2 );
