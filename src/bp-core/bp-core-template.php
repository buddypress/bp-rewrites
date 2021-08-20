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
 * This private function checks if a BuddyPress function retrieving a BuddyPress global was called too early.
 *
 * Doing this check inside `BuddyPress::__get()` would probably be better to improve this backcompat mechanism.
 *
 * @since ?.0.0
 *
 * @param string $function  The function name. Required.
 * @param string $bp_global The BuddyPress global name. Required.
 * @return mixed            The BuddyPress global value set using the BP Legacy URL parser.
 */
function _was_called_too_early( $function, $bp_global ) {
	$retval = null;

	if ( did_action( 'bp_parse_query' ) ) {
		return $retval;
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		ob_start();
		_doing_it_wrong( esc_html( $function ), esc_html__( 'Please wait for the `bp_parse_query` hook to be fired before using it.' ), 'TBD' );
		$doing_it_wrong = ob_get_clean();

		$debug_backtrace = explode( ', ' . esc_html( rtrim( $function, '()' ) ), wp_debug_backtrace_summary() ); // phpcs:ignore
		$debug_backtrace = reset( $debug_backtrace );

		printf(
			'<div style="border-left: solid 4px red; padding: 0 1em; margin: 1em">
				%s
			</div>',
			wpautop( $doing_it_wrong . "\n" . $debug_backtrace . ".\n" ) // phpcs:ignore
		);
	}

	/*
	 * @todo Set the requested BP Global using the BP Legacy URL parser.
	 */
	$retval = null;

	return $retval;
}

/**
 * Adds backward compatibility when `\bp_current_component()` is called too early.
 *
 * @since ?.0.0
 *
 * @param false|string $current_component False if the current component is not set yet. The component name otherwise.
 * @return null|string                    Null if the current component is not set yet. The component name otherwise.
 */
function bp_current_component( $current_component = false ) {
	if ( ! $current_component ) {
		$current_component = _was_called_too_early( 'bp_current_component()', 'current_component' );
	}

	return $current_component;
}
add_filter( 'bp_current_component', __NAMESPACE__ . '\bp_current_component', 1, 1 );

/**
 * Adds backward compatibility when `bp_current_action()` is called too early.
 *
 * @since ?.0.0
 *
 * @param string $current_action False if the current action is not set yet. The component action name otherwise.
 * @return null|string           Null if the current action is not set yet. The component action name otherwise.
 */
function bp_current_action( $current_action = '' ) {
	if ( ! $current_action ) {
		$current_component = _was_called_too_early( 'bp_current_action()', 'current_action' );
	}

	return $current_action;
}
add_filter( 'bp_current_action', __NAMESPACE__ . '\bp_current_action', 1, 1 );
