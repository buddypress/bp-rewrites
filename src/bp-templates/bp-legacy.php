<?php
/**
 * Required BP Legacy edits.
 *
 * @package buddypress\bp-templates\bp-nouveau
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loop through each Primary nav item to filter its link.
 *
 * @since 1.0.0
 */
function bp_legacy_displayed_user_nav() {
	$user_nav_items = buddypress()->members->nav->get_primary();
	if ( $user_nav_items ) {
		foreach ( $user_nav_items as $user_nav_item ) {
			if ( ! isset( $user_nav_item->css_id ) || ! $user_nav_item->css_id ) {
				continue;
			}

			add_filter( 'bp_get_displayed_user_nav_' . $user_nav_item->css_id, __NAMESPACE__ . '\bp_get_displayed_user_nav', 1, 2 );
		}
	}
}
add_action( 'bp_setup_nav', __NAMESPACE__ . '\bp_legacy_displayed_user_nav', 1000 );

/**
 * The `buddypress/members/index.php` needs to use BP Rewrites to build the Members Directory My Friends nav.
 *
 * From this plugin, we need to disable it first to reset it a bit later.
 *
 * @see `bp_legacy_reset_members_directory_my_friends_nav()`
 *
 * @since 1.0.0
 */
function bp_legacy_disable_members_directory_my_friends_nav() {
	add_filter( 'bp_get_total_friend_count', '__return_zero' );
}
add_action( 'bp_before_directory_members_tabs', __NAMESPACE__ . '\bp_legacy_disable_members_directory_my_friends_nav', 1 );

/**
 * Resets the the Members Directory My Friends nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_members_directory_my_friends_nav() {
	remove_filter( 'bp_get_total_friend_count', '__return_zero' );

	if ( ! is_user_logged_in() || ! bp_is_active( 'friends' ) ) {
		return;
	}

	$user_id = bp_loggedin_user_id();
	$count   = bp_get_total_friend_count( $user_id );

	if ( $count ) {
		$parent_slug    = bp_get_friends_slug();
		$rewrite_id     = sprintf( 'bp_member_%s', $parent_slug );
		$slug           = 'my-friends'; // This shouldn't be hardcoded.
		$my_friends_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="members-personal">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $my_friends_url ),
			sprintf(
				/* translators: %s is the amount of friends for the current user. */
				esc_html__( 'My Friends %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}
}
add_action( 'bp_members_directory_member_types', __NAMESPACE__ . '\bp_legacy_reset_members_directory_my_friends_nav', 1 );

/**
 * The `buddypress/groups/index.php` needs to use BP Rewrites to build the Members Directory My Groups nav.
 *
 * From this plugin, we need to disable it first to reset it a bit later.
 *
 * @see `bp_legacy_reset_groups_directory_my_groups_nav()`
 *
 * @since 1.0.0
 */
function bp_legacy_disable_groups_directory_my_groups_nav() {
	add_filter( 'bp_get_total_group_count_for_user', '__return_zero' );
}
add_action( 'bp_before_directory_groups_content', __NAMESPACE__ . '\bp_legacy_disable_groups_directory_my_groups_nav', 1 );

/**
 * Resets the the Groups Directory My Groups nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_groups_directory_my_groups_nav() {
	remove_filter( 'bp_get_total_group_count_for_user', '__return_zero' );

	if ( ! is_user_logged_in() ) {
		return;
	}

	$user_id = bp_loggedin_user_id();
	$count   = bp_get_total_group_count_for_user( $user_id );

	if ( $count ) {
		$parent_slug   = bp_get_groups_slug();
		$rewrite_id    = sprintf( 'bp_member_%s', $parent_slug );
		$slug          = 'my-groups'; // This shouldn't be hardcoded.
		$my_groups_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="groups-personal">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $my_groups_url ),
			sprintf(
				/* translators: %s: current user groups count. */
				esc_html__( 'My Groups %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}
}
add_action( 'bp_groups_directory_group_filter', __NAMESPACE__ . '\bp_legacy_reset_groups_directory_my_groups_nav', 1 );

/**
 * Resets the the Activity Directory My Friends nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_activity_directory_my_friends_nav() {
	if ( ! is_user_logged_in() || ! bp_is_active( 'friends' ) ) {
		return;
	}

	$user_id = bp_loggedin_user_id();
	$count   = bp_get_total_friend_count( $user_id );

	if ( $count ) {
		$parent_slug  = bp_get_activity_slug();
		$rewrite_id   = sprintf( 'bp_member_%s', $parent_slug );
		$slug         = bp_get_friends_slug();
		$activity_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="activity-friends">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $activity_url ),
			sprintf(
				/* translators: %s is the amount of friends for the current user. */
				esc_html__( 'My Friends %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}

	// Disable My Friends nav item.
	add_filter( 'bp_get_total_friend_count', '__return_zero' );
}
add_action( 'bp_before_activity_type_tab_friends', __NAMESPACE__ . '\bp_legacy_reset_activity_directory_my_friends_nav', 1 );

