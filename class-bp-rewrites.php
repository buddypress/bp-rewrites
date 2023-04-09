<?php
/**
 * BuddyPress Rewrites development plugin.
 *
 * @package   bp-rewrites
 * @author    The BuddyPress Community
 * @license   GPL-2.0+
 * @link      https://buddypress.org
 *
 * @buddypress-plugin
 * Plugin Name:       BP Rewrites
 * Plugin URI:        https://github.com/buddypress/bp-rewrites
 * Description:       BuddyPress Rewrites development plugin.
 * Version:           1.6.0
 * Author:            The BuddyPress Community
 * Author URI:        https://buddypress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages/
 * Text Domain:       bp-rewrites
 * GitHub Plugin URI: https://github.com/buddypress/bp-rewrites
 * Requires at least: 5.9
 * Requires PHP:      5.6
 * Requires Plugins:  buddypress
 */

namespace BP\Rewrites;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class
 *
 * @since 1.0.0
 */
final class BP_Rewrites {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Load Globals & Functions.
		$inc_path = plugin_dir_path( __FILE__ ) . 'inc/';

		require $inc_path . 'globals.php';
		require $inc_path . 'functions.php';
		require $inc_path . 'loader.php';

		if ( is_admin() ) {
			require $inc_path . 'admin.php';
		}
	}

	/**
	 * Checks whether BuddyPress is active.
	 *
	 * @since 1.0.0
	 */
	public static function is_buddypress_supported() {
		$bp_plugin_basename      = 'buddypress/bp-loader.php';
		$is_buddypress_supported = false;
		$sitewide_plugins        = (array) get_site_option( 'active_sitewide_plugins', array() );

		if ( $sitewide_plugins ) {
			$is_buddypress_supported = isset( $sitewide_plugins[ $bp_plugin_basename ] );
		}

		if ( ! $is_buddypress_supported ) {
			$plugins                 = (array) get_option( 'active_plugins', array() );
			$is_buddypress_supported = in_array( $bp_plugin_basename, $plugins, true );
		}

		if ( $is_buddypress_supported ) {
			$is_buddypress_supported = version_compare( bp_get_version(), '12.0.0-alpha', '<' );
		}

		return $is_buddypress_supported;
	}

	/**
	 * Toggle Directory pages post types.
	 *
	 * @since 1.0.0
	 */
	public static function toggle_post_types() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/update.php';

		// Run the updater.
		updater();
	}

	/**
	 * Displays an admin notice to explain how to install BP Rewrites.
	 *
	 * @since 1.0.0
	 */
	public static function admin_notice() {
		if ( self::is_buddypress_supported() ) {
			return false;
		}

		$bp_plugin_link = sprintf( '<a href="%s">BuddyPress</a>', esc_url( _x( 'https://wordpress.org/plugins/buddypress', 'BuddyPress WP plugin directory URL', 'bp-rewrites' ) ) );

		printf(
			'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
			sprintf(
				/* translators: 1. is the link to the BuddyPress plugin on the WordPress.org plugin directory. */
				esc_html__( 'BP Rewrites requires the %1$s plugin to be active and its version must be %2$s. Please deactivate BP Rewrites, activate %1$s %2$s and only then, reactivate BP Rewrites.', 'bp-rewrites' ),
				$bp_plugin_link, // phpcs:ignore
				'<b>< 12.0.0</b>' // phpcs:ignore
			)
		);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 */
	public static function start() {
		// This plugin is only usable with BuddyPress.
		if ( ! self::is_buddypress_supported() ) {
			return false;
		}

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Start plugin.
 *
 * @since 1.0.0
 *
 * @return BP_Rewrites The main instance of the plugin.
 */
function bp_rewrites() {
	return BP_Rewrites::start();
}
add_action( 'bp_loaded', __NAMESPACE__ . '\bp_rewrites', -1 );

/*
 * Use Activation and Deactivation to switch directory pages post type between WP pages
 * and BuddyPress one.
 */
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\BP_Rewrites', 'toggle_post_types' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\BP_Rewrites', 'toggle_post_types' ) );

// Displays a notice to inform BP Rewrites needs to be activated after BuddyPress.
add_action( 'admin_notices', array( __NAMESPACE__ . '\BP_Rewrites', 'admin_notice' ) );
