<?php
/**
 * BP Rewrites Funcions.
 *
 * @package bp-rewrites\inc\functions
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load translation.
 *
 * @since 1.0.0
 */
function load_translation() {
	$bpr = bp_rewrites();

	// Load translations.
	load_plugin_textdomain( 'bp-rewrites', false, trailingslashit( basename( $bpr->dir ) ) . 'languages' );
}
add_action( 'bp_loaded', __NAMESPACE__ . '\load_translation' );

/**
 * Setup the Core component.
 *
 * @since 1.0.0
 */
function override_bp_core() {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/bp-core/classes/class-core-component.php';

	buddypress()->core = new Core_Component();
}
remove_action( 'bp_loaded', 'bp_setup_core', 0 );
add_action( 'bp_loaded', __NAMESPACE__ . '\override_bp_core', 0 );

/**
 * Setup the Activity Component.
 *
 * @since 1.0.0
 */
function bp_setup_activity() {
	require_once bp_rewrites()->dir . 'src/bp-activity/classes/class-activity-component.php';

	buddypress()->activity = new Activity_Component();
}

/**
 * Setup the Blogs Component.
 *
 * @since 1.0.0
 */
function bp_setup_blogs() {
	require_once bp_rewrites()->dir . 'src/bp-blogs/classes/class-blogs-component.php';

	buddypress()->blogs = new Blogs_Component();
}

/**
 * Setup the Friends Component.
 *
 * @since 1.0.0
 */
function bp_setup_friends() {
	require_once bp_rewrites()->dir . 'src/bp-friends/classes/class-friends-component.php';

	buddypress()->friends = new Friends_Component();
}

/**
 * Set up the Groups component.
 *
 * @since 1.0.0
 */
function bp_setup_groups() {
	require_once bp_rewrites()->dir . 'src/bp-groups/classes/class-groups-component.php';

	buddypress()->groups = new Groups_Component();
}

/**
 * Setup the Members Component.
 *
 * @since 1.0.0
 */
function bp_setup_members() {
	require_once bp_rewrites()->dir . 'src/bp-members/classes/class-members-component.php';

	buddypress()->members = new Members_Component();
}

/**
 * Setup the Messages Component.
 *
 * @since 1.0.0
 */
function bp_setup_messages() {
	require_once bp_rewrites()->dir . 'src/bp-messages/classes/class-messages-component.php';

	buddypress()->messages = new Messages_Component();
}

/**
 * Setup the Notifications Component.
 *
 * @since 1.0.0
 */
function bp_setup_notifications() {
	require_once bp_rewrites()->dir . 'src/bp-notifications/classes/class-notifications-component.php';

	buddypress()->notifications = new Notifications_Component();
}

/**
 * Setup the Settings Component.
 *
 * @since 1.0.0
 */
function bp_setup_settings() {
	require_once bp_rewrites()->dir . 'src/bp-settings/classes/class-settings-component.php';

	buddypress()->settings = new Settings_Component();
}

/**
 * Setup the Settings Component.
 *
 * @since 1.0.0
 */
function bp_setup_xprofile() {
	require_once bp_rewrites()->dir . 'src/bp-xprofile/classes/class-xprofile-component.php';

	buddypress()->profile = new XProfile_Component();
}

/**
 * Override BuddyPress Components.
 *
 * @since 1.0.0
 */
function override_bp_components() {
	// Avoid BP Components initialization to directly replace them by BP Rewrites ones.
	$bp_components = array(
		'activity'      => array(
			'callback' => 'bp_setup_activity',
			'priority' => 6,
		),
		'blogs'         => array(
			'callback' => 'bp_setup_blogs',
			'priority' => 6,
		),
		'friends'       => array(
			'callback' => 'bp_setup_friends',
			'priority' => 6,
		),
		'groups'        => array(
			'callback' => 'bp_setup_groups',
			'priority' => 6,
		),
		'members'       => array(
			'callback' => 'bp_setup_members',
			'priority' => 1,
		),
		'messages'      => array(
			'callback' => 'bp_setup_messages',
			'priority' => 6,
		),
		'notifications' => array(
			'callback' => 'bp_setup_notifications',
			'priority' => 6,
		),
		'settings'      => array(
			'callback' => 'bp_setup_settings',
			'priority' => 6,
		),
		'xprofile'      => array(
			'callback' => 'bp_setup_xprofile',
			'priority' => 2,
		),
	);

	foreach ( $bp_components as $component => $hook ) {
		if ( ! bp_is_active( $component ) ) {
			continue;
		}

		// Unregister BuddyPress components.
		remove_action( 'bp_setup_components', $hook['callback'], $hook['priority'] );

		// Register this plugin's components.
		add_action( 'bp_setup_components', __NAMESPACE__ . '\\' . $hook['callback'], $hook['priority'], 0 );
	}
}
add_action( 'bp_setup_components', __NAMESPACE__ . '\override_bp_components', 0 );

