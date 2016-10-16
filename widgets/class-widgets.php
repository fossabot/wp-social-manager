<?php
/**
 * Widgets: Widgets class.
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Widgets
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * Widget Class to register custom widgets of the plugin.
 *
 * @since 1.0.0
 */
final class Widgets {

	/**
	 * Common arguments passed in a Class or a function.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $args;

	/**
	 * The plugin path directory.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * Constructor.
	 *
	 * Initialize the class, set its properties, load the dependencies,
	 * and run the WordPress Hooks to register the custom widgets.
	 *
	 * @since 1.0.0
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

		$this->path_dir = plugin_dir_path( __FILE__ );

		$this->requires();
		$this->hooks();
	}

	/**
	 * Load the required dependencies for the widgets.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function requires() {
		require_once( $this->path_dir . 'partials/class-social-profiles.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function hooks() {
		add_action( 'widgets_init', array( $this, 'setups' ) );
	}

	/**
	 * Run the widget setups.
	 *
	 * The setups may involve running some Classes, Functions, or WordPress Hooks
	 * that are required to render the widget properly.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {
		register_widget( new WidgetSocialProfiles( $this->args ) );
	}
}
