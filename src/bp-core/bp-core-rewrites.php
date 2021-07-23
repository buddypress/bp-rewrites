<?php
/**
 * BuddyPress Rewrites.
 *
 * @package buddypress\bp-core
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since ?.0.0
 */
function bp_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}

/**
 * Are Pretty URLs active ?
 *
 * @since ?.0.0
 *
 * @return bool True if Pretty URLs are on. False otherwise.
 */
function bp_has_pretty_urls() {
	$has_plain_urls = ! get_option( 'permalink_structure', '' );
	return ! $has_plain_urls;
}

/**
 * Returns the slug to use for the nav item of the requested component.
 *
 * @since ?.0.0
 *
 * @param string $component_id The BuddyPress component's ID.
 * @param string $rewrite_id   The nav item's rewrite ID.
 * @param string $default_slug The nav item's default slug.
 * @return string              The slug to use for the nav item of the requested component.
 */
function bp_rewrites_get_slug( $component_id = '', $rewrite_id = '', $default_slug = '' ) {
	$directory_pages = bp_core_get_directory_pages();
	$slug            = $default_slug;

	if ( ! isset( $directory_pages->{$component_id}->custom_slugs ) || ! $rewrite_id ) {
		return $slug;
	}

	$custom_slugs = (array) $directory_pages->{$component_id}->custom_slugs;
	if ( isset( $custom_slugs[ $rewrite_id ] ) && $custom_slugs[ $rewrite_id ] ) {
		$slug = $custom_slugs[ $rewrite_id ];
	}

	return $slug;
}

/**
 * Returns the rewrite ID of a customized slug.
 *
 * @since ?.0.0
 *
 * @param string $component_id The component ID (eg: `activity` for the BP Activity component).
 * @param string $slug         The customized slug.
 * @return string
 */
function bp_rewrites_get_custom_slug_rewrite_id( $component_id = '', $slug = '' ) {
	$directory_pages = bp_core_get_directory_pages();

	if ( ! isset( $directory_pages->{$component_id}->custom_slugs ) || ! $slug ) {
		return null;
	}

	$custom_slugs = (array) $directory_pages->{$component_id}->custom_slugs;

	// If there's a match it's a custom slug.
	return array_search( $slug, $custom_slugs, true );
}

/**
 * Builds a BuddyPress link using the WP Rewrite API.
 *
 * @todo Allow customization using `bp_rewrites_get_slug()`
 *       Describe parameter.
 *
 * @since ?.0.0
 *
 * @param array $args An array of parameters.
 * @return string The BuddyPress link.
 */
function bp_rewrites_get_link( $args = array() ) {
	$bp   = buddypress();
	$link = '#';

	$r = wp_parse_args(
		$args,
		array(
			'component_id'                 => '',
			'directory_type'               => '',
			'single_item'                  => '',
			'single_item_component'        => '',
			'single_item_action'           => '',
			'single_item_action_variables' => array(),
		)
	);

	$are_urls_pretty = bp_has_pretty_urls();

	if ( ! isset( $bp->{$r['component_id']} ) ) {
		return $link;
	}

	$component = $bp->{$r['component_id']};
	unset( $r['component_id'] );

	// Using plain links.
	if ( ! $are_urls_pretty ) {
		if ( ! isset( $r['member_register'] ) && ! isset( $r['member_activate'] ) ) {
			$r['directory'] = 1;
		}

		$r  = array_filter( $r );
		$qv = array();

		foreach ( $component->rewrite_ids as $key => $rewrite_id ) {
			if ( ! isset( $r[ $key ] ) ) {
				continue;
			}

			$qv[ $rewrite_id ] = $r[ $key ];
		}

		$link = add_query_arg( $qv, home_url( '/' ) );

		// Using pretty URLs.
	} else {
		if ( ! isset( $component->rewrite_ids['directory'] ) || ! isset( $component->directory_permastruct ) ) {
			return $link;
		}

		if ( isset( $r['member_register'] ) ) {
			$link = str_replace( '%' . $component->rewrite_ids['member_register'] . '%', '', $component->register_permastruct );
			unset( $r['member_register'] );
		} elseif ( isset( $r['member_activate'] ) ) {
			$link = str_replace( '%' . $component->rewrite_ids['member_activate'] . '%', '', $component->activate_permastruct );
			unset( $r['member_activate'] );
		} elseif ( isset( $r['create_single_item'] ) ) {
			$link = str_replace( '%' . $component->rewrite_ids['directory'] . '%', 'create', $component->directory_permastruct );
			unset( $r['create_single_item'] );
		} else {
			$link = str_replace( '%' . $component->rewrite_ids['directory'] . '%', $r['single_item'], $component->directory_permastruct );

			// Remove the members directory slug when root profiles are on.
			if ( bp_core_enable_root_profiles() && 'members' === $component->id && isset( $r['single_item'] ) && $r['single_item'] ) {
				$link = str_replace( $bp->members->root_slug . '/', '', $link );
			}

			unset( $r['single_item'] );
		}

		$r = array_filter( $r );

		if ( isset( $r['directory_type'] ) && $r['directory_type'] ) {
			if ( 'members' === $component->id ) {
				array_unshift( $r, bp_get_members_member_type_base() );
			} elseif ( 'groups' === $component->id ) {
				array_unshift( $r, bp_get_groups_group_type_base() );
			} else {
				unset( $r['directory_type'] );
			}
		}

		if ( isset( $r['single_item_action_variables'] ) && $r['single_item_action_variables'] ) {
			$r['single_item_action_variables'] = join( '/', (array) $r['single_item_action_variables'] );
		}

		if ( isset( $r['create_single_item_variables'] ) && $r['create_single_item_variables'] ) {
			$r['create_single_item_variables'] = join( '/', (array) $r['create_single_item_variables'] );
		}

		$link = home_url( user_trailingslashit( '/' . rtrim( $link, '/' ) . '/' . join( '/', $r ) ) );
	}

	return $link;
}
