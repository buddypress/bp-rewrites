<?php
/**
 * BP Rewrites Blogs Component.
 *
 * @package bp-rewrites\src\bp-blogs\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Creates our Blogs component.
 *
 * @since 1.0.0
 */
class Blogs_Component extends \BP_Blogs_Component {
	/**
	 * Start the blogs component setup process.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { /* phpcs:ignore */
		parent::__construct();
	}

	/**
	 * Set up component global variables.
	 *
	 * @since 1.0.0
	 *
	 * @see BP_Component::setup_globals() for a description of arguments.
	 *
	 * @param array $args See BP_Component::setup_globals() for a description.
	 */
	public function setup_globals( $args = array() ) {
		parent::setup_globals( $args );

		bp_component_setup_globals(
			array(
				'rewrite_ids' => array(
					'directory'                    => 'bp_blogs',
					'single_item_action'           => 'bp_blogs_action',
					'single_item_action_variables' => 'bp_blogs_action_variables',
				),
			),
			$this
		);
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
		$blogs_slug = bp_get_blogs_slug();
		$main_nav   = buddypress()->members->nav->get_primary( array( 'component_id' => $this->id ), false );

		// Make sure the main navigation was built the right way.
		if ( ! is_array( $main_nav ) || ! isset( $main_nav[ $blogs_slug ] ) ) {
			return;
		}

		// Set the main nav slug.
		$main_nav = reset( $main_nav );
		$slug     = $main_nav['slug'];

		// Set the main nav `rewrite_id` property.
		$rewrite_id             = sprintf( 'bp_member_%s', $blogs_slug );
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
		reset_secondary_nav( $slug, $rewrite_id );
	}

	/**
	 * Set up bp-blogs integration with the WordPress admin bar.
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
	 * This should be done inside `BP_Blogs_Component::setup_admin_bar()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_nav The Admin Bar items.
	 * @return array The Admin Bar items.
	 */
	public function reset_admin_nav( $wp_admin_nav = array() ) {
		remove_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'reset_admin_nav' ), 10 );

		if ( $wp_admin_nav ) {
			$parent_slug     = bp_get_blogs_slug();
			$rewrite_id      = sprintf( 'bp_member_%s', $parent_slug );
			$root_nav_parent = buddypress()->my_account_menu_id;
			$user_id         = bp_loggedin_user_id();

			// NB: these slugs should probably be customizable.
			$viewes_slugs = array(
				'my-account-' . $this->id . '-my-sites' => 'my-sites',
			);

			foreach ( $wp_admin_nav as $key_item_nav => $item_nav ) {
				$item_nav_id = $item_nav['id'];

				// The Admin Nav Item to access the Create Blog form is specific.
				if ( 'my-account-' . $this->id . '-create' === $item_nav_id ) {
					$wp_admin_nav[ $key_item_nav ]['href'] = bp_get_blog_create_link();

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
		if ( ! is_multisite() ) {
			return parent::add_rewrite_tags( $rewrite_tags );
		}

		$rewrite_tags = array(
			'directory'                    => array(
				'id'    => '%' . $this->rewrite_ids['directory'] . '%',
				'regex' => '([1]{1,})',
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
		if ( ! is_multisite() ) {
			return parent::add_rewrite_rules( $rewrite_rules );
		}

		$rewrite_rules = array(
			'paged-directory'              => array(
				'regex' => $this->root_slug . '/page/?([0-9]{1,})/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&paged=$matches[1]',
			),
			'single-item-action-variables' => array(
				'regex' => $this->root_slug . '/([^/]+)/(.+?)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item_action'] . '=$matches[1]&' . $this->rewrite_ids['single_item_action_variables'] . '=$matches[2]',
			),
			'single-item-action'           => array(
				'regex' => $this->root_slug . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item_action'] . '=$matches[1]',
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
		if ( ! is_multisite() ) {
			return parent::add_permastructs( $permastructs );
		}

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
		if ( ! is_multisite() ) {
			return parent::parse_query( $query );
		}

		// Get the BuddyPress main instance.
		$bp = buddypress();

		if ( home_url( '/' ) === bp_get_requested_url() && bp_is_directory_homepage( $this->id ) ) {
			$query->set( $this->rewrite_ids['directory'], 1 );
		}

		if ( 1 === (int) $query->get( $this->rewrite_ids['directory'] ) ) {
			$bp->current_component = 'blogs';

			$current_action = $query->get( $this->rewrite_ids['single_item_action'] );
			if ( $current_action ) {
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

			// Set the BuddyPress queried object.
			if ( isset( $bp->pages->blogs->id ) ) {
				$query->queried_object    = get_post( $bp->pages->blogs->id );
				$query->queried_object_id = $query->queried_object->ID;
			}
		}

		parent::parse_query( $query );
	}
}