/**
 * Code to move inside BP_Component::setup_globals().
 *
 * @since ?.0.0
 *
 * @param array        $args {
 *     Optional. An array of arguments.
 *     @see BP_Component::setup_globals() for the list of arguments.
 * }
 * @param BP_Component $component The component to add globals to.
 */
function bp_component_setup_globals( $args = array(), $component = null ) {
	$r = wp_parse_args(
		$args,
		array(
			'rewrite_ids' => array(),
		)
	);

	/**
	 * Filters the component's rewrite IDs if available.
	 *
	 * @since ?.0.0
	 *
	 * @param array $value The list of rewrite IDs of the component.
	 */
	$component->rewrite_ids = apply_filters( 'bp_' . $component->id . '_rewrite_ids', $r['rewrite_ids'] );

	// Set the component's directory permastruct early so that it's available to build links.
	if ( $component->has_directory && isset( $component->rewrite_ids['directory'] ) ) {
		$component->directory_permastruct = $component->root_slug . '/%' . $component->rewrite_ids['directory'] . '%';
	}
}

/**
 * Code to move inside BP_Component::add_rewrite_tags().
 *
 * @since ?.0.0
 *
 * @param array $rewrite_tags {
 *     Associative array of arguments list used to register WordPress permastructs.
 *     The main array keys describe the rules type and allow individual edits if needed.
 *
 *     @type string $id    The name of the new rewrite tag. Required.
 *     @type string $regex The regular expression to substitute the tag for in rewrite rules.
 *                         Required.
 * }
 */
function bp_component_add_rewrite_tags( $rewrite_tags = array() ) {
	if ( $rewrite_tags ) {
		foreach ( (array) $rewrite_tags as $rewrite_tag ) {
			if ( ! isset( $rewrite_tag['id'] ) || ! isset( $rewrite_tag['regex'] ) ) {
				continue;
			}

			add_rewrite_tag( $rewrite_tag['id'], $rewrite_tag['regex'] );
		}
	}
}

/**
 * Code to move inside BP_Component::add_rewrite_rules().
 *
 * @since ?.0.0
 *
 * @param array $rewrite_rules {
 *     Associative array of arguments list used to register WordPress permastructs.
 *     The main array keys describe the rules type and allow individual edits if needed.
 *
 *     @type string $regex    Regular expression to match request against. Required.
 *     @type string $query    The corresponding query vars for this rewrite rule. Required.
 *     @type string $priority The Priority of the new rule. Accepts 'top' or 'bottom'. Optional.
 *                            Default 'top'.
 * }
 */
function bp_component_add_rewrite_rules( $rewrite_rules = array() ) {
	$priority = 'top';

	if ( $rewrite_rules ) {
		foreach ( $rewrite_rules as $rewrite_rule ) {
			if ( ! isset( $rewrite_rule['regex'] ) || ! isset( $rewrite_rule['query'] ) ) {
				continue;
			}

			if ( ! isset( $rewrite_rule['priority'] ) || ! $rewrite_rule['priority'] ) {
				$rewrite_rule['priority'] = $priority;
			}

			add_rewrite_rule( $rewrite_rule['regex'], $rewrite_rule['query'], $rewrite_rule['priority'] );
		}
	}
}

/**
 * Code to move inside BP_Component::add_permastructs().
 *
 * @since ?.0.0
 *
 * @param array $permastructs {
 *      Associative array of arguments list used to register WordPress permastructs.
 *      The main array keys hold the name argument of the `add_permastruct()` function.
 *
 *      @type string $permastruct The permalink structure. Required.
 *      @type array  $args        The permalink structure arguments. Optional.
 * }
 */
