<?php
/**
 * Public: ViewPublic class
 *
 * @package SocialManager
 * @subpackage Public
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
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
		$this->option_slug = $plugin->get_opts();
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
	 *
	 * @return void
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
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'setups' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, and if necessary, WordPress Hooks
	 * that are required to render the public-facing side.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setups() {

		$metas = new Metas( $this->plugin );
		$endpoints = new Endpoints( $this->plugin, $metas );

		do_action( 'ninecodes_social_manager_instance', 'metas', $metas );
		do_action( 'ninecodes_social_manager_instance', 'endpoints', $endpoints );

		new WPHead( $metas );

		new ButtonsContent( $endpoints );
		new ButtonsImage( $endpoints );

		$this->routes = new APIRoutes( $endpoints );

		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register Stylesheet
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function register_styles() {

		wp_register_style( $this->plugin_slug, $this->path_url . 'css/styles.min.css', array(), $this->version, 'all' );
		wp_style_add_data( $this->plugin_slug, 'rtl', 'replace' );
	}

	/**
	 * Register JavaScripts
	 *
	 * We serve two kind of interfaces, JSON and HTML.
	 *
	 * The app.js will be enqueued if the site supports the JSON mode, and it requires
	 * Underscore.js, and Backbone.js aside of jQuery. The scripts is enqueued for HTML mode
	 * and it only requires jQuery to work.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function register_scripts() {

		wp_register_script( $this->plugin_slug . '-app', $this->path_url . 'js/app.min.js', array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		wp_register_script( $this->plugin_slug, $this->path_url . 'js/scripts.min.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Load the stylesheets for the public-facing side.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_styles() {

		if ( $this->is_load_stylesheet() ) {
			wp_enqueue_style( $this->plugin_slug );
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

		if ( ! $this->is_load_scripts() ) {
			return;
		}

		if ( $this->is_json_mode() ) {
			wp_enqueue_script( $this->plugin_slug . '-app' );
		} else {
			wp_enqueue_script( $this->plugin_slug );
		}
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

		$ptc = $this->plugin->get_option( 'buttons_content', 'post_types' );

		$buttons_image = $this->plugin->get_option( 'buttons_image' ); // "Buttons Image" options;
		$pti = ! $buttons_image['enabled'] ? array() : $buttons_image['post_types'];

		$post_types = array_unique( array_merge( $ptc, $pti ) );

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		return (bool) $this->plugin->get_option( 'enqueue', 'enable_stylesheet' );
	}

	/**
	 * Is the scripts should be loaded?
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_load_scripts() {

		$ptc = $this->plugin->get_option( 'buttons_content', 'post_types' );
		$pti = $this->plugin->get_option( 'buttons_image', 'post_types' );

		$post_types = array_unique( array_merge( $ptc, $pti ) );

		if ( ! is_singular( $post_types ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is the JSON mode being used.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean 	Return 'true' if the Buttons Mode is 'json',
	 * 					and 'false' if the Buttons Mode is 'html'.
	 */
	protected function is_json_mode() {

		$buttons_mode = $this->plugin->get_option( 'modes', 'buttons_mode' );

		if ( 'json' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'json' === $buttons_mode ) {
			return true;
		}

		return false;
	}
}
