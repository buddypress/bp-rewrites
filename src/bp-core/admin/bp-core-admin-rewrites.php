<?php
/**
 * BuddyPress Rewrites Admin Functions.
 *
 * @package buddypress\bp-core\admin
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders BuddyPress URLs admin panel.
 *
 * @since 1.0.0
 */
function bp_core_admin_rewrites_settings() {
	$bp              = buddypress();
	$bp_pages        = $bp->pages;
	$reordered_pages = array();

	if ( isset( $bp_pages->register ) ) {
		$reordered_pages['register'] = $bp_pages->register;
		unset( $bp_pages->register );
	}

	if ( isset( $bp_pages->activate ) ) {
		$reordered_pages['activate'] = $bp_pages->activate;
		unset( $bp_pages->activate );
	}

	if ( $reordered_pages ) {
		foreach ( $reordered_pages as $page_key => $reordered_page ) {
			$bp_pages->{$page_key} = $reordered_page;
		}
	}

	$bp_members_primary_nav_items = array();

	bp_core_admin_tabbed_screen_header( __( 'BuddyPress Settings', 'bp-rewrites' ), __( 'URLs', 'bp-rewrites' ) );
	?>
	<div class="buddypress-body">
		<div class="health-check-body">
			<form action="" method="post" id="bp-admin-rewrites-form">
			<?php foreach ( $bp->pages as $component_id => $directory_data ) : ?>
				<div class="site-health-issues-wrapper">
					<h3>
						<?php
						if ( isset( $bp->{$component_id}->name ) && $bp->{$component_id}->name ) {
							echo esc_html( $bp->{$component_id}->name );
						} else {
							echo esc_html( $directory_data->title );
						}
						?>
					</h3>

					<div class="health-check-accordion">
						<h4 class="health-check-accordion-heading">
							<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php echo esc_attr( $component_id ); ?>-directory" type="button">
								<span class="title"><?php esc_html_e( 'Directory', 'bp-rewrites' ); ?></span>
								<span class="icon"></span>
							</button>
						</h4>
						<div id="health-check-accordion-block-<?php echo esc_attr( $component_id ); ?>-directory" class="health-check-accordion-panel" hidden="hidden">
							<table class="form-table" role="presentation">
								<tr>
									<th scope="row">
										<label for="<?php echo esc_attr( sprintf( '%s-directory-title', sanitize_key( $component_id ) ) ); ?>">
											<?php esc_html_e( 'Directory title', 'bp-rewrites' ); ?>
										</label>
									</th>
									<td>
										<input type="text" class="code" name="<?php printf( 'components[%d][post_title]', absint( $directory_data->id ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-directory-title', sanitize_key( $component_id ) ) ); ?>" value="<?php echo esc_attr( $directory_data->title ); ?>">
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="<?php echo esc_attr( sprintf( '%s-directory-slug', sanitize_key( $component_id ) ) ); ?>">
											<?php esc_html_e( 'Directory slug', 'bp-rewrites' ); ?>
										</label>
									</th>
									<td>
										<input type="text" class="code" name="<?php printf( 'components[%d][post_name]', absint( $directory_data->id ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-directory-slug', sanitize_key( $component_id ) ) ); ?>" value="<?php echo esc_attr( $directory_data->slug ); ?>">
									</td>
								</tr>
							</table>
						</div>
					</div>

				<?php if ( 'members' === $component_id ) : ?>
					<div class="health-check-accordion">
						<h4 class="health-check-accordion-heading">
							<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-member-primary-nav" type="button">
								<span class="title"><?php esc_html_e( 'Single Member primary views slugs', 'bp-rewrites' ); ?></span>
								<span class="icon"></span>
							</button>
						</h4>
						<div id="health-check-accordion-block-member-primary-nav" class="health-check-accordion-panel" hidden="hidden">
							<table class="form-table" role="presentation">
								<?php
								foreach ( $bp->members->nav->get_primary() as $primary_nav_item ) :
									if ( ! isset( $primary_nav_item['rewrite_id'] ) || ! $primary_nav_item['rewrite_id'] ) {
										continue;
									}

									$bp_members_primary_nav_items[ $primary_nav_item['slug'] ] = $primary_nav_item['name'];
									?>
									<tr>
										<th scope="row">
											<label class="bp-nav-slug" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $primary_nav_item['rewrite_id'] ) ) ); ?>">
												<?php
												printf(
													/* translators: %s is the member primary view name */
													esc_html_x( '"%s" slug', 'member primary view name URL admin label', 'bp-rewrites' ),
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
						</div>
					</div>

					<?php foreach ( $bp_members_primary_nav_items as $bp_members_primary_nav_slug => $bp_members_primary_nav_name ) : ?>
						<div class="health-check-accordion">
							<h4 class="health-check-accordion-heading">
								<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="<?php echo esc_attr( sprintf( 'health-check-accordion-block-member-%s-secondary-nav', $bp_members_primary_nav_slug ) ); ?>" type="button">
									<?php /* translators: %s is the BP Component name the secondery views belong to. */ ?>
									<span class="title"><?php echo esc_html( sprintf( __( 'Single Member %s secondary views slugs', 'bp-rewrites' ), _bp_strip_spans_from_title( $bp_members_primary_nav_name ) ) ); ?></span>
									<span class="icon"></span>
								</button>
							</h4>
							<div id="<?php echo esc_attr( sprintf( 'health-check-accordion-block-member-%s-secondary-nav', $bp_members_primary_nav_slug ) ); ?>" class="health-check-accordion-panel" hidden="hidden">
								<table class="form-table" role="presentation">

										<?php
										foreach ( $bp->members->nav->get_secondary( array( 'parent_slug' => $bp_members_primary_nav_slug ) ) as $secondary_nav_item ) :
											if ( ! isset( $secondary_nav_item['rewrite_id'] ) || ! $secondary_nav_item['rewrite_id'] ) {
												continue;
											}
											?>
											<tr>
												<th scope="row">
													<label class="bp-nav-slug" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $secondary_nav_item['rewrite_id'] ) ) ); ?>">
														<?php
														printf(
															/* translators: %s is the member secondary view name */
															esc_html_x( '"%s" slug', 'member secondary view name URL admin label', 'bp-rewrites' ),
															esc_html( _bp_strip_spans_from_title( $secondary_nav_item['name'] ) )
														);
														?>
													</label>
												</th>
												<td>
													<input type="text" class="code" name="<?php printf( 'components[%1$d][_bp_component_slugs][%2$s]', absint( $directory_data->id ), esc_attr( $secondary_nav_item['rewrite_id'] ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $secondary_nav_item['rewrite_id'] ) ) ); ?>" value="<?php echo esc_attr( bp_rewrites_get_slug( $component_id, $secondary_nav_item['rewrite_id'], $secondary_nav_item['slug'] ) ); ?>">
												</td>
											</tr>
										<?php endforeach; ?>
								</table>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( 'groups' === $component_id ) : ?>

					<?php
					foreach (
						array(
							'create' => __( 'Single Group Creation steps slugs', 'bp-rewrites' ),
							'read'   => __( 'Single Group Member views slugs', 'bp-rewrites' ),
							'manage' => __( 'Single Group Admin views slugs', 'bp-rewrites' ),
						) as $view_type => $view_type_title ) :
						?>

						<div class="health-check-accordion">
							<h4 class="health-check-accordion-heading">
								<button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-group-<?php echo esc_attr( $view_type ); ?>" type="button">
									<span class="title"><?php echo esc_html( $view_type_title ); ?></span>
									<span class="icon"></span>
								</button>
							</h4>
							<div id="health-check-accordion-block-group-<?php echo esc_attr( $view_type ); ?>" class="health-check-accordion-panel" hidden="hidden">
								<table class="form-table" role="presentation">

								<?php
								if ( 'create' === $view_type ) :
									foreach ( bp_get_group_restricted_views() as $group_create_restricted_view ) :
										?>
										<tr>
											<th scope="row">
												<label style="margin-left: 2em; display: inline-block; vertical-align: middle" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $group_create_restricted_view['rewrite_id'] ) ) ); ?>">
													<?php echo esc_html( $group_create_restricted_view['name'] ); ?>
												</label>
											</th>
											<td>
												<input type="text" class="code" name="<?php printf( 'components[%1$d][_bp_component_slugs][%2$s]', absint( $directory_data->id ), esc_attr( $group_create_restricted_view['rewrite_id'] ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $group_create_restricted_view['rewrite_id'] ) ) ); ?>" value="<?php echo esc_attr( bp_rewrites_get_slug( $component_id, $group_create_restricted_view['rewrite_id'], $group_create_restricted_view['slug'] ) ); ?>">
											</td>
										</tr>
										<?php
									endforeach;

								endif;

								foreach ( bp_get_group_views( $view_type ) as $group_view ) :
									if ( ! isset( $group_view['rewrite_id'] ) || ! $group_view['rewrite_id'] ) {
										continue;
									}
									?>
										<tr>
											<th scope="row">
												<label style="margin-left: 2em; display: inline-block; vertical-align: middle" for="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $group_view['rewrite_id'] ) ) ); ?>">
													<?php
													printf(
														/* translators: %s is group view name */
														esc_html_x( '"%s" slug', 'group view name URL admin label', 'bp-rewrites' ),
														esc_html( _bp_strip_spans_from_title( $group_view['name'] ) )
													);
													?>
												</label>
											</th>
											<td>
												<input type="text" class="code" name="<?php printf( 'components[%1$d][_bp_component_slugs][%2$s]', absint( $directory_data->id ), esc_attr( $group_view['rewrite_id'] ) ); ?>" id="<?php echo esc_attr( sprintf( '%s-slug', sanitize_key( $group_view['rewrite_id'] ) ) ); ?>" value="<?php echo esc_attr( bp_rewrites_get_slug( $component_id, $group_view['rewrite_id'], $group_view['slug'] ) ); ?>">
											</td>
										</tr>
									<?php endforeach; ?>
								</table>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				</div>

				<?php endforeach; ?>

				<p class="submit clear">
					<input class="button-primary" type="submit" name="bp-admin-rewrites-submit" id="bp-admin-rewrites-submit" value="<?php esc_attr_e( 'Save Settings', 'bp-rewrites' ); ?>"/>
				</p>

				<?php wp_nonce_field( 'bp-admin-rewrites-setup' ); ?>

			</form>
		</div>
	</div>
	<?php
}

