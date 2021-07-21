<?php
/**
 * BP Rewrites Globals.
 *
 * @package bp-rewrites\inc\globals
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register plugin globals.
 *
 * @since 1.0.0
 */
function globals() {
	$bpr = bp_rewrites();

	$bpr->version = '1.0.0-alpha';

	// Path.
	$bpr->dir = plugin_dir_path( dirname( __FILE__ ) );

	// URL.
	$bpr->url = plugins_url( dirname( __FILE__ ) );
}
add_action( 'bp_include', __NAMESPACE__ . '\globals', 2 );
