<?php
/**
 * Settings: Capabilities actions.
 *
 * @package buddypress\bp-settings\actions
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The link used for the redirection should use BP Rewrites.
 *
 * @since 1.0.0
 */
function bp_settings_action_capabilities() {
	bp_core_redirect( bp_get_requested_url() );
}
add_action( 'bp_settings_capabilities_after_save', __NAMESPACE__ . '\bp_settings_action_capabilities', 1 );

/**
 * The form action is not using BP Rewrites.
 *
 * From this plugin the easiest fix is to empty it.
 *
 * @since 1.0.0
 */
function empty_capabilities_form_action() {
	empty_form_action( 'account-capabilities-form' );
}
add_action( 'wp_footer', __NAMESPACE__ . '\empty_capabilities_form_action' );
