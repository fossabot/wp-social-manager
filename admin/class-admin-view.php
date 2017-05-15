<?php
/**
 * This file defines the Admin_View class.
 *
 * @package SocialManager
 * @subpackage Admin
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Admin_View class delivers admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since 1.0.0
 */
final class Admin_View {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The plugin Admin directory path.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	protected $path_dir;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( __FILE__ );

		spl_autoload_register( array( $this, 'requires' ) );

		$this->setups();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 - Use autoloader
	 * @access protected
	 *
	 * @param string $class_name Loaded class name in the "admin-view.php".
	 * @return void
	 */
	protected function requires( $class_name ) {

		$class_name = str_replace( __NAMESPACE__ . '\\', '', $class_name );
		$class_path = $this->path_dir . 'partials/class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

		if ( file_exists( $class_path ) ) {
			require_once( $class_path );
		}

		add_action( 'plugins_loaded', array( $this, 'requires_metabox' ) );
	}

	/**
	 * Load the required dependencies when plugins are already loaded.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function requires_metabox() {
		require_once( $this->path_dir . 'partials/metabox/butterbean.php' );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, andn sometimes WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function setups() {

		$settings = new Settings( $this->plugin );
		$user = new User( $this->plugin );

		static $metabox;
		if ( is_null( $metabox ) ) {
			$metabox = new Metabox( $this->plugin );
		}
	}
}