/**
 * Handle saving of the BuddyPress customizable slugs.
 *
 * @since 1.0.0
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

	$directory_pages     = (array) bp_core_get_directory_pages();
	$current_page_slugs  = wp_list_pluck( $directory_pages, 'slug', 'id' );
	$current_page_titles = wp_list_pluck( $directory_pages, 'title', 'id' );
	$reset_rewrites      = false;

	// Data is sanitized inside the foreach loop.
	$components = wp_unslash( $_POST['components'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	foreach ( $components as $page_id => $posted_data ) {
		$postarr = array();

		if ( ! isset( $current_page_slugs[ $page_id ] ) ) {
			continue;
		}

		$postarr['ID'] = $page_id;

		if ( isset( $posted_data['post_title'] ) ) {
			$post_title = sanitize_text_field( $posted_data['post_title'] );

			if ( $current_page_titles[ $page_id ] !== $post_title ) {
				$postarr['post_title'] = $post_title;
			}
		}

		if ( isset( $posted_data['post_name'] ) ) {
			$post_name = sanitize_text_field( $posted_data['post_name'] );

			if ( $current_page_slugs[ $page_id ] !== $post_name ) {
				$reset_rewrites       = true;
				$postarr['post_name'] = $post_name;
			}
		}

		if ( isset( $posted_data['_bp_component_slugs'] ) && is_array( $posted_data['_bp_component_slugs'] ) ) {
			$postarr['meta_input']['_bp_component_slugs'] = array_map( 'sanitize_title', $posted_data['_bp_component_slugs'] );
		}

		if ( isset( $posted_data['_bp_component_slugs']['bp_group_create'] ) ) {
			$new_current_group_create_slug    = sanitize_text_field( $posted_data['_bp_component_slugs']['bp_group_create'] );
			$current_group_create_custom_slug = '';

			if ( isset( $directory_pages->groups->custom_slugs['bp_group_create'] ) ) {
				$current_group_create_custom_slug = $directory_pages->groups->custom_slugs['bp_group_create'];
			}

			if ( $new_current_group_create_slug !== $current_group_create_custom_slug ) {
				$reset_rewrites = true;
			}
		}

		wp_update_post( $postarr );
	}

	// Make sure the WP rewrites will be regenarated at next page load.
	if ( $reset_rewrites ) {
		bp_delete_rewrite_rules();
	}

	wp_safe_redirect( add_query_arg( 'updated', 'true', $base_url ) );
}
add_action( 'bp_admin_init', __NAMESPACE__ . '\bp_core_admin_rewrites_setup_handler' );
