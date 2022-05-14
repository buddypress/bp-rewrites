<?php
/**
 * Required BP Nouveau edits.
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
 * `\bp_nouveau_get_nav_link()` needs to be edited to stop modifying the nav item links.
 *
 * @since 1.0.0
 *
 * @param string $link     The URL for the nav item.
 * @param object $nav_item The nav item object.
 * @return string The URL for the nav item.
 */
function bp_nouveau_get_nav_link( $link = '', $nav_item = null ) {
	if ( isset( $nav_item->link ) && $nav_item->link ) {
		$link = $nav_item->link;
	}

	return $link;
}
add_filter( 'bp_nouveau_get_nav_link', __NAMESPACE__ . '\bp_nouveau_get_nav_link', 1, 2 );

/**
 * `\bp_nouveau_get_members_directory_nav_items()` needs to use BP Rewrites to built the "my friends" URL.
 *
 * @since 1.0.0
 *
 * @param array $nav_items An associative array containing the Directory nav items.
 * @return array           An associative array containing the Directory nav items.
 */
function bp_nouveau_get_members_directory_nav_items( $nav_items = array() ) {
	if ( isset( $nav_items['personal'] ) ) {
		$parent_slug = bp_get_friends_slug();
		$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
		$slug        = 'my-friends'; // This shouldn't be hardcoded.

		// Override the personal link of the Members Directory Nav.
		$nav_items['personal']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => bp_loggedin_user_id(),
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	return $nav_items;
}
add_action( 'bp_nouveau_get_members_directory_nav_items', __NAMESPACE__ . '\bp_nouveau_get_members_directory_nav_items', 1, 1 );

/**
 * `\bp_nouveau_get_groups_directory_nav_items()` needs to use BP Rewrites to built the "my groups" URL.
 *
 * @since 1.0.0
 *
 * @param array $nav_items An associative array containing the Directory nav items.
 * @return array           An associative array containing the Directory nav items.
 */
function bp_nouveau_get_groups_directory_nav_items( $nav_items = array() ) {
	if ( isset( $nav_items['personal'] ) ) {
		$parent_slug = bp_get_groups_slug();
		$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
		$slug        = 'my-groups'; // This shouldn't be hardcoded.

		// Override the personal link of the Groups Directory Nav.
		$nav_items['personal']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => bp_loggedin_user_id(),
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	if ( isset( $nav_items['create'] ) ) {
		// Override the personal link of the Groups Directory Nav.
		$nav_items['create']['link'] = bp_get_group_create_link();
	}

	return $nav_items;
}
add_action( 'bp_nouveau_get_groups_directory_nav_items', __NAMESPACE__ . '\bp_nouveau_get_groups_directory_nav_items', 1, 1 );

/**
 * `\bp_nouveau_get_activity_directory_nav_items()` needs to use BP Rewrites to built the nav item URLs.
 *
 * @since 1.0.0
 *
 * @param array $nav_items An associative array containing the Directory nav items.
 * @return array           An associative array containing the Directory nav items.
 */
function bp_nouveau_get_activity_directory_nav_items( $nav_items = array() ) {
	$parent_slug = bp_get_activity_slug();
	$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
	$user_id     = bp_loggedin_user_id();

	if ( isset( $nav_items['favorites'] ) ) {
		$slug = 'favorites'; // This shouldn't be hardcoded.

		// Override the activity favorites link of the Activity Directory Nav.
		$nav_items['favorites']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	if ( isset( $nav_items['friends'] ) ) {
		$slug = bp_get_friends_slug();

		// Override the activity friends link of the Activity Directory Nav.
		$nav_items['friends']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	if ( isset( $nav_items['groups'] ) ) {
		$slug = bp_get_groups_slug();

		// Override the activity groups link of the Activity Directory Nav.
		$nav_items['groups']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	if ( isset( $nav_items['mentions'] ) ) {
		$slug = 'mentions'; // This shouldn't be hardcoded.

		// Override the activity groups link of the Activity Directory Nav.
		$nav_items['mentions']['link'] = bp_members_rewrites_get_nav_url(
			array(
				'user_id'        => $user_id,
				'rewrite_id'     => $rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $slug,
			)
		);
	}

	return $nav_items;
}
add_action( 'bp_nouveau_get_activity_directory_nav_items', __NAMESPACE__ . '\bp_nouveau_get_activity_directory_nav_items', 1, 1 );

/**
 * `bp_nouveau_get_nav_scope()` needs to be edited to stop using the nav slug.
 *
 * @since 1.0.0
 *
 * @param array $attributes The field attributes.
 * @return array The field attributes.
 */
function bp_nouveau_reset_nav_scope( $attributes = array() ) {
	if ( isset( $attributes['data-bp-user-scope'] ) ) {
		$scoped_rewrite_id = '';
		$current_component = \bp_current_component();

		if ( $current_component ) {
			$rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'members', $attributes['data-bp-user-scope'], $current_component );

			if ( $rewrite_id ) {
				$attributes['data-bp-user-scope'] = str_replace(
					array( 'bp_member_' . $current_component . '_', '_' ),
					array( '', '-' ),
					$rewrite_id
				);
			}
		}
	}

	return $attributes;
}
add_filter( 'bp_get_form_field_attributes', __NAMESPACE__ . '\bp_nouveau_reset_nav_scope', 1, 1 );

/**
 * At the `bp_init` time, the BuddyPress Component global variables are not fully set.
 *
 * @since 1.0.0
 */
function bp_nouveau_reset_hooks() {
	if ( bp_is_active( 'notifications' ) ) {
		remove_action( 'bp_init', 'bp_nouveau_notifications_init_filters', 20 );
		add_action( 'bp_parse_query', 'bp_nouveau_notifications_init_filters', 20 );
	}

	if ( bp_is_active( 'messages' ) ) {
		remove_action( 'bp_init', 'bp_nouveau_push_sitewide_notices', 99 );
		add_action( 'bp_parse_query', 'bp_nouveau_push_sitewide_notices', 99 );
	}
}
add_action( 'bp_init', __NAMESPACE__ . '\bp_nouveau_reset_hooks', 1 );

/**
 * `\bp_nouveau_activity_get_rss_link()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @param string $url The URL built for the BP Legacy URL parser.
 * @return string     The URL built for the BP Rewrites URL parser.
 */
function bp_nouveau_activity_get_rss_link( $url = '' ) {
	return bp_activity_rewrites_get_member_rss_url();
}
add_filter( 'bp_nouveau_activity_get_rss_link', __NAMESPACE__ . '\bp_nouveau_activity_get_rss_link', 1, 1 );

/**
 * `\bp_nouveau_messages_adjust_admin_nav()` needs to be edited to use BP Rewrites.
 *
 * @since 1.2.0
 *
 * @param array $admin_nav The list of WP Admin Bar Messages items.
 * @return array The list of WP Admin Bar Messages items.
 */
function bp_nouveau_messages_adjust_admin_nav( $admin_nav = array() ) {
	if ( ! is_array( $admin_nav ) ) {
		return $admin_nav;
	}

	$parent_slug = bp_get_messages_slug();
	$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );

	// Get the generated front-end link.
	$user_messages_link = bp_members_rewrites_get_nav_url(
		array(
			'user_id'        => bp_loggedin_user_id(),
			'rewrite_id'     => $rewrite_id,
			'item_component' => $parent_slug,
			'item_action'    => bp_rewrites_get_slug( 'members', $rewrite_id . '_notices', 'notices' ),
		)
	);

	if ( $user_messages_link ) {
		foreach ( $admin_nav as $nav_iterator => $nav ) {
			if ( $user_messages_link !== $nav['href'] ) {
				continue;
			}

			$admin_nav[ $nav_iterator ]['href'] = esc_url(
				add_query_arg(
					array( 'page' => 'bp-notices' ),
					bp_get_admin_url( 'users.php' )
				)
			);
		}
	}

	return $admin_nav;
}

/**
 * Adjust some Nouveau actions and filters.
 *
 * @since 1.2.0
 */
function component_actions_and_filters() {
	if ( bp_is_active( 'messages' ) ) {
		remove_filter( 'bp_messages_admin_nav', 'bp_nouveau_messages_adjust_admin_nav', 10, 1 );
		add_filter( 'bp_messages_admin_nav', __NAMESPACE__ . '\bp_nouveau_messages_adjust_admin_nav', 20, 1 );
	}
}
add_action( 'bp_nouveau_includes', __NAMESPACE__ . '\component_actions_and_filters', 11 );
