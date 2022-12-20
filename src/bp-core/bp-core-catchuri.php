<?php
/**
 * BuddyPress Catch URI functions.
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
 * Code to move in `\bp_core_load_template()`.
 *
 * @since 1.0.0
 */
function bp_core_load_template() {
	if ( 'bp_core_pre_load_template' === current_action() || ( 'bp_setup_theme_compat' === current_action() && is_buddypress() ) ) {
		global $wp_query;

		// Check if a BuddyPress component's direcory is set as homepage.
		$wp_query->is_home = bp_is_directory_homepage( \bp_current_component() );
	}
}
add_action( 'bp_core_pre_load_template', __NAMESPACE__ . '\bp_core_load_template' );
add_action( 'bp_setup_theme_compat', __NAMESPACE__ . '\bp_core_load_template' );

/**
 * Get a specific BuddyPress URI segment based on the current URI.
 *
 * You shouldn't really have to use this function unless you need to find a BP
 * URI segment earlier than the `'bp_parse_query'` action.
 *
 * @since 1.0.0
 *
 * @param array $bp_global An array containing the BuddyPress global name. Required.
 * @return mixed|false The global value if found. False otherwise.
 */
function bp_core_get_from_uri( $bp_global = array() ) {
	// Don't do this on non-root blogs unless multiblog mode is on.
	if ( ! bp_is_root_blog() && ! bp_is_multiblog_mode() ) {
		return false;
	}

	$bp         = buddypress();
	$backcompat = bp_rewrites()->backcompat;

	if ( ! is_array( $bp_global ) ) {
		$bp_global = array( $bp_global );
	}

	$main_key = array_shift( $bp_global );

	// Get existing BuddyPress URI if already calculated.
	if ( isset( $bp->unfiltered_uri ) && $bp->unfiltered_uri ) {
		$bp_uri = $bp->unfiltered_uri;

		// calculate the BuddyPress URI.
	} elseif ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$requested_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		/**
		 * Filters the BuddyPress global URI path.
		 *
		 * @since 1.0.0
		 *
		 * @param string $path Path to set.
		 */
		$path = apply_filters( 'bp_uri', $requested_uri );

		// Get the regular site path.
		$site_path = trim( bp_core_get_site_path(), '/' );

		// Get the multisite subdirectory install site path.
		if ( is_multisite() && ! is_subdomain_install() ) {
			$current_blog = get_site();
			$site_path    = trim( $current_blog->path, '/' );
		}

		// strip site path from URI.
		$path   = str_replace( $site_path, '', $path );
		$bp_uri = explode( '/', trim( wp_parse_url( $path, PHP_URL_PATH ), '/' ) );

		if ( 'unfiltered_uri' === $main_key ) {
			return $bp_uri;
		}

		// save for later.
		$bp->unfiltered_uri = $bp_uri;
	} else {
		return false;
	}

	if ( ! isset( $backcompat[ $main_key ] ) || is_null( $backcompat[ $main_key ] ) ) {
		if ( ! isset( $bp->pages ) || ! $bp->pages ) {
			/*
			 * As `\bp_groups_user_can_filter` is triggered way to early, we need to make
			 * sure the `'bp_restricted'` status is registered to fetch potential directory pages
			 * using this status.
			 */
			$is_restricted_status_registered = get_post_status_object( 'bp_restricted' );
			if ( ! $is_restricted_status_registered ) {
				bp_rewrites_register_post_status();
			}

			$bp->pages = bp_core_get_directory_pages();
		}

		foreach ( $bp->pages as $component_id => $component ) {
			$bp_component_index = array_search( $component->slug, $bp_uri, true );

			if ( 0 === $bp_component_index ) {
				$backcompat['current_component'] = $component_id;
				array_shift( $bp_uri );

				if ( ! $bp_uri ) {
					break;
				}

				$backcompat['current_action'] = reset( $bp_uri );

				// Let's take care of the Members component.
				if ( 'members' === $component_id ) {
					// Reset the current component.
					$backcompat['current_component'] = false;

					// Try to get a potiential member.
					$member_data = bp_rewrites_get_member_data();
					$member      = get_user_by( $member_data['field'], $backcompat['current_action'] );

					if ( $member instanceof \WP_User ) {
						// Reset the current action.
						$backcompat['current_action'] = '';

						// Set the displayed user.
						$backcompat['displayed_user'] = (object) array(
							'id'       => $member->ID,
							'userdata' => bp_core_get_core_userdata( $member->ID ),
							'fullname' => $member->display_name,
							'domain'   => bp_member_rewrites_get_url( $member->ID, bp_core_get_username( $member->ID ) ),
						);

						// Check if the user has a custom front.
						$backcompat['displayed_user']->front_template = bp_displayed_user_get_front_template( $backcompat['displayed_user'] );

						array_shift( $bp_uri );

						if ( ! $bp_uri ) {
							if ( $backcompat['displayed_user']->front_template ) {
								$backcompat['current_component'] = 'front';
							} elseif ( bp_is_active( 'activity' ) ) {
								$backcompat['current_component'] = $bp->activity->id;
							} else {
								$backcompat['current_component'] = ( 'xprofile' === $bp->profile->id ) ? 'profile' : $bp->profile->id;
							}
						} else {
							$member_component_slug       = reset( $bp_uri );
							$member_component_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'members', $member_component_slug );
							if ( $member_component_rewrite_id ) {
								$member_component_slug = str_replace( 'bp_member_', '', $member_component_rewrite_id );
							}

							$backcompat['current_component'] = $member_component_slug;
							array_shift( $bp_uri );

							if ( $bp_uri ) {
								$backcompat['current_action'] = reset( $bp_uri );
								array_shift( $bp_uri );

								// All the rest is action variables.
								$backcompat['action_variables'] = $bp_uri;
							}
						}
					} else {
						array_shift( $bp_uri );

						if ( $bp_uri && bp_get_members_member_type_base() === $backcompat['current_action'] ) {
							$member_type_slug = reset( $bp_uri );

							$member_type = bp_get_member_types(
								array(
									'has_directory'  => true,
									'directory_slug' => $member_type_slug,
								)
							);

							if ( $member_type ) {
								$backcompat['current_action']      = '';
								$backcompat['current_member_type'] = reset( $member_type );
							}
						}
					}

					// Let's take care of the Groups component.
				} elseif ( 'groups' === $component_id && bp_is_active( 'groups' ) ) {
					// The backcompat global is not set yet. Let's make it true temporarly!
					$restore_current_component = $bp->current_component;
					$bp->current_component     = 'groups';

					/*
					 * Make sure the Groups Component globals are set.
					 *
					 * This is needed because we are setting **ALL** BuddyPress globals into the
					 * backcompat one as soon as one of the globals is requested. When checking
					 * early one of these globals, the Groups Component globals might not be set
					 * yet. E.g. : when BP_Core is using `bp_update_is_item_admin();` it's the
					 * case for instance as `bp_user_has_access()` is checking `bp_is_my_profile()`
					 * which uses `bp_displayed_user_id()`.
					 */
					if ( ! isset( $bp->groups->table_name, $bp->groups->table_name_groupmeta ) ) {
						$bp->groups->setup_globals();
					}

					$current_group = $bp->groups->set_current_group( $backcompat['current_action'], true );

					// Restore the BP global.
					$bp->current_component = $restore_current_component;

					if ( isset( $current_group->id ) && $current_group->id ) {
						$backcompat['current_item']  = $backcompat['current_action'];
						$backcompat['current_group'] = $current_group;

						// Remove the current Group's slug.
						array_shift( $bp_uri );

						if ( ! $bp_uri ) {
							$backcompat['current_action'] = '';
						} else {
							$context        = 'bp_group_read_';
							$current_action = array_shift( $bp_uri );

							// Get the rewrite ID corresponfing to the custom slug.
							$current_action_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $current_action, $context );
							if ( $current_action_rewrite_id ) {
								$current_action = str_replace( $context, '', $current_action_rewrite_id );
								$current_action = str_replace( '_', '-', $current_action );
							}

							$backcompat['current_action'] = $current_action;
							$action_variables             = $bp_uri;

							if ( $action_variables ) {
								// In the Manage context, we need to translate custom slugs to BP Expected variables.
								if ( 'admin' === $current_action ) {
									$context = 'bp_group_manage_';

									// Get the rewrite ID corresponfing to the custom slug.
									$first_action_variable_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $action_variables[0], $context );

									if ( $first_action_variable_rewrite_id ) {
										$first_action_variable = str_replace( $context, '', $first_action_variable_rewrite_id );

										// Make sure the action is stored as a slug: underscores need to be replaced by dashes.
										$action_variables[0] = str_replace( '_', '-', $first_action_variable );
									}
								}
							}

							$backcompat['action_variables'] = $action_variables;
						}
					} elseif ( isset( $bp_uri[1] ) && bp_get_groups_group_type_base() === $backcompat['current_action'] ) {
						$group_type_slug = array_pop( $bp_uri );

						$group_type = bp_groups_get_group_types(
							array(
								'has_directory'  => true,
								'directory_slug' => $group_type_slug,
							)
						);

						if ( $group_type ) {
							$backcompat['current_directory_type'] = reset( $group_type );
							$backcompat['action_variables']       = array( $group_type_slug );
						}
					} elseif ( bp_rewrites_get_slug( 'groups', 'bp_group_create', 'create' ) === $backcompat['current_action'] ) {
						$backcompat['current_action'] = 'create';

						array_shift( $bp_uri );
						$action_variables = $bp_uri;

						if ( isset( $action_variables[1] ) ) {
							$context = 'bp_group_create_';

							// Get the rewrite ID corresponfing to the custom slug.
							$second_action_variable_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $action_variables[1], $context );
							if ( $second_action_variable_rewrite_id ) {
								$second_action_variable = str_replace( $context, '', $second_action_variable_rewrite_id );
								$action_variables[0]    = 'step';
								$action_variables[1]    = str_replace( '_', '-', $second_action_variable );
							}
						}

						$backcompat['action_variables'] = $action_variables;
					}
				} else {
					array_shift( $bp_uri );
					$backcompat['action_variables'] = $bp_uri;
				}
			}
		}

		foreach ( $backcompat as $key_compat => $value_compat ) {
			if ( ! is_null( $value_compat ) ) {
				continue;
			}

			$backcompat[ $key_compat ] = false;
		}

		bp_rewrites()->backcompat = $backcompat;
	}

	if ( ! isset( $backcompat[ $main_key ] ) ) {
		return false;
	}

	// Most of the BuddyPress globals.
	$retval = $backcompat[ $main_key ];

	if ( $bp_global ) {
		$sub_key = array_shift( $bp_global );
		$retval  = false;

		// e.g.: specific case of the displayed user ID.
		if ( isset( $backcompat[ $main_key ]->{$sub_key} ) ) {
			$retval = $backcompat[ $main_key ]->{$sub_key};
		}
	}

	return $retval;
}
