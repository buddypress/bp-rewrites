<?php
/**
 * BuddyPress Rewrites Admin Functions.
 *
 * @package buddypress\bp-core\admin
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders BuddyPress URLs admin panel.
 *
 * @since ?.0.O
 */
function bp_core_admin_rewrites_settings() {
	$bp = buddypress();
	?>
	<div class="wrap">

		<h1><?php esc_html_e( 'BuddyPress Settings', 'buddypress' ); ?></h1>

		<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs( __( 'URLs', 'buddypress' ) ); ?></h2>
		<h2><?php esc_html_e( 'BuddyPress URLs', 'buddypress' ); ?></h2>

		<hr class="hr-separator">

		<form action="" method="post" id="bp-admin-rewrites-form">

			<?php foreach ( $bp->pages as $component_id => $directory_data ) : ?>

				<h3><?php echo esc_html( $directory_data->title ); ?></h3>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( sprintf( '%s-directory-slug', sanitize_key( $component_id ) ) ); ?>">
								<?php esc_html_e( 'Directory slug', 'buddypress' ); ?>
							</label>
						</th>
						<td>
							<input type="text" class="code" name="<?php printf( 'components[%d][post_name]', absint( $directory_data->id ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-directory-slug', sanitize_key( $component_id ) ) ); ?>" value="<?php echo esc_attr( $directory_data->slug ); ?>">
						</td>
					</tr>
				</table>

				<?php if ( 'members' === $component_id ) : ?>
					<h4><?php esc_html_e( 'Single Member primary navigation slugs', 'buddypress' ); ?></h4>
					<table class="form-table" role="presentation">
						<?php
						foreach ( $bp->members->nav->get_primary() as $primary_nav_item ) :
							if ( ! isset( $primary_nav_item['rewrite_id'] ) || ! $primary_nav_item['rewrite_id'] ) {
								continue;
							}
							?>
							<tr>
								<th scope="row">
									<label style="margin-left: 2em; display: inline-block; vertical-align: middle" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $primary_nav_item['rewrite_id'] ) ) ); ?>">
										<?php
										printf(
											/* translators: %s is the primary nav item name */
											esc_html__( '"%s" slug', 'buddypress' ),
											esc_html( _bp_strip_spans_from_title( $primary_nav_item['name'] ) )
										);
										?>
									</label>
								</th>
								<td>
									<input type="text" class="code" name="<?php printf( 'components[%1$d][_bp_component_slugs][%2$s]', absint( $directory_data->id ), esc_attr( $primary_nav_item['rewrite_id'] ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $primary_nav_item['rewrite_id'] ) ) ); ?>" value="<?php echo esc_attr( bp_rewrites_get_slug( $component_id, $primary_nav_item['rewrite_id'], $primary_nav_item['slug'] ) ); ?>">
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>

				<?php if ( 'groups' === $component_id ) : ?>
					<h4><?php esc_html_e( 'Single Group navigation slugs', 'buddypress' ); ?></h4>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label style="margin-left: 2em; display: inline-block; vertical-align: middle" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $primary_nav_item['rewrite_id'] ) ) ); ?>">
									@todo!
								</label>
							</th>
							<td></td>
						</tr>
					</table>
				<?php endif; ?>

			<?php endforeach; ?>

			<p class="submit clear">
				<input class="button-primary" type="submit" name="bp-admin-rewrites-submit" id="bp-admin-rewrites-submit" value="<?php esc_attr_e( 'Save Settings', 'buddypress' ); ?>"/>
			</p>

			<?php wp_nonce_field( 'bp-admin-rewrites-setup' ); ?>

		</form>
	</div>

	<?php
}

/**
 * Handle saving of the BuddyPress customizable slugs.
 *
 * @since ?.0.0
 */
function bp_core_admin_rewrites_setup_handler() {
	if ( ! isset( $_POST['bp-admin-rewrites-submit'] ) ) {
		return;
	}

	check_admin_referer( 'bp-admin-rewrites-setup' );

	$base_url = bp_get_admin_url( add_query_arg( 'page', 'bp-rewrites-settings', 'admin.php' ) );

	if ( ! isset( $_POST['components'] ) ) {
		wp_safe_redirect( add_query_arg( 'error', 'true', $base_url ) );
	}

	$current_page_slugs   = wp_list_pluck( bp_core_get_directory_pages(), 'slug', 'id' );
	$directory_slug_edits = array();

	$components = wp_unslash( $_POST['components'] ); // phpcs:ignore
	foreach ( $components as $page_id => $slugs ) {
		$postarr = array();

		if ( ! isset( $current_page_slugs[ $page_id ] ) ) {
			continue;
		}

		$postarr['ID'] = $page_id;

		if ( $current_page_slugs[ $page_id ] !== $slugs['post_name'] ) {
			$directory_slug_edits[] = $page_id;
			$postarr['post_name']   = $slugs['post_name'];
		}

		if ( isset( $slugs['_bp_component_slugs'] ) && is_array( $slugs['_bp_component_slugs'] ) ) {
			$postarr['meta_input']['_bp_component_slugs'] = array_map( 'sanitize_title', $slugs['_bp_component_slugs'] );
		}

		wp_update_post( $postarr );
	}

	// Make sure the WP rewrites will be regenarated at next page load.
	if ( $directory_slug_edits ) {
		bp_delete_rewrite_rules();
	}

	wp_safe_redirect( add_query_arg( 'updated', 'true', $base_url ) );
}
add_action( 'bp_admin_init', __NAMESPACE__ . '\bp_core_admin_rewrites_setup_handler' );
