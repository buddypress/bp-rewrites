<?php
/**
 * BP Rewrites Globals.
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
 * Register plugin globals.
 *
 * @since 1.0.0
 */
function globals() {
	$bpr = bp_rewrites();

	$bpr->version = '1.6.0';

	// Path.
	$plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
	$bpr->dir   = $plugin_dir;

	// URL.
	$plugin_url      = plugins_url( '', dirname( __FILE__ ) );
	$bpr->url        = $plugin_url;
	$bpr->backcompat = array(
		'current_component'      => null, // The BP Component, except for the Members one when a user is displayed.
		'current_item'           => null, // Only used for the Groups single item.
		'current_action'         => null,
		'action_variables'       => null,
		'displayed_user'         => null, // Only used for the Members single item.
		'current_member_type'    => null, // Only used for the Members component.
		'current_group'          => null, // Only used for the Groups single item.
		'current_directory_type' => null, // Only used for the Groups component.
	);

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
