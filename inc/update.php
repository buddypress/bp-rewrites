<?php
/**
 * BP Rewrites Updater.
 *
 * @package bp-rewrites\inc
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Switch directory pages between WP pages and BuddyPress post type.
 *
 * @since 1.0.0
 */
function updater() {
	if ( ! BP_Rewrites::is_buddypress_supported() ) {
		return false;
	}

	$directory_pages   = bp_core_get_directory_pages();
	$nav_menu_item_ids = array();
	$post_type         = 'buddypress';
	$item_object       = 'page';

	if ( 'deactivate_bp-rewrites/class-bp-rewrites.php' === current_action() ) {
		$post_type   = 'page';
		$item_object = 'buddypress';
	}

	// Do not check post slugs nor post types.
	remove_filter( 'wp_unique_post_slug', __NAMESPACE__ . '\bp_unique_page_slug', 10 );
	remove_action( 'update_option_bp-pages', __NAMESPACE__ . '\bp_core_add_page_mappings', 10 );

	// Update Directory pages post types.
	foreach ( $directory_pages as $directory_page ) {
		$nav_menu_item_ids[] = $directory_page->id;

		// Switch the post type.
		wp_update_post(
			array(
				'ID'          => $directory_page->id,
				'post_type'   => $post_type,
				'post_status' => 'publish',
			)
		);
	}

	// Update nav menu items!
	$nav_menus = wp_get_nav_menus( array( 'hide_empty' => true ) );
	foreach ( $nav_menus as $nav_menu ) {
		$items = wp_get_nav_menu_items( $nav_menu->term_id );

		foreach ( $items as $item ) {
			if ( $item_object !== $item->object || ! in_array( $item->object_id, $nav_menu_item_ids, true ) ) {
				continue;
			}

			wp_update_nav_menu_item(
				$nav_menu->term_id,
				$item->ID,
				array(
					'menu-item-db-id'       => $item->db_id,
					'menu-item-object-id'   => $item->object_id,
					'menu-item-object'      => $post_type,
					'menu-item-parent-id'   => $item->menu_item_parent,
					'menu-item-position'    => $item->menu_order,
					'menu-item-type'        => 'post_type',
					'menu-item-title'       => $item->title,
					'menu-item-url'         => $item->url,
					'menu-item-description' => $item->description,
					'menu-item-attr-title'  => $item->attr_title,
					'menu-item-target'      => $item->target,
					'menu-item-classes'     => implode( ' ', (array) $item->classes ),
					'menu-item-xfn'         => $item->xfn,
					'menu-item-status'      => 'publish',
				)
			);
		}
	}

	// Finally make sure to rebuilt permalinks at next page load.
	delete_option( 'rewrite_rules' );
}
