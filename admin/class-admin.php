<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

namespace XCo\WPSocialManager;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class ViewAdmin {

	/**
	 * [$arguments description]
	 * @var [type]
	 */
	private $args;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The plugin URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_dir;

	/**
	 * The plugin URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_url;

	/**
	 * [$settings description]
	 * @var [type]
	 */
	protected $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( array $args ) {

		$this->args = $args;
		$this->version = $args[ 'version' ];
		$this->plugin_name = $args[ 'plugin_name' ];

		$this->setups();
		$this->requires();

		add_action( 'admin_init', array( $this, 'setting_init' ) );
		add_action( 'admin_menu', array( $this, 'setting_menu' ) );
	}

	/**
	 * [setup description]
	 * @return [type] [description]
	 */
	public function setups() {

		$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	public function requires() {

		require_once( $this->plugin_dir . 'partials/class-metabox.php' );
		require_once( $this->plugin_dir . 'partials/class-settings.php' );
		require_once( $this->plugin_dir . 'partials/class-users.php' );
	}

	/**
	 * [setups description]
	 * @return [type] [description]
	 */
	public function setting_init() {

		$users = new SettingScreenUser( $this->args );
		$settings = new SettingScreenAdmin( $this->args );

		$this->settings = $settings;
	}

	/**
	 * [setting_menu description]
	 * @return [type] [description]
	 */
	public function setting_menu() {

		$menu_title  = esc_html__( 'Social', 'wp-social-manager' );
		$page_title  = esc_html__( 'Social Settings', 'wp-social-manager' );
		$page_screen = add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_name, function() {
			$this->settings->render_screen();
		} );

		add_action( "load-{$page_screen}", array( $this, 'load_scripts' ) );
	}

	/**
	 * [load_scripts description]
	 * @return [type] [description]
	 */
	public function load_scripts() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Social_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Social_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, $this->plugin_url . 'css/styles.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Social_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Social_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, $this->plugin_url . 'js/scripts.js', array( 'jquery', 'wp-util' ), $this->version, true );
		wp_enqueue_media();
	}
}