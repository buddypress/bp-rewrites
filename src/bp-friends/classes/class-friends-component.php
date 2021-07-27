<?php
/**
 * BP Rewrites Friends Component.
 *
 * @package bp-rewrites\src\bp-friends\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Main Friends Class.
 *
 * @since 1.0.0
 */
class Friends_Component extends \BP_Friends_Component {
	/**
	 * Start the friends component setup process.
	 *
	 * @since 1.0.0
	 */
	public function __construct() { /* phpcs:ignore */
		parent::__construct();
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
		$slug                   = bp_get_friends_slug();
		$main_nav['rewrite_id'] = 'bp_member_' . $slug;

		buddypress()->members->nav->edit_nav( $main_nav, $slug );
	}
}
