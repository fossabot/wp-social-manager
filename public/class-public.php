<?php
/**
 * Public: ViewPublic class
 *
 * @package SocialManager
 * @subpackage Public
 */

namespace SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die( 'Shame on you!' ); // Abort.
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, enqueue the stylesheet and JavaScript,
 * and register custom API Routes using the built-in WP-API infrastructure.
 *
 * @since 1.0.0
 */
final class ViewPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The aboslute path directory to the .
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * The absolut URL path to the plugin directory.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_url;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $version;

	/**
	 * Theme support features.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var ThemeSupports
	 */
	protected $theme_supports;

	/**
	 * The Metas class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Metas
	 */
	protected $metas;

	/**
	 * Constructor.
	 *
	 * Initialize the class, set its properties, load the dependencies,
	 * and run the WordPress Hooks to enqueue styles and JavaScripts
	 * in the public-facing side, and register custom API Routes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->plugin_slug = $plugin->get_slug();
		$this->plugin_opts = $plugin->get_opts();
		$this->version = $plugin->get_version();
		$this->theme_supports = $plugin->get_theme_supports();

		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_url = plugin_dir_url( __FILE__ );

		$this->requires();
		$this->hooks();
	}

	/**
	 * Load the required dependencies.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function requires() {

		require_once( $this->path_dir . 'partials/class-metas.php' );
		require_once( $this->path_dir . 'partials/class-wp-head.php' );
		require_once( $this->path_dir . 'partials/class-endpoints.php' );
		require_once( $this->path_dir . 'partials/class-api-routes.php' );
		require_once( $this->path_dir . 'partials/class-buttons.php' );
		require_once( $this->path_dir . 'partials/class-buttons-content.php' );
		require_once( $this->path_dir . 'partials/class-buttons-image.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'setups' ) );
		add_action( 'init', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, and if necessary, WordPress Hooks
	 * that are required to render the public-facing side.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function setups() {

		$metas = new Metas( $this->plugin );

		new WPHead( $metas );

		$endpoints = new Endpoints( $this->plugin, $metas );

		new ButtonsContent( $endpoints );
		new ButtonsImage( $endpoints );

		$this->routes = new APIRoutes( $endpoints );
		$this->register_scripts();
	}

	/**
	 * Register JavaScripts handles.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_scripts() {

		if ( $this->routes->is_load_routes() ) {
			wp_register_script( $this->plugin_slug, $this->path_url . 'js/app.js', array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		} else {
			wp_register_script( $this->plugin_slug, $this->path_url . 'js/scripts.js', array( 'jquery' ), $this->version, true );
		}
	}

	/**
	 * Load the stylesheets for the public-facing side.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_styles() {

		if ( $this->is_load_stylesheet() ) {
			wp_enqueue_style( $this->plugin_slug, $this->path_url . 'css/styles.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Load the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug );
	}

	/**
	 * Is the stylesheet should be loaded?
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean 	Return 'false' if the Theme being set the 'stylesheet' to 'true',
	 * 					via the 'add_theme_support' function.
	 * 					It will also return 'false' if the 'Enable Stylesheet' is unchecked.
	 */
	protected function is_load_stylesheet() {

		if ( true === (bool) $this->theme_supports->is( 'stylesheet' ) ) {
			return false;
		}

		$stylesheet = $this->plugin->get_option( 'advanced', 'enable_stylesheet' );

		if ( $stylesheet ) {
			return (bool) $stylesheet;
		}

		return true;
	}
}
