<?php
/**
 * The file that defines the Core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package WPSocialManager
 * @author  Thoriq Firdaus <tfirdau@outlook.com>
 */

namespace XCo\WPSocialManager;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class Core {

	/**
	 * Common arguments passed in a Class or a function.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	array
	 */
	protected $args;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $plugin_name;

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $plugin_opts;

	/**
	 * The plugin path directory.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $path_dir;

	/**
	 * The current version of the plugin.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var    	string
	 */
	protected $version;

	/**
	 * The Language class instance.
	 *
	 * @since 	1.0.0
	 * @var 	Languages
	 */
	public $languages;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->plugin_name = $args['plugin_name'];
		$this->plugin_opts = $args['plugin_opts'];
		$this->version = $args['version'];

		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );

		$this->requires();
		$this->setups();
		$this->hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function requires() {

		require_once( $this->path_dir . 'includes/class-i18n.php' );
		require_once( $this->path_dir . 'includes/class-utilities.php' );

		require_once( $this->path_dir . 'admin/class-admin.php' );
		require_once( $this->path_dir . 'public/class-public.php' );
		require_once( $this->path_dir . 'widgets/class-widgets.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
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
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function setups() {

		$this->languages = new Languages( $this->plugin_name );

		if ( is_admin() ) {
			$admin = new ViewAdmin( $this->args );
		} else {
			$public = new ViewPublic( $this->args );
		}

		$widgets = new Widgets( $this->args );
	}
}