function bp_component_add_permastructs( $permastructs = array() ) {
	if ( $permastructs ) {
		foreach ( (array) $permastructs as $name => $params ) {
			if ( ! $name || ! isset( $params['permastruct'] ) || ! $params['permastruct'] ) {
				continue;
			}

			if ( ! $params['args'] ) {
				$params['args'] = array();
			}

			$args = wp_parse_args(
				$params['args'],
				array(
					'with_front'  => false,
					'ep_mask'     => EP_NONE,
					'paged'       => true,
					'feed'        => false,
					'forcomments' => false,
					'walk_dirs'   => true,
					'endpoints'   => false,
				)
			);

			// Add the permastruct.
			add_permastruct( $name, $params['permastruct'], $args );
		}
	}
}

/**
 * Code to move inside BP_Component::parse_query().
 *
 * @since ?.0.0
 *
 * @param \WP_Query $query The main WP_Query.
 */
function bp_component_parse_query( \WP_Query $query ) {
	if ( is_buddypress() ) {
		// This should be `array( $this, 'pre_query' )`.
		add_filter( 'posts_pre_query', __NAMESPACE__ . '\bp_component_pre_query', 10, 2 );
	}
}

/**
 * Code to move inside BP_Component::pre_query().
 *
 * Make sure to avoid querying for regular posts when displaying a BuddyPress page.
 *
 * @since ?.0.0
 *
 * @param  null      $return A null value to use the regular WP Query.
 * @param  \WP_Query $query  The WP Query object.
 * @return null|array       Null if not displaying a BuddyPress page.
 *                          An array containing the BuddyPress directory Post otherwise.
 */
function bp_component_pre_query( $return = null, \WP_Query $query = null ) {
	// This should be `array( $this, 'pre_query' )`.
	remove_filter( 'posts_pre_query', __NAMESPACE__ . '\bp_component_pre_query', 10 );

	$queried_object = $query->get_queried_object();

	if ( $queried_object instanceof \WP_Post && 'buddypress' === get_post_type( $queried_object ) ) {
		/*
		 * @todo check the following in BuddyPress Core.
		 *
		 * `bp_current_user_can` is removing the first argument if it's an integer and use it as the
		 * site's ID for backward compatibility. Is this still necessary?
		 */
		if ( ! bp_current_user_can( 'bp_read', array( 'bp_page' => $queried_object ) ) ) {
			$bp                    = buddypress();
			$bp->current_component = 'core';

			// Unset other BuddyPress URI globals.
			foreach ( array( 'current_item', 'current_action', 'action_variables', 'displayed_user' ) as $global ) {
				if ( 'action_variables' === $global ) {
					$bp->{$global} = array();
				} elseif ( 'displayed_user' === $global ) {
					$bp->{$global} = new \stdClass();
				} else {
					$bp->{$global} = '';
				}
			}

			// Reset the post.
			$post = (object) array(
				'ID'             => 0,
				'post_type'      => 'buddypress',
				'post_name'      => 'restricted',
				'post_title'     => __( 'Restricted area', 'bp-rewrites' ),
				'post_content'   => buffer_template_asset(),
				'comment_status' => 'closed',
				'comment_count'  => 0,
			);

			// Reset the queried object.
			$query->queried_object    = get_post( $post );
			$query->queried_object_id = $query->queried_object->ID;

			// Reset the posts.
			$posts = array( $query->queried_object );

			// Reset some WP Query properties.
			$query->found_posts   = 1;
			$query->max_num_pages = 1;
			$query->posts         = $posts;
			$query->post          = $post;
			$query->post_count    = 1;
			$query->is_home       = false;
			$query->is_front_page = false;
			$query->is_page       = false;
			$query->is_single     = true;
			$query->is_archive    = false;
			$query->is_tax        = false;

			// Make sure no comments are displayed for this page.
			add_filter( 'comments_pre_query', 'bp_comments_pre_query', 10, 2 );

			if ( function_exists( 'wp_get_global_styles' ) ) {
				add_action( 'bp_enqueue_community_scripts', __NAMESPACE__ . '\add_bp_login_block_inline_style' );
			}

			// Return the posts making sure no additional queries are performed.
			return $posts;
		}

		// Return the posts making sure no additional queries are performed.
		return array( $queried_object );
	}

	// Leave WordPress query posts.
	return null;
}

/**
 * Code to move inside BP_Core::register_post_types().
 *
 * @since ?.0.0
 */
