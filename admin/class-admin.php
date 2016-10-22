<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Admin
 */

namespace NineCodes\SocialManager;

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

		new Settings( $this->plugin );
		new User( $this->plugin );
		SocialMetaBox::get_instance( $this->plugin );
	}
}
