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