function bp_core_register_post_types() {
	if ( (int) get_current_blog_id() === bp_get_post_type_site_id() ) {
		register_post_type(
			'buddypress',
			array(
				'label'               => _x( 'BuddyPress Directories', 'Post Type label', 'bp-rewrites' ),
				'labels'              => array(
					'singular_name' => _x( 'BuddyPress Directory', 'Post Type singular name', 'bp-rewrites' ),
				),
				'description'         => __( 'The BuddyPress Post Type is used when Pretty URLs are active.', 'bp-rewrites' ),
				'public'              => false,
				'hierarchical'        => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_ui'             => false,
				'show_in_nav_menus'   => true,
				'show_in_rest'        => false,
				'supports'            => array( 'title' ),
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'delete_with_user'    => false,
			)
		);

		bp_rewrites_register_post_status();
	}
}
add_action( 'bp_core_register_post_types', __NAMESPACE__ . '\bp_core_register_post_types' );

/**
 * Registers a specific post status used to make the community only visible to members.
 *
 * @since 1.5.0
 */
function bp_rewrites_register_post_status() {
	// Check BuddyPress is >= 11.0.
	if ( function_exists( 'bp_core_get_directory_pages_stati' ) ) {
		register_post_status(
			'bp_restricted',
			array(
				'label'    => _x( 'Restricted to members', 'post status', 'bp-rewrites' ),
				'public'   => false,
				'internal' => true,
			)
		);
	}
}

/**
 * Checks if the displayed page is the WP Login one.
 *
 * @since 1.0.0
 */
function is_login_page() {
	$is_login = false;

	if ( isset( $GLOBALS['pagenow'] ) && ( false !== strpos( $GLOBALS['pagenow'], 'wp-login.php' ) ) ) {
		$is_login = true;
	} elseif ( isset( $_SERVER['SCRIPT_NAME'] ) ) {
		$script_name = esc_url_raw( wp_unslash( $_SERVER['SCRIPT_NAME'] ) );
		$is_login    = false !== strpos( $script_name, 'wp-login.php' );
	}

	return $is_login;
}

/**
 * Neutralize the BuddyPress Legacy URL parser.
 *
 * @since 1.0.0
 */
function disable_buddypress_legacy_url_parser() {
	remove_action( 'bp_init', 'bp_core_set_uri_globals', 2 );

	// Stop hooking `bp_init` to setup the canonical stack & BP document title.
	remove_action( 'bp_init', 'bp_setup_canonical_stack', 5 );
	remove_action( 'bp_init', 'bp_core_action_search_site', 7 );
	remove_action( 'bp_init', 'bp_setup_title', 8 );
	remove_action( 'bp_init', '_bp_maybe_remove_redirect_canonical' );
	remove_action( 'bp_init', 'bp_remove_adjacent_posts_rel_link' );

	/*
	 * @todo raise the priority of the action hooked to `bp_parse_query` in
	 * `\BP_Component::setup_actions()`.
	 * Then raise the following priorities accordingly and before `10`.
	 */

	// Start hooking `bp_parse_query` to setup the canonical stack & BP document title.
	add_action( 'bp_parse_query', 'bp_setup_canonical_stack', 11 );
	add_action( 'bp_parse_query', 'bp_core_action_search_site', 13, 0 );
	add_action( 'bp_parse_query', 'bp_setup_title', 14 );
	add_action( 'bp_parse_query', '_bp_maybe_remove_redirect_canonical', 20 );
	add_action( 'bp_parse_query', 'bp_remove_adjacent_posts_rel_link', 20 );

	// On front-end, hook to `bp_parse_query` instead of `bp_init` to set up the navigation.
	if ( wp_using_themes() || wp_doing_ajax() || is_login_page() ) {
		remove_action( 'bp_init', 'bp_setup_nav', 6 );
		add_action( 'bp_parse_query', 'bp_setup_nav', 12 );
	}

	/*
	 * Remove the Members invitations WP Admin Bar menu items to override it later
	 * so that links are built using BP Rewrites.
	 *
	 * @see `BP\Rewrites\bp_members_admin_bar_add_invitations_menu()`
	 */
	remove_action( 'bp_setup_admin_bar', 'bp_members_admin_bar_add_invitations_menu', 90 );

	/*
	 * This filter is causing a too early call to `bp_is_activity_directory()`
	 *
	 * More globally the Activity Heartbeat feature should be improved by putting the script
	 * inside its own file.
	 */
	remove_filter( 'bp_core_get_js_dependencies', 'bp_activity_get_js_dependencies', 10 );
}
add_action( 'bp_init', __NAMESPACE__ . '\disable_buddypress_legacy_url_parser', 1 );

