<?php
/**
 * Required BP Nouveau edits.
 *
 * @package buddypress\bp-templates\bp-nouveau
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_nouveau_get_nav_link()` needs to be edited to stop modifying the nav item links.
 *
 * @since ?.0.0
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
 * @since ?.0.0
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
 * `\bp_nouveau_get_groups_directory_nav_items()` needs to use BP Rewrites to built the "my friends" URL.
 *
 * @since ?.0.0
 *
 * @param array $nav_items An associative array containing the Directory nav items.
 * @return array           An associative array containing the Directory nav items.
 */
function bp_nouveau_get_groups_directory_nav_items( $nav_items = array() ) {
	if ( isset( $nav_items['personal'] ) ) {
		$parent_slug = bp_get_groups_slug();
		$rewrite_id  = sprintf( 'bp_member_%s', $parent_slug );
		$slug        = 'my-groups'; // This shouldn't be hardcoded.

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

	if ( isset( $nav_items['create'] ) ) {
		// Override the personal link of the Members Directory Nav.
		$nav_items['create']['link'] = bp_get_group_create_link();
	}

	return $nav_items;
}
add_action( 'bp_nouveau_get_groups_directory_nav_items', __NAMESPACE__ . '\bp_nouveau_get_groups_directory_nav_items', 1, 1 );

/**
 * `\bp_nouveau_get_activity_directory_nav_items()` needs to use BP Rewrites to built the nav item URLs.
 *
 * @since ?.0.0
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
