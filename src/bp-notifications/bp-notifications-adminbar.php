<?php
/**
 * BuddyPress Notifications Admin Bar functions.
 *
 * Admin Bar functions for the Notifications component.
 *
 * @package buddypress\bp-notifications
 * @since 1.9.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `bp_notifications_toolbar_menu()` needs to use BP Rewrites to build URLs.
 *
 * @since 1.0.0
 */
function bp_notifications_toolbar_menu() {
	global $wp_admin_bar;

	if ( is_user_logged_in() ) {
		$user_id       = bp_loggedin_user_id();
		$notifications = bp_notifications_get_notifications_for_user( $user_id, 'object' );
		$count         = ! empty( $notifications ) ? count( $notifications ) : 0;
		$alert_class   = (int) $count > 0 ? 'pending-count alert' : 'count no-alert';
		$menu_title    = '<span id="ab-pending-notifications" class="' . $alert_class . '">' . number_format_i18n( $count ) . '</span>';
		$menu_link     = bp_notifications_rewrites_get_member_action_url( $user_id );

		// Add the top-level Notifications button.
		$wp_admin_bar->add_node(
			array(
				'parent' => 'top-secondary',
				'id'     => 'bp-notifications',
				'title'  => $menu_title,
				'href'   => $menu_link,
			)
		);

		if ( ! empty( $notifications ) ) {
			foreach ( (array) $notifications as $notification ) {
				$wp_admin_bar->add_node(
					array(
						'parent' => 'bp-notifications',
						'id'     => 'notification-' . $notification->id,
						'title'  => $notification->content,
						'href'   => $notification->href,
					)
				);
			}
		} else {
			$wp_admin_bar->add_node(
				array(
					'parent' => 'bp-notifications',
					'id'     => 'no-notifications',
					'title'  => __( 'No new notifications', 'bp-rewrites' ),
					'href'   => $menu_link,
				)
			);
		}
	}
}

/**
 * Replace `\bp_notifications_toolbar_menu()` with `BP\Rewrites\bp_notifications_toolbar_menu()`.
 *
 * @since 1.0.0
 */
function reset_notifications_admin_bar() {
	remove_action( 'admin_bar_menu', 'bp_members_admin_bar_notifications_menu', 90 );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\bp_notifications_toolbar_menu', 90 );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\reset_notifications_admin_bar', 1 );
