<?php
/**
 * BuddyPress Filters.
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
 * `bp_core_activation_signup_blog_notification()` need to use BP Rewrites to build the `activate-site.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function bp_core_activation_signup_blog_notification( $args = array() ) {
	if ( isset( $args['tokens']['activate-site.url'], $args['tokens']['key_blog'] ) && $args['tokens']['activate-site.url'] && $args['tokens']['key_blog'] ) {
		$args['tokens']['activate-site.url'] = esc_url_raw( bp_activation_rewrites_get_url( $args['tokens']['key_blog'] ) );
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\bp_core_activation_signup_blog_notification', 10, 1 );

/**
 * Eventually appendf BuddyPress directories to WP Dropdown's pages control.
 *
 * @since 1.0.0
 *
 * @param WP_Post[] $pages Array of page objects.
 * @param array     $args  Array of get_pages() arguments.
 * @return WP_Post[]       Array of page objects, potentially including BP directories.
 */
function bp_core_include_directory_on_front( $pages = array(), $args = array() ) {
	$is_page_on_front_dropdown = false;

	if ( isset( $args['name'] ) ) {
		$is_page_on_front_dropdown = 'page_on_front' === $args['name'];

		if ( is_customize_preview() ) {
			$is_page_on_front_dropdown = '_customize-dropdown-pages-page_on_front' === $args['name'];
		}
	}

	if ( $is_page_on_front_dropdown ) {
		$directories = bp_core_get_directory_pages();
		$null        = '0000-00-00 00:00:00';

		foreach ( (array) $directories as $component => $directory ) {
			if ( 'activate' === $component || 'register' === $component ) {
				continue;
			}

			$post = (object) array(
				'ID'                    => $directory->id,
				'post_author'           => 0,
				'post_date'             => $null,
				'post_date_gmt'         => $null,
				'post_content'          => '',
				'post_title'            => $directory->title,
				'post_excerpt'          => '',
				'post_status'           => 'publish',
				'comment_status'        => 'closed',
				'ping_status'           => 'closed',
				'post_password'         => '',
				'post_name'             => $directory->slug,
				'pinged'                => '',
				'to_ping'               => '',
				'post_modified'         => $null,
				'post_modified_gmt'     => $null,
				'post_content_filtered' => '',
				'post_parent'           => 0,
				'guid'                  => bp_rewrites_get_url(
					array(
						'component_id' => $component,
					)
				),
				'menu_order'            => 0,
				'post_type'             => 'buddypress',
				'post_mime_type'        => '',
				'comment_count'         => 0,
				'filter'                => 'raw',
			);

			$pages[] = get_post( $post );
		}

		$pages = bp_alpha_sort_by_key( $pages, 'post_title' );
	}

	return $pages;
}
add_filter( 'get_pages', __NAMESPACE__ . '\bp_core_include_directory_on_front', 10, 2 );

/**
 * Set the page title when the restricted page is displayed.
 *
 * @since 1.5.0
 *
 * @param array $bp_title_parts The document title parts.
 * @return array The document title parts.
 */
function bp_get_title_parts( $bp_title_parts ) {
	if ( \bp_is_current_component( 'core' ) ) {
		$post = get_post();

		if ( 'buddypress' === get_post_type( $post ) && 'restricted' === get_post_field( 'post_name', $post, 'raw' ) ) {
			$bp_title_parts = array( esc_html( $post->post_title ) );
		}
	}

	return $bp_title_parts;
}
add_filter( 'bp_get_title_parts', __NAMESPACE__ . '\bp_get_title_parts', 10, 1 );
