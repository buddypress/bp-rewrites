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
		// The `$main_nav` needs to include a `rewrite_id` property.
		add_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'setup_main_nav_rewrite_id' ), 10 );

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Setup the main nav rewrite id.
	 *
	 * This should be done inside `bp_core_new_nav_item()`.
	 *
	 * @since 1.0.0
	 */
	public function setup_main_nav_rewrite_id() {
		remove_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'setup_main_nav_rewrite_id' ), 10 );

		$main_nav               = (array) buddypress()->members->nav->get( $this->id );
		$slug                   = bp_get_blogs_slug();
		$main_nav['rewrite_id'] = 'bp_member_' . $slug;

		buddypress()->members->nav->edit_nav( $main_nav, $slug );
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
		add_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'adjust_admin_bar' ), 10, 1 );

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Adjust WordPress admin bar items.
	 *
	 * This should be done inside `BP_Blogs_Component::setup_admin_bar()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_nav The Admin Bar items.
	 * @return array The Admin Bar items.
	 */
	public function adjust_admin_bar( $wp_admin_nav = array() ) {
		remove_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'adjust_admin_bar' ), 10, 1 );

		foreach ( $wp_admin_nav as $key_item_nav => $item_nav ) {
			if ( 'my-account-' . $this->id . '-create' !== $item_nav['id'] ) {
				continue;
			}

			$wp_admin_nav[ $key_item_nav ]['href'] = bp_get_blog_create_link();
		}

		return $wp_admin_nav;
	}

	/**
	 * Add the component's rewrite tags.
	 *
	 * @since ?.0.0
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
				'regex' => '(.*?)',
			),
		);

		bp_component_add_rewrite_tags( $rewrite_tags );

		\BP_Component::add_rewrite_tags( $rewrite_tags );
	}

	/**
	 * Add the component's rewrite rules.
	 *
	 * @since ?.0.0
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
				'regex' => $this->root_slug . '/([^/]+)/(.*?)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item_action'] . '=$matches[1]&' . $this->rewrite_ids['single_item_action_variables'] . '=$matches[2]',
			),
			'single-item-action'           => array(
				'regex' => $this->root_slug . '/([^/]+)/?$',
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1&' . $this->rewrite_ids['single_item_action'] . '=$matches[1]',
			),
			'directory'                    => array(
				'regex' => $this->root_slug,
				'query' => 'index.php?' . $this->rewrite_ids['directory'] . '=1',
			),
		);

		bp_component_add_rewrite_rules( $rewrite_rules );

		\BP_Component::add_rewrite_rules( $rewrite_rules );
	}

	/**
	 * Add the component's directory permastructs.
	 *
	 * @since ?.0.0
	 *
	 * @param array $permastructs Optional. See BP_Component::add_permastructs() for
	 *                            description.
	 */
	public function add_permastructs( $permastructs = array() ) {
		if ( ! is_multisite() ) {
			return parent::add_permastructs( $structs );
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
	 * @since ?.0.0
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
			$query->queried_object    = get_post( $bp->pages->blogs->id );
			$query->queried_object_id = $query->queried_object->ID;
		}

		parent::parse_query( $query );
	}
}
