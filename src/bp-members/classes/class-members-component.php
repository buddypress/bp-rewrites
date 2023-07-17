<?php
/**
 * BP Rewrites Members Component.
 *
 * @package bp-rewrites\src\bp-members\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Defines the BuddyPress Members Component.
 *
 * @since 1.0.0
 */
class Members_Component extends \BP_Members_Component {
	/**
	 * Start the members component setup process.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { /* phpcs:ignore */
		parent::__construct();
	}

	/**
	 * Magic getter.
	 *
	 * This exists specifically to avoid a fatal error when a plugin tries to create
	 * a BP Nav Item too early.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The name of the property.
	 * @return null|BP\Rewrites\Core_Nav_Compat
	 */
	public function __get( $key = '' ) {
		$retval = null;

		if ( isset( $this->{$key} ) ) {
			$retval = $this->{$key};
		} elseif ( 'nav' === $key && ! did_action( 'bp_parse_query' ) ) {
			$core_path = trailingslashit( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) ) . 'bp-core/';
			require_once $core_path . 'classes/class-core-nav-compat.php';

			$retval    = new Core_Nav_Compat();
			$this->nav = $retval;
		}

		return $retval;
	}

	/**
	 * Set up additional globals for the component.
	 *
	 * NB: Setting the displayed user as well as the BP Members Nav at this stage in
	 * `parent::setup_globals()` is too early. The displayed user & the Nav has to be
	 * set in `Members_Component::parse_query()`.
	 *
	 * @since 1.0.0
	 */
	public function setup_additional_globals() {
		$bp = buddypress();

		/** Rewrites *********************************************************
		 */

		bp_component_setup_globals(
			array(
				'rewrite_ids' => array(
					'directory'                    => 'bp_members',
					'directory_type'               => 'bp_members_type',
					'single_item'                  => 'bp_member',
					'single_item_component'        => 'bp_member_component',
					'single_item_action'           => 'bp_member_action',
					'single_item_action_variables' => 'bp_member_action_variables',
					'member_register'              => 'bp_register',
					'member_activate'              => 'bp_activate',
					'member_activate_key'          => 'bp_activate_key',
				),
			),
			$this
		);

		// Set-up Extra permastructs for the register and activate pages.
		$this->register_permastruct = bp_get_signup_slug() . '/%' . $this->rewrite_ids['member_register'] . '%';
		$this->activate_permastruct = bp_get_activate_slug() . '/%' . $this->rewrite_ids['member_activate'] . '%';

		/** Logged in user ***************************************************
		 */

		// The core userdata of the user who is currently logged in.
		$bp->loggedin_user->userdata = bp_core_get_core_userdata( bp_loggedin_user_id() );

		// Fetch the full name for the logged in user.
		$bp->loggedin_user->fullname = '';
		if ( isset( $bp->loggedin_user->userdata->display_name ) ) {
			$bp->loggedin_user->fullname = $bp->loggedin_user->userdata->display_name;
		}

		// Hits the DB on single WP installs so get this separately.
		$bp->loggedin_user->is_super_admin = is_super_admin( bp_loggedin_user_id() );
		$bp->loggedin_user->is_site_admin  = $bp->loggedin_user->is_super_admin;

		// The domain for the user currently logged in. eg: http://example.com/members/andy.
		$bp->loggedin_user->domain = bp_member_rewrites_get_url( bp_loggedin_user_id() );

		/** Signup ***********************************************************
		 */

		$bp->signup = new \stdClass();

		/** Profiles Fallback ************************************************
		 */

		if ( ! bp_is_active( 'xprofile' ) ) {
			$bp->profile       = new \stdClass();
			$bp->profile->slug = 'profile';
			$bp->profile->id   = 'profile';
		}

		/** Network Invitations **************************************************
		 */

		$bp->members->invitations = new \stdClass();

		// Initialize the Members Nav for WP Admin context.
		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->nav = new \BP_Core_Nav();
		}
	}

	/**
	 * Set up canonical stack for this component.
	 *
	 * @since 1.0.0
	 */
	public function setup_canonical_stack() {
		parent::setup_canonical_stack();

		$bp = buddypress();

		if ( \bp_displayed_user_id() ) {
			$bp->canonical_stack['base_url'] = bp_member_rewrites_get_url( \bp_displayed_user_id() );
			$item_component                  = \bp_current_component();

			if ( $item_component ) {
				$bp->canonical_stack['component'] = bp_rewrites_get_slug( 'members', 'bp_member_' . $item_component, $item_component );

				if ( isset( $bp->default_component ) && \bp_is_current_component( $bp->default_component ) && ! \bp_current_action() ) {
					unset( $bp->canonical_stack['component'] );
				}
			}

			$current_action = \bp_current_action();
			if ( $current_action ) {
				// The action is stored as a slug, Rewrite IDs are keys: dashes need to be replaced by underscores.
				$rewrite_id = sprintf( 'bp_member_%s_', $item_component ) . str_replace( '-', '_', $current_action );

				$bp->canonical_stack['action'] = bp_rewrites_get_slug( 'members', $rewrite_id, $current_action );
			}
		}
	}

	/**
	 * Used to very briefly use the current user ID as the displayed one.
	 *
	 * @since 1.0.0
	 *
	 * @return int The current user ID.
	 */
	public function override_displayed_user_id() {
		return (int) get_current_user_id();
	}

	/**
	 * Get the Avatar and Cover image subnavs.
	 *
	 * @since 1.0.0
	 *
	 * @return array The Avatar and Cover image subnavs.
	 */
	public function get_avatar_cover_image_subnavs() {
		$has_filter = false;

		/*
		 * As `BP_Members_Component::get_avatar_cover_image_subnavs()` uses `\bp_get_members_component_link()` which
		 * checks a user is displayed before generating the URL which is required to create a Subnav item, we need
		 * to briefly override the displayed user ID to be able to customize Avatar & Cover Image slugs.
		 */
		if ( current_user_can( 'manage_options' ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$request_uri     = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			$admin_url_parts = wp_parse_url( $request_uri );

			if ( isset( $admin_url_parts['path'], $admin_url_parts['query'] ) && false !== strpos( $admin_url_parts['path'], 'wp-admin/admin.php' ) && false !== strpos( $admin_url_parts['query'], 'bp-rewrites-settings' ) ) {
				add_filter( 'bp_displayed_user_id', array( $this, 'override_displayed_user_id' ) );
				$has_filter = true;
			}
		}

		$subnavs = parent::get_avatar_cover_image_subnavs();

		if ( $has_filter ) {
			remove_filter( 'bp_displayed_user_id', array( $this, 'override_displayed_user_id' ) );
		}

		return $subnavs;
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

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Reset the component's navigation using BP Rewrites.
	 *
	 * @since 1.0.0
	 */
	public function reset_nav() {
		remove_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'reset_nav' ), 20 );

		// Get the main nav.
		$members_slug = bp_get_profile_slug();
		$main_nav     = buddypress()->members->nav->get_primary( array( 'component_id' => $this->id ), false );

		if ( bp_displayed_user_has_front_template() ) {
			$members_slug = 'front';
		}

		// Make sure the main navigation was built the right way.
		if ( ! is_array( $main_nav ) || ! isset( $main_nav[ $members_slug ] ) ) {
			return;
		}

		// Set the main nav slug.
		$main_nav = reset( $main_nav );
		$slug     = $main_nav['slug'];

		// Set the main nav `rewrite_id` property.
		$rewrite_id             = sprintf( 'bp_member_%s', $members_slug );
		$main_nav['rewrite_id'] = $rewrite_id;

		// Reset the link using BP Rewrites.
		$main_nav['link'] = bp_members_rewrites_get_nav_url(
			array(
				'rewrite_id'     => $rewrite_id,
				'item_component' => $slug,
			)
		);

		// Update the primary nav item.
		buddypress()->members->nav->edit_nav( $main_nav, $slug );

		// Update the secondary nav items.
		reset_secondary_nav( $slug, $rewrite_id, $this->id );

		// In this case a new navigation is created for a fake profile component id.
		if ( bp_displayed_user_has_front_template() && ! bp_is_active( 'xprofile' ) ) {
			$profile_slug = bp_get_profile_slug();

			// Get the profile main nav.
			$profile_nav               = buddypress()->members->nav->get_primary( array( 'slug' => $profile_slug ), false );
			$profile_nav['rewrite_id'] = 'bp_member_profile';

			// Reset the link using BP Rewrites.
			$profile_nav['link'] = bp_members_rewrites_get_nav_url(
				array(
					'rewrite_id'     => 'bp_member_profile',
					'item_component' => $profile_slug,
				)
			);

			// Update the primary nav item.
			buddypress()->members->nav->edit_nav( $profile_nav, $profile_slug );

			// Update the secondary nav items.
			reset_secondary_nav( $profile_slug, 'bp_member_profile', $this->id );
		}
	}

	/**
	 * Set up bp-members integration with the WordPress admin bar.
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
	 * This should be done inside `BP_Members_Component::setup_admin_bar()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_nav The Admin Bar items.
	 * @return array The Admin Bar items.
	 */
	public function reset_admin_nav( $wp_admin_nav = array() ) {
		remove_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'reset_admin_nav' ), 10 );

		if ( $wp_admin_nav ) {
			$parent_slug     = bp_get_profile_slug();
			$rewrite_id      = 'bp_member_profile';
			$root_nav_parent = buddypress()->my_account_menu_id;
			$user_id         = bp_loggedin_user_id();
			$viewes_slugs    = array(
				'my-account-' . $this->id . '-public' => 'public',
				'my-account-' . $this->id . '-change-avatar' => 'change-avatar',
				'my-account-' . $this->id . '-change-cover-image' => 'change-cover-image',
			);

			foreach ( $wp_admin_nav as $key_item_nav => $item_nav ) {
				$item_nav_id = $item_nav['id'];
				$url_params  = array(
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
					$url_params['item_action'] = bp_rewrites_get_slug( $this->id, $sub_nav_rewrite_id, $viewes_slugs[ $item_nav_id ] );
				}

				$wp_admin_nav[ $key_item_nav ]['href'] = bp_members_rewrites_get_nav_url( $url_params );
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
			'member-register'              => array(
				'id'    => '%' . $this->rewrite_ids['member_register'] . '%',
				'regex' => '([1]{1,})',
			),
			'member-activate'              => array(
				'id'    => '%' . $this->rewrite_ids['member_activate'] . '%',
				'regex' => '([1]{1,})',
			),
			'member-activate-key'          => array(
				'id'    => '%' . $this->rewrite_ids['member_activate_key'] . '%',
				'regex' => '([^/]+)',
			),
			'single-item'                  => array(
				'id'    => '%' . $this->rewrite_ids['single_item'] . '%',
				'regex' => '([^/]+)',
			),
			'single-item-component'        => array(
				'id'    => '%' . $this->rewrite_ids['single_item_component'] . '%',
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
		$rewrite_rules = array(
			'member-register'              => array(
				'regex' => bp_get_signup_slug(),
				'query' => 'index.php?' . $this->rewrite_ids['member_register'] . '=1',
			),
			'member-activate-key'          => array(
				'regex' => bp_get_activate_slug() . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['member_activate'] . '=1&' . $this->rewrite_ids['member_activate_key'] . '=$matches[1]',
			),
			'member-activate'              => array(
				'regex' => bp_get_activate_slug(),
				'query' => 'index.php?' . $this->rewrite_ids['member_activate'] . '=1',
			),
			'paged-directory-type'         => array(
				'regex' => $this->root_slug . '/' . bp_get_members_member_type_base() . '/([^/]+)/page/?([0-9]{1,})/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['directory_type'] . '=$matches[1]&paged=$matches[2]',
			),
			'directory-type'               => array(
				'regex' => $this->root_slug . '/' . bp_get_members_member_type_base() . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['directory_type'] . '=$matches[1]',
			),
			'paged-directory'              => array(
				'regex' => $this->root_slug . '/page/?([0-9]{1,})/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&paged=$matches[1]',
			),
			'single-item-action-variables' => array(
				'regex' => $this->root_slug . '/([^/]+)/([^/]+)/([^/]+)/(.+?)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]&' . $this->rewrite_ids['single_item_component'] . '=$matches[2]&' . $this->rewrite_ids['single_item_action'] . '=$matches[3]&' . $this->rewrite_ids['single_item_action_variables'] . '=$matches[4]',
			),
			'single-item-action'           => array(
				'regex' => $this->root_slug . '/([^/]+)/([^/]+)/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]&' . $this->rewrite_ids['single_item_component'] . '=$matches[2]&' . $this->rewrite_ids['single_item_action'] . '=$matches[3]',
			),
			'single-item-component'        => array(
				'regex' => $this->root_slug . '/([^/]+)/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item'] . '=$matches[1]&' . $this->rewrite_ids['single_item_component'] . '=$matches[2]',
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
			$this->rewrite_ids['directory']       => array(
				'permastruct' => $this->directory_permastruct,
				'args'        => array(),
			),
			// Register permastruct.
			$this->rewrite_ids['member_register'] => array(
				'permastruct' => $this->register_permastruct,
				'args'        => array(),
			),
			// Activate permastruct.
			$this->rewrite_ids['member_activate'] => array(
				'permastruct' => $this->activate_permastruct,
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
		// Init the current member and member type.
		$member      = false;
		$member_type = false;
		$member_data = bp_rewrites_get_member_data();

		if ( isset( $member_data['object'] ) && $member_data['object'] ) {
			bp_reset_query( trailingslashit( $this->root_slug ) . $GLOBALS['wp']->request, $query );
			$member = $member_data['object'];

			// Make sure the Member's screen is fired.
			add_action( 'bp_screens', 'bp_members_screen_display_profile', 3 );
		}

		if ( home_url( '/' ) === bp_get_requested_url() && bp_is_directory_homepage( $this->id ) ) {
			$query->set( $this->rewrite_ids['directory'], 1 );
		}

		// Which component are we displaying?
		$is_members_component  = 1 === (int) $query->get( $this->rewrite_ids['directory'] );
		$is_register_component = 1 === (int) $query->get( $this->rewrite_ids['member_register'] );
		$is_activate_component = 1 === (int) $query->get( $this->rewrite_ids['member_activate'] );

		// Get BuddyPress main instance.
		$bp = buddypress();

		if ( $is_members_component ) {
			$bp->current_component = 'members';
			$member_slug           = $query->get( $this->rewrite_ids['single_item'] );
			$member_type_slug      = $query->get( $this->rewrite_ids['directory_type'] );

			if ( $member_slug ) {
				/**
				 * Filter the portion of the URI that is the displayed user's slug.
				 *
				 * Eg. example.com/ADMIN (when root profiles is enabled)
				 *     example.com/members/ADMIN (when root profiles isn't enabled)
				 *
				 * ADMIN would be the displayed user's slug.
				 *
				 * @since 2.6.0
				 *
				 * @param string $member_slug
				 */
				$member_slug           = apply_filters( 'bp_core_set_uri_globals_member_slug', $member_slug );
				$bp->current_component = '';

				// Unless root profiles are on, the member shouldn't be set yet.
				if ( ! $member ) {
					$member = get_user_by( $member_data['field'], $member_slug );

					if ( ! $member ) {
						bp_do_404();
						return;
					}
				}

				// If the member is marked as a spammer, 404 (unless logged-in user is a super admin).
				if ( bp_is_user_spammer( $member->ID ) ) {
					if ( bp_current_user_can( 'bp_moderate' ) ) {
						bp_core_add_message( __( 'This user has been marked as a spammer. Only site admins can view this profile.', 'bp-rewrites' ), 'warning' );
					} else {
						bp_do_404();
						return;
					}
				}

				// Set the displayed user and the current item.
				$bp->displayed_user->id = $member->ID;
				$bp->current_item       = $member_slug;

				// The core userdata of the user who is currently being displayed.
				if ( ! isset( $bp->displayed_user->userdata ) || ! $bp->displayed_user->userdata ) {
					$bp->displayed_user->userdata = bp_core_get_core_userdata( \bp_displayed_user_id() );
				}

				// Fetch the full name displayed user.
				if ( ! isset( $bp->displayed_user->fullname ) || ! $bp->displayed_user->fullname ) {
					$bp->displayed_user->fullname = '';
					if ( isset( $bp->displayed_user->userdata->display_name ) ) {
						$bp->displayed_user->fullname = $bp->displayed_user->userdata->display_name;
					}
				}

				// The domain for the user currently being displayed.
				if ( ! isset( $bp->displayed_user->domain ) || ! $bp->displayed_user->domain ) {
					$bp->displayed_user->domain = bp_member_rewrites_get_url( \bp_displayed_user_id() );
				}

				// If A user is displayed, check if there is a front template.
				if ( bp_get_displayed_user() ) {
					$bp->displayed_user->front_template = bp_displayed_user_get_front_template();
				}

				$member_component = $query->get( $this->rewrite_ids['single_item_component'] );
				if ( $member_component ) {
					// Check if the member's component slug has been customized.
					$item_component_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'members', $member_component );
					if ( $item_component_rewrite_id ) {
						$member_component = str_replace( 'bp_member_', '', $item_component_rewrite_id );
					}

					$bp->current_component = $member_component;
				}

				$current_action = $query->get( $this->rewrite_ids['single_item_action'] );
				if ( $current_action ) {
					$context = sprintf( 'bp_member_%s_', $bp->current_component );

					// Check if the member's component action slug has been customized.
					$item_component_action_rewrite_id = bp_rewrites_get_custom_slug_rewrite_id( 'members', $current_action, $context );
					if ( $item_component_action_rewrite_id ) {
						$custom_action_slug = str_replace( $context, '', $item_component_action_rewrite_id );

						// Make sure the action is stored as a slug: underscores need to be replaced by dashes.
						$current_action = str_replace( '_', '-', $custom_action_slug );
					}

					$bp->current_action = $current_action;
				}

				$action_variables = $query->get( $this->rewrite_ids['single_item_action_variables'] );
				if ( $action_variables ) {
					if ( ! is_array( $action_variables ) ) {
						$bp->action_variables = explode( '/', ltrim( $action_variables, '/' ) );
					} else {
						$bp->action_variables = $action_variables;
					}
				}

				// Is this a member type query?
			} elseif ( $member_type_slug ) {
				$member_type = bp_get_member_types(
					array(
						'has_directory'  => true,
						'directory_slug' => $member_type_slug,
					)
				);

				if ( $member_type ) {
					$member_type             = reset( $member_type );
					$bp->current_member_type = $member_type;
				} else {
					$bp->current_component = '';
					bp_do_404();
					return;
				}
			}

			/**
			 * Set the BuddyPress queried object.
			 */
			if ( isset( $bp->pages->members->id ) ) {
				$query->queried_object    = get_post( $bp->pages->members->id );
				$query->queried_object_id = $query->queried_object->ID;

				if ( $member ) {
					$query->queried_object->single_item_name = $member->display_name;
				} elseif ( $member_type ) {
					$query->queried_object->directory_type_name = $member_type;
				}
			}

			// Handle the custom registration page.
		} elseif ( $is_register_component ) {
			$bp->current_component = 'register';

			// Handle the custom activation page.
		} elseif ( $is_activate_component ) {
			$bp->current_component = 'activate';

			$current_action = $query->get( $this->rewrite_ids['member_activate_key'] );
			if ( $current_action ) {
				$bp->current_action = $current_action;
			}
		}

		// Initialize the nav for the members component.
		$this->nav = new \BP_Core_Nav();

		bp_component_parse_query( $query );

		\BP_Component::parse_query( $query );
	}
}
