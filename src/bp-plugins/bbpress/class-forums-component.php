<?php
/**
 * BP Rewrites Forums Component.
 *
 * @package bp-rewrites\src\bp-plugins\bbpress
 * @since 1.3.0
 */

namespace BP\Rewrites;

/**
 * Main Forums Class.
 *
 * @since 1.3.0
 */
class Forums_Component extends \BBP_Forums_Component {
	/**
	 * Overrides parent::includes() to adapt the group extension
	 * to BP Rewrites.
	 *
	 * @since 1.3.0
	 *
	 * @param array $includes An array containing the scripts to include.
	 */
	public function includes( $includes = array() ) {
		parent::includes( $includes );

		// BuddyPress Group Extension class.
		if ( bbp_is_group_forums_active() && bp_is_active( 'groups' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-forums-group-extension.php';
		}
	}

	/**
	 * Instantiate classes for BuddyPress integration
	 *
	 * @since 1.3.0
	 */
	public function setup_components() {
		// Always load the members component.
		bbpress()->extend->buddypress->members = new \BBP_BuddyPress_Members();

		// Create new activity class.
		if ( bp_is_active( 'activity' ) ) {
			bbpress()->extend->buddypress->activity = new \BBP_BuddyPress_Activity();
		}

		// Register the group extension only if groups are active.
		if ( bbp_is_group_forums_active() && bp_is_active( 'groups' ) ) {
			bp_register_group_extension( 'BP\Rewrites\Forums_Group_Extension' );
		}
	}
}
