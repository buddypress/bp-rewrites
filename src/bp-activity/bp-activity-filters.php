<?php
/**
 * Filters related to the Activity component.
 *
 * @package buddypress\bp-activity
 * @since 1.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Activity Group view.
 *
 * @since 1.0.0
 *
 * @param array $views The list of potential Group views.
 * @return array       The list of potential Group views (including the Activity one).
 */
function bp_activity_get_group_view( $views = array() ) {
	$views['activity'] = array(
		'rewrite_id'      => 'bp_group_read_activity',
		'slug'            => 'activity',
		'name'            => _x( 'Activity', 'Group activity view', 'bp-rewrites' ),
		'screen_function' => 'groups_screen_group_activity',
		'position'        => 11,
		'user_has_access' => false,
		'no_access_url'   => '',
		'item_css_id'     => 'activity',
	);

	return $views;
}
add_filter( 'bp_get_group_custom_read_views', __NAMESPACE__ . '\bp_activity_get_group_view', 1, 1 );
