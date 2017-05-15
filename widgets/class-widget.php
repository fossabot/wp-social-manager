<?php
/**
 * Widgets: Widgets class.
 *
 * @package SocialManager
 * @subpackage Widgets
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \WP_Widget;

/**
 * The Widget abstract class is used for registering custom widgets of the plugin.
 *
 * @since 2.0.0
 */
abstract class Widget extends WP_Widget {

	/**
	 * The Plugin class instance.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The plugin path directory.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * The absolut URL path to the plugin directory.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_url;

	/**
	 * Constructor.
	 *
	 * Initialize the class, set its properties, load the dependencies,
	 * and run the WordPress Hooks to register the custom widgets.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @see __construct()
	 *
	 * @param array $options Widget options. Default empty array.
	 */
	function __construct( array $options ) {

		$options = wp_parse_args( $options, array(
			'id' => '',
			'name' => '',
			'description' => '',
		) );

		$this->plugin = ninecodes_social_manager();
		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_url = plugin_dir_url( __FILE__ );

		parent::__construct( $this->plugin->slug() . "-{$options['id']}", $options['name'], array(
			'classname' => $options['id'],
			'description' => $options['description'],
			'customize_selective_refresh' => true,
		) );
	}
}

// Load Core widget of the plugin.
require_once( plugin_dir_path( __FILE__ ) . 'partials/class-widget-social-profile.php' );
