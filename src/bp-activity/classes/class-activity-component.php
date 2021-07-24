<?php
/**
 * BP Rewrites Activity Component.
 *
 * @package bp-rewrites\src\bp-activity\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Activity Class.
 *
 * @since 1.0.0
 */
class Activity_Component extends \BP_Activity_Component {
	/**
	 * Start the activity component setup process.
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
					'directory'                    => 'bp_activities',
					'single_item_action'           => 'bp_activity_action',
					'single_item_action_variables' => 'bp_activity_action_variables',
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
		add_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'setup_main_nav_rewrite_id' ) );

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
		remove_action( 'bp_' . $this->id . '_setup_nav', array( $this, 'setup_main_nav_rewrite_id' ) );

		$main_nav               = (array) buddypress()->members->nav->get( $this->id );
		$slug                   = bp_get_activity_slug();
		$main_nav['rewrite_id'] = 'bp_member_' . $slug;

		buddypress()->members->nav->edit_nav( $main_nav, $slug );
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
		if ( 1 === (int) $query->get( $this->rewrite_ids['directory'] ) ) {
			$bp = buddypress();

			// Set the Activity component as current.
			$bp->current_component = 'activity';

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

			/**
			 * Set the BuddyPress queried object.
			 */
			$query->queried_object    = get_post( $bp->pages->activity->id );
			$query->queried_object_id = $query->queried_object->ID;
		}

		bp_component_parse_query( $query );

		\BP_Component::parse_query( $query );
	}
}
