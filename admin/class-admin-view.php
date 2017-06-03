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
	 * @access public
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The plugin Admin directory path.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * The plugin Module directory path.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_module;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_module = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'modules' );

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

		require_once( $this->path_module . 'settings/class-settings.php' );
		require_once( $this->path_module . 'settings/class-fields.php' );
		require_once( $this->path_module . 'metabox/ninecodes-metabox.php' );
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
		$metabox = new Metabox( $this->plugin );
		$user = new User( $this->plugin );
	}
}
