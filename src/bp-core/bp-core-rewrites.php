<?php
/**
 * BuddyPress Rewrites.
 *
 * @package buddypress\bp-core
 * @since 1.0.0
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
 * @since 1.0.0
 */
function bp_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}

/**
 * Are Pretty URLs active ?
 *
 * @since 1.0.0
 *
 * @return bool True if Pretty URLs are on. False otherwise.
 */
function bp_has_pretty_urls() {
	$has_plain_urls = ! get_option( 'permalink_structure', '' );
	return ! $has_plain_urls;
}

/**
 * Get needed data to find a member single item from the request.
 *
 * @since 1.0.0
 *
 * @param string $request The request used during parsing.
 * @return array Data to find a member single item from the request.
 */
function bp_rewrites_get_member_data( $request = '' ) {
	$member_data = array( 'field' => 'slug' );

	if ( bp_is_username_compatibility_mode() ) {
		$member_data = array( 'field' => 'login' );
	}

	if ( bp_core_enable_root_profiles() ) {
		if ( ! $request ) {
			$request = $GLOBALS['wp']->request;
		}

		$request_chunks = explode( '/', ltrim( $request, '/' ) );
		$member_chunk   = reset( $request_chunks );

		// Try to get an existing member to eventually reset the WP Query.
		$member_data['object'] = get_user_by( $member_data['field'], $member_chunk );
	}

	return $member_data;
}

/**
 * Returns the members single item (member) slug.
 *
 * @since 1.0.0
 *
 * @param int $user_id The User ID.
 * @return string The member slug.
 */
function bp_rewrites_get_member_slug( $user_id = 0 ) {
	$bp = buddypress();

	$prop = 'user_nicename';
	if ( bp_is_username_compatibility_mode() ) {
		$prop = 'user_login';
	}

	if ( (int) bp_loggedin_user_id() === (int) $user_id ) {
		$slug = isset( $bp->loggedin_user->userdata->{$prop} ) ? $bp->loggedin_user->userdata->{$prop} : null;
	} elseif ( (int) \bp_displayed_user_id() === (int) $user_id ) {
		$slug = isset( $bp->displayed_user->userdata->{$prop} ) ? $bp->displayed_user->userdata->{$prop} : null;
	} else {
		$slug = bp_core_get_username( $user_id );
	}

	return $slug;
}

/**
 * Returns the slug to use for the view belonging to the requested component.
 *
 * @since 1.0.0
 *
 * @param string $component_id The BuddyPress component's ID.
 * @param string $rewrite_id   The view rewrite ID.
 * @param string $default_slug The view default slug.
 * @return string              The slug to use for the view belonging to the requested component.
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
 * @since 1.0.0
 *
 * @param string $component_id The component ID (eg: `activity` for the BP Activity component).
 * @param string $slug         The customized slug.
 * @param string $context      The context for the customized slug, useful when the same slug is used
 *                             for more than one rewrite ID of the same component.
 * @return string              The rewrite ID matching the customized slug.
 */
function bp_rewrites_get_custom_slug_rewrite_id( $component_id = '', $slug = '', $context = '' ) {
	$directory_pages = bp_core_get_directory_pages();

	if ( ! isset( $directory_pages->{$component_id}->custom_slugs ) || ! $slug ) {
		return null;
	}

	$custom_slugs = (array) $directory_pages->{$component_id}->custom_slugs;
	$rewrite_ids  = array_keys( $custom_slugs, $slug, true );

	if ( 1 < count( $rewrite_ids ) && isset( $context ) && $context ) {
		foreach ( $rewrite_ids as $rewrite_id_key => $rewrite_id ) {
			if ( false !== strpos( $rewrite_id, $context ) ) {
				continue;
			}

			unset( $rewrite_ids[ $rewrite_id_key ] );
		}
	}

	// Always return the first match.
	return reset( $rewrite_ids );
}

/**
 * Builds a BuddyPress link using the WP Rewrite API.
 *
 * @todo Allow customization using `bp_rewrites_get_slug()`
 *       Describe parameter.
 *
 * @since 1.0.0
 *
 * @param array $args An array of parameters.
 * @return string The BuddyPress link.
 */
function bp_rewrites_get_url( $args = array() ) {
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

	if ( ! isset( $bp->{$r['component_id']}->rewrite_ids ) ) {
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
			$create_slug = 'create';
			if ( 'groups' === $component->id ) {
				$create_slug = bp_rewrites_get_slug( 'groups', 'bp_group_create', 'create' );
			}

			$link = str_replace( '%' . $component->rewrite_ids['directory'] . '%', $create_slug, $component->directory_permastruct );
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
