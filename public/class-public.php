<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       github.com/tfirdaus
 * @since      1.0.0
 *
 * @package    Wp_Social_Manager
 * @subpackage Wp_Social_Manager/public
 */


namespace XCo\WPSocialManager;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Social_Manager
 * @subpackage Wp_Social_Manager/public
 * @author     Thoriq Firdaus <tfirdaus@outlook.com>
 */
class ViewPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * [$routes description]
	 * @var [type]
	 */
	private $routes;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $args ) {

		$this->plugin_name = $args[ 'plugin_name' ];
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->version = $args[ 'version' ];

		$this->path_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->path_url = trailingslashit( plugin_dir_url( __FILE__ ) );

		$this->requires();
		$this->hooks();
		$this->setups();
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	public function requires() {

		require_once( $this->path_dir . 'partials/class-metas.php' );
		require_once( $this->path_dir . 'partials/class-routes.php' );
		require_once( $this->path_dir . 'partials/class-buttons.php' );
	}

	/**
	 * [hooks description]
	 * @return [type] [description]
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'register_api_routes' ) );
	}

	public function setups() {

		/**
		 * [$this->routes description]
		 * @var APIRoutes
		 */
		$this->routes = new APIRoutes( $this->plugin_name, new Metas( $this->plugin_opts ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_admin() )
			return;

		wp_enqueue_style( $this->plugin_name, $this->path_url . 'css/styles.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_admin() )
			return;

		wp_enqueue_script( $this->plugin_name, $this->path_url . 'js/public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * [register_api_routes description]
	 * @return [type] [description]
	 */
	public function register_api_routes() {
		add_filter( 'rest_api_init', array( $this->routes, 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( $this->routes, 'localize_scripts' ) );
	}
}
