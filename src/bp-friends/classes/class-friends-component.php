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
		$friends_slug = bp_get_friends_slug();
		$main_nav     = buddypress()->members->nav->get_primary( array( 'component_id' => $this->id ), false );

		// Make sure the main navigation was built the right way.
		if ( ! is_array( $main_nav ) || ! isset( $main_nav[ $friends_slug ] ) ) {
			return;
		}

		// Set the main nav slug.
		$main_nav = reset( $main_nav );
		$slug     = $main_nav['slug'];

		// Set the main nav `rewrite_id` property.
		$rewrite_id             = sprintf( 'bp_member_%s', $friends_slug );
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

		// Get the sub nav items for this main nav.
		$sub_nav_items = buddypress()->members->nav->get_secondary( array( 'parent_slug' => $slug ), false );

		// Update the secondary nav items.
		reset_secondary_nav( $slug, $rewrite_id );
	}

	/**
	 * Set up bp-friends integration with the WordPress admin bar.
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
	 * This should be done inside `BP_Friends_Component::setup_admin_bar()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_admin_nav The Admin Bar items.
	 * @return array The Admin Bar items.
	 */
	public function reset_admin_nav( $wp_admin_nav = array() ) {
		remove_filter( 'bp_' . $this->id . '_admin_nav', array( $this, 'reset_admin_nav' ), 10 );

		if ( $wp_admin_nav ) {
			$parent_slug     = bp_get_friends_slug();
			$rewrite_id      = sprintf( 'bp_member_%s', $parent_slug );
			$root_nav_parent = buddypress()->my_account_menu_id;
			$user_id         = bp_loggedin_user_id();

			// NB: these slugs should probably be customizable.
			$viewes_slugs = array(
				'my-account-' . $this->id . '-friendships' => 'my-friends',
				'my-account-' . $this->id . '-requests'    => 'requests',
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
					$url_params['item_action'] = bp_rewrites_get_slug( 'members', $sub_nav_rewrite_id, $viewes_slugs[ $item_nav_id ] );
				}

				$wp_admin_nav[ $key_item_nav ]['href'] = bp_members_rewrites_get_nav_url( $url_params );
			}
		}

		return $wp_admin_nav;
	}
}
