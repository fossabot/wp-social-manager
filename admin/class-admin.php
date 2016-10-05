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
	 * The plugin URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_dir;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->path_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		$this->requires();
		$this->setups();
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	public function requires() {

		require_once( $this->path_dir . 'partials/class-settings.php' );
		require_once( $this->path_dir . 'partials/class-settings-user.php' );
		require_once( $this->path_dir . 'partials/class-settings-validation.php' );

		require_once( $this->path_dir . 'partials/class-metabox.php' );
	}

	/**
	 * [setups description]
	 * @return [type] [description]
	 */
	public function setups() {

		$admin  = new Settings( $this->args );
		$users  = new SettingsUser( $this->args );
	}
}
