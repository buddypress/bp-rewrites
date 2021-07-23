<?php
/**
 * BP Rewrites Loader.
 *
 * @package bp-rewrites\inc\loader
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loader function.
 *
 * @since 1.0.0
 *
 * @param string $plugin_dir The plugin root directory.
 */
function includes( $plugin_dir = '' ) {
	$path = trailingslashit( $plugin_dir );

	// Core is always required.
	require $path . 'src/bp-core/bp-core-rewrites.php';
	require $path . 'src/bp-core/bp-core-functions.php';

	if ( is_admin() ) {
		require $path . 'src/bp-core/admin/bp-core-admin-functions.php';
		require $path . 'src/bp-core/admin/bp-core-admin-rewrites.php';
	}
}
add_action( '_bp_rewrites_includes', __NAMESPACE__ . '\includes', 1, 1 );