/**
 * This should be inside `bp_core_get_directory_pages()`.
 *
 * @since 1.0.0
 *
 * @param object $pages Object holding BuddyPress directory page names and slugs.
 * @return object       The same objects with custom slugs.
 */
function get_components_custom_slugs( $pages = null ) {
	if ( $pages ) {
		foreach ( $pages as $component_id => $page ) {
			$pages->{$component_id}->custom_slugs = get_post_meta( $page->id, '_bp_component_slugs', true );
			$pages->{$component_id}->visibility   = get_post_status( $page->id );
		}
	}

	return $pages;
}
add_filter( 'bp_core_get_directory_pages', __NAMESPACE__ . '\get_components_custom_slugs', 1 );

/**
 * Resets Secondary nav items for given parent slug and rewrite ID.
 *
 * @since 1.0.0
 *
 * @param string $parent_slug       The subnav parent's item slug.
 * @param string $parent_rewrite_id The subnav parent's item rewrite ID.
 * @param string $component_id      The component's ID (eg: members, groups).
 */
function reset_secondary_nav( $parent_slug = '', $parent_rewrite_id = '', $component_id = '' ) {
	if ( ! $parent_slug || ! $parent_rewrite_id ) {
		return;
	}

	$bp = buddypress();

	if ( ! $component_id ) {
		$component_id = $bp->members->id;
	}

	// Get the sub nav items for this main nav.
	$sub_nav_items = $bp->{$component_id}->nav->get_secondary( array( 'parent_slug' => $parent_slug ), false );

	// Loop inside it to reset the link using BP Rewrites before updating it.
	foreach ( $sub_nav_items as $sub_nav_item ) {
		$builtin_subnav_slug = $sub_nav_item['slug'];

		// Set the subnav rewrite ID.
		$sub_nav_item['rewrite_id'] = sprintf(
			'%1$s_%2$s',
			$parent_rewrite_id,
			str_replace( '-', '_', $sub_nav_item['slug'] )
		);

		// Reset subnav slug with potential custom slug.
		$subnav_slug = bp_rewrites_get_slug( $component_id, $sub_nav_item['rewrite_id'], $sub_nav_item['slug'] );

		$sub_nav_item['link'] = bp_members_rewrites_get_nav_url(
			array(
				'rewrite_id'     => $parent_rewrite_id,
				'item_component' => $parent_slug,
				'item_action'    => $subnav_slug,
			)
		);

		// Update the secondary nav item.
		$bp->{$component_id}->nav->edit_nav( $sub_nav_item, $builtin_subnav_slug, $parent_slug );
	}
}

/**
 * Simple utilily to empty a form action.
 *
 * @since 1.0.0
 *
 * @param string $form_id The Form ID attribute.
 */
function empty_form_action( $form_id = '' ) {
	if ( ! $form_id ) {
		return;
	} else {
		$form_id = sanitize_text_field( $form_id );
	}

	printf(
		"<script type=\"text/javascript\">\n%s\n</script>\n",
		sprintf(
			'( function() {
				var bpRewritesReplaceFormAction = function() {
					document.querySelector( \'#%s\' ).setAttribute( \'action\', \'\' );
				};

				if ( \'loading\' === document.readyState ) {
					document.addEventListener( \'DOMContentLoaded\', bpRewritesReplaceFormAction() );
				} else {
					bpRewritesReplaceFormAction();
				}
			} )();',
			$form_id //phpcs:ignore
		)
	);
}

/**
 * Checks if current context is a BuddyPress Ajax request.
 *
 * @since 1.4.0
 *
 * @return bool True if current context is a BuddyPress Ajax request. False otherwise.
 */
function is_bp_doing_ajax() {
	return isset( buddypress()->ajax->WP );
}

/**
 * Adds the `bp_restricted` status to the allowed BP Directory Pages stati.
 *
 * @since 1.5.0
 *
 * @param array $stati The allowed BP Directory Pages stati.
 * @return array The allowed BP Directory Pages stati.
 */
function get_directory_pages_stati( $stati = array() ) {
	$stati[] = 'bp_restricted';
	return $stati;
}
add_filter( 'bp_core_get_directory_pages_stati', __NAMESPACE__ . '\get_directory_pages_stati' );

