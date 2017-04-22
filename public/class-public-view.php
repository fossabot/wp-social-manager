<?php
/**
 * Public: Public_View class
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
final class Public_View {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Made `protected`
	 * @access protected
	 * @var string
	 */
	protected $plugin;

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

		if ( is_admin() ) {
			return;
		}

		$this->plugin = $plugin;

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

		require_once( $this->path_dir . 'partials/class-meta.php' );
		require_once( $this->path_dir . 'partials/class-wp-head.php' );
		require_once( $this->path_dir . 'partials/class-wp-footer.php' );
		require_once( $this->path_dir . 'partials/class-endpoint.php' );
		require_once( $this->path_dir . 'partials/class-rest-button.php' );

		require_once( $this->path_dir . 'partials/button/class-button-interface.php' );
		require_once( $this->path_dir . 'partials/button/class-button.php' );
		require_once( $this->path_dir . 'partials/button/class-button-content.php' );
		require_once( $this->path_dir . 'partials/button/class-button-image.php' );

		require_once( $this->path_dir . 'partials/function-template-tags.php' );
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

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), -10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), -10 );
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

		$wp_head = new WP_Head( $this->plugin );
		$wp_footer = new WP_Footer();

		$buttons_content = new Button_Content( $this->plugin );
		$buttons_image = new Button_Image( $this->plugin );
		$rest_buttons = new REST_Button( $this->plugin );

		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register Stylesheet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_styles() {

		wp_register_style( $this->plugin->plugin_slug, $this->path_url . 'css/styles.min.css', array(), $this->plugin->version, 'all' );
		wp_style_add_data( $this->plugin->plugin_slug, 'rtl', 'replace' );
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
	public function register_scripts() {

		wp_register_script( "{$this->plugin->plugin_slug}-app", "{$this->path_url}js/app.min.js", array( 'jquery', 'underscore', 'backbone' ), $this->plugin->version, true );
		wp_register_script( $this->plugin->plugin_slug, "{$this->path_url}js/scripts.min.js", array( 'jquery' ), $this->plugin->version, true );
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
			wp_enqueue_style( $this->plugin->plugin_slug );
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
			wp_enqueue_script( $this->plugin->plugin_slug . '-app' );
		} else {
			wp_enqueue_script( $this->plugin->plugin_slug );
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
	public function is_load_stylesheet() {
		/*
		 * Don't load the plugin stylesheet, if the theme already loads its own stylesheet
		 * via the 'add_theme_support()' function.
		 */
		if ( true === (bool) $this->plugin->theme_support()->is( 'stylesheet' ) ) {
			return false;
		}

		if ( ! $this->is_buttons_active() ) {
			return false;
		}

		return 'on' === $this->plugin->get_option( 'enqueue', 'enable_stylesheet' );
	}

	/**
	 * Is the scripts should be loaded?
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	public function is_load_scripts() {

		$load = true;

		if ( ! $this->is_buttons_active() || ! is_singular() ) {
			$load = false;
		}

		return $load;
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
	public function is_json_mode() {

		$button_mode = $this->plugin->get_option( 'modes', 'buttons_mode' );
		$theme_support = $this->plugin->theme_support()->is( 'button_mode' );

		if ( 'json' === $theme_support || 'json' === $button_mode ) {
			return true;
		}

		return false;
	}

	/**
	 * Is the buttons active?
	 *
	 * - Check if the buttons content has the post types set.
	 * - Check if the buttons image is enabled. If the buttons image is enabled, check if it has the post types set.
	 * - Check if the the current post types are viewed.
	 *
	 * @since 1.0.0
	 * @since 1.0.5 - Wrap the button conditional for single post in `is_singular()`.
	 * @access public
	 *
	 * @return boolean True or false depending on the above conditions.
	 */
	public function is_buttons_active() {

		$active = true;

		if ( is_singular() ) {

			$buttons_image = $this->plugin->get_option( 'buttons_image' ); // Get "Buttons Image" options.

			$post_types_content = $this->plugin->get_option( 'buttons_content', 'post_type' );
			$post_types_image = isset( $buttons_image['enable'] ) && 'on' === $buttons_image['enable'] ? $buttons_image['post_type'] : array();

			$post_types = array_keys( array_unique(
				array_merge(
					array_filter( $post_types_content ),
					array_filter( $post_types_image )
				)
			) );

			if ( empty( $post_types ) ) {
				$active = false;
			}
		}

		return $active;
	}
}
