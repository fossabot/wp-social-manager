<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package 	WPSocialManager
 * @subpackage 	Public
 * @author  	Thoriq Firdaus <tfirdau@outlook.com>
 */

namespace XCo\WPSocialManager;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, enqueue the stylesheet and JavaScript,
 * and register custom API Routes with the built-in WP-API infrastructure.
 *
 * @since 1.0.0
 */
final class ViewPublic {

	/**
	 * Common arguments passed in a Class or a function.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var 	array
	 */
	protected $args;

	/**
	 * The ID of this plugin.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var    	string
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string
	 */
	protected $version;

	/**
	 * The options defining the public-facing fuctionalities.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var 	mixed
	 */
	protected $options;

	/**
	 * APIRoutes instance.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 * @var 	APIRoutes
	 */
	private $routes;

	/**
	 * Constructor.
	 *
	 * Initialize the class, set its properties, load the dependencies,
	 * and run the WordPress Hooks to enqueue styles and JavaScripts
	 * in the public-facing side, and register custom API Routes
	 *
	 * @since  	1.0.0
	 * @access 	public
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name    The unique identifier of this plugin.
	 *     @type string $plugin_opts    The unique identifier or prefix for database names.
	 *     @type string $version        The plugin version number.
	 * }
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->plugin_name = $args['plugin_name'];
		$this->plugin_opts = $args['plugin_opts'];
		$this->version = $args['version'];

		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_url = plugin_dir_url( __FILE__ );

		$this->requires();
		$this->hooks();
		$this->setups();
	}

	/**
	 * Load the required dependencies.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 */
	protected function requires() {

		require_once( $this->path_dir . 'partials/class-metas.php' );
		require_once( $this->path_dir . 'partials/class-routes.php' );
		require_once( $this->path_dir . 'partials/class-buttons.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'register_api_routes' ) );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, and if necessary, WordPress Hooks
	 * that are required to render the public-facing side.
	 *
	 * @since  	1.0.0
	 * @access 	protected
	 */
	protected function setups() {

		new Buttons( $this->args );

		$this->routes  = new APIRoutes( $this->args, new Metas( $this->args ) );

		$this->options = (object) get_option( "{$this->plugin_opts}_advanced" );
	}

	/**
	 * Register the stylesheets for the public-facing side.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function enqueue_styles() {

		if ( is_admin() ) { // Prevent the the stylesheets to be loaded in the admin area.
			return;
		}

		if ( (bool) $this->options->enableStylesheet ) {
			wp_enqueue_style( $this->plugin_name, $this->path_url . 'css/styles.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function enqueue_scripts() {

		if ( is_admin() ) { // Prevent the the stylesheets to be loaded in the admin area.
			return;
		}

		wp_enqueue_script( $this->plugin_name, $this->path_url . 'js/scripts.js', array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
	}

	/**
	 * Register custom WP-API Routes
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function register_api_routes() {

		add_filter( 'rest_api_init', array( $this->routes, 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( $this->routes, 'localize_scripts' ) );
	}
}
