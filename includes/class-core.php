<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       github.com/tfirdaus
 * @since      1.0.0
 *
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/includes
 */

namespace XCo\WPSocialManager;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/includes
 * @author     Thoriq Firdaus <tfirdaus@outlook.com>
 */
class Core {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * [$plugin_opts description]
	 * @var [type]
	 */
	protected $plugin_opts;

	/**
	 * [$plugin_dir description]
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $path_dir;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->plugin_name = $args[ 'plugin_name' ];
		$this->plugin_opts = $args[ 'plugin_opts' ];
		$this->version = $args[ 'version' ];

		$this->path_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );

		$this->requires();
		$this->setups();
		$this->hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	protected function requires() {

		require_once( $this->path_dir . 'includes/class-i18n.php' );
		require_once( $this->path_dir . 'includes/class-utilities.php' );

		require_once( $this->path_dir . 'admin/class-admin.php' );
		require_once( $this->path_dir . 'public/class-public.php' );
		require_once( $this->path_dir . 'widgets/class-widgets.php' );
	}

	/**
	 * [hooks description]
	 * @access private
	 * @return [type] [description]
	 */
	protected function hooks() {

		add_action( 'plugins_loaded', array( $this->languages, 'load_plugin_textdomain' ) );
	}

	/**
	 * [setups description]
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	protected function setups() {

		/**
		 * [$this->languages description]
		 * @var Languages
		 */
		$this->languages = new Languages( $this->plugin_name );

		if ( is_admin() ) {

			/**
			 * [$admin description]
			 * @var ViewAdmin
			 */
			$admin = new ViewAdmin( $this->args );
		}

		if ( ! is_admin() ) {

			/**
			 * [$public description]
			 * @var ViewPublic
			 */
			$public = new ViewPublic( $this->args );
		}

		/**
		 * [$widgets description]
		 * @var Widgets
		 */
		$widgets = new Widgets( $this->args );
	}
}
