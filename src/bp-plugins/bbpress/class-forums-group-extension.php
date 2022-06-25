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

		// hardcoded slugs (used by bbPress).
		$this->topic_slug = 'topic';
		$this->reply_slug = 'reply';

		$this->setup_actions();
		$this->setup_filters();
		$this->fully_loaded();
	}

	/**
	 * Temporarly override the wp_redirect URL.
	 *
	 * @since 1.3.0
	 *
	 * @return string The URL to redirect the user to.
	 */
	public function override_redirect() {
		// only do it once!
		remove_filter( 'wp_redirect', array( $this, 'override_redirect' ), 10, 0 );

		return bp_rewrites_get_url(
			array(
				'component_id'                 => 'groups',
				'single_item'                  => \groups_get_current_group()->slug,
				'single_item_action'           => bp_rewrites_get_slug( 'groups', 'bp_group_read_admin', 'admin' ),
				'single_item_action_variables' => array( bbp_get_group_admin_forum_slug() ),
			)
		);
	}

	/**
	 * Make sure bbPress is using BP Rewrites to build the redirect URL.
	 *
	 * @since 1.3.0
	 *
	 * @param int $group_id The group ID.
	 */
	public function edit_screen_save( $group_id = 0 ) {
		add_filter( 'wp_redirect', array( $this, 'override_redirect' ), 10, 0 );

		parent::edit_screen_save( $group_id );
	}

	/**
	 * Redirect to the group forum topic page.
	 *
	 * @since 1.3.0
	 *
	 * @param string $redirect_url The original redirect URL.
	 * @param string $redirect_to  The value of the `redirect_to` query parameter.
	 * @param int    $topic_id     The topic ID.
	 * @return string The URL to redirect the user to, built using BP Rewrites.
	 */
	public function new_topic_redirect_to( $redirect_url = '', $redirect_to = '', $topic_id = 0 ) {
		if ( \bp_is_group() ) {
			$topic        = \bbp_get_topic( $topic_id );
			$topic_hash   = '#post-' . $topic_id;
			$redirect_url = bp_rewrites_get_url(
				array(
					'component_id'                 => 'groups',
					'single_item'                  => \groups_get_current_group()->slug,
					'single_item_action'           => bbp_get_group_forum_slug(),
					'single_item_action_variables' => array( $this->topic_slug, $topic->post_name ),
				)
			);

			$redirect_url .= $topic_hash;
		}

		return $redirect_url;
	}


	/**
	 * Redirect to the group forum topic page.
	 *
	 * @since 1.3.0
	 *
	 * @param string $redirect_url The original redirect URL.
	 * @param string $redirect_to  The value of the `redirect_to` query parameter.
	 * @param int    $reply_id     The topic reply ID.
	 * @return string The URL to redirect the user to, built using BP Rewrites.
	 */
	public function new_reply_redirect_to( $redirect_url = '', $redirect_to = '', $reply_id = 0 ) {

		if ( bp_is_group() ) {
			$topic_id       = bbp_get_reply_topic_id( $reply_id );
			$topic          = bbp_get_topic( $topic_id );
			$reply_position = bbp_get_reply_position( $reply_id, $topic_id );
			$reply_page     = ceil( (int) $reply_position / (int) bbp_get_replies_per_page() );
			$reply_hash     = '#post-' . $reply_id;
			$reply_url_args = array(
				'component_id'                 => 'groups',
				'single_item'                  => \groups_get_current_group()->slug,
				'single_item_action'           => bbp_get_group_forum_slug(),
				'single_item_action_variables' => array( $this->topic_slug, $topic->post_name ),
			);

			// Don't include pagination if on first page.
			if ( 1 >= $reply_page ) {
				$redirect_url = bp_rewrites_get_url( $reply_url_args ) . $reply_hash;

				// Include pagination.
			} else {
				$reply_url_args['single_item_action_variables'] = array( $this->topic_slug, $topic->post_name, bbp_get_paged_slug(), $reply_page );
				$redirect_url                                   = bp_rewrites_get_url( $reply_url_args ) . $reply_hash;
			}

			// Add topic view query arg back to end if it is set.
			if ( bbp_get_view_all() ) {
				$redirect_url = bbp_add_view_all( $redirect_url );
			}
		}

		return $redirect_url;
	}

	/**
	 * Returns the Group object for the requested forum ID.
	 *
	 * @since 1.3.0
	 *
	 * @param int $forum_id The forum ID.
	 * @return BP_Groups_Group|null The Group object or null if no groups were found.
	 */
	public function get_group_for_forum( $forum_id ) {
		$group_ids = \bbp_get_forum_group_ids( $forum_id );
		$group_id  = reset( $group_ids );

		if ( ! $group_id ) {
			return null;
		}

		$group = \bp_get_group( $group_id );
		if ( ! isset( $group->id ) || (int) $group->id !== (int) $group_id ) {
			return null;
		}

		return $group;
	}

	/**
	 * Map a forum permalink to its group forum using BP Rewrites.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url      The Group Forum URL.
	 * @param int    $forum_id The Group Forum ID.
	 * @return string The Group Forum URL built using BP Rewrites.
	 */
	public function map_forum_permalink_to_group( $url, $forum_id ) {
		$group = $this->get_group_for_forum( $forum_id );

		if ( is_null( $group ) ) {
			return $url;
		}

		return bp_rewrites_get_url(
			array(
				'component_id'       => 'groups',
				'single_item'        => $group->slug,
				'single_item_action' => bbp_get_group_forum_slug(),
			)
		);
	}

	/**
	 * Map a topic permalink to its group forum using BP Rewrites.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url      The Group Topic URL.
	 * @param int    $topic_id The Group Topic ID.
	 * @return string The Group Topic URL built using BP Rewrites.
	 */
	public function map_topic_permalink_to_group( $url, $topic_id ) {
		$forum_id   = \bbp_get_topic_forum_id( $topic_id );
		$topic_name = \get_post_field( 'post_name', $topic_id );
		$group      = $this->get_group_for_forum( $forum_id );

		if ( is_null( $group ) ) {
			return $url;
		}

		return bp_rewrites_get_url(
			array(
				'component_id'                 => 'groups',
				'single_item'                  => $group->slug,
				'single_item_action'           => bbp_get_group_forum_slug(),
				'single_item_action_variables' => array( $this->topic_slug, $topic_name ),
			)
		);
	}

	/**
	 * Map a reply permalink to its group forum using BP Rewrites.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url      The Group Reply URL.
	 * @param int    $reply_id The Group Reply ID.
	 * @return string The Group Reply URL built using BP Rewrites.
	 */
	public function map_reply_permalink_to_group( $url, $reply_id ) {
		$forum_id   = \bbp_get_reply_forum_id( $reply_id );
		$topic_id   = \bbp_get_reply_topic_id( $reply_id );
		$topic_name = \get_post_field( 'post_name', $topic_id );
		$group      = $this->get_group_for_forum( $forum_id );

		if ( is_null( $group ) ) {
			return $url;
		}

		return bp_rewrites_get_url(
			array(
				'component_id'                 => 'groups',
				'single_item'                  => $group->slug,
				'single_item_action'           => bbp_get_group_forum_slug(),
				'single_item_action_variables' => array( $this->topic_slug, $topic_name ),
			)
		) . '#post-' . $reply_id;
	}

	/**
	 * Map a reply edit permalink to its group forum using BP Rewrites.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url      The Group Reply URL.
	 * @param int    $reply_id The Group Reply ID.
	 * @return string The Group Reply edit URL built using BP Rewrites.
	 */
	public function map_reply_edit_url_to_group( $url, $reply_id ) {
		$forum_id = \bbp_get_reply_forum_id( $reply_id );
		$group    = $this->get_group_for_forum( $forum_id );

		if ( is_null( $group ) ) {
			return $url;
		}

		return bp_rewrites_get_url(
			array(
				'component_id'                 => 'groups',
				'single_item'                  => $group->slug,
				'single_item_action'           => bbp_get_group_forum_slug(),
				'single_item_action_variables' => array( $this->reply_slug, $reply_id, bbpress()->edit_id ),
			)
		);
	}

	/**
	 * Overrides bbPress `post_link`, `page_link` & `post_type_link` filters.
	 *
	 * @since 1.3.0
	 *
	 * @param int    $post_id The post type ID.
	 * @param string $url     The post type permalink.
	 * @return string The post type permalink built usint BP Rewrites.
	 */
	public function maybe_map_permalink_to_group( $post_id, $url ) {
		$post_type = get_post_type( $post_id );

		if ( bbp_get_reply_post_type() === $post_type ) {
			$url = $this->map_reply_permalink_to_group( $url, $post_id );
		} elseif ( bbp_get_topic_post_type() === $post_type ) {
			$url = $this->map_topic_permalink_to_group( $url, $post_id );
		} elseif ( bbp_get_forum_post_type() === $post_type ) {
			$url = $this->map_forum_permalink_to_group( $url, $post_id );
		}

		return $url;
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
