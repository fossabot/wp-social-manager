<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Admin
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since 1.0.0
 */
final class ViewAdmin {

	/**
	 * Common arguments passed in a Class or a function.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $args;

	/**
	 * The plugin URL.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $path_dir;

	/**
	 * The ThemeSupports instance.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var ThemeSupports
	 */
	protected $supports;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array 		$args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 * @param ThemeSupports $supports 	The ThemeSupports instance.
	 */
	public function __construct( array $args, ThemeSupports $supports ) {

		$this->args = $args;

		$this->path_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		$this->supports = $supports;

		$this->requires();
		$this->setups();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function requires() {

		require_once( $this->path_dir . 'partials/class-settings.php' );
		require_once( $this->path_dir . 'partials/class-validation.php' );

		require_once( $this->path_dir . 'partials/class-user.php' );
		require_once( $this->path_dir . 'partials/class-metabox.php' );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, andn sometimes WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {

		$admin = new Settings( $this->args, $this->supports );
		$users = new SettingsUser( $this->args );

		SocialMetaBox::get_instance( $this->args );
	}
}
