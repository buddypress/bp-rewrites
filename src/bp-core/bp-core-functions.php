<?php
/**
 * BuddyPress Common functions.
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the BuddyPress Post Type site ID.
 *
 * @since 1.0.0
 *
 * @return int The site ID the BuddyPress Post Type should be registered on.
 */
function bp_get_post_type_site_id() {
	$site_id = bp_get_root_blog_id();

	/*
	 * @todo This will need to be improved to take in account
	 * specific configurations like multiblog.
	 */
	return (int) apply_filters( 'bp_get_post_type_site_id', $site_id );
}

/**
 * Make sure the BuddyPress Component `post_name` are unique.
 *
 * Goal is to avoid a slug conflict between a Page and a BuddyPress Component `post_name`.
 *
 * @since 1.0.0
 *
 * @param string $slug          The post slug.
 * @param int    $post_ID       Post ID.
 * @param string $post_status   The post status.
 * @param string $post_type     Post type.
 * @param int    $post_parent   Post parent ID.
 * @param string $original_slug The original post slug.
 */
function bp_unique_page_slug( $slug = '', $post_ID = 0, $post_status = '', $post_type = '', $post_parent = 0, $original_slug = '' ) {
	if ( ( 'buddypress' === $post_type || 'page' === $post_type ) && $slug === $original_slug && 'publish' === $post_status ) {
		$args = array();

		if ( 'page' === $post_type ) {
			$args['post_type'] = 'buddypress';
		} else {
			$args['post_type'] = 'page';
		}

		$pages      = get_pages( $args );
		$post_names = wp_list_pluck( get_pages( $args ), 'post_name' );
		if ( in_array( $slug, $post_names, true ) ) {
			$suffix = 2;
			do {
				$alt_post_name   = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$post_name_check = in_array( $alt_post_name, $post_names, true );
				$suffix++;
			} while ( $post_name_check );
			$slug = $alt_post_name;
		}
	}

	return $slug;
}
add_filter( 'wp_unique_post_slug', __NAMESPACE__ . '\bp_unique_page_slug', 10, 6 );

/**
 * Make sure newly created Directory pages have the `buddypress` post type.
 *
 * @todo This should be managed into `\bp_core_add_page_mappings()`.
 *
 * @since 1.0.0
 *
 * @param array $previous_bp_pages The previous BuddyPress directory pages.
 * @param array $bp_pages          The current BuddyPress directory pages.
 */
function bp_core_add_page_mappings( $previous_bp_pages = array(), $bp_pages = array() ) {
	// Prevent infinite loops.
	remove_action( 'update_option_bp-pages', __NAMESPACE__ . '\bp_core_add_page_mappings', 10, 2 );

	$page_switches = array();

	if ( $bp_pages ) {
		$bp_pages = (array) $bp_pages;

		$pages = get_pages(
			array(
				'include' => $bp_pages,
				'number'  => count( $bp_pages ),
			)
		);

		if ( $pages ) {
			$updates = wp_filter_object_list( $pages, array( 'post_type' => 'page' ) );
			if ( $updates ) {
				foreach ( $updates as $update ) {
					if ( 'buddypress' === get_post_type( $update ) ) {
						continue;
					}

					$page_switches[] = $update->ID;
				}
			}
		}
	}

	if ( $page_switches ) {
		// Do not check post slugs.
		remove_filter( 'wp_unique_post_slug', __NAMESPACE__ . '\bp_unique_page_slug', 10 );

		foreach ( $page_switches as $page_id ) {
			wp_update_post(
				array(
					'ID'        => $page_id,
					'post_type' => 'buddypress',
				)
			);
		}
	}

	// Reset rewrite rules at next page load.
	bp_delete_rewrite_rules();
}
add_action( 'update_option_bp-pages', __NAMESPACE__ . '\bp_core_add_page_mappings', 10, 2 );

/**
 * Sets BuddyPress directory link.
 *
 * @since 1.0.0
 *
 * @param  string   $link The post type link.
 * @param  \WP_Post $post The post type object.
 * @return string        The post type link.
 */
function bp_page_directory_link( $link, \WP_Post $post ) {
	if ( 'buddypress' !== get_post_type( $post ) ) {
		return $link;
	}

	$directory_pages = wp_filter_object_list( (array) bp_core_get_directory_pages(), array( 'id' => $post->ID ) );
	$component       = key( $directory_pages );

	return bp_rewrites_get_url( array( 'component_id' => $component ) );
}
add_filter( 'post_type_link', __NAMESPACE__ . '\bp_page_directory_link', 1, 2 );

/**
 * Checks if a component's directory is set as the site's homepage.
 *
 * @since 1.0.0
 *
 * @param string $component The component ID.
 * @return bool True if a component's directory is set as the site's homepage.
 *              False otherwise.
 */
function bp_is_directory_homepage( $component = '' ) {
	$is_directory_homepage = false;
	$is_page_on_front      = 'page' === get_option( 'show_on_front', 'posts' );
	$page_id_on_front      = get_option( 'page_on_front', 0 );
	$directory_pages       = bp_core_get_directory_pages();

	if ( $is_page_on_front && isset( $directory_pages->{$component} ) && (int) $page_id_on_front === (int) $directory_pages->{$component}->id ) {
		$is_directory_homepage = true;
	}

	return $is_directory_homepage;
}
