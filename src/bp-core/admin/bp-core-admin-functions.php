<?php
/**
 * BuddyPress Common Admin Functions.
 *
 * @package buddypress\bp-core\admin
 * @since 2.3.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This code should be in `\bp_core_get_admin_settings_tabs()`
 *
 * @since 1.0.0
 *
 * @param array $tabs Tabs list.
 * @return array      Edited tabs list.
 */
function bp_core_get_admin_settings_tabs( $tabs = array() ) {
	$tabs['1'] = array(
		'id'   => 'bp-rewrites-settings',
		'href' => bp_get_admin_url( add_query_arg( array( 'page' => 'bp-rewrites-settings' ), 'admin.php' ) ),
		'name' => __( 'URLs', 'bp-rewrites' ),
	);

	return $tabs;
}
add_filter( 'bp_core_get_admin_settings_tabs', __NAMESPACE__ . '\bp_core_get_admin_settings_tabs', 10, 2 );

/**
 * Include the BP Rewrites tab to the Admin tabs needing specific inline styles.
 *
 * @since 1.0.0
 *
 * @param array $submenu_pages The BP_Admin submenu pages passed by reference.
 */
function bp_admin_submenu_pages( &$submenu_pages = array() ) {
	$submenu_pages['settings']['bp-rewrites-settings'] = get_plugin_page_hookname( 'bp-rewrites-settings', get_settings_page() );
}
add_action( 'bp_admin_submenu_pages', __NAMESPACE__ . '\bp_admin_submenu_pages', 10, 1 );

/**
 * Remove `bp-page-settings` submenu page to make sure only BP Rewrites tab working.
 *
 * @since 1.0.0
 */
function bp_remove_page_settings_submenu_page() {
	remove_submenu_page( get_settings_page(), 'bp-page-settings' );
}
add_action( 'bp_admin_init', __NAMESPACE__ . '\bp_remove_page_settings_submenu_page' );

/**
 * Returns the current visibility of community pages.
 *
 * @since ?.0.0
 *
 * @return string The current visibility of community pages.
 */
function bp_admin_get_community_visibility() {
	$visibility = 'anyone';
	$bp_pages   = (array) buddypress()->pages;

	// Remove pages needing to always be visible.
	unset( $bp_pages['activate'], $bp_pages['register'] );

	if ( count( $bp_pages ) === count( wp_filter_object_list( $bp_pages, array( 'visibility' => 'bp_restricted' ) ) ) ) {
		$visibility = 'members';
	}

	return $visibility;
}

/**
 * Outputs the community visibility settings.
 *
 * @since ?.0.0
 */
function bp_admin_setting_callback_community_visibility() {
	$visibility = bp_admin_get_community_visibility();
	?>

		<select name="_bp_community_visibility" id="_bp_community_visibility" aria-describedby="_bp_community_visibility_description">
			<option value="publish" <?php selected( 'anyone', $visibility, true ); ?>><?php esc_html_e( 'Anyone', 'bp-rewrites' ); ?></option>
			<option value="bp_restricted" <?php selected( 'members', $visibility, true ); ?>><?php esc_html_e( 'Members', 'bp-rewrites' ); ?></option>
		</select>
		<p id="_bp_community_visibility_description" class="description"><?php esc_html_e( 'Choose "Members" to restrict your community area to logged in members only. Choose "Anyone" to allow any user to access to your community area.', 'bp-rewrites' ); ?></p>

	<?php
}

/**
 * Sanitizes the community visibility option and updates community pages status.
 *
 * @since ?.0.0
 *
 * @param string $status The post status to use for community pages.
 * @return string The sanitized community visibility option.
 */
function bp_admin_setting_callback_community_update_visibility( $status = 'publish' ) {
	$visibility = bp_admin_get_community_visibility();

	if ( 'publish' !== $status && 'bp_restricted' !== $status ) {
		return 'members' === $visibility ? 'bp_restricted' : 'publish';
	}

	if ( $visibility !== $status ) {
		foreach ( buddypress()->pages as $key => $page ) {
			if ( 'register' === $key || 'activate' === $key ) {
				continue;
			}

			wp_update_post(
				array(
					'ID'          => $page->id,
					'post_status' => $status,
				)
			);
		}
	}

	return $status;
}

/**
 * Registers the community visibility setting into BuddyPress options.
 *
 * @since ?.0.0
 */
function bp_register_admin_settings() {
	// Check BuddyPress is >= 11.0.
	if ( ! function_exists( 'bp_core_get_directory_pages_stati' ) ) {
		return;
	}

	// Community visibility.
	add_settings_field( '_bp_community_visibility', __( 'Community Visibility', 'bp-rewrites' ), __NAMESPACE__ . '\bp_admin_setting_callback_community_visibility', 'buddypress', 'bp_main', array( 'label_for' => '_bp_community_visibility' ) );
	register_setting( 'buddypress', '_bp_community_visibility', __NAMESPACE__ . '\bp_admin_setting_callback_community_update_visibility' );
}
add_action( 'bp_register_admin_settings', __NAMESPACE__ . '\bp_register_admin_settings', 100 );
