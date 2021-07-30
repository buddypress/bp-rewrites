<?php
/**
 * BP Rewrites Settings Component.
 *
 * @package bp-rewrites\src\bp-settings\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Main Settings Class.
 *
 * @since 1.0.0
 */
class Settings_Component extends \BP_Settings_Component {
	/**
	 * Start the settings component setup process.
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
		$main_nav = buddypress()->members->nav->get_primary( array( 'component_id' => $this->id ), false );

		// Set the main nav slug.
		$main_nav = reset( $main_nav );
		$slug     = $main_nav['slug'];

		// Set the main nav`rewrite_id` property.
		$main_nav['rewrite_id'] = sprintf( 'bp_member_%s', bp_get_settings_slug() );

		// Reset the link using BP Rewrites.
		$main_nav['link'] = bp_members_rewrites_get_nav_url( $main_nav );

		// Update the primary nav item.
		buddypress()->members->nav->edit_nav( $main_nav, $slug );

		// Get the sub nav items for this main nav.
		$sub_nav_items = buddypress()->members->nav->get_secondary( array( 'parent_slug' => $slug ), false );

		// Loop inside it to reset the link using BP Rewrites before updating it.
		foreach ( $sub_nav_items as $sub_nav_item ) {
			$sub_nav_item['link'] = bp_members_rewrites_get_nav_url( $sub_nav_item );

			// Update the secondary nav item.
			buddypress()->members->nav->edit_nav( $sub_nav_item, $sub_nav_item['slug'], $slug );
		}
	}
}
