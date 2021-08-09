<?php
/**
 * Settings: General actions.
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
 * @since ?.0.0
 */
function bp_settings_action_general() {
	bp_core_redirect( bp_get_requested_url() );
}
add_action( 'bp_core_general_settings_after_save', __NAMESPACE__ . '\bp_settings_action_general', 1 );

/**
 * The form action is not using BP Rewrites.
 *
 * From this plugin the easiest fix is to empty it.
 *
 * @since 1.0.0
 */
function empty_general_form_action() {
	$form_id = 'your-profile';
	if ( 'legacy' === bp_get_theme_package_id() ) {
		$form_id = 'settings-form';
	}

	empty_form_action( $form_id );
}
add_action( 'wp_footer', __NAMESPACE__ . '\empty_general_form_action' );
