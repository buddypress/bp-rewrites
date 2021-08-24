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
 * Disable BuddyPress Components.
 *
 * @since 1.0.0
 */
function disable_bp_components() {
	// @todo Add other BP Components.
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
add_action( 'bp_setup_components', __NAMESPACE__ . '\disable_bp_components', 0 );

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
	remove_filter( 'posts_pre_query', __NAMESPACE__ . '\bp_component_pre_query', 10, 2 );

	$queried_object = $query->get_queried_object();

	if ( $queried_object instanceof \WP_Post && 'buddypress' === get_post_type( $queried_object ) ) {
		return array( $queried_object );
	}

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
				'label'               => _x( 'BuddyPress Directories', 'Post Type label', 'buddypress' ),
				'labels'              => array(
					'singular_name' => _x( 'BuddyPress Directory', 'Post Type singular name', 'buddypress' ),
				),
				'description'         => __( 'The BuddyPress Post Type is used when Pretty URLs are active.', 'buddypress' ),
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
	}
}
add_action( 'bp_core_register_post_types', __NAMESPACE__ . '\bp_core_register_post_types' );

/**
 * Checks if the displayed page is the WP Login one.
 *
 * @since 1.0.0
 */
function is_login_page() {
	$is_login = false;

	if ( isset( $GLOBALS['pagenow'] ) && ( false !== strpos( $GLOBALS['pagenow'], 'wp-login.php' ) ) ) {
		$is_login = true;
	} elseif ( isset( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) ) { // phpcs:ignore
		$is_login = true;
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
	add_action( 'bp_parse_query', 'bp_core_action_search_site', 13 );
	add_action( 'bp_parse_query', 'bp_setup_title', 14 );
	add_action( 'bp_parse_query', '_bp_maybe_remove_redirect_canonical', 20 );
	add_action( 'bp_parse_query', 'bp_remove_adjacent_posts_rel_link', 20 );

	/**
	 * On front-end, hook to `bp_parse_query` instead of `bp_init` to set up the navigation.
	 *
	 * @todo replace `apply_filters( 'wp_using_themes', defined( 'WP_USE_THEMES' ) && WP_USE_THEMES )`
	 * with `wp_using_themes()` once BP Required version is >= 5.1
	 *
	 * @see `bp_nav_menu_get_loggedin_pages()`
	 */
	if ( apply_filters( 'wp_using_themes', defined( 'WP_USE_THEMES' ) && WP_USE_THEMES ) || wp_doing_ajax() || is_login_page() ) {
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
		}
	}

	return $pages;
}
add_filter( 'bp_core_get_directory_pages', __NAMESPACE__ . '\get_components_custom_slugs', 1 );

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
