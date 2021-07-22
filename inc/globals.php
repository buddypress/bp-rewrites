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
	$plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
	$bpr->dir   = $plugin_dir;

	// URL.
	$plugin_url = plugins_url( dirname( __FILE__ ) );
	$bpr->url   = $plugin_url;

	/**
	 * Private (do not use) hook used to include files early.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_dir The plugin root directory.
	 */
	do_action( '_bp_rewrites_includes', $plugin_dir );
}
add_action( 'bp_loaded', __NAMESPACE__ . '\globals', 1 );
