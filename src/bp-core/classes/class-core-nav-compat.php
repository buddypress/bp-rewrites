<?php
/**
 * BP Rewrites Core Component.
 *
 * @package bp-rewrites\src\bp-core\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * BP Core Nav Backcompat class.
 *
 * @since 1.0.0
 */
class Core_Nav_Compat {
	/**
	 * An array containing nav items created too early.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $nav = array();

	/**
	 * Hooks to `bp_parse_query` before `bp_setup_nav` to output a doing it wrong message.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'bp_parse_query', array( $this, 'doing_it_wrong' ), 11 );
	}

	/**
	 * Outputs a doing it wrong message.
	 *
	 * @since 1.0.0
	 */
	public function doing_it_wrong() {
		$nav_item_slugs = wp_list_pluck( $this->nav, 'slug' );

		_doing_it_wrong(
			'BuddyPress Members Nav',
			sprintf(
				/* Translators: 1: the list of nav items that were not added. */
				esc_html__( 'Please wait for the `bp_setup_nav` hook to be fired before trying to create a nav or a subnav item. The following nav item slugs were not created: %s.', 'bp-rewrites' ),
				'<strong style="color: red">' . implode( ', ', array_map( 'esc_html', $nav_item_slugs ) ) . '</strong>'
			),
			'BP Rewrites 1.0.0'
		);
	}

	/**
	 * Adds a new nav item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The nav item's arguments.
	 */
	public function add_nav( $args ) {
		$this->nav[] = (object) $args;
	}
}
