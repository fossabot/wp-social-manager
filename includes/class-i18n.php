<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       github.com/tfirdaus
 * @since      1.0.0
 *
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/includes
 */


namespace XCo\WPSocialManager;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/includes
 * @author     Thoriq Firdaus <tfirdaus@outlook.com>
 */
class Languages {

	/**
	 * [$plugin_name description]
	 * @var [type]
	 */
	protected $plugin_name;

	/**
	 * [$plugin_dir description]
	 * @var [type]
	 */
	protected $plugin_dir;

	/**
	 * [__construct description]
	 * @param [type] $args [description]
	 */
	public function __construct( $domain ) {

		$this->domain = $domain;
		$this->plugin_dir = trailingslashit( dirname( dirname( plugin_basename( __FILE__ ) ) ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->domain, false, $this->plugin_dir . 'languages/' );
	}
}
