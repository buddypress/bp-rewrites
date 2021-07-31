<?php
/**
 * Core BuddyPress Navigational Functions.
 *
 * This file is not loaded by the plugin. It is only there to remember
 * we need to edit when we'll merge this "feature as a plugin" plugin:
 * - `bp_core_new_nav_item()` so that function arguments includes the `rewrite_id` one.
 * - `bp_core_create_nav_link()` should use a BP Rewrites function to build links.
 * - `bp_core_create_subnav_link() should use a BP Rewrites function to build links.
 *
 * @todo Now the BuddyBar has been removed from code, maybe this file
 * should be renammed in favor of `bp-core-navigation.php`?
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
