<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @author      Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package     WPSocialManager
 * @subpackage  Admin
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
	private $plugin_dir;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->path_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		$this->requires();
		$this->setups();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since  1.0.0
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
	 * The setups may involve running some Classes, Functions, or WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function setups() {

		$admin = new Settings( $this->args );
		$users = new SettingsUser( $this->args );

		$meta = SocialMetaBox::get_instance( $this->args );
	}
}
