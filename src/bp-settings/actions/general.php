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
 * @since 1.0.0
 */
function bp_settings_action_general() {
	bp_core_redirect( bp_get_requested_url() );
}
add_action( 'bp_core_general_settings_after_save', __NAMESPACE__ . '\bp_settings_action_general', 1 );

/**
 * `bp_settings_action_general()` need to use BP Rewrites to build the `verify.url` Email tokens argument.
 *
 * @since 1.0.0
 *
 * @param array $args Email tokens.
 * @return array      Email tokens.
 */
function bp_settings_action_general_verify_email_change( $args = array() ) {
	if ( isset( $args['tokens']['verify.url'] ) && $args['tokens']['verify.url'] ) {
		// Get URL query vars.
		$query_vars = array();
		wp_parse_str( wp_parse_url( $args['tokens']['verify.url'], PHP_URL_QUERY ), $query_vars );

		// Set Rewrites params.
		$slug       = bp_get_settings_slug();
		$rewrite_id = sprintf( 'bp_member_%s', $slug );

		$args['tokens']['verify.url'] = esc_url_raw(
			add_query_arg(
				$query_vars,
				bp_member_rewrites_get_url(
					\bp_displayed_user_id(),
					'',
					array(
						'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
					)
				)
			)
		);
	}

	return $args;
}
add_filter( 'bp_before_send_email_parse_args', __NAMESPACE__ . '\bp_settings_action_general_verify_email_change', 10, 1 );

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
