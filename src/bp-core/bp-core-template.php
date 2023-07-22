<?php
/**
 * Core component template tag functions.
 *
 * @package buddypress\bp-core
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * List BP Exceptions needing a fix.
 *
 * @since 1.4.0
 *
 * @param string $trace The debug backtrace called functions.
 * @return bool True if the debug backtrace contains an exception. False otherwise.
 */
function _is_bp_was_called_too_early_exceptions( $trace = '' ) {
	$is_exception = false;
	$exceptions   = array(
		// `BP_Core::setup_globals()` checks the displayed user ID too early when using `bp_user_has_access()`.
		'BP_Core->setup_globals, bp_user_has_access',
		// `bp_groups_user_can_filter` shouldn't use `bp_get_current_group_id()` that early.
		'BP_Admin->setup_actions, bp_current_user_can',
	);

	foreach ( $exceptions as $exception ) {
		if ( false === strpos( $trace, $exception ) ) {
			continue;
		}

		$is_exception = true;
	}

	return $is_exception;
}

/**
 * This private function checks if a BuddyPress function retrieving a BuddyPress global was called too early.
 *
 * Doing this check inside `BuddyPress::__get()` would probably be better to improve this backcompat mechanism.
 *
 * @since 1.0.0
 *
 * @param string $function  The function name. Required.
 * @param array  $bp_global An array containing the BuddyPress global name. Required.
 * @return mixed            The BuddyPress global value set using the BP Legacy URL parser.
 */
function _was_called_too_early( $function, $bp_global ) {
	if ( did_action( 'bp_parse_query' ) || ! needs_query_check() ) {
		return null;
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$debug_backtrace = explode( ', ' . esc_html( rtrim( $function, '()' ) ), wp_debug_backtrace_summary() ); // phpcs:ignore
		$debug_backtrace = reset( $debug_backtrace );

		// If it's not an exception display an error notice.
		if ( ! _is_bp_was_called_too_early_exceptions( $debug_backtrace ) ) {
			ob_start();
			_doing_it_wrong( esc_html( $function ), esc_html__( 'Please wait for the `bp_parse_query` hook to be fired before using it.', 'bp-rewrites' ), 'BP Rewrites' );
			$doing_it_wrong = ob_get_clean();

			printf(
				'<div style="border-left: solid 4px red; padding: 0 1em; margin: 1em">
					%s
				</div>',
				wpautop( $doing_it_wrong . "\n" . $debug_backtrace . ".\n" ) // phpcs:ignore
			);
		}
	}

	// Get the requested BP Global using the BP Legacy URL parser.
	return bp_core_get_from_uri( $bp_global );
}

/**
 * Adds backward compatibility when `\bp_current_component()` is called too early.
 *
 * @since 1.0.0
 *
 * @param false|string $current_component False if the current component is not set yet. The component name otherwise.
 * @return null|string                    Null if the current component is not set yet. The component name otherwise.
 */
function bp_current_component( $current_component = false ) {
	if ( ! $current_component ) {
		$current_component = _was_called_too_early( 'bp_current_component()', array( 'current_component' ) );
	}

	return $current_component;
}
add_filter( 'bp_current_component', __NAMESPACE__ . '\bp_current_component', 1, 1 );

/**
 * Adds backward compatibility when `\bp_is_current_component()` is called too early.
 *
 * NB: this would probably be unnecessary if `\bp_is_current_component()` was using `\bp_current_component()`.
 *
 * @since 1.0.0
 *
 * @param bool   $is_current_component True if the current component is the one being checked. False otherwise.
 * @param string $component            The component ID to check.
 * @return bool                        True if the current component is the one being checked. False otherwise.
 */
function bp_is_current_component( $is_current_component = false, $component = '' ) {
	if ( ! $is_current_component && bp_current_component() === $component ) {
		$is_current_component = true;
	}

	return $is_current_component;
}
add_filter( 'bp_is_current_component', __NAMESPACE__ . '\bp_is_current_component', 1, 2 );

/**
 * Adds backward compatibility when `bp_current_action()` is called too early.
 *
 * @since 1.0.0
 *
 * @param string $current_action Empty string if the current action is not set yet. The component action name otherwise.
 * @return null|string           Null if the current action is not set yet. The component action name otherwise.
 */
function bp_current_action( $current_action = '' ) {
	if ( ! $current_action ) {
		$current_action = _was_called_too_early( 'bp_current_action()', array( 'current_action' ) );
	}

	return $current_action;
}
add_filter( 'bp_current_action', __NAMESPACE__ . '\bp_current_action', 1, 1 );

/**
 * Adds backward compatibility when `bp_current_item()` is called too early.
 *
 * @since 1.0.0
 *
 * @param false|string $current_item False if the current item is not set yet. The single item slug otherwise.
 * @return null|string               Null if the current item is not set yet. The single item slug otherwise.
 */
function bp_current_item( $current_item = '' ) {
	if ( ! $current_item ) {
		$current_item = _was_called_too_early( 'bp_current_item()', array( 'current_item' ) );
	}

	return $current_item;
}
add_filter( 'bp_current_item', __NAMESPACE__ . '\bp_current_item', 1, 1 );

/**
 * Adds backward compatibility when `bp_action_variables()` is called too early.
 *
 * @since 1.0.0
 *
 * @param false|array $action_variables False if the action variables are not set yet. The action variables otherwise.
 * @return null|array                   Null if the action variables are not set yet. The action variables otherwise.
 */
function bp_action_variables( $action_variables = array() ) {
	if ( ! $action_variables ) {
		$action_variables = _was_called_too_early( 'bp_action_variables()', array( 'action_variables' ) );
	}

	return $action_variables;
}
add_filter( 'bp_action_variables', __NAMESPACE__ . '\bp_action_variables', 1, 1 );

/**
 * Adds backward compatibility when `bp_displayed_user_id()` is called too early.
 *
 * @since 1.0.0
 *
 * @param int $user_id 0 if the displayed user ID is not set yet. The the displayed user ID otherwise.
 * @return null|int    Null if the the displayed user ID is not set yet. The the displayed user ID otherwise.
 */
function bp_displayed_user_id( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = _was_called_too_early( 'bp_displayed_user_id()', array( 'displayed_user', 'id' ) );
	}

	return $user_id;
}
add_filter( 'bp_displayed_user_id', __NAMESPACE__ . '\bp_displayed_user_id', 1, 1 );
