<?php
/**
 * Groups: Create actions.
 *
 * @package buddypress\bp-groups\actions
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\groups_action_create_group()` needs to be edited to allow slug customization and use BP Rewrites.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function groups_action_create_group() {

	// If we're not at domain.org/groups/create/ then return false.
	if ( ! bp_is_groups_component() || ! bp_is_current_action( 'create' ) ) {
		return false;
	}

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( ! bp_user_can_create_groups() ) {
		bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'bp-rewrites' ), 'error' );
		bp_core_redirect( bp_groups_rewrites_get_url() );
	}

	$bp = buddypress();

	// Make sure creation steps are in the right order.
	\groups_action_sort_creation_steps();

	// Use the first action variable to set the creation step.
	$bp->groups->current_create_step = bp_action_variable( 1 );

	// Allow plugins to filter this step.
	$current_create_step = bp_get_groups_current_create_step();

	// If no current step is set, reset everything so we can start a fresh group creation.
	if ( ! $current_create_step ) {
		unset( $bp->groups->current_create_step );
		unset( $bp->groups->completed_create_steps );

		setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

		$reset_steps = true;
		$step        = array_shift( $bp->groups->group_creation_steps );
		$first_step  = bp_rewrites_get_slug( 'groups', $step['rewrite_id'], $step['default_slug'] );

		bp_core_redirect( bp_get_group_create_link( $first_step ) );
	}

	// If this is a creation step that is not recognized, just redirect them back to the first screen.
	if ( $current_create_step && empty( $bp->groups->group_creation_steps[ $current_create_step ] ) ) {
		bp_core_add_message( __( 'There was an error saving group details. Please try again.', 'bp-rewrites' ), 'error' );
		bp_core_redirect( bp_get_group_create_link() );
	}

	// Fetch the currently completed steps variable.
	if ( isset( $_COOKIE['bp_completed_create_steps'] ) && ! isset( $reset_steps ) ) {
		$bp->groups->completed_create_steps = json_decode( base64_decode( stripslashes( $_COOKIE['bp_completed_create_steps'] ) ) ); // phpcs:ignore
	}

	// Set the ID of the new group, if it has already been created in a previous step.
	if ( bp_get_new_group_id() ) {
		$bp->groups->current_group = groups_get_group( $bp->groups->new_group_id );

		// Only allow the group creator to continue to edit the new group.
		if ( ! bp_is_group_creator( $bp->groups->current_group, bp_loggedin_user_id() ) ) {
			bp_core_add_message( __( 'Only the group creator may continue editing this group.', 'bp-rewrites' ), 'error' );
			bp_core_redirect( bp_get_group_create_link() );
		}
	}

	// If the save, upload or skip button is hit, lets calculate what we need to save.
	if ( isset( $_POST['save'] ) ) {

		// Get the create step.
		$current_create_step = bp_get_groups_current_create_step();
		$current_step_info   = $bp->groups->group_creation_steps[ $current_create_step ];

		// Use customizable slugs if available.
		if ( isset( $current_step_info['rewrite_id'], $current_step_info['default_slug'] ) ) {
			$current_step_slug = bp_rewrites_get_slug( 'groups', $current_step_info['rewrite_id'], $current_step_info['default_slug'] );
		} else {
			$current_step_slug = $current_create_step;
		}

		// Check the nonce.
		check_admin_referer( 'groups_create_save_' . bp_get_groups_current_create_step() );

		if ( 'group-details' === $current_create_step ) {
			if ( empty( $_POST['group-name'] ) || empty( $_POST['group-desc'] ) || ! strlen( trim( $_POST['group-name'] ) ) || ! strlen( trim( $_POST['group-desc'] ) ) ) { // phpcs:ignore
				bp_core_add_message( __( 'Please fill in all of the required fields', 'bp-rewrites' ), 'error' );
				bp_core_redirect( bp_get_group_create_link( $current_step_slug ) );
			}

			$new_group_id = 0;
			if ( isset( $bp->groups->new_group_id ) ) {
				$new_group_id = $bp->groups->new_group_id;
			}

			$new_group_name = sanitize_text_field( wp_unslash( $_POST['group-name'] ) );
			$new_group_slug = sanitize_title( $new_group_name );
			$new_group_desc = sanitize_textarea_field( wp_unslash( $_POST['group-desc'] ) );

			$bp->groups->new_group_id = groups_create_group(
				array(
					'group_id'     => $new_group_id,
					'name'         => $new_group_name,
					'description'  => $new_group_desc,
					'slug'         => groups_check_slug( $new_group_slug ),
					'date_created' => bp_core_current_time(),
					'status'       => 'public',
				)
			);

			if ( ! $bp->groups->new_group_id ) {
				bp_core_add_message( __( 'There was an error saving group details. Please try again.', 'bp-rewrites' ), 'error' );
				bp_core_redirect( bp_get_group_create_link( $current_step_slug ) );
			}
		}

		if ( 'group-settings' === $current_create_step ) {
			$group_status       = 'public';
			$group_enable_forum = 1;

			if ( ! isset( $_POST['group-show-forum'] ) ) {
				$group_enable_forum = 0;
			}

			if ( isset( $_POST['group-status'] ) ) {
				$posted_group_status = sanitize_text_field( wp_unslash( $_POST['group-status'] ) );
				if ( 'private' === $posted_group_status || 'hidden' === $posted_group_status ) {
					$group_status = $posted_group_status;
				}
			}

			$bp->groups->new_group_id = groups_create_group(
				array(
					'group_id'     => $bp->groups->new_group_id,
					'status'       => $group_status,
					'enable_forum' => $group_enable_forum,
				)
			);

			if ( ! $bp->groups->new_group_id ) {
				bp_core_add_message( __( 'There was an error saving group details. Please try again.', 'bp-rewrites' ), 'error' );
				bp_core_redirect( bp_get_group_create_link( $current_step_slug ) );
			}

			// Save group types.
			if ( ! empty( $_POST['group-types'] ) ) {
				$group_types = array_map( 'sanitize_text_field', wp_unslash( $_POST['group-types'] ) );
				bp_groups_set_group_type( $bp->groups->new_group_id, $group_types );
			}

			/**
			 * Filters the allowed invite statuses.
			 *
			 * @since 1.5.0
			 *
			 * @param array $value Array of statuses allowed.
			 *                     Possible values are 'members, 'mods', and 'admins'.
			 */
			$allowed_invite_status = (array) apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );

			// Default invite status.
			$invite_status = 'members';
			if ( ! empty( $_POST['group-invite-status'] ) ) {
				$submitted_invite_status = sanitize_text_field( wp_unslash( $_POST['group-invite-status'] ) );

				if ( in_array( $submitted_invite_status, $allowed_invite_status, true ) ) {
					$invite_status = $submitted_invite_status;
				}
			}

			groups_update_groupmeta( $bp->groups->new_group_id, 'invite_status', $invite_status );
		}

		if ( 'group-invites' === $current_create_step ) {
			if ( ! empty( $_POST['friends'] ) ) {
				$friends = array_map( 'intval', wp_unslash( $_POST['friends'] ) );

				foreach ( (array) $friends as $friend ) {
					groups_invite_user(
						array(
							'user_id'  => $friend,
							'group_id' => $bp->groups->new_group_id,
						)
					);
				}
			}

			groups_send_invites(
				array(
					'group_id' => $bp->groups->new_group_id,
				)
			);
		}

		/**
		 * Fires before finalization of group creation and cookies are set.
		 *
		 * This hook is a variable hook dependent on the current step
		 * in the creation process.
		 *
		 * @since 1.1.0
		 */
		do_action( 'groups_create_group_step_save_' . $current_create_step );

		/**
		 * Fires after the group creation step is completed.
		 *
		 * Mostly for clearing cache on a generic action name.
		 *
		 * @since 1.1.0
		 */
		do_action( 'groups_create_group_step_complete' );

		/**
		 * Once we have successfully saved the details for this step of the creation process
		 * we need to add the current step to the array of completed steps, then update the cookies
		 * holding the information
		 */
		$completed_create_steps = array();
		if ( isset( $bp->groups->completed_create_steps ) ) {
			$completed_create_steps = $bp->groups->completed_create_steps;
		}

		if ( ! in_array( $current_create_step, $completed_create_steps, true ) ) {
			$bp->groups->completed_create_steps[] = $current_create_step;
		}

		// Reset cookie info.
		setcookie( 'bp_new_group_id', $bp->groups->new_group_id, time() + 60 * 60 * 24, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		setcookie( 'bp_completed_create_steps', base64_encode( json_encode( $bp->groups->completed_create_steps ) ), time() + 60 * 60 * 24, COOKIEPATH, COOKIE_DOMAIN, is_ssl() ); // phpcs:ignore

		// If we have completed all steps and hit done on the final step we
		// can redirect to the completed group.
		$keys = array_keys( $bp->groups->group_creation_steps );
		if ( count( $keys ) === count( $bp->groups->completed_create_steps ) && array_pop( $keys ) === $current_create_step ) {
			unset( $bp->groups->current_create_step );
			unset( $bp->groups->completed_create_steps );

			setcookie( 'bp_new_group_id', false, time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
			setcookie( 'bp_completed_create_steps', false, time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

			// Once we completed all steps, record the group creation in the activity stream.
			if ( bp_is_active( 'activity' ) ) {
				groups_record_activity(
					array(
						'type'    => 'created_group',
						'item_id' => $bp->groups->new_group_id,
					)
				);
			}

			/**
			 * Fires after the group has been successfully created.
			 *
			 * @since 1.1.0
			 *
			 * @param int $new_group_id ID of the newly created group.
			 */
			do_action( 'groups_group_create_complete', $bp->groups->new_group_id );

			bp_core_redirect( bp_group_rewrites_get_url( $bp->groups->current_group ) );
		} else {
			/**
			 * Since we don't know what the next step is going to be (any plugin can insert steps)
			 * we need to loop the step array and fetch the next step that way.
			 */
			foreach ( $keys as $key ) {
				if ( $key === $current_create_step ) {
					$next = 1;
					continue;
				}

				if ( isset( $next ) ) {
					$next_step = $key;
					break;
				}
			}

			// Set the next step slug.
			$next_step_info = array();
			if ( isset( $bp->groups->group_creation_steps[ $next_step ] ) ) {
				$next_step_info = $bp->groups->group_creation_steps[ $next_step ];
			}

			$next_step_slug = $next_step;

			// Use customizable slugs if available.
			if ( isset( $next_step_info['rewrite_id'], $next_step_info['default_slug'] ) ) {
				$next_step_slug = bp_rewrites_get_slug( 'groups', $next_step_info['rewrite_id'], $next_step_info['default_slug'] );
			}

			bp_core_redirect( bp_get_group_create_link( $next_step_slug ) );
		}
	}

	// Remove invitations.
	if ( 'group-invites' === $current_create_step && ! empty( $_REQUEST['user_id'] ) && is_numeric( $_REQUEST['user_id'] ) ) {
		if ( ! check_admin_referer( 'groups_invite_uninvite_user' ) ) {
			return false;
		}

		$message   = __( 'Invite successfully removed', 'bp-rewrites' );
		$error     = false;
		$user_id   = intval( wp_unslash( $_REQUEST['user_id'] ) );
		$uninvited = groups_uninvite_user( $user_id, $bp->groups->new_group_id );

		if ( ! $uninvited ) {
			$message = __( 'There was an error removing the invite', 'bp-rewrites' );
			$error   = 'error';
		}

		bp_core_add_message( $message, $error );
		bp_core_redirect( bp_get_group_create_link( $current_step_slug ) );
	}

	// Group avatar is handled separately.
	if ( 'group-avatar' === $current_create_step && isset( $_POST['upload'] ) ) {
		if ( ! isset( $bp->avatar_admin ) ) {
			$bp->avatar_admin = new stdClass();
		}

		if ( ! empty( $_FILES ) && isset( $_POST['upload'] ) ) {
			// Normally we would check a nonce here, but the group save nonce is used instead.
			// Pass the file to the avatar upload handler.
			if ( bp_core_avatar_handle_upload( $_FILES, 'groups_avatar_upload_dir' ) ) {
				$bp->avatar_admin->step = 'crop-image';

				// Make sure we include the jQuery jCrop file for image cropping.
				add_action( 'wp_print_scripts', 'bp_core_add_jquery_cropper' );
			}
		}

		// If the image cropping is done, crop the image and save a full/thumb version.
		if ( isset( $_POST['avatar-crop-submit'] ) && isset( $_POST['upload'] ) && isset( $_POST['image_src'] ) ) {

			// Normally we would check a nonce here, but the group save nonce is used instead.
			$args = array(
				'object'        => 'group',
				'avatar_dir'    => 'group-avatars',
				'item_id'       => $bp->groups->current_group->id,
				'original_file' => esc_url_raw( wp_unslash( $_POST['image_src'] ) ),
				'crop_x'        => ! isset( $_POST['x'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['x'] ) ),
				'crop_y'        => ! isset( $_POST['y'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['y'] ) ),
				'crop_w'        => ! isset( $_POST['w'] ) ? bp_core_avatar_full_width() : sanitize_text_field( wp_unslash( $_POST['w'] ) ),
				'crop_h'        => ! isset( $_POST['h'] ) ? bp_core_avatar_full_height() : sanitize_text_field( wp_unslash( $_POST['h'] ) ),
			);

			$cropped_avatar = bp_core_avatar_handle_crop( $args, 'array' );

			if ( ! $cropped_avatar ) {
				bp_core_add_message( __( 'There was an error saving the group profile photo, please try uploading again.', 'bp-rewrites' ), 'error' );
			} else {
				/**
				 * Fires after a group avatar is uploaded.
				 *
				 * @since 2.8.0
				 * @since 10.0.0 Adds a new param: an array containing the full, thumb avatar and the timestamp.
				 *
				 * @param int    $group_id       ID of the group.
				 * @param string $type           Avatar type. 'crop' or 'camera'.
				 * @param array  $args           Array of parameters passed to the crop handler.
				 * @param array  $cropped_avatar Array containing the full, thumb avatar and the timestamp.
				 */
				do_action( 'groups_avatar_uploaded', bp_get_current_group_id(), 'crop', $args, $cropped_avatar );

				bp_core_add_message( __( 'The group profile photo was uploaded successfully.', 'bp-rewrites' ) );
			}
		}
	}

	/**
	 * Filters the template to load for the group creation screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the group creation template to load.
	 */
	\bp_core_load_template( apply_filters( 'groups_template_create_group', 'groups/create' ) );
}
add_action( 'bp_actions', __NAMESPACE__ . '\groups_action_create_group' );

/**
 * \groups_action_sort_creation_steps() needs to include the `rewrite_id` piece of info.
 *
 * @since 1.0.0
 */
function groups_action_sort_creation_steps() {
	$bp    = buddypress();
	$views = bp_get_group_views( 'create' );

	foreach ( $bp->groups->group_creation_steps as $step_id => $step_info ) {
		if ( isset( $views[ $step_id ] ) ) {
			$bp->groups->group_creation_steps[ $step_id ]['rewrite_id']   = $views[ $step_id ]['rewrite_id'];
			$bp->groups->group_creation_steps[ $step_id ]['default_slug'] = $views[ $step_id ]['slug'];
		}
	}
}
add_action( 'groups_action_sort_creation_steps', __NAMESPACE__ . '\groups_action_sort_creation_steps', 1 );
