<?php
/**
 * BP Rewrites Core Component.
 *
 * @package bp-rewrites\src\bp-core\classes
 * @since 1.0.0
 */

namespace BP\Rewrites;

/**
 * Main Groups Class.
 *
 * @since 1.0.0
 */
class Core_Component extends \BP_Core {

	/**
	 * Parse the WP_Query and eventually display the component's directory or single item.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query Required. See BP_Component::parse_query() for
	 *                        description.
	 */
	public function parse_query( $query ) {
		// Search doesn't have an associated page, so we check for it separately.
		if ( isset( $_POST['search-terms'] ) && $query->get( 'pagename' ) === bp_get_search_slug() ) { // phpcs:ignore
			buddypress()->current_component = bp_get_search_slug();
		}

		bp_component_parse_query( $query );

		\BP_Component::parse_query( $query );
	}
}
