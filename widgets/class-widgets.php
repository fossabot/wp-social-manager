<?php
/**
 * Widgets: Widgets class.
 *
 * @package SocialManager
 * @subpackage Widgets
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Widget class is used for registering custom widgets of the plugin.
 *
 * @since 1.0.0
 */
final class Widgets {


	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * The plugin path directory.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * Constructor.
	 *
	 * Initialize the class, set its properties, load the dependencies,
	 * and run the WordPress Hooks to register the custom widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( __FILE__ );

		$this->requires();
		$this->hooks();
	}

	/**
	 * Load the required dependencies for the widgets.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function requires() {

		require_once( $this->path_dir . 'partials/class-social-profiles.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'widgets_init', array( $this, 'setups' ) );
	}

	/**
	 * Run the widget setups.
	 *
	 * The setups may involve running some Classes, Functions, or WordPress Hooks
	 * that are required to render the widget properly.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function setups() {

		register_widget( __NAMESPACE__ . '\\WidgetSocialProfiles' );

		do_action( 'ninecodes_social_manager_widget_setups', $this );
	}

	/**
	 * Get the plugin slug.
	 *
	 * Slug is a unique identifier of the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The plugin slug.
	 */
	public function get_slug() {

		return $this->plugin->get_slug();
	}

	/**
	 * Get the plugin opts.
	 *
	 * Opts herein is the unique identifier of the plugin option name.
	 * It may be used for prefixing the option name or meta key.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The plugin opts.
	 */
	public function get_opts() {

		return $this->plugin->get_slug();
	}

	/**
	 * Get the theme supports.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return ThemeSupports instance.
	 */
	public function get_theme_supports() {

		return $this->plugin->theme_supports;
	}

	/**
	 * Get the options saved in the database `wp_options`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $name The option name.
	 * @param string $key  The array key to retrieve from the option.
	 * @return mixed The option value or null if option is not available.
	 */
	public function get_option( $name = '', $key = '' ) {
		return $this->plugin->get_option( $name, $key );
	}
}
