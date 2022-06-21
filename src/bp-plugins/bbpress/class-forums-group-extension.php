<?php
/**
 * BP Rewrites Forums Group Extension.
 *
 * @package bp-rewrites\src\bp-plugins\bbpress
 * @since 1.3.0
 */

namespace BP\Rewrites;

/**
 * Forums Group Extension Class.
 *
 * @since 1.3.0
 */
class Forums_Group_Extension extends \BBP_Forums_Group_Extension {
	/**
	 * The bbPress plugin should migrate to the new BP Group Extension API.
	 *
	 * @see https://codex.buddypress.org/developer/group-extension-api/
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		\BP_Group_Extension::init(
			array(
				'slug'              => 'forum',
				'name'              => esc_html__( 'Forum', 'bbpress' ),
				'nav_item_name'     => esc_html__( 'Forum', 'bbpress' ),
				'visibility'        => 'public',
				'nav_item_position' => 10,
				'enable_nav_item'   => true,
				'template_file'     => 'groups/single/plugins',
				'display_hook'      => 'bp_template_content',
				'screens'           => array(
					'create' => array(
						'position'             => 15,
						'enabled'              => true,
						'screen_callback'      => array( $this, 'create_screen' ),
						'screen_save_callback' => array( $this, 'create_screen_save' ),
					),
					'edit'   => array(
						'enabled'              => true,
						'screen_callback'      => array( $this, 'edit_screen' ),
						'screen_save_callback' => array( $this, 'edit_screen_save' ),
					),
				),
				'show_tab'          => __NAMESPACE__ . '\bbp_show_group_tab',
			)
		);

		// Forces the show tab callback to always be triggered.
		$this->params['show_tab'] = 'noone';

		$this->setup_actions();
		$this->setup_filters();
		$this->fully_loaded();
	}

	/**
	 * Setup the group forums class actions.
	 *
	 * PS: Too bad this one is private :(.
	 *
	 * @since 1.3.0
	 */
	private function setup_actions() {

		// Possibly redirect.
		add_action( 'bbp_template_redirect', array( $this, 'redirect_canonical' ) );

		// Remove group forum cap map when view is done.
		add_action( 'bbp_after_group_forum_display', array( $this, 'remove_group_forum_meta_cap_map' ) );

		// Validate group IDs when editing topics & replies.
		add_action( 'bbp_edit_topic_pre_extras', array( $this, 'validate_topic_forum_id' ) );
		add_action( 'bbp_edit_reply_pre_extras', array( $this, 'validate_reply_to_id' ) );

		// Check if group-forum attributes should be changed.
		add_action( 'groups_group_after_save', array( $this, 'update_group_forum' ) );

		// bbPress needs to listen to BuddyPress group deletion.
		add_action( 'groups_before_delete_group', array( $this, 'disconnect_forum_from_group' ) );

		// Adds a bbPress meta-box to the new BuddyPress Group Admin UI.
		add_action( 'bp_groups_admin_meta_boxes', array( $this, 'group_admin_ui_edit_screen' ) );

		// Saves the bbPress options if they come from the BuddyPress Group Admin UI.
		add_action( 'bp_group_admin_edit_after', array( $this, 'edit_screen_save' ) );

		// Adds a hidden input value to the "Group Settings" page.
		add_action( 'bp_before_group_settings_admin', array( $this, 'group_settings_hidden_field' ) );
	}

	/**
	 * Setup the group forums class filters.
	 *
	 * PS: Too bad this one is private :(.
	 *
	 * @since 1.3.0
	 */
	private function setup_filters() {

		// Group forum pagination.
		add_filter( 'bbp_topic_pagination', array( $this, 'topic_pagination' ) );
		add_filter( 'bbp_replies_pagination', array( $this, 'replies_pagination' ) );

		// Tweak the redirect field.
		add_filter( 'bbp_new_topic_redirect_to', array( $this, 'new_topic_redirect_to' ), 10, 3 );
		add_filter( 'bbp_new_reply_redirect_to', array( $this, 'new_reply_redirect_to' ), 10, 3 );

		// Map forum/topic/reply permalinks to their groups.
		add_filter( 'bbp_get_forum_permalink', array( $this, 'map_forum_permalink_to_group' ), 10, 2 );
		add_filter( 'bbp_get_topic_permalink', array( $this, 'map_topic_permalink_to_group' ), 10, 2 );
		add_filter( 'bbp_get_reply_permalink', array( $this, 'map_reply_permalink_to_group' ), 10, 2 );

		// Map reply edit links to their groups.
		add_filter( 'bbp_get_reply_edit_url', array( $this, 'map_reply_edit_url_to_group' ), 10, 2 );

		// Map assorted template function permalinks.
		add_filter( 'post_link', array( $this, 'post_link' ), 10, 2 );
		add_filter( 'page_link', array( $this, 'page_link' ), 10, 2 );
		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 2 );

		// Map group forum activity items to groups.
		add_filter( 'bbp_before_record_activity_parse_args', array( $this, 'map_activity_to_group' ) );

		// Only add these filters if inside a group forum.
		if ( bp_is_single_item() && bp_is_group() && bp_is_current_action( $this->slug ) ) {

			// Ensure bbp_is_single_forum() returns true on group forums.
			add_filter( 'bbp_is_single_forum', array( $this, 'is_single_forum' ) );

			// Ensure bbp_is_single_topic() returns true on group forum topics.
			add_filter( 'bbp_is_single_topic', array( $this, 'is_single_topic' ) );

			// Allow group member to view private/hidden forums.
			add_filter( 'bbp_map_meta_caps', array( $this, 'map_group_forum_meta_caps' ), 10, 4 );

			// Group member permissions to view the topic and reply forms.
			add_filter( 'bbp_current_user_can_access_create_topic_form', array( $this, 'form_permissions' ) );
			add_filter( 'bbp_current_user_can_access_create_reply_form', array( $this, 'form_permissions' ) );
		}
	}

	/**
	 * Allow the variables, actions, and filters to be modified by third party
	 * plugins and themes.
	 *
	 * PS: Too bad this one is private :(.
	 *
	 * @since 1.3.0
	 */
	private function fully_loaded() {
		do_action_ref_array( 'bbp_buddypress_groups_loaded', array( $this ) );
	}
}
