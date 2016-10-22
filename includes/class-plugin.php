<?php
/**
 * The file that defines the Core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package NineCodes\SocialManager
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die( 'Shame on you!' ); // Abort.
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $plugin_slug = 'wp-social-manager';

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $plugin_opts = 'wp_social_manager';

	/**
	 * The current version of the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $version = '1.0.0';

	/**
	 * The path directory relative to the current file.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $path_dir;

	/**
	 * An array of option added by the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	array
	 */
	protected $options;

	/**
	 * The ThemeSupports class instance.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var 	ThemeSupports
	 */
	protected $theme_supports;

	/**
	 * The Languages class instance.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var 	Languages
	 */
	public $languages;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	void
	 */
	function __construct() {

		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );

		$this->requires();
		$this->setups();
		$this->hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @return 	void
	 */
	protected function requires() {

		require_once( $this->path_dir . 'includes/class-i18n.php' );
		require_once( $this->path_dir . 'includes/class-helpers.php' );
		require_once( $this->path_dir . 'includes/class-options.php' );
		require_once( $this->path_dir . 'includes/class-theme-supports.php' );

		require_once( $this->path_dir . 'admin/class-admin.php' );
		require_once( $this->path_dir . 'public/class-public.php' );
		require_once( $this->path_dir . 'widgets/class-widgets.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 */
	protected function hooks() {

		add_action( 'init', array( $this->languages, 'load_plugin_textdomain' ) );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, or WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @return 	void
	 */
	protected function setups() {

		$this->options = array(
			'profiles' => get_option( "{$this->plugin_opts}_profiles" ),
			'buttons_content' => get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttons_image' => get_option( "{$this->plugin_opts}_buttons_image" ),
			'metas_site' => get_option( "{$this->plugin_opts}_metas_site" ),
			'advanced' => get_option( "{$this->plugin_opts}_advanced" ),
			'modes' => get_option( "{$this->plugin_opts}_modes" ),
		);

		$this->theme_supports = new ThemeSupports();
		$this->languages = new Languages( $this->plugin_slug );

		new ViewAdmin( $this );
		new ViewPublic( $this );
		new Widgets( $this );
	}

	/**
	 * Get the plugin version.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	string The plugin version number.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the plugin slug.
	 *
	 * Slug is a unique identifier of the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	string The plugin slug.
	 */
	public function get_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Get the plugin opts.
	 *
	 * Opts herein is the unique identifier of the plugin option name.
	 * It may be used for prefixing the option name or meta key.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	string The plugin opts.
	 */
	public function get_opts() {
		return $this->plugin_opts;
	}

	/**
	 * Get the theme supports.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	ThemeSupports
	 */
	public function get_theme_supports() {
		return $this->theme_supports;
	}

	/**
	 * Get the options saved in the database `wp_options`.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param  	string $name The option name.
	 * @param  	string $key  The array key to retrieve from the option.
	 * @return 	mixed        The option value or null if option is not available.
	 */
	public function get_option( $name, $key ) {
		return isset( $this->options[ $name ][ $key ] ) ? $this->options[ $name ][ $key ] : null;
	}
}
