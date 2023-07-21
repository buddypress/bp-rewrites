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
		if ( needs_query_check() ) {
			add_action( 'bp_parse_query', array( $this, 'doing_it_wrong' ), 11 );
		}

		add_action( 'bp_setup_nav', array( $this, 'compat' ), 1000 );
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
				esc_html__( 'Please wait for the `bp_setup_nav` hook to be fired before trying to create a nav or a subnav item. The following nav item slugs are problematic: %s.', 'bp-rewrites' ),
				'<strong style="color: red">' . implode( ', ', array_map( 'esc_html', $nav_item_slugs ) ) . '</strong>'
			),
			'BP Rewrites'
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
		$slug = '';
		if ( isset( $args['slug'] ) ) {
			$slug = $args['slug'];
		}

		if ( isset( $args['parent_slug'] ) && isset( $args['link'] ) && ! isset( $args['parent_url'] ) ) {
			$path               = wp_parse_url( $args['link'], PHP_URL_PATH );
			$args['parent_url'] = str_replace( $slug, '', untrailingslashit( home_url( $path ) ) );

			// Unset the link to be sure subnav will be created into the self::compat() method.
			unset( $args['link'] );
		}

		$this->nav[ $slug ] = (object) $args;
	}

	/**
	 * Adds a new nav item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Filters to select the specific primary.
	 * @param bool  $sort True Not used.
	 * @return object|null The primary object nav if found. Null otherwise.
	 */
	public function get_primary( $args = array(), $sort = true ) {
		$slug = '';
		if ( isset( $args['slug'] ) ) {
			$slug = $args['slug'];
		}

		if ( ! isset( $this->nav[ $slug ] ) ) {
			return null;
		}

		return $this->nav[ $slug ];
	}

	/**
	 * Prevents a notice when the secondary nav wasn't set the right way.
	 *
	 * @since 1.2.0
	 *
	 * @return null
	 */
	public function get_secondary() {
		return null;
	}

	/**
	 * Restores problematic nav items.
	 *
	 * @since 1.0.0
	 */
	public function compat() {
		foreach ( $this->nav as $nav_item ) {
			$nav_args = (array) $nav_item;

			if ( ! isset( $nav_args['parent_slug'] ) ) {
				bp_core_new_nav_item( $nav_args );
			} else {
				bp_core_new_subnav_item( $nav_args );
			}
		}
	}

	/**
	 * Prevents a fatal error when trying to edit a nav too early.
	 *
	 * @since 1.4.0
	 *
	 * @param array  $args        The nav item's arguments.
	 * @param string $slug        The slug of the nav item.
	 * @param string $parent_slug The slug of the parent nav item (required to edit a child).
	 * @return null
	 */
	public function edit_nav( $args = array(), $slug = '', $parent_slug = '' ) {
		return null;
	}
}
