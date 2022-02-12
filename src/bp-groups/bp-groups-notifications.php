<?php
/**
 * BuddyPress Groups Notifications.
 *
 * @package buddypress\bp-groups
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter to rebuild URL for the new membership requests notification.
 *
 * @see BP\Rewrites\groups_format_notifications()
 *
 * @since 1.0.0
 *
 * @param array|string $content The content of the notification.
 * @param array        ...$args The filter arguments.
 * @return array|string         The content of the notification with rebuilt URL.
 */
function bp_groups_format_new_membership_requests( $content, ...$args ) {
	$notification_link = bp_groups_rewrites_get_notification_action_url( $args[0], $args[4] );

	if ( $notification_link ) {
		$notification_link = add_query_arg( 'n', 1, $notification_link );

		if ( is_array( $content ) ) {
			$content['link'] = $notification_link;
		} else {
			$content = '<a href="' . $notification_link . '">' . $args[3] . '</a>';
		}
	}

	return $content;
}

/**
 * Filter to rebuild URL for the membership_request reject/accept notification.
 *
 * @see BP\Rewrites\groups_format_notifications()
 *
 * @since 1.0.0
 *
 * @param array|string $content The content of the notification.
 * @param array        ...$args The filter arguments.
 * @return array|string         The content of the notification with rebuilt URL.
 */
function bp_groups_format_membership_request_action( $content, ...$args ) {
	$notification_link = add_query_arg( 'n', 1, rtrim( $args[3], '?n=1' ) );

	if ( is_array( $content ) ) {
		$content['link'] = $notification_link;
	} else {
		$content = '<a href="' . $notification_link . '">' . $args[2] . '</a>';
	}

	return $content;
}

/**
 * Filter to rebuild URL for the promoted member notification.
 *
 * @see BP\Rewrites\groups_format_notifications()
 *
 * @since 1.0.0
 *
 * @param array|string $content The content of the notification.
 * @param array        ...$args The filter arguments.
 * @return array|string         The content of the notification with rebuilt URL.
 */
function bp_groups_format_member_promoted( $content, ...$args ) {
	$args              = array_filter( $args );
	$notification_link = add_query_arg( 'n', 1, trim( end( $args ), '?n=1' ) );

	if ( is_array( $content ) ) {
		$content['link'] = $notification_link;
	} else {
		if ( is_numeric( $args[0] ) ) {
			$text = $args[1];
		} else {
			$text = $args[2];
		}

		$content = '<a href="' . $notification_link . '">' . $text . '</a>';
	}

	return $content;
}

/**
 * Filter to rebuild URL for the group invite notification.
 *
 * @see BP\Rewrites\groups_format_notifications()
 *
 * @since 1.0.0
 *
 * @param array|string $content The content of the notification.
 * @param array        ...$args The filter arguments.
 * @return array|string         The content of the notification with rebuilt URL.
 */
function bp_groups_format_group_invite( $content, ...$args ) {
	$user_id = \bp_displayed_user_id();

	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	$notification_link = add_query_arg( 'n', 1, bp_groups_rewrites_get_member_action_url( $user_id, 'invites' ) );

	if ( is_array( $content ) ) {
		$content['link'] = $notification_link;
	} else {
		if ( is_numeric( $args[0] ) ) {
			$text = $args[1];
		} else {
			$text = $args[2];
		}

		$content = '<a href="' . $notification_link . '">' . $text . '</a>';
	}

	return $content;
}

/**
 * `\groups_format_notifications()` needs to use BP Rewrite to build URLs.
 *
 * This function is hooked to `bp_init` and registers the above filters.
 *
 * @since 1.0.0
 */
function groups_format_notifications() {
	$actions = array(
		'new_membership_requests'     => 6,
		'new_membership_request'      => 6,
		'membership_request_accepted' => 5,
		'membership_request_rejected' => 5,
		'member_promoted_to_admin'    => 5,
		'member_promoted_to_mod'      => 5,
		'group_invite'                => 5,
	);

	foreach ( $actions as $action => $num_args ) {
		$filter_suffix = $action;
		if ( 'new_membership_request' === $action ) {
			$filter_suffix = 'new_membership_requests';
		} elseif ( 'membership_request_accepted' === $action || 'membership_request_rejected' === $action ) {
			$filter_suffix = 'membership_request_action';
		} elseif ( 'member_promoted_to_admin' === $action || 'member_promoted_to_mod' === $action ) {
			$filter_suffix = 'member_promoted';
		}

		add_filter( 'bp_groups_single_' . $action . '_notification', __NAMESPACE__ . '\bp_groups_format_' . $filter_suffix, 1, $num_args );
		add_filter( 'bp_groups_multiple_' . $action . '_notification', __NAMESPACE__ . '\bp_groups_format_' . $filter_suffix, 1, $num_args );
	}
}
add_action( 'bp_init', __NAMESPACE__ . '\groups_format_notifications', 50 );

/**
 * `groups_notification_new_membership_request()` need to use BP Rewrites
 * to build the `activate.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function groups_notification_new_membership_request( $args = array() ) {
	if ( isset( $args['tokens']['group-requests.url'], $args['tokens']['group'] ) && $args['tokens']['group-requests.url'] && $args['tokens']['group'] ) {
		// Get potential customized slugs.
		$action  = bp_rewrites_get_slug( 'groups', 'bp_group_read_admin', 'admin' );
		$action .= '/' . bp_rewrites_get_slug( 'groups', 'bp_group_manage_membership_requests', 'membership-requests' );

		$args['tokens']['group-requests.url'] = esc_url_raw(
			bp_group_rewrites_get_action_url(
				$action,
				$args['tokens']['group']
			)
		);
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\groups_notification_new_membership_request', 10, 1 );
