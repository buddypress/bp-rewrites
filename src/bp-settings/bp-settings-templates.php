<?php
/**
 * BuddyPress Settings Template Functions.
 *
 * @package buddypress\bp-settings
 * @since 1.5.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the User's settings URL.
 *
 * @since 1.0.0
 *
 * @param int          $user_id    The user ID.
 * @param array        $query_vars Additional data to include to the URL as query vars.
 * @param false|string $nonce      The name of the nonce to use or false.
 */
function bp_settings_get_member_url( $user_id = 0, $query_vars = array(), $nonce = false ) {
	if ( ! $user_id ) {
		$user_id = \bp_displayed_user_id();
	}

	$slug       = bp_get_settings_slug();
	$rewrite_id = sprintf( 'bp_member_%s', $slug );

	$url = bp_member_rewrites_get_url(
		$user_id,
		'',
		array(
			'single_item_component' => bp_rewrites_get_slug( 'members', $rewrite_id, $slug ),
		)
	);

	if ( $query_vars ) {
		$url = add_query_arg( $query_vars, $url );
	}

	if ( $nonce ) {
		$url = wp_nonce_url( $url, $nonce );
	}

	return $url;
}

/**
 * `\bp_settings_pending_email_notice()` needs to be edited to use BP Rewrites.
 *
 * @since 1.0.0
 */
function bp_settings_pending_email_notice() {
	// Remove the BuddyPress hook to replace it by BP Rewrites one.
	remove_action( 'bp_before_member_settings_template', 'bp_settings_pending_email_notice' );

	$pending_email = bp_get_user_meta( \bp_displayed_user_id(), 'pending_email_change', true );

	if ( empty( $pending_email['newemail'] ) ) {
		return;
	}

	if ( bp_get_displayed_user_email() === $pending_email['newemail'] ) {
		return;
	}

	?>

	<div id="message" class="bp-template-notice error">
		<p>
			<?php
			printf(
				/* translators: %s: new email address */
				esc_html__( 'There is a pending change of your email address to %s.', 'bp-rewrites' ),
				'<code>' . esc_html( $pending_email['newemail'] ) . '</code>'
			);
			?>
			<br />
			<?php
			printf(
				/* translators: 1: email address. 2: cancel email change url. */
				esc_html__( 'Check your email (%1$s) for the verification link, or %2$s.', 'bp-rewrites' ),
				'<code>' . esc_html( $pending_email['newemail'] ) . '</code>',
				sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url(
						bp_settings_get_member_url(
							bp_displayed_user_id(),
							array(
								'dismiss_email_change' => 1,
							),
							'bp_dismiss_email_change'
						)
					),
					esc_html__( 'cancel the pending change', 'bp-rewrites' )
				)
			);
			?>
		</p>
	</div>

	<?php
}
add_action( 'bp_before_member_settings_template', __NAMESPACE__ . '\bp_settings_pending_email_notice', 1 );
