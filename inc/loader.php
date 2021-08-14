<?php
/**
 * BP Rewrites Loader.
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
 * Loader function.
 *
 * @since 1.0.0
 *
 * @param string $plugin_dir The plugin root directory.
 */
function includes( $plugin_dir = '' ) {
	$path = trailingslashit( $plugin_dir );

	// Core is always required.
	require $path . 'src/bp-core/bp-core-template-loader.php';
	require $path . 'src/bp-core/bp-core-rewrites.php';
	require $path . 'src/bp-core/bp-core-catchuri.php';
	require $path . 'src/bp-core/bp-core-functions.php';
	require $path . 'src/bp-core/bp-core-filters.php';

	// The Members component is always active.
	require $path . 'src/bp-members/bp-members-rewrites.php';
	require $path . 'src/bp-members/bp-members-functions.php';
	require $path . 'src/bp-members/bp-members-adminbar.php';
	require $path . 'src/bp-members/bp-members-invitations.php';
	require $path . 'src/bp-members/bp-members-template.php';
	require $path . 'src/bp-members/bp-members-filters.php';

	if ( bp_is_active( 'activity' ) ) {
		require $path . 'src/bp-activity/bp-activity-template.php';
		require $path . 'src/bp-activity/bp-activity-rewrites.php';
		require $path . 'src/bp-activity/bp-activity-functions.php';

		if ( bp_is_active( 'notifications' ) ) {
			require $path . 'src/bp-activity/bp-activity-notifications.php';
		}
	}

	if ( bp_is_active( 'blogs' ) ) {
		require $path . 'src/bp-blogs/bp-blogs-template.php';
		require $path . 'src/bp-blogs/bp-blogs-rewrites.php';
	}

	if ( bp_is_active( 'friends' ) ) {
		require $path . 'src/bp-friends/bp-friends-template.php';
		require $path . 'src/bp-friends/bp-friends-rewrites.php';
		require $path . 'src/bp-friends/bp-friends-functions.php';

		if ( bp_is_active( 'notifications' ) ) {
			require $path . 'src/bp-friends/bp-friends-notifications.php';
		}
	}

	if ( bp_is_active( 'groups' ) ) {
		require $path . 'src/bp-groups/bp-groups-template.php';
		require $path . 'src/bp-groups/bp-groups-rewrites.php';
		require $path . 'src/bp-groups/bp-groups-functions.php';

		if ( bp_is_active( 'notifications' ) ) {
			require $path . 'src/bp-groups/bp-groups-notifications.php';
		}
	}

	if ( bp_is_active( 'messages' ) ) {
		require $path . 'src/bp-messages/bp-messages-template.php';
		require $path . 'src/bp-messages/bp-messages-functions.php';

		if ( bp_is_active( 'notifications' ) ) {
			require $path . 'src/bp-messages/bp-messages-notifications.php';
		}
	}

	if ( bp_is_active( 'notifications' ) ) {
		require $path . 'src/bp-notifications/bp-notifications-adminbar.php';
		require $path . 'src/bp-notifications/bp-notifications-template.php';
		require $path . 'src/bp-notifications/bp-notifications-rewrites.php';
	}

	if ( bp_is_active( 'settings' ) ) {
		require $path . 'src/bp-settings/bp-settings-templates.php';
	}

	if ( bp_is_active( 'xprofile' ) ) {
		require $path . 'src/bp-xprofile/bp-xprofile-rewrites.php';
		require $path . 'src/bp-xprofile/bp-xprofile-template.php';
	}

	$template_pack_dir = sprintf( $path . 'src/bp-templates/bp-%s.php', bp_get_theme_package_id() );
	if ( file_exists( $template_pack_dir ) ) {
		require $template_pack_dir;
	}

	if ( is_admin() ) {
		require $path . 'src/bp-core/admin/bp-core-admin-functions.php';
		require $path . 'src/bp-core/admin/bp-core-admin-rewrites.php';
	}
}
add_action( '_bp_rewrites_includes', __NAMESPACE__ . '\includes', 1, 1 );
