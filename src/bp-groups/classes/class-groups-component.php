<?php
/**
 * BP Rewrites Groups Component.
 *
 * @package bp-rewrites\src\bp-groups\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Main Groups Class.
 *
 * @since 1.0.0
 */
class Groups_Component extends \BP_Groups_Component {
	/**
	 * Start the groups component setup process.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { /* phpcs:ignore */
		parent::__construct();
	}

	/**
	 * Include Groups component files.
	 *
	 * @since 1.0.0
	 *
	 * @see BP_Component::includes() for a description of arguments.
	 *
	 * @param array $includes See BP_Component::includes() for a description.
	 */
	public function includes( $includes = array() ) {
		// Set includes directory.
		$inc_dir = trailingslashit( bp_rewrites()->dir ) . 'src/bp-groups/';
		require_once $inc_dir . 'classes/class-bp-group-extension.php';

		parent::includes( $includes );
	}

	/**
	 * Late includes method.
	 *
	 * @since 1.0.0
	 */
	public function late_includes() {
		parent::late_includes();

		// Let's override completely this function.
		remove_action( 'bp_actions', 'groups_action_create_group' );

		// Set includes directory.
		$inc_dir = trailingslashit( bp_rewrites()->dir ) . 'src/bp-groups/';

		if ( bp_is_groups_component() && is_user_logged_in() && 'create' === \bp_current_action() ) {
			require $inc_dir . 'actions/create.php';
		}

		if ( bp_is_group() && bp_is_item_admin() && is_user_logged_in() ) {
			require $inc_dir . 'screens/single/admin.php';

			if ( in_array( bp_get_group_current_admin_tab(), array( 'edit-details', 'group-settings', 'manage-members', 'membership-requests' ), true ) ) {
				require $inc_dir . 'screens/single/admin/' . bp_get_group_current_admin_tab() . '.php';
			}
		}
	}

	/**
	 * Set the current Group object using its slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group_slug       The Group slug used into the URL path.
	 * @param bool   $doing_backcompat True if we're running this for backward compatibility.
	 *                                 False otherwise. Default: `false`.
	 * @return int|\BP_Groups_Group The current group found. 0 otherwise.
	 */
	public function set_current_group( $group_slug = '', $doing_backcompat = false ) {
		if ( ! bp_is_groups_component() || ! $group_slug ) {
			return 0;
		}

		// Get the BuddyPress main instance.
		$bp = buddypress();

		// Try to find a group ID matching the requested slug.
		$group_id = \BP_Groups_Group::group_exists( $group_slug );
		if ( ! $group_id ) {
			$group_id = \BP_Groups_Group::get_id_by_previous_slug( $group_slug );
		}

		// The Group was not found?
		if ( ! $group_id ) {
			return 0;
		}

		// Set the single item and init the current group.
		if ( ! $doing_backcompat ) {
			$bp->is_single_item = true;
		}

		$current_group = 0;

		/**
		 * Filters the current PHP Class being used.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Name of the class being used.
		 */
		$current_group_class = apply_filters( 'bp_groups_current_group_class', 'BP_Groups_Group' );

		if ( 'BP_Groups_Group' === $current_group_class ) {
			$current_group = groups_get_group( $group_id );

		} else {

			/**
			 * Filters the current group object being instantiated from previous filter.
			 *
			 * @since 1.5.0
			 *
			 * @param object $value Newly instantiated object for the group.
			 */
			$current_group = apply_filters( 'bp_groups_current_group_object', new $current_group_class( $group_id ) );
		}

		// We have a group let's add some other usefull things.
		if ( $current_group ) {
			$current_group->id = (int) $current_group->id;

			if ( ! $doing_backcompat ) {
				// Using "item" not "group" for generic support in other components.
				if ( bp_current_user_can( 'bp_moderate' ) ) {
					bp_update_is_item_admin( true, 'groups' );
				} else {
					bp_update_is_item_admin( groups_is_user_admin( bp_loggedin_user_id(), $current_group->id ), 'groups' );
				}

				// If the user is not an admin, check if they are a moderator.
				if ( ! bp_is_item_admin() ) {
					bp_update_is_item_mod( groups_is_user_mod( bp_loggedin_user_id(), $current_group->id ), 'groups' );
				}
			}

			// Check once if the current group has a custom front template.
			$current_group->front_template = bp_groups_get_front_template( $current_group );

			if ( ! $doing_backcompat ) {
				// Initialize the nav for the groups component.
				$this->nav = new \BP_Core_Nav( $current_group->id );

				/**
				 * Fires once the `current_group` global is fully set.
				 *
				 * @since 10.0.0
				 *
				 * @param BP_Groups_Group|object $current_group The current group object.
				 */
				do_action_ref_array( 'bp_groups_set_current_group', array( $current_group ) );
			}
		}

		return $current_group;
	}

	/**
	 * Set up additional globals for the component.
	 *
	 * @since 1.0.0
	 */
	public function setup_additional_globals() {
		$bp = buddypress();

		// Set Rewrites globals.
		bp_component_setup_globals(
			array(
				'rewrite_ids' => array(
					'directory'                    => 'bp_groups',
					'directory_type'               => 'bp_groups_type',
					'create_single_item'           => 'bp_group_create',
					'create_single_item_variables' => 'bp_group_create_variables',
					'single_item'                  => 'bp_group',
					'single_item_action'           => 'bp_group_action',
					'single_item_action_variables' => 'bp_group_action_variables',
				),
			),
			$this
		);

		/* Single Group Globals **********************************************/

		/**
		 * Filters the list of illegal groups names/slugs.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of illegal group names/slugs.
		 */
		$this->forbidden_names = apply_filters(
			'groups_forbidden_names',
			array(
				'my-groups',
				'create',
				'invites',
				'send-invites',
				'forum',
				'delete',
				'add',
				'admin',
				'request-membership',
				'members',
				'settings',
				'avatar',
				$this->slug,
				$this->root_slug,
			)
		);

		// Set default Group creation steps.
		$group_creation_steps = bp_get_group_views( 'create' );

		// If avatar uploads are disabled, remove avatar view.
		$disabled_avatar_uploads = (int) bp_disable_group_avatar_uploads();
		if ( $disabled_avatar_uploads || empty( $bp->avatar->show_avatars ) ) {
			unset( $group_creation_steps['group-avatar'] );
		}

		// If cover images are disabled, remove its view.
		if ( ! bp_group_use_cover_image_header() ) {
			unset( $group_creation_steps['group-cover-image'] );
		}

		// If the invitations feature is not active, remove the corresponding view.
		if ( ! bp_is_active( 'groups', 'invitations' ) ) {
			unset( $group_creation_steps['group-invites'] );
		}

		/**
		 * Filters the preconfigured groups creation steps.
		 *
		 * @since 1.1.0
		 *
		 * @param array $group_creation_steps Array of preconfigured group creation steps.
		 */
		$this->group_creation_steps = apply_filters( 'groups_create_group_steps', $group_creation_steps );

		/**
		 * Filters the list of valid groups statuses.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of valid group statuses.
		 */
		$this->valid_status = apply_filters(
			'groups_valid_status',
			array(
				'public',
				'private',
				'hidden',
			)
		);

		// Auto join group when non group member performs group activity.
		$this->auto_join = defined( 'BP_DISABLE_AUTO_GROUP_JOIN' ) && BP_DISABLE_AUTO_GROUP_JOIN ? false : true;
	}

	/**
	 * Set up canonical stack for this component.
	 *
	 * @since 1.0.0
	 */
	public function setup_canonical_stack() {
		parent::setup_canonical_stack();

		$bp = buddypress();

		if ( bp_is_groups_component() && isset( $this->current_group->id ) && $this->current_group->id ) {
			$current_action = \bp_current_action();

			if ( $current_action ) {
				// The action is stored as a slug, Rewrite IDs are keys: dashes need to be replaced by underscores.
				$rewrite_id = 'bp_group_read_' . str_replace( '-', '_', $current_action );

				$bp->canonical_stack['action'] = bp_rewrites_get_slug( 'groups', $rewrite_id, $current_action );

				if ( isset( $bp->action_variables[0] ) ) {
					// Copy the global to leave it unchanged.
					$action_variables = $bp->action_variables;

					// The action variable is stored as a slug, Rewrite IDs are keys: dashes need to be replaced by underscores.
					$first_action_variable_rewrite_id = 'bp_group_manage_' . str_replace( '-', '_', $action_variables[0] );

					// Reset the first action variable with the customized slug.
					$action_variables[0] = bp_rewrites_get_slug( 'groups', $first_action_variable_rewrite_id, $action_variables[0] );

					// Update the canonical stack.
					$bp->canonical_stack['action_variables'] = $action_variables;
				}
			}
		}
	}

	/**
	 * Set up component navigation.
	 *
	 * @since 1.0.0
	 *
	 * @see BP_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See BP_Component::setup_nav() for description.
	 * @param array $sub_nav  Optional. See BP_Component::setup_nav() for description.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) { /* phpcs:ignore */
		// The `$main_nav` needs to be reset.
		add_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'reset_nav' ), 20 );

		// Overrides are needed for the Group's navivation.
		add_action( 'groups_setup_nav', array( $this, 'group_nav_overrides' ) );

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Reset the component's navigation using BP Rewrites.
	 *
	 * @since 1.0.0
	 */
	public function reset_nav() {
		remove_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'reset_nav' ), 20 );

		// Get the BuddyPress Main instance.
		$bp = buddypress();

		// Get the main nav.
		$groups_slug = bp_get_groups_slug();
		$main_nav    = $bp->members->nav->get_primary( array( 'component_id' => $this->id ), false );

		// Make sure the main navigation was built the right way.
		if ( ! is_array( $main_nav ) || ! isset( $main_nav[ $groups_slug ] ) ) {
			return;
		}

		// Set the main nav slug.
		$main_nav = reset( $main_nav );
		$slug     = $main_nav['slug'];

		// Set the main nav `rewrite_id` property.
		$rewrite_id             = sprintf( 'bp_member_%s', $groups_slug );
		$main_nav['rewrite_id'] = $rewrite_id;

		// Reset the link using BP Rewrites.
		$main_nav['link'] = bp_members_rewrites_get_nav_url(
			array(
				'rewrite_id'     => $rewrite_id,
				'item_component' => $slug,
			)
		);

		// Update the primary nav item.
		$bp->members->nav->edit_nav( $main_nav, $slug );

		// Update the secondary nav items.
		reset_secondary_nav( $slug, $rewrite_id );
	}

	/**
	 * Overrides the screen function of Group Admin nav items.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $has_access True if user can access to the Groupe. False otherwise.
	 */
	public function group_nav_overrides( $has_access = false ) {
		remove_action( 'groups_setup_nav', array( $this, 'group_nav_overrides' ) );

		if ( isset( $this->current_group->id ) && $this->current_group->id ) {
			$bp         = buddypress();
			$group_id   = $this->current_group->id;
			$group_link = bp_group_rewrites_get_url( $this->current_group );
			$group_slug = $this->current_group->slug;

			// Get the Group Main Nav.
			$group_main_nav = $bp->groups->nav->get_primary( array( 'slug' => $group_slug ) );
			$group_main_nav = reset( $group_main_nav );

			// Reset the Main Group Nav link.
			$group_main_nav['link'] = $group_link;
			$bp->groups->nav->edit_nav( $group_main_nav, $group_slug );

			// Get the Group Sub Nav.
			$group_sub_nav_items = $bp->groups->nav->get_secondary( array( 'parent_slug' => $group_slug ) );

			// Get the Group views for the `read` context.
			$group_read_views = bp_get_group_views( 'read' );

			// Loop inside it to reset the link using BP Rewrites before updating it.
			foreach ( $group_sub_nav_items as $group_sub_nav_item ) {
				$view_slug = $group_sub_nav_item['slug'];

				if ( isset( $group_read_views[ $view_slug ] ) && $group_read_views[ $view_slug ] ) {
					$group_sub_nav_item['rewrite_id'] = $group_read_views[ $view_slug ]['rewrite_id'];
				} else {
					$group_sub_nav_item['rewrite_id'] = '';
				}

				if ( 'home' === $group_sub_nav_item['slug'] ) {
					$group_sub_nav_item['link'] = $group_link;
				} else {
					$group_sub_nav_item['link'] = bp_group_nav_rewrites_get_url( $this->current_group, $group_sub_nav_item['slug'], $group_sub_nav_item['rewrite_id'] );
				}

				// Update the secondary nav item.
				$bp->groups->nav->edit_nav( $group_sub_nav_item, $group_sub_nav_item['slug'], $group_slug );
			}

			// If the user is a group admin, then show the group admin nav item.
			if ( bp_is_item_admin() ) {
				$admin_link = bp_group_admin_rewrites_get_url( $this->current_group );
				$admin_nav  = $bp->groups->nav->get_secondary(
					array(
						'parent_slug' => $this->current_group->slug,
						'slug'        => 'admin',
					),
					false
				);

				// Remove the nav.
				bp_core_remove_subnav_item( $this->current_group->slug, 'admin', 'groups' );

				// Restore the nav with a new screen function.
				$admin_nav = (array) reset( $admin_nav );
				bp_core_new_subnav_item(
					array(
						'name'            => $admin_nav['name'],
						'slug'            => $admin_nav['slug'],
						'parent_url'      => $group_link,
						'parent_slug'     => $this->current_group->slug,
						'screen_function' => __NAMESPACE__ . '\groups_screen_group_admin',
						'position'        => $admin_nav['position'],
						'user_has_access' => $admin_nav['user_has_access'],
						'item_css_id'     => $admin_nav['css_id'],
						'no_access_url'   => $admin_nav['no_access_url'],
						'link'            => $admin_link,
					),
					'groups'
				);

				// Get all Management items.
				$admin_subnav_items = buddypress()->groups->nav->get_secondary(
					array(
						'parent_slug' => $this->current_group->slug . '_manage',
					),
					false
				);

				// Get the Group views for the `manage` context.
				$group_manage_views = bp_get_group_views( 'manage' );

				// Replace all screen functions.
				foreach ( $admin_subnav_items as $admin_subnav_item ) {
					if ( 'groups_screen_group_admin' !== $admin_subnav_item['screen_function'] ) {
						continue;
					}

					bp_core_remove_subnav_item( $this->current_group->slug . '_manage', $admin_subnav_item['slug'], 'groups' );

					$manage_slug = $admin_subnav_item['slug'];
					if ( isset( $group_manage_views[ $manage_slug ] ) && $group_manage_views[ $manage_slug ] ) {
						$admin_subnav_item['rewrite_id'] = $group_manage_views[ $manage_slug ]['rewrite_id'];
					} else {
						$admin_subnav_item['rewrite_id'] = '';
					}

					bp_core_new_subnav_item(
						array(
							'name'              => $admin_subnav_item['name'],
							'slug'              => $admin_subnav_item['slug'],
							'position'          => $admin_subnav_item['position'],
							'parent_url'        => $admin_link,
							'parent_slug'       => $this->current_group->slug . '_manage',
							'screen_function'   => __NAMESPACE__ . '\groups_screen_group_admin',
							'user_has_access'   => $admin_subnav_item['user_has_access'],
							'show_in_admin_bar' => $admin_subnav_item['show_in_admin_bar'],
							'link'              => bp_group_admin_rewrites_get_form_url( $this->current_group, $admin_subnav_item['slug'], $admin_subnav_item['rewrite_id'] ),
						),
						'groups'
					);
				}
			}
		}
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since 1.5.0
	 *
	 * @see BP_Component::setup_admin_bar() for a description of arguments.
	 *
	 * @param array $wp_admin_nav See BP_Component::setup_admin_bar()
	 *                            for description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		add_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'reset_admin_nav' ), 10, 1 );

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Reset WordPress admin bar nav items for the component.
	 *
	 * This should be done inside `BP_Groups_Component::setup_admin_bar()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_nav The Admin Bar items.
	 * @return array The Admin Bar items.
	 */
	public function reset_admin_nav( $wp_admin_nav = array() ) {
		remove_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'reset_admin_nav' ), 10 );

		if ( $wp_admin_nav ) {
			$parent_slug     = bp_get_groups_slug();
			$rewrite_id      = sprintf( 'bp_member_%s', $parent_slug );
			$root_nav_parent = buddypress()->my_account_menu_id;
			$user_id         = bp_loggedin_user_id();

			// NB: these slugs should probably be customizable.
			$viewes_slugs = array(
				'my-account-' . $this->id . '-memberships' => 'my-groups',
				'my-account-' . $this->id . '-invites'     => 'invites',
			);

			foreach ( $wp_admin_nav as $key_item_nav => $item_nav ) {
				$item_nav_id = $item_nav['id'];

				// The Admin Nav Item to access the Create Group form is specific.
				if ( 'my-account-' . $this->id . '-create' === $item_nav_id ) {
					$wp_admin_nav[ $key_item_nav ]['href'] = bp_get_group_create_link();

					// Edit other Admin Nav Items.
				} else {
					$url_params = array(
						'user_id'        => $user_id,
						'rewrite_id'     => $rewrite_id,
						'item_component' => $parent_slug,
					);

					if ( $root_nav_parent !== $item_nav['parent'] && isset( $viewes_slugs[ $item_nav_id ] ) ) {
						$sub_nav_rewrite_id        = sprintf(
							'%1$s_%2$s',
							$rewrite_id,
							str_replace( '-', '_', $viewes_slugs[ $item_nav_id ] )
						);
						$url_params['item_action'] = bp_rewrites_get_slug( 'members', $sub_nav_rewrite_id, $viewes_slugs[ $item_nav_id ] );
					}

					$wp_admin_nav[ $key_item_nav ]['href'] = bp_members_rewrites_get_nav_url( $url_params );
				}
			}
		}

		return $wp_admin_nav;
	}

	/**
	 * Add the component's rewrite tags.
	 *
	 * @since 1.0.0
	 *
	 * @param array $rewrite_tags Optional. See BP_Component::add_rewrite_tags() for
	 *                            description.
	 */
	public function add_rewrite_tags( $rewrite_tags = array() ) {
		$rewrite_tags = array(
			'directory'                    => array(
				'id'    => '%' . $this->rewrite_ids['directory'] . '%',
				'regex' => '([1]{1,})',
			),
			'directory-type'               => array(
				'id'    => '%' . $this->rewrite_ids['directory_type'] . '%',
				'regex' => '([^/]+)',
			),
			'create-single-item'           => array(
				'id'    => '%' . $this->rewrite_ids['create_single_item'] . '%',
				'regex' => '([1]{1,})',
			),
			'create-single-item-variables' => array(
				'id'    => '%' . $this->rewrite_ids['create_single_item_variables'] . '%',
				'regex' => '(.+?)',
			),
			'single-item'                  => array(
				'id'    => '%' . $this->rewrite_ids['single_item'] . '%',
				'regex' => '([^/]+)',
			),
			'single-item-action'           => array(
				'id'    => '%' . $this->rewrite_ids['single_item_action'] . '%',
				'regex' => '([^/]+)',
			),
			'single-item-action-variables' => array(
				'id'    => '%' . $this->rewrite_ids['single_item_action_variables'] . '%',
				'regex' => '(.+?)',
			),
		);

		bp_component_add_rewrite_tags( $rewrite_tags );

		\BP_Component::add_rewrite_tags( $rewrite_tags );
	}

	/**
	 * Add the component's rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @param array $rewrite_rules Optional. See BP_Component::add_rewrite_rules() for
	 *                             description.
	 */
	public function add_rewrite_rules( $rewrite_rules = array() ) {
		$create_slug = bp_rewrites_get_slug( 'groups', 'bp_group_create', 'create' );

		$rewrite_rules = array(
			'create-single-item-variables' => array(
				'regex' => $this->root_slug . '/' . $create_slug . '/(.+?)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['create_single_item'] . '=1&' . $this->rewrite_ids['create_single_item_variables'] . '=$matches[1]',
			),
			'create-single-item'           => array(
				'regex' => $this->root_slug . '/' . $create_slug . '/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['create_single_item'] . '=1',
			),
			'paged-directory-type'         => array(
				'regex' => $this->root_slug . '/' . bp_get_groups_group_type_base() . '/([^/]+)/page/?([0-9]{1,})/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['directory_type'] . '=$matches[1]&paged=$matches[2]',
			),
			'directory-type'               => array(
				'regex' => $this->root_slug . '/' . bp_get_groups_group_type_base() . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['directory_type'] . '=$matches[1]',
			),
			'paged-directory'              => array(
				'regex' => $this->root_slug . '/page/?([0-9]{1,})/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&paged=$matches[1]',
			),
			'single-item-action-variables' => array(
				'regex' => $this->root_slug . '/([^/]+)/([^/]+)/(.+?)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]&' . $this->rewrite_ids['single_item_action'] . '=$matches[2]&' . $this->rewrite_ids['single_item_action_variables'] . '=$matches[3]',
			),
			'single-item-action'           => array(
				'regex' => $this->root_slug . '/([^/]+)/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]&' . $this->rewrite_ids['single_item_action'] . '=$matches[2]',
			),
			'single-item'                  => array(
				'regex' => $this->root_slug . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]',
			),
			'directory'                    => array(
				'regex' => $this->root_slug . '/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1',
			),
		);

		bp_component_add_rewrite_rules( $rewrite_rules );

		\BP_Component::add_rewrite_rules( $rewrite_rules );
	}

	/**
	 * Add the component's directory permastructs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $permastructs Optional. See BP_Component::add_permastructs() for
	 *                            description.
	 */
	public function add_permastructs( $permastructs = array() ) {
		$permastructs = array(
			// Directory permastruct.
			$this->rewrite_ids['directory'] => array(
				'permastruct' => $this->directory_permastruct,
				'args'        => array(),
			),
		);

		bp_component_add_permastructs( $permastructs );

		\BP_Component::add_permastructs( $permastructs );
	}

	/**
	 * Parse the WP_Query and eventually display the component's directory or single item.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query Required. See BP_Component::parse_query() for
	 *                        description.
	 */
	public function parse_query( $query ) {
		if ( home_url( '/' ) === bp_get_requested_url() && bp_is_directory_homepage( $this->id ) ) {
			$query->set( $this->rewrite_ids['directory'], 1 );
		}

		if ( 1 === (int) $query->get( $this->rewrite_ids['directory'] ) ) {
			$bp                    = buddypress();
			$group_type            = false;
			$bp->current_component = 'groups';
			$group_slug            = $query->get( $this->rewrite_ids['single_item'] );
			$group_type_slug       = $query->get( $this->rewrite_ids['directory_type'] );
			$is_group_create       = 1 === (int) $query->get( $this->rewrite_ids['create_single_item'] );

			if ( $group_slug ) {
				$this->current_group = $this->set_current_group( $group_slug );

				if ( ! $this->current_group ) {
					$bp->current_component = false;
					bp_do_404();
					return;
				}

				// Set the current item using the group slug.
				$bp->current_item = $group_slug;

				$current_action = $query->get( $this->rewrite_ids['single_item_action'] );
				if ( $current_action ) {
					$context = 'bp_group_read_';

					// Get the rewrite ID corresponfing to the custom slug.
					$current_action_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $current_action, $context );

					if ( $current_action_rewrite_id ) {
						$current_action = str_replace( $context, '', $current_action_rewrite_id );

						// Make sure the action is stored as a slug: underscores need to be replaced by dashes.
						$current_action = str_replace( '_', '-', $current_action );
					}

					// Set the BuddyPress global.
					$bp->current_action = $current_action;
				}

				$action_variables = $query->get( $this->rewrite_ids['single_item_action_variables'] );
				if ( $action_variables ) {
					if ( ! is_array( $action_variables ) ) {
						$action_variables = explode( '/', ltrim( $action_variables, '/' ) );
					}

					// In the Manage context, we need to translate custom slugs to BP Expected variables.
					if ( 'admin' === $bp->current_action ) {
						$context = 'bp_group_manage_';

						// Get the rewrite ID corresponfing to the custom slug.
						$first_action_variable_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $action_variables[0], $context );

						if ( $first_action_variable_rewrite_id ) {
							$first_action_variable = str_replace( $context, '', $first_action_variable_rewrite_id );

							// Make sure the action is stored as a slug: underscores need to be replaced by dashes.
							$action_variables[0] = str_replace( '_', '-', $first_action_variable );
						}
					}

					// Set the BuddyPress global.
					$bp->action_variables = $action_variables;
				}
			} elseif ( $group_type_slug ) {
				$group_type = bp_groups_get_group_types(
					array(
						'has_directory'  => true,
						'directory_slug' => $group_type_slug,
					)
				);

				if ( $group_type ) {
					$group_type                   = reset( $group_type );
					$this->current_directory_type = $group_type;
					$bp->current_action           = bp_get_groups_group_type_base();
					$bp->action_variables         = array( $group_type_slug );
				} else {
					$bp->current_component = false;
					bp_do_404();
					return;
				}
			} elseif ( $is_group_create ) {
				$bp->current_action = 'create';

				if ( bp_user_can_create_groups() && isset( $_COOKIE['bp_new_group_id'] ) ) {
					$bp->groups->new_group_id = (int) $_COOKIE['bp_new_group_id'];
				}

				$create_variables = $query->get( $this->rewrite_ids['create_single_item_variables'] );
				if ( $create_variables ) {
					$context          = 'bp_group_create_';
					$action_variables = array();

					if ( ! is_array( $create_variables ) ) {
						$action_variables = explode( '/', ltrim( $create_variables, '/' ) );
					} else {
						$action_variables = $create_variables;
					}

					// The slug of the step is the second action variable.
					if ( isset( $action_variables[1] ) && $action_variables[1] ) {
						// Get the rewrite ID corresponfing to the custom slug.
						$second_action_variable_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'groups', $action_variables[1], $context );

						// Reset the action variable with BP Default create step slug.
						if ( $second_action_variable_rewrite_id ) {
							$second_action_variable = str_replace( $context, '', $second_action_variable_rewrite_id );

							// Make sure the action is stored as a slug: underscores need to be replaced by dashes.
							$action_variables[1] = str_replace( '_', '-', $second_action_variable );
						}
					}

					$bp->action_variables = $action_variables;
				}
			}

			/**
			 * Set the BuddyPress queried object.
			 */
			if ( isset( $bp->pages->groups->id ) ) {
				$query->queried_object    = get_post( $bp->pages->groups->id );
				$query->queried_object_id = $query->queried_object->ID;

				if ( $this->current_group ) {
					$query->queried_object->single_item_name = $this->current_group->name;
				} elseif ( $group_type ) {
					$query->queried_object->directory_type_name = $group_type;
				}
			}
		}

		bp_component_parse_query( $query );

		\BP_Component::parse_query( $query );
	}
}