/**
 * Resets the the Activity Directory My Groups nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_activity_directory_my_groups_nav() {
	if ( ! is_user_logged_in() || ! bp_is_active( 'groups' ) ) {
		return;
	}

	$user_id = bp_loggedin_user_id();
	$count   = bp_get_total_group_count_for_user( $user_id );

	if ( $count ) {
		$parent_slug  = bp_get_activity_slug();
		$rewrite_id   = sprintf( 'bp_member_%s', $parent_slug );
		$slug         = bp_get_groups_slug();
		$activity_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="activity-groups">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $activity_url ),
			sprintf(
				/* translators: %s: current user groups count. */
				esc_html__( 'My Groups %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}

	// Disable My Groups nav item.
	add_filter( 'bp_get_total_group_count_for_user', '__return_zero' );
}
add_action( 'bp_before_activity_type_tab_groups', __NAMESPACE__ . '\bp_legacy_reset_activity_directory_my_groups_nav', 1 );

/**
 * Resets the the Activity Directory My Favorites/Mentions nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_activity_directory_favorites_mentions_nav() {
	if ( ! is_user_logged_in() || ! bp_is_active( 'groups' ) ) {
		return;
	}

	$user_id     = bp_loggedin_user_id();
	$parent_slug = bp_get_activity_slug();
	$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
	$count       = bp_get_total_favorite_count_for_user( $user_id );

	if ( $count ) {
		$slug         = 'favorites'; // This shouldn't be hardcoded.
		$activity_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="activity-favorites">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $activity_url ),
			sprintf(
				/* translators: %s: current user groups count. */
				esc_html__( 'My Favorites %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}

	if ( bp_activity_do_mentions() ) {
		$slug         = 'mentions'; // This shouldn't be hardcoded.
		$activity_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		$mentions_count        = bp_get_total_mention_count_for_user( $user_id );
		$mentions_count_output = '';

		if ( $mentions_count ) {
			$mentions_count_output = sprintf(
				'&nbsp;<strong>
					<span>%s</span>
				</strong>',
				sprintf(
					/* translators: %s: new mentions count */
					_nx( '%s new', '%s new', $mentions_count, 'Number of new activity mentions', 'bp-rewrites' ),
					$mentions_count
				)
			);
		}

		printf(
			'<li id="activity-mentions">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $activity_url ),
			sprintf(
				'%1$s%2$s',
				esc_html__( 'Mentions', 'bp-rewrites' ),
				$mentions_count_output // phpcs:ignore
			)
		);
	}

	// Disable Favorites/Mentions nav items.
	add_filter( 'bp_get_total_favorite_count_for_user', '__return_false' );
	add_filter( 'bp_activity_do_mentions', '__return_false' );
}
add_action( 'bp_before_activity_type_tab_favorites', __NAMESPACE__ . '\bp_legacy_reset_activity_directory_favorites_mentions_nav', 1 );

/**
 * Restore Activity Directory Nav Item counts/features.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_activity_directory_nav_counts() {
	remove_filter( 'bp_get_total_friend_count', '__return_zero' );
	remove_filter( 'bp_get_total_group_count_for_user', '__return_zero' );
	remove_filter( 'bp_get_total_favorite_count_for_user', '__return_false' );
	remove_filter( 'bp_activity_do_mentions', '__return_false' );
}
add_action( 'bp_activity_type_tabs', __NAMESPACE__ . '\bp_legacy_reset_activity_directory_nav_counts', 1 );

/**
 * The `buddypress/blogs/index.php` needs to use BP Rewrites to build the Blogs Directory My Sites nav.
 *
 * From this plugin, we need to disable it first to reset it a bit later.
 *
 * @see `bp_legacy_reset_blogs_directory_my_blogs_nav()`
 *
 * @since 1.0.0
 */
function bp_legacy_disable_blogs_directory_my_blogs_nav() {
	add_filter( 'bp_get_total_blog_count_for_user', '__return_zero' );
}
add_action( 'bp_before_directory_blogs_tabs', __NAMESPACE__ . '\bp_legacy_disable_blogs_directory_my_blogs_nav', 1 );

/**
 * Resets the the Blogss Directory My Sites nav using BP Rewrites to build the nav link.
 *
 * @since 1.0.0
 */
function bp_legacy_reset_blogs_directory_my_blogs_nav() {
	remove_filter( 'bp_get_total_blog_count_for_user', '__return_zero' );

	if ( ! is_user_logged_in() || ! bp_is_active( 'blogs' ) ) {
		return;
	}

	$user_id = bp_loggedin_user_id();
	$count   = bp_get_total_blog_count_for_user( $user_id );

	if ( $count ) {
		$parent_slug  = bp_get_blogs_slug();
		$rewrite_id   = sprintf( 'bp_member_%s', $parent_slug );
		$slug         = 'my-sites'; // This shouldn't be hardcoded.
		$my_blogs_url = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);

		printf(
			'<li id="blogs-personal">
				<a href="%1$s">%2$s</a>
			</li>',
			esc_url( $my_blogs_url ),
			sprintf(
				/* translators: %s: current user blogs count */
				esc_html__( 'My Sites %s', 'bp-rewrites' ),
				'<span>' . $count . '</span>' // phpcs:ignore
			)
		);
	}
}
add_action( 'bp_blogs_directory_blog_types', __NAMESPACE__ . '\bp_legacy_reset_blogs_directory_my_blogs_nav', 1 );
