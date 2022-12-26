<?php
/**
 * Core Block Template functions.
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Customizations to perform into the Block Theme.
 *
 * 1. Edit the `functions.php` so that `buddypress` support is added to the theme: `add_theme_support( 'buddypress' );`.
 * 2. Add a `buddypress` subfolder to the Theme's `templates` one to reproduce the BuddyPress PHP template hierarchy replacing `.php` file exts with `.html`.
 * 3. Edit the Theme's theme.json to describe the BuddyPress custom templates using the `customTemplates` property. Eg:
 * {
 *		"name": "buddypress/members/index",
 *		"title": "Member directory (BuddyPress)",
 *		"postTypes": [
 *			"buddypress"
 *		]
 *	}
 */

/**
 * Adds the BuddyPress template type to WordPress template types.
 *
 * @since 1.5.0
 *
 * @param array $templates WordPress template types.
 * @return array BuddyPress template types.
 */
function bp_block_set_template_hierarchy( $templates ) {
	$queried_object = get_queried_object();
	if ( isset( $queried_object->templates ) ) {
		/*
		 * Question:
		 * Should we include `archive-buddypress.php` or `single-buddypress.php` templates to templates returned
		 * by `get_default_block_template_types()`?
		 */
		array_unshift( $templates, 'buddypress/' . current( $queried_object->templates ) );
	}

	return $templates;
}

/**
 * Replaces the found BuddyPress template with a Block Template for block themes supporting `buddypress`.
 *
 * @since 1.5.0
 *
 * @param string $template  The located template.
 * @param array  $templates The requested templates.
 * @return string The block template canvas once the block template is set.
 */
function bp_locate_block_template( $template = '', $templates = array() ) {
	$requested_template = reset( $templates );
	$queried_object     = get_queried_object();

	if ( current_theme_supports( 'buddypress' ) && wp_is_block_theme() && in_array( $requested_template, $queried_object->templates, true ) ) {
		global $wp_query;
		$queried_object->templates = array( $requested_template );

		$wp_query->is_post_type_archive = true;
		$wp_query->is_archive           = true;
		$wp_query->is_home              = false;
		$wp_query->is_404               = false;

		$wp_query->set( 'post_type', 'buddypress' );

		add_filter( 'archive_template_hierarchy', __NAMESPACE__ . '\bp_block_set_template_hierarchy', 10, 1 );
		$template = get_archive_template();
		remove_filter( 'archive_template_hierarchy', __NAMESPACE__ . '\bp_block_set_template_hierarchy', 10, 1 );
	}

	return $template;
}
add_filter( 'bp_located_template', __NAMESPACE__ . '\bp_locate_block_template', 1, 2 );
