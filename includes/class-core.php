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
	 * [$plugin_dir description]
	 * @var [type]
	 */
	protected $plugin_dir;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->setups();
		$this->requires();

		$this->locales();
		$this->define_admin();
		$this->define_public();
	}

	private function setups() {

		$this->plugin_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Social_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Social_Manager_i18n. Defines internationalization functionality.
	 * - WP_Social_Manager_Admin. Defines all hooks for the admin area.
	 * - WP_Social_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function requires() {

		require_once( $this->plugin_dir . 'includes/class-utilities.php' );
		require_once( $this->plugin_dir . 'includes/class-i18n.php' );
		// require_once( $this->plugin_dir . 'includes/class-options.php' );

		require_once( $this->plugin_dir . 'admin/class-admin.php' );
		require_once( $this->plugin_dir . 'public/class-public.php' );
		// require_once( $this->plugin_dir . 'widgets/class-social-links.php' );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Social_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function locales() {

		$plugin_i18n = new Languages();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ));
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin() {

		if ( !is_admin() )
			return;

		$admins = new ViewAdmin( $this->args );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public() {

		if ( is_admin() )
			return;

		$public = new ViewPublic( $this->args );
	}
}
