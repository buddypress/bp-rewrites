<?php
/**
 * BP Rewrites Group Extension.
 *
 * @package bp-rewrites\src\bp-groups\classes
 * @since   1.0.0
 */

if ( ! class_exists( 'BP_Group_Extension', false ) ) :
	/**
	 * Adjustments to make the Group Extension API "BP Rewrites" ready.
	 *
	 * @since 1.0.0
	 */
	class BP_Group_Extension {

		/**
		 * Information about this extension's screens.
		 *
		 * @since 1.8.0
		 * @var   array
		 */
		public $screens = array();

		/**
		 * The name of the extending class.
		 *
		 * @since 1.8.0
		 * @var   string
		 */
		public $class_name = '';

		/**
		 * A ReflectionClass object of the current extension.
		 *
		 * @since 1.8.0
		 * @var   ReflectionClass
		 */
		public $class_reflection = null;

		/**
		 * Parsed configuration parameters for the extension.
		 *
		 * @since 1.8.0
		 * @var   array
		 */
		public $params = array();

		/**
		 * Raw config params, as passed by the extending class.
		 *
		 * @since 2.1.0
		 * @var   array
		 */
		public $params_raw = array();

		/**
		 * The ID of the current group.
		 *
		 * @since 1.8.0
		 * @var   int
		 */
		public $group_id = 0;

		/**
		 * The slug of the current extension.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $slug = '';

		/**
		 * The translatable name of the current extension.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $name = '';

		/**
		 * The visibility of the extension tab. 'public' or 'private'.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $visibility = 'public';

		/**
		 * The numeric position of the main nav item.
		 *
		 * @since 1.1.0
		 * @var   int
		 */
		public $nav_item_position = 81;

		/**
		 * Whether to show the nav item.
		 *
		 * @since 1.1.0
		 * @var   bool
		 */
		public $enable_nav_item = true;

		/**
		 * Whether the current user should see the navigation item.
		 *
		 * @since 2.1.0
		 * @var   bool
		 */
		public $user_can_see_nav_item;

		/**
		 * The Callback function to use before showing the navigation item.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $show_tab_callback = '';

		/**
		 * List of Group access levels.
		 *
		 * @since 1.0.0
		 * @var string[]
		 */
		public $access_levels = array( 'noone', 'admin', 'mod', 'member', 'loggedin', 'anyone' );

		/**
		 * Whether the current user can visit the tab.
		 *
		 * @since 2.1.0
		 * @var   bool
		 */
		public $user_can_visit;

		/**
		 * The text of the nav item. Defaults to self::name.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $nav_item_name = '';

		/**
		 * The WP action that self::widget_display() is attached to.
		 *
		 * Default: 'groups_custom_group_boxes'.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $display_hook = 'groups_custom_group_boxes';

		/**
		 * The template file used to load the plugin content.
		 *
		 * Default: 'groups/single/plugins'.
		 *
		 * @since 1.1.0
		 * @var   string
		 */
		public $template_file = 'groups/single/plugins';

		/**
		 * Has the extension been initialized?
		 *
		 * @since 1.8.0
		 * @var   bool
		 */
		protected $initialized = false;

		/**
		 * Extension properties as set by legacy extensions.
		 *
		 * @since 1.8.0
		 * @var   array
		 */
		protected $legacy_properties = array();

		/**
		 * Converted legacy parameters.
		 *
		 * @since 1.8.0
		 * @var   array
		 */
		protected $legacy_properties_converted = array();

		/**
		 * Redirect location as defined by post-edit save callback.
		 *
		 * @since 2.1.0
		 * @var   string
		 */
		protected $post_save_redirect;

		/**
		 * Miscellaneous data as set by the __set() magic method.
		 *
		 * @since 1.8.0
		 * @var   array
		 */
		protected $data = array();

		/**
		 * The content of the group tab.
		 *
		 * @since 1.1.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function display( $group_id = null ) {
		}

		/**
		 * Content displayed in a widget sidebar, if applicable.
		 *
		 * @since 1.1.0
		 */
		public function widget_display() {
		}

		/**
		 * The content of the group create step tab.
		 *
		 * @since 1.1.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function create_screen( $group_id = null ) {
		}

		/**
		 * Group create step tab handler.
		 *
		 * @since 1.1.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function create_screen_save( $group_id = null ) {
		}

		/**
		 * The content of the Manage sub tab.
		 *
		 * @since 1.1.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function edit_screen( $group_id = null ) {
		}

		/**
		 * Group Manage sub tab handler.
		 *
		 * @since 1.1.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function edit_screen_save( $group_id = null ) {
		}

		/**
		 * The content of Group's WP Administration screen metabox.
		 *
		 * @since 1.8.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function admin_screen( $group_id = null ) {
		}

		/**
		 * Group's WP Administration screen handler.
		 *
		 * @since 1.8.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function admin_screen_save( $group_id = null ) {
		}

		/**
		 * Provide the fallback markup for Group's Create/Admin/Edit screens.
		 *
		 * @since 1.8.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function settings_screen( $group_id = null ) {
		}

		/**
		 * Group's Fallback handler for the Create/Admin/Edit screens.
		 *
		 * @since 1.8.0
		 *
		 * @param int|null $group_id ID of the group to display.
		 */
		public function settings_screen_save( $group_id = null ) {
		}

		/**
		 * Initialize the Group extension, using the extension config settings.
		 *
		 * @since 1.8.0
		 * @since 2.1.0 Added 'access' and 'show_tab' arguments to `$args`.
		 * @since 1.0.0 Set the Group Extension views.
		 *
		 * @param array $args {
		 *     Array of initialization arguments.
		 *     @see \BP_Group_Extension for more details about these arguments.
		 * }
		 */
		public function init( $args = array() ) {
			// Store the raw arguments.
			$this->params_raw = $args;
			$this->parse_legacy_properties();

			$args = $this->parse_args_r( $args, $this->legacy_properties_converted );

			// Parse with defaults.
			$this->params = $this->parse_args_r(
				$args,
				array(
					'slug'              => $this->slug,
					'name'              => $this->name,
					'visibility'        => $this->visibility,
					'nav_item_position' => $this->nav_item_position,
					'enable_nav_item'   => (bool) $this->enable_nav_item,
					'nav_item_name'     => $this->nav_item_name,
					'display_hook'      => $this->display_hook,
					'template_file'     => $this->template_file,
					'screens'           => $this->get_default_screens(),
					'access'            => null,
					'show_tab'          => null,
				)
			);

			$show_tab = $this->params['show_tab'];
			if ( $show_tab && ! in_array( $show_tab, $this->access_levels, true ) && is_callable( $show_tab ) ) {
				$this->show_tab_callback = $show_tab;

				// Group Admin can always see.
				$this->params['show_tab'] = 'admin';
			}

			$this->initialized = true;

			// Specific to BP Rewrites.
			$bp                    = buddypress();
			$group_extension_class = get_class( $this );
			$slug                  = $this->params['slug'];
			$name                  = $this->params['name'];
			$rewrite_id_suffix     = str_replace( '-', '_', $slug );

			// Populate Slugs And Names to allow BP Rewrites customizations.
			if ( isset( $bp->groups->group_extensions[ $group_extension_class ] ) ) {
				$default_data = array(
					'slug' => $slug,
					'name' => $name,
				);

				$bp->groups->group_extensions[ $group_extension_class ] = array(
					'read'   => array(
						$slug => array_merge(
							$default_data,
							array(
								'rewrite_id' => 'bp_group_read_' . $rewrite_id_suffix,
							)
						),
					),
					'manage' => array(
						$slug => array_merge(
							$default_data,
							array(
								'rewrite_id' => 'bp_group_manage_' . $rewrite_id_suffix,
							)
						),
					),
					'create' => array(
						$slug => array_merge(
							$default_data,
							array(
								'rewrite_id' => 'bp_group_create_' . $rewrite_id_suffix,
							)
						),
					),
				);

				if ( $this->params['nav_item_name'] ) {
					$bp->groups->group_extensions[ $group_extension_class ]['read']['name'] = $this->params['nav_item_name'];
				}

				foreach ( $this->params['screens'] as $screen => $data ) {
					$screen_key = $screen;
					if ( 'admin' === $screen ) {
						continue;
					}

					if ( 'edit' === $screen ) {
						$screen_key = 'manage';
					}

					if ( ! $data['enabled'] ) {
						unset( $bp->groups->group_extensions[ $group_extension_class ][ $screen_key ] );
						continue;
					}

					if ( isset( $data['slug'] ) && $data['slug'] ) {
						$bp->groups->group_extensions[ $group_extension_class ][ $screen_key ][ $slug ]['slug'] = $data['slug'];
					}

					if ( isset( $data['name'] ) && $data['name'] ) {
						$bp->groups->group_extensions[ $group_extension_class ][ $screen_key ][ $slug ]['name'] = $data['name'];
					}
				}
			}
		}

		/**
		 * The main setup routine for the extension.
		 *
		 * @since 1.1.0
		 */
		public function _register() { // phpcs:ignore

			// Detect and parse properties set by legacy extensions.
			$this->parse_legacy_properties();

			/*
			 * Initialize, if necessary. This should only happen for
			 * legacy extensions that don't call parent::init() themselves.
			 */
			if ( true !== $this->initialized ) {
				$this->init();
			}

			// Set some config values, based on the parsed params.
			$this->group_id          = $this->get_group_id();
			$this->slug              = $this->params['slug'];
			$this->name              = $this->params['name'];
			$this->visibility        = $this->params['visibility'];
			$this->nav_item_position = $this->params['nav_item_position'];
			$this->nav_item_name     = $this->params['nav_item_name'];
			$this->display_hook      = $this->params['display_hook'];
			$this->template_file     = $this->params['template_file'];

			// Configure 'screens': create, admin, and edit contexts.
			$this->setup_screens();

			// Configure access-related settings.
			$this->setup_access_settings();

			/*
			 * Mirror configuration data so it's accessible to plugins
			 * that look for it in its old locations.
			 */
			$this->setup_legacy_properties();

			// Hook the extension into BuddyPress.
			$this->setup_display_hooks();
			$this->setup_create_hooks();
			$this->setup_edit_hooks();
			$this->setup_admin_hooks();
		}

		/**
		 * Set up some basic info about the Extension.
		 *
		 * @since 1.8.0
		 */
		protected function setup_class_info() {
			if ( empty( $this->class_name ) ) {
				$this->class_name = get_class( $this );
			}

			if ( is_null( $this->class_reflection ) ) {
				$this->class_reflection = new ReflectionClass( $this->class_name );
			}
		}

		/**
		 * Checks if the Group/URI Globals had time to be set.
		 *
		 * @since 1.0.0
		 *
		 * @return bool True if the Group/URI Globals had time to be set.
		 *              False otherwise.
		 */
		protected static function query_parsed() {
			$retval = false;

			if ( is_admin() && ! wp_doing_ajax() ) {
				$retval = did_action( 'bp_init' );
			} else {
				$retval = did_action( 'bp_parse_query' );
			}

			return $retval;
		}

		/**
		 * Get the current group ID.
		 *
		 * @since 1.8.0
		 *
		 * @return int
		 */
		public static function get_group_id() {
			$group_id = 0;

			// Wait until the Group ID can have been set.
			if ( self::query_parsed() ) {
				$group_id = bp_get_current_group_id();
			} else {
				_doing_it_wrong( __METHOD__, esc_html__( 'Please wait for the `bp_parse_query` hook to be fired before trying to get the Group ID.', 'bp-rewrites' ), 'BP Rewrites' );
				$current_group = BP\Rewrites\bp_core_get_from_uri( array( 'current_group' ) );

				if ( isset( $current_group->id ) && $current_group->id ) {
					$group_id = $current_group->id;
				}
			}

			// On the admin, get the group id out of the $_GET params.
			if ( empty( $group_id ) && is_admin() ) {
				// phpcs:disable WordPress.Security.NonceVerification
				$admin_page = '';
				if ( isset( $_GET['page'] ) ) {
					$admin_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
				}

				if ( 'bp-groups' === $admin_page && isset( $_GET['gid'] ) ) {
					$group_id = (int) sanitize_text_field( wp_unslash( $_GET['gid'] ) );
				}
				// phpcs:enable WordPress.Security.NonceVerification
			}

			/*
			 * This fallback will only be hit when the create step is very
			 * early.
			 */
			if ( empty( $group_id ) && bp_get_new_group_id() ) {
				$group_id = bp_get_new_group_id();
			}

			/*
			 * On some setups, the group id has to be fetched out of the
			 * $_POST array
			 * @todo Figure out why this is happening during group creation.
			 */
			// phpcs:disable WordPress.Security.NonceVerification
			if ( empty( $group_id ) && isset( $_POST['group_id'] ) ) {
				$group_id = (int) sanitize_text_field( wp_unslash( $_POST['group_id'] ) );
			}
			// phpcs:enable WordPress.Security.NonceVerification

			return $group_id;
		}

		/**
		 * Gather configuration data about your screens.
		 *
		 * @since 1.8.0
		 *
		 * @return array
		 */
		protected function get_default_screens() {
			$this->setup_class_info();

			$screens = array(
				'create' => array(
					'position' => 81,
				),
				'edit'   => array(
					'submit_text' => __( 'Save Changes', 'bp-rewrites' ),
				),
				'admin'  => array(
					'metabox_context'  => 'normal',
					'metabox_priority' => 'core',
				),
			);

			foreach ( $screens as $context => &$screen ) {
				$screen['enabled'] = true;
				$screen['name']    = $this->name;
				$screen['slug']    = $this->slug;

				$screen['screen_callback']      = $this->get_screen_callback( $context, 'screen' );
				$screen['screen_save_callback'] = $this->get_screen_callback( $context, 'screen_save' );
			}

			return $screens;
		}

		/**
		 * Set up screens array based on params.
		 *
		 * @since 1.8.0
		 */
		protected function setup_screens() {
			foreach ( (array) $this->params['screens'] as $context => $screen ) {
				if ( empty( $screen['slug'] ) ) {
					$screen['slug'] = $this->slug;
				}

				if ( empty( $screen['name'] ) ) {
					$screen['name'] = $this->name;
				}

				$this->screens[ $context ] = $screen;
			}
		}

		/**
		 * Set up access-related settings for this extension.
		 *
		 * @since 2.1.0
		 */
		protected function setup_access_settings() {

			// Bail if no group ID is available.
			if ( empty( $this->group_id ) ) {
				return;
			}

			// Backward compatibility.
			if ( isset( $this->params['enable_nav_item'] ) ) {
				$this->enable_nav_item = (bool) $this->params['enable_nav_item'];
			}

			// Tab Access.
			$this->user_can_visit = false;

			/*
			 * Backward compatibility for components that do not provide
			 * explicit 'access' parameter.
			 */
			if ( empty( $this->params['access'] ) ) {
				if ( false === $this->params['enable_nav_item'] ) {
					$this->params['access'] = 'noone';
				} else {
					$group = groups_get_group( $this->group_id );

					if ( ! empty( $group->status ) && 'public' === $group->status ) {
						// Tabs in public groups are accessible to anyone by default.
						$this->params['access'] = 'anyone';
					} else {
						// All other groups have members-only as the default.
						$this->params['access'] = 'member';
					}
				}
			}

			// Parse multiple access conditions into an array.
			$access_conditions = $this->params['access'];
			if ( ! is_array( $access_conditions ) ) {
				$access_conditions = explode( ',', $access_conditions );
			}

			/*
			 * If the current user meets at least one condition, the
			 * get access.
			 */
			foreach ( $access_conditions as $access_condition ) {
				if ( $this->user_meets_access_condition( $access_condition ) ) {
					$this->user_can_visit = true;
					break;
				}
			}

			// Tab Visibility.
			$this->user_can_see_nav_item = false;

			/*
			 * Backward compatibility for components that do not provide
			 * explicit 'show_tab' parameter.
			 */
			if ( empty( $this->params['show_tab'] ) ) {
				if ( false === $this->params['enable_nav_item'] ) {
					/*
					 * The enable_nav_item index is only false if it's been
					 * defined explicitly as such in the
					 * constructor. So we always trust this value.
					 */
					$this->params['show_tab'] = 'noone';

				} elseif ( isset( $this->params_raw['enable_nav_item'] ) || isset( $this->params_raw['visibility'] ) ) {
					/*
					 * If enable_nav_item or visibility is passed,
					 * we assume this  is a legacy extension.
					 * Legacy behavior is that enable_nav_item=true +
					 * visibility=private implies members-only.
					 */
					if ( 'public' !== $this->visibility ) {
						$this->params['show_tab'] = 'member';
					} else {
						$this->params['show_tab'] = 'anyone';
					}
				} else {
					/*
					 * No show_tab or enable_nav_item value is
					 * available, so match the value of 'access'.
					 */
					$this->params['show_tab'] = $this->params['access'];
				}
			}

			// Parse multiple access conditions into an array.
			$access_conditions = $this->params['show_tab'];
			if ( ! is_array( $access_conditions ) ) {
				$access_conditions = explode( ',', $access_conditions );
			}

			/*
			 * If the current user meets at least one condition, the
			 * get access.
			 */
			foreach ( $access_conditions as $access_condition ) {
				if ( $this->user_meets_access_condition( $access_condition ) ) {
					$this->user_can_see_nav_item = true;
					break;
				}
			}
		}

		/**
		 * Check whether the current user meets an access condition.
		 *
		 * @since 2.1.0
		 *
		 * @param  string $access_condition 'anyone', 'loggedin', 'member',
		 *                                  'mod', 'admin' or 'noone'.
		 * @return bool
		 */
		protected function user_meets_access_condition( $access_condition ) {

			switch ( $access_condition ) {
				case 'admin':
					$meets_condition = groups_is_user_admin( bp_loggedin_user_id(), $this->group_id );
					break;

				case 'mod':
					$meets_condition = groups_is_user_mod( bp_loggedin_user_id(), $this->group_id );
					break;

				case 'member':
					$meets_condition = groups_is_user_member( bp_loggedin_user_id(), $this->group_id );
					break;

				case 'loggedin':
					$meets_condition = is_user_logged_in();
					break;

				case 'noone':
					$meets_condition = false;
					break;

				case 'anyone':
				default:
					$meets_condition = true;
					break;
			}

			return $meets_condition;
		}

		/**
		 * Returns the Rewrite ID of the Group Extension Item according to the context.
		 *
		 * @since 1.0.0
		 *
		 * @param string $context One of these contexts: 'create', 'manage', 'read'.
		 * @return string         The found Rewrite ID, an empty string otherwise.
		 */
		protected function get_rewrite_id_for( $context = '' ) {
			$rewrite_id            = '';
			$group_extensions      = buddypress()->groups->group_extensions;
			$group_extension_class = get_class( $this );

			if ( isset( $group_extensions[ $group_extension_class ][ $context ][ $this->slug ]['rewrite_id'] ) ) {
				$rewrite_id = $group_extensions[ $group_extension_class ][ $context ][ $this->slug ]['rewrite_id'];
			}

			return $rewrite_id;
		}

		/**
		 * Hook this extension's group tab into BuddyPress, if necessary.
		 *
		 * @since 1.8.0
		 */
		protected function setup_display_hooks() {

			// Bail if not a group.
			if ( ! bp_is_group() ) {
				return;
			}

			// Backward compatibility only.
			if ( ( 'public' !== $this->visibility ) && ! buddypress()->groups->current_group->user_has_access ) {
				return;
			}

			// If the user can see the nav item, we create it.
			$user_can_see_nav_item = $this->user_can_see_nav_item();

			if ( $user_can_see_nav_item ) {
				$current_group   = groups_get_current_group();
				$group_permalink = bp_get_group_permalink( $current_group );
				$rewrite_id      = $this->get_rewrite_id_for( 'read' );
				$link            = '';

				if ( $rewrite_id ) {
					$link = BP\Rewrites\bp_group_nav_rewrites_get_url( $current_group, $this->slug, $rewrite_id );
				}

				bp_core_create_subnav_link(
					array(
						'name'            => ! $this->nav_item_name ? $this->name : $this->nav_item_name,
						'slug'            => $this->slug,
						'parent_slug'     => bp_get_current_group_slug(),
						'parent_url'      => $group_permalink,
						'position'        => $this->nav_item_position,
						'item_css_id'     => 'nav-' . $this->slug,
						'screen_function' => array( &$this, '_display_hook' ),
						'user_has_access' => $user_can_see_nav_item,
						'no_access_url'   => $group_permalink,
						'link'            => $link,
					),
					'groups'
				);
			}

			// If the user can visit the screen, we register it.
			$user_can_visit = $this->user_can_visit();

			if ( $user_can_visit ) {
				$group_permalink = bp_get_group_permalink( groups_get_current_group() );

				bp_core_register_subnav_screen_function(
					array(
						'slug'            => $this->slug,
						'parent_slug'     => bp_get_current_group_slug(),
						'screen_function' => array( &$this, '_display_hook' ),
						'user_has_access' => $user_can_visit,
						'no_access_url'   => $group_permalink,
					),
					'groups'
				);

				// When we are viewing the extension display page, set the title and options title.
				if ( bp_is_current_action( $this->slug ) ) {
					add_filter( 'bp_group_user_has_access', array( $this, 'group_access_protection' ), 10, 2 );

					$extension_name = $this->name;
					add_action(
						'bp_template_content_header',
						function () use ( $extension_name ) {
							echo esc_attr( $extension_name );
						}
					);
					add_action(
						'bp_template_title',
						function () use ( $extension_name ) {
							echo esc_attr( $extension_name );
						}
					);
				}
			}

			// Hook the group home widget.
			if ( bp_is_group_home() ) {
				add_action( $this->display_hook, array( &$this, 'widget_display' ) );
			}
		}

		/**
		 * Hook the main display method, and loads the template file.
		 *
		 * @since 1.1.0
		 */
		public function _display_hook() { // phpcs:ignore
			add_action( 'bp_template_content', array( &$this, 'call_display' ) );

			/**
			 * Filters the template to load for the main display method.
			 *
			 * @since 1.0.0
			 *
			 * @param string $template_file Path to the template to load.
			 */
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', $this->template_file ) );
		}

		/**
		 * Call the display() method.
		 *
		 * @since 2.1.1
		 */
		public function call_display() {
			$this->display( $this->group_id );
		}

		/**
		 * Determine whether the current user should see this nav tab.
		 *
		 * @since 2.1.0
		 *
		 * @param  bool $user_can_see_nav_item Whether or not the user can see the nav item.
		 * @return bool
		 */
		public function user_can_see_nav_item( $user_can_see_nav_item = false ) {

			// Always allow moderators to see nav items, even if explicitly 'noone'.
			if ( ( 'noone' !== $this->params['show_tab'] ) && bp_current_user_can( 'bp_moderate' ) ) {
				return true;
			}

			if ( $this->show_tab_callback ) {
				return call_user_func( $this->show_tab_callback );
			}

			return $this->user_can_see_nav_item;
		}

		/**
		 * Determine whether the current user has access to visit this tab.
		 *
		 * @since 2.1.0
		 *
		 * @param  bool $user_can_visit Whether or not the user can visit the tab.
		 * @return bool
		 */
		public function user_can_visit( $user_can_visit = false ) {

			// Always allow moderators to visit a tab, even if explicitly 'noone'.
			if ( ( 'noone' !== $this->params['access'] ) && bp_current_user_can( 'bp_moderate' ) ) {
				return true;
			}

			return $this->user_can_visit;
		}

		/**
		 * Filter the access check in bp_groups_group_access_protection() for this extension.
		 *
		 * @since 2.1.0
		 *
		 * @param  bool  $user_can_visit Whether or not the user can visit the tab.
		 * @param  array $no_access_args Array of args to help determine access.
		 * @return bool
		 */
		public function group_access_protection( $user_can_visit, &$no_access_args ) {
			$user_can_visit = $this->user_can_visit();

			if ( ! $user_can_visit && is_user_logged_in() ) {
				$current_group = groups_get_group( $this->group_id );

				$no_access_args['message']  = __( 'You do not have access to this content.', 'bp-rewrites' );
				$no_access_args['root']     = bp_get_group_permalink( $current_group ) . 'home/';
				$no_access_args['redirect'] = false;
			}

			return $user_can_visit;
		}

		/**
		 * Hook this extension's Create step into BuddyPress, if necessary.
		 *
		 * @since 1.8.0
		 */
		protected function setup_create_hooks() {
			if ( ! $this->is_screen_enabled( 'create' ) ) {
				return;
			}

			$screen      = $this->screens['create'];
			$create_data = array(
				'name'     => $screen['name'],
				'slug'     => $screen['slug'],
				'position' => $screen['position'],
			);

			$rewrite_id = $this->get_rewrite_id_for( 'create' );
			if ( $rewrite_id ) {
				$create_data['rewrite_id']   = $rewrite_id;
				$create_data['default_slug'] = $screen['slug'];
			}

			// Insert the group creation step for the new group extension.
			buddypress()->groups->group_creation_steps[ $screen['slug'] ] = $create_data;

			/*
			 * The maybe_ methods check to see whether the create_*
			 * callbacks should be invoked (ie, are we on the
			 * correct group creation step). Hooked in separate
			 * methods because current creation step info not yet
			 * available at this point.
			 */
			add_action( 'groups_custom_create_steps', array( $this, 'maybe_create_screen' ) );
			add_action( 'groups_create_group_step_save_' . $screen['slug'], array( $this, 'maybe_create_screen_save' ) );
		}

		/**
		 * Call the create_screen() method, if we're on the right page.
		 *
		 * @since 1.8.0
		 */
		public function maybe_create_screen() {
			if ( ! bp_is_group_creation_step( $this->screens['create']['slug'] ) ) {
				return;
			}

			call_user_func( $this->screens['create']['screen_callback'], $this->group_id );
			$this->nonce_field( 'create' );

			/*
			 * The create screen requires an additional nonce field
			 * due to a quirk in the way the templates are built.
			 */
			wp_nonce_field( 'groups_create_save_' . bp_get_groups_current_create_step(), '_wpnonce', false );
		}

		/**
		 * Call the create_screen_save() method, if we're on the right page.
		 *
		 * @since 1.8.0
		 */
		public function maybe_create_screen_save() {
			if ( ! bp_is_group_creation_step( $this->screens['create']['slug'] ) ) {
				return;
			}

			$this->check_nonce( 'create' );
			call_user_func( $this->screens['create']['screen_save_callback'], $this->group_id );
		}

		/**
		 * Hook this extension's Edit panel into BuddyPress, if necessary.
		 *
		 * @since 1.8.0
		 */
		protected function setup_edit_hooks() {
			// Bail if not in a group.
			if ( ! bp_is_group() ) {
				return;
			}

			// Bail if not an edit screen.
			if ( ! $this->is_screen_enabled( 'edit' ) || ! bp_is_item_admin() ) {
				return;
			}

			$screen = $this->screens['edit'];

			$position  = isset( $screen['position'] ) ? (int) $screen['position'] : 10;
			$position += 40;

			$current_group = groups_get_current_group();
			$admin_link    = BP\Rewrites\bp_group_admin_rewrites_get_url( $current_group );
			$rewrite_id    = $this->get_rewrite_id_for( 'manage' );
			$link          = '';

			if ( $rewrite_id ) {
				$link = BP\Rewrites\bp_group_admin_rewrites_get_form_url( $current_group, $this->slug, $rewrite_id );
			}

			$subnav_args = array(
				'name'            => $screen['name'],
				'slug'            => $screen['slug'],
				'parent_slug'     => $current_group->slug . '_manage',
				'parent_url'      => $admin_link,
				'user_has_access' => bp_is_item_admin(),
				'position'        => $position,
				'screen_function' => 'groups_screen_group_admin',
				'link'            => $link,
			);

			// Should we add a menu to the Group's WP Admin Bar.
			if ( ! empty( $screen['show_in_admin_bar'] ) ) {
				$subnav_args['show_in_admin_bar'] = true;
			}

			// Add the tab to the manage navigation.
			bp_core_new_subnav_item( $subnav_args, 'groups' );

			// Catch the edit screen and forward it to the plugin template.
			if ( bp_is_groups_component() && bp_is_current_action( 'admin' ) && bp_is_action_variable( $screen['slug'], 0 ) ) {
				$this->call_edit_screen_save( $this->group_id );

				add_action( 'groups_custom_edit_steps', array( &$this, 'call_edit_screen' ) );

				/*
				 * Determine the proper template and save for later
				 * loading.
				 */
				if ( '' !== bp_locate_template( array( 'groups/single/home.php' ), false ) ) {
					$this->edit_screen_template = '/groups/single/home';
				} else {
					add_action(
						'bp_template_content_header',
						function () {
							echo '<ul class="content-header-nav">';
							bp_group_admin_tabs();
							echo '</ul>';
						}
					);
					add_action( 'bp_template_content', array( &$this, 'call_edit_screen' ) );
					$this->edit_screen_template = '/groups/single/plugins';
				}

				/*
				 * We load the template at bp_screens, to give all
				 * extensions a chance to load.
				 */
				add_action( 'bp_screens', array( $this, 'call_edit_screen_template_loader' ) );
			}
		}

		/**
		 * Call the edit_screen() method.
		 *
		 * @since 1.8.0
		 */
		public function call_edit_screen() {
			ob_start();
			call_user_func( $this->screens['edit']['screen_callback'], $this->group_id );
			$screen = ob_get_contents();
			ob_end_clean();

			echo $this->maybe_add_submit_button( $screen ); // phpcs:ignore

			$this->nonce_field( 'edit' );
		}

		/**
		 * Check the nonce, and call the edit_screen_save() method.
		 *
		 * @since 1.8.0
		 */
		public function call_edit_screen_save() {
			// phpcs:disable WordPress.Security.NonceVerification
			if ( empty( $_POST ) ) {
				return;
			}
			// phpcs:enable WordPress.Security.NonceVerification

			/*
			 * When DOING_AJAX, the POST global will be populated, but we
			 * should assume it's a save.
			 */
			if ( wp_doing_ajax() ) {
				return;
			}

			$this->check_nonce( 'edit' );

			/*
			 * Detect whether the screen_save_callback is performing a
			 * redirect, so that we don't do one of our own.
			 */
			add_filter( 'wp_redirect', array( $this, 'detect_post_save_redirect' ) );

			// Call the extension's save routine.
			call_user_func( $this->screens['edit']['screen_save_callback'], $this->group_id );

			// Clean up detection filters.
			remove_filter( 'wp_redirect', array( $this, 'detect_post_save_redirect' ) );

			// Perform a redirect only if one has not already taken place.
			if ( empty( $this->post_save_redirect ) ) {

				/**
				 * Filters the URL to redirect to after group edit screen save.
				 *
				 * Only runs if a redirect has not already occurred.
				 *
				 * @since 2.1.0
				 *
				 * @param string $value URL to redirect to.
				 */
				$redirect_to = apply_filters( 'bp_group_extension_edit_screen_save_redirect', bp_get_requested_url() );

				bp_core_redirect( $redirect_to );
			}
		}

		/**
		 * Load the template that houses the Edit screen.
		 *
		 * @since 1.8.0
		 *
		 * @see BP_Group_Extension::setup_edit_hooks()
		 */
		public function call_edit_screen_template_loader() {
			bp_core_load_template( $this->edit_screen_template );
		}

		/**
		 * Add a submit button to the edit form, if it needs one.
		 *
		 * @since 1.8.0
		 *
		 * @param  string $screen The screen markup, captured in the output
		 *                        buffer.
		 * @return string $screen The same markup, with a submit button added.
		 */
		protected function maybe_add_submit_button( $screen = '' ) {
			if ( $this->has_submit_button( $screen ) ) {
				return $screen;
			}

			return $screen . sprintf(
				'<div id="%s"><input type="submit" name="save" value="%s" id="%s"></div>',
				'bp-group-edit-' . $this->slug . '-submit-wrapper',
				$this->screens['edit']['submit_text'],
				'bp-group-edit-' . $this->slug . '-submit'
			);
		}

		/**
		 * Does the given markup have a submit button?
		 *
		 * @since 1.8.0
		 *
		 * @param  string $screen The markup to check.
		 * @return bool True if a Submit button is found, otherwise false.
		 */
		public static function has_submit_button( $screen = '' ) {
			$pattern = "/<input[^>]+type=[\'\"]submit[\'\"]/";
			preg_match( $pattern, $screen, $matches );
			return ! empty( $matches[0] );
		}

		/**
		 * Detect redirects hardcoded into edit_screen_save() callbacks.
		 *
		 * @since 2.1.0
		 *
		 * @param  string $redirect Redirect string.
		 * @return string
		 */
		public function detect_post_save_redirect( $redirect = '' ) {
			if ( ! empty( $redirect ) ) {
				$this->post_save_redirect = $redirect;
			}

			return $redirect;
		}

		/**
		 * Hook this extension's Admin metabox into BuddyPress, if necessary.
		 *
		 * @since 1.8.0
		 */
		protected function setup_admin_hooks() {
			if ( ! $this->is_screen_enabled( 'admin' ) || ! is_admin() ) {
				return;
			}

			// Hook the admin screen markup function to the content hook.
			add_action( 'bp_groups_admin_meta_box_content_' . $this->slug, array( $this, 'call_admin_screen' ) );

			// Initialize the metabox.
			add_action( 'bp_groups_admin_meta_boxes', array( $this, 'meta_box_display_callback' ) );

			// Catch the metabox save.
			add_action( 'bp_group_admin_edit_after', array( $this, 'call_admin_screen_save' ), 10 );
		}

		/**
		 * Call the admin_screen() method, and add a nonce field.
		 *
		 * @since 1.8.0
		 */
		public function call_admin_screen() {
			call_user_func( $this->screens['admin']['screen_callback'], $this->group_id );
			$this->nonce_field( 'admin' );
		}

		/**
		 * Check the nonce, and call the admin_screen_save() method.
		 *
		 * @since 1.8.0
		 */
		public function call_admin_screen_save() {
			$this->check_nonce( 'admin' );
			call_user_func( $this->screens['admin']['screen_save_callback'], $this->group_id );
		}

		/**
		 * Create the Dashboard meta box for this extension.
		 *
		 * @since 1.7.0
		 */
		public function meta_box_display_callback() {
			// phpcs:disable WordPress.Security.NonceVerification
			$group_id = 0;
			if ( isset( $_GET['gid'] ) ) {
				$group_id = (int) sanitize_text_field( wp_unslash( $_GET['gid'] ) );
			}
			// phpcs:enable WordPress.Security.NonceVerification

			$screen = $this->screens['admin'];

			$extension_slug = $this->slug;
			$callback       = function () use ( $extension_slug, $group_id ) {
				do_action( 'bp_groups_admin_meta_box_content_' . $extension_slug, $group_id );
			};

			add_meta_box(
				$screen['slug'],
				$screen['name'],
				$callback,
				get_current_screen()->id,
				$screen['metabox_context'],
				$screen['metabox_priority']
			);
		}

		/**
		 * Generate the nonce fields for a settings form.
		 *
		 * @since 1.8.0
		 *
		 * @param string $context Screen context. 'create', 'edit', or 'admin'.
		 */
		public function nonce_field( $context = '' ) {
			wp_nonce_field( 'bp_group_extension_' . $this->slug . '_' . $context, '_bp_group_' . $context . '_nonce_' . $this->slug );
		}

		/**
		 * Check the nonce on a submitted settings form.
		 *
		 * @since 1.8.0
		 *
		 * @param string $context Screen context. 'create', 'edit', or 'admin'.
		 */
		public function check_nonce( $context = '' ) {
			check_admin_referer( 'bp_group_extension_' . $this->slug . '_' . $context, '_bp_group_' . $context . '_nonce_' . $this->slug );
		}

		/**
		 * Is the specified screen enabled?
		 *
		 * @since 1.8.0
		 *
		 * @param  string $context Screen context. 'create', 'edit', or 'admin'.
		 * @return bool True if the screen is enabled, otherwise false.
		 */
		public function is_screen_enabled( $context = '' ) {
			$enabled = false;

			if ( isset( $this->screens[ $context ] ) ) {
				$enabled = $this->screens[ $context ]['enabled'] && is_callable( $this->screens[ $context ]['screen_callback'] );
			}

			return (bool) $enabled;
		}

		/**
		 * Get the appropriate screen callback for the specified context/type.
		 *
		 * @since 1.8.0
		 *
		 * @param  string $context Screen context. 'create', 'edit', or 'admin'.
		 * @param  string $type    Screen type. 'screen' or 'screen_save'. Default:
		 *                         'screen'.
		 * @return callable A callable function handle.
		 */
		public function get_screen_callback( $context = '', $type = 'screen' ) {
			$callback = '';

			// Try the context-specific callback first.
			$method  = $context . '_' . $type;
			$rmethod = $this->class_reflection->getMethod( $method );
			if ( isset( $rmethod->class ) && $this->class_name === $rmethod->class ) {
				$callback = array( $this, $method );
			}

			if ( empty( $callback ) ) {
				$fallback_method  = 'settings_' . $type;
				$rfallback_method = $this->class_reflection->getMethod( $fallback_method );
				if ( isset( $rfallback_method->class ) && $this->class_name === $rfallback_method->class ) {
					$callback = array( $this, $fallback_method );
				}
			}

			return $callback;
		}

		/**
		 * Recursive argument parsing.
		 *
		 * @since 1.8.0
		 *
		 * @param  array $a First set of arguments.
		 * @param  array $b Second set of arguments.
		 * @return array Parsed arguments.
		 */
		public static function parse_args_r( &$a, $b ) {
			$a = (array) $a;
			$b = (array) $b;
			$r = $b;

			foreach ( $a as $k => &$v ) {
				if ( is_array( $v ) && isset( $r[ $k ] ) ) {
					$r[ $k ] = self::parse_args_r( $v, $r[ $k ] );
				} else {
					$r[ $k ] = $v;
				}
			}

			return $r;
		}

		/**
		 * Provide access to otherwise unavailable object properties.
		 *
		 * @since 1.8.0
		 *
		 * @param  string $key Property name.
		 * @return mixed The value if found, otherwise null.
		 */
		public function __get( $key ) {
			if ( isset( $this->legacy_properties[ $key ] ) ) {
				return $this->legacy_properties[ $key ];
			} elseif ( isset( $this->data[ $key ] ) ) {
				return $this->data[ $key ];
			} else {
				return null;
			}
		}

		/**
		 * Provide a fallback for isset( $this->foo ) when foo is unavailable.
		 *
		 * @since 1.8.0
		 *
		 * @param  string $key Property name.
		 * @return bool True if the value is set, otherwise false.
		 */
		public function __isset( $key ) {
			if ( isset( $this->legacy_properties[ $key ] ) ) {
				return true;
			} elseif ( isset( $this->data[ $key ] ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Allow plugins to set otherwise unavailable object properties.
		 *
		 * @since 1.8.0
		 *
		 * @param string $key   Property name.
		 * @param mixed  $value Property value.
		 */
		public function __set( $key, $value ) {

			if ( empty( $this->initialized ) ) {
				$this->data[ $key ] = $value;
			}

			switch ( $key ) {
				case 'enable_create_step':
					$this->screens['create']['enabled'] = $value;
					break;

				case 'enable_edit_item':
					$this->screens['edit']['enabled'] = $value;
					break;

				case 'enable_admin_item':
					$this->screens['admin']['enabled'] = $value;
					break;

				case 'create_step_position':
					$this->screens['create']['position'] = $value;
					break;

				// Note: 'admin' becomes 'edit' to distinguish from Dashboard 'admin'.
				case 'admin_name':
					$this->screens['edit']['name'] = $value;
					break;

				case 'admin_slug':
					$this->screens['edit']['slug'] = $value;
					break;

				case 'create_name':
					$this->screens['create']['name'] = $value;
					break;

				case 'create_slug':
					$this->screens['create']['slug'] = $value;
					break;

				case 'admin_metabox_context':
					$this->screens['admin']['metabox_context'] = $value;
					break;

				case 'admin_metabox_priority':
					$this->screens['admin']['metabox_priority'] = $value;
					break;

				default:
					$this->data[ $key ] = $value;
					break;
			}
		}

		/**
		 * Return a list of legacy properties.
		 *
		 * @since 1.8.0
		 *
		 * @return array List of legacy property keys.
		 */
		protected function get_legacy_property_list() {
			return array(
				'name',
				'slug',
				'admin_name',
				'admin_slug',
				'create_name',
				'create_slug',
				'visibility',
				'create_step_position',
				'nav_item_position',
				'admin_metabox_context',
				'admin_metabox_priority',
				'enable_create_step',
				'enable_nav_item',
				'enable_edit_item',
				'enable_admin_item',
				'nav_item_name',
				'display_hook',
				'template_file',
			);
		}

		/**
		 * Parse legacy properties.
		 *
		 * @since 1.8.0
		 */
		protected function parse_legacy_properties() {

			// Only run this one time.
			if ( ! empty( $this->legacy_properties_converted ) ) {
				return;
			}

			$properties = $this->get_legacy_property_list();

			// By-reference variable for convenience.
			$lpc =& $this->legacy_properties_converted; // phpcs:ignore

			foreach ( $properties as $property ) {

				// No legacy config exists for this key.
				if ( ! isset( $this->{$property} ) ) {
					continue;
				}

				// Grab the value and record it as appropriate.
				$value = $this->{$property};

				switch ( $property ) {
					case 'enable_create_step':
						$lpc['screens']['create']['enabled'] = (bool) $value;
						break;

					case 'enable_edit_item':
						$lpc['screens']['edit']['enabled'] = (bool) $value;
						break;

					case 'enable_admin_item':
						$lpc['screens']['admin']['enabled'] = (bool) $value;
						break;

					case 'create_step_position':
						$lpc['screens']['create']['position'] = $value;
						break;

					// Note: 'admin' becomes 'edit' to distinguish from Dashboard 'admin'.
					case 'admin_name':
						$lpc['screens']['edit']['name'] = $value;
						break;

					case 'admin_slug':
						$lpc['screens']['edit']['slug'] = $value;
						break;

					case 'create_name':
						$lpc['screens']['create']['name'] = $value;
						break;

					case 'create_slug':
						$lpc['screens']['create']['slug'] = $value;
						break;

					case 'admin_metabox_context':
						$lpc['screens']['admin']['metabox_context'] = $value;
						break;

					case 'admin_metabox_priority':
						$lpc['screens']['admin']['metabox_priority'] = $value;
						break;

					default:
						$lpc[ $property ] = $value;
						break;
				}
			}
		}

		/**
		 * Set up legacy properties.
		 *
		 * @since 1.8.0
		 *
		 * @see BP_Group_Extension::__get()
		 */
		protected function setup_legacy_properties() {

			// Only run this one time.
			if ( ! empty( $this->legacy_properties ) ) {
				return;
			}

			$properties = $this->get_legacy_property_list();
			$params     = $this->params;
			$lp         =& $this->legacy_properties; // phpcs:ignore

			foreach ( $properties as $property ) {
				switch ( $property ) {
					case 'enable_create_step':
						$lp['enable_create_step'] = $params['screens']['create']['enabled'];
						break;

					case 'enable_edit_item':
						$lp['enable_edit_item'] = $params['screens']['edit']['enabled'];
						break;

					case 'enable_admin_item':
						$lp['enable_admin_item'] = $params['screens']['admin']['enabled'];
						break;

					case 'create_step_position':
						$lp['create_step_position'] = $params['screens']['create']['position'];
						break;

					// Note: 'admin' becomes 'edit' to distinguish from Dashboard 'admin'.
					case 'admin_name':
						$lp['admin_name'] = $params['screens']['edit']['name'];
						break;

					case 'admin_slug':
						$lp['admin_slug'] = $params['screens']['edit']['slug'];
						break;

					case 'create_name':
						$lp['create_name'] = $params['screens']['create']['name'];
						break;

					case 'create_slug':
						$lp['create_slug'] = $params['screens']['create']['slug'];
						break;

					case 'admin_metabox_context':
						$lp['admin_metabox_context'] = $params['screens']['admin']['metabox_context'];
						break;

					case 'admin_metabox_priority':
						$lp['admin_metabox_priority'] = $params['screens']['admin']['metabox_priority'];
						break;

					default:
						// All other items get moved over.
						$lp[ $property ] = $params[ $property ];

						// Also reapply to the object, for backpat.
						$this->{$property} = $params[ $property ];

						break;
				}
			}
		}
	}
endif;
