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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $args ) {

		$this->arguments   = $args;
		$this->plugin_name = $args[ 'plugin_name' ];
		$this->option_name = $args[ 'option_name' ];
		$this->version     = $args[ 'version' ];

		$this->requires();

		add_action( 'init', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	public function requires() {

		require_once plugin_dir_path( __FILE__ ) . 'partials/class-buttons-content.php';
		require_once plugin_dir_path( __FILE__ ) . 'partials/class-buttons-image.php';
		require_once plugin_dir_path( __FILE__ ) . 'partials/class-metas.php';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_admin() )
			return;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/styles.css', array(), $this->version, 'all' );
		wp_enqueue_style( "{$this->plugin_name}-twentysixteen", plugin_dir_url( __FILE__ ) . 'css/styles-twentysixteen.css', array( $this->plugin_name, 'twentysixteen' ), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_admin() )
			return;

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );
	}
}
