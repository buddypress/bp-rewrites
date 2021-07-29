<?php
/**
 * Groups: Create actions.
 *
 * @package buddypress\bp-groups\actions
 * @since 3.0.0
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @todo `groups_action_create_group()` should be edited so that `bp_get_groups_directory_permalink() . 'create/step/'`
 * is replaced by `bp_get_group_create_link()`.
 */