/**
 * Get Templates directory.
 *
 * Templates in this directory are used as default templates.
 *
 * @since 1.5.0
 */
function get_templates_dir() {
	return trailingslashit( bp_rewrites()->dir ) . 'src/bp-templates';
}

/**
 * Temporarly add the templates directory to the BuddyPress Templates stack.
 *
 * @since 1.5.0
 *
 * @param array $stack The BuddyPress Templates stack.
 * @return array       The same Templates stack including the templates directory.
 */
function get_template_stack( $stack = array() ) {
	return array_merge( $stack, array( get_templates_dir() ) );
}

/**
 * Start filtering the template stack to include the templates directory.
 *
 * @since 1.5.0
 */
function start_overriding_template_stack() {
	add_filter( 'bp_get_template_stack', __NAMESPACE__ . '\get_template_stack' );
}

/**
 * Stop filtering the template stack to exclude the templates directory.
 *
 * @since 1.5.0
 */
function stop_overriding_template_stack() {
	remove_filter( 'bp_get_template_stack', __NAMESPACE__ . '\get_template_stack' );
}

/**
 * Buffer the required template asset.
 *
 * @since 1.5.0
 *
 * @param string $name The template name to use.
 */
function buffer_template_asset( $name = 'restricted-message' ) {
	// Temporarly overrides the BuddyPress Template Stack.
	start_overriding_template_stack();

	// Load the template parts.
	$content = \bp_buffer_template_part( 'assets/utils/' . $name, null, false );

	// Stop overidding the BuddyPress Template Stack.
	stop_overriding_template_stack();

	return $content;
}

/**
 * Use WP global styles to improve the bp/login-form submit button display.
 *
 * @since 1.5.0
 */
function add_bp_login_block_inline_style() {
	$styles     = wp_get_global_styles();
	$rules      = array();
	$hover_rule = '';

	if ( isset( $styles['elements']['button']['color']['text'] ) ) {
		$rules[] = 'color: ' . $styles['elements']['button']['color']['text'] . ';';
	}

	if ( isset( $styles['elements']['button']['color']['background'] ) ) {
		$hover_rule = 'background: ' . $styles['elements']['button']['color']['background'] . ';';
		$rules[]    = $hover_rule;
		$rules[]    = 'background: var(--wp--preset--color--primary);';
	}

	if ( isset( $styles['elements']['button']['spacing']['padding'] ) ) {
		$rules[] = 'padding: ' . $styles['elements']['button']['spacing']['padding'] . ';';
	}

	if ( $rules && $hover_rule ) {
		wp_add_inline_style(
			'bp-login-form-block',
			sprintf(
				'input#bp-login-widget-submit {
					border-width: 0;
					cursor: pointer;
					%1$s
				}

				input#bp-login-widget-submit,
				#bp-login-widget-form .bp-login-widget-register-link {
					font-size: var(--wp--preset--font-size--medium);
					vertical-align: baseline;
				}

				input#bp-login-widget-submit:hover {
					%2$s
				}',
				implode( "\n", $rules ),
				$hover_rule
			)
		);
	}
}

/**
 * Checks if the current context needs a query check.
 *
 * @since 1.6.0
 *
 * @return boolean True if the current context needs a query check. False otherwise.
 */
function needs_query_check() {
	$retval      = null;
	$request_uri = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	// Into WP Admin context BP Front end globals are not set.
	$request  = wp_parse_url( $request_uri, PHP_URL_PATH );
	$is_admin = ( false !== strpos( $request, '/wp-admin' ) || is_admin() ) && ! wp_doing_ajax();

	// Into the following contexts BP Front end globals are also not set.
	$wp_specific_paths   = array( '/wp-login.php', '/wp-comments-post.php', '/wp-signup.php', '/wp-activate.php', '/wp-trackback.php' );
	$is_wp_specific_path = false;

	foreach ( $wp_specific_paths as $wp_specific_path ) {
		if ( false === strpos( $request, $wp_specific_path ) ) {
			continue;
		}

		$is_wp_specific_path = true;
	}

	// The BP REST API needs more work.
	$is_rest = false !== strpos( $request, '/' . rest_get_url_prefix() ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST );

	// The XML RPC API needs more work.
	$is_xmlrpc = defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST;

	$bypass_query_check = $is_admin || $is_wp_specific_path || $is_rest || $is_xmlrpc || wp_doing_cron();

	return ! $bypass_query_check;
}
