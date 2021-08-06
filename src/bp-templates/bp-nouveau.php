<?php
/**
 * Required BP Nouveau edits.
 *
 * @package buddypress\bp-templates\bp-nouveau
 * @since ?.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * `\bp_nouveau_get_nav_link()` needs to be edited to stop modifying the nav item links.
 *
 * @since ?.0.0
 *
 * @param string $link     The URL for the nav item.
 * @param object $nav_item The nav item object.
 * @return string The URL for the nav item.
 */
function bp_nouveau_get_nav_link( $link = '', $nav_item = null ) {
	if ( isset( $nav_item->link ) && $nav_item->link ) {
		$link = $nav_item->link;
	}

	return $link;
}
add_filter( 'bp_nouveau_get_nav_link', __NAMESPACE__ . '\bp_nouveau_get_nav_link', 1, 2 );
