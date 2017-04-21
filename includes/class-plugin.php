<?php
/**
 * The file that defines the Core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package SocialManager
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $plugin_slug = 'ninecodes-social-manager';

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $option_slug = 'ncsocman';

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $version = '1.2.0-alpha.1';

	/**
	 * The Theme_Support class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Theme_Support
	 */
	public $theme_support;

	/**
	 * The path directory relative to the current file.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * An array of option added by the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	function __construct() {

		$this->options = array(
			'profiles' => "{$this->option_slug}_profiles",
			'buttons_content' => "{$this->option_slug}_buttons_content",
			'buttons_image' => "{$this->option_slug}_buttons_image",
			'metas_site' => "{$this->option_slug}_metas_site",
			'enqueue' => "{$this->option_slug}_enqueue",
			'modes' => "{$this->option_slug}_modes",
		);
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {

		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );

		$this->requires();
		$this->setups();
		$this->hooks();

		/**
		 * Fires after the plugin has been initialized.
		 *
		 * @since 1.2.0
		 *
		 * @param Plugin $this The Plugin class instance.
		 */
		do_action( 'ninecodes_social_manager_init', $this );
	}

	/**
	 * Clean database upon uninstalling the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function uninstall() {

		foreach ( $this->options as $key => $option_name ) {
			delete_option( $option_name );
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function requires() {

		require_once( $this->path_dir . 'includes/function-utilities.php' );

		require_once( $this->path_dir . 'includes/class-languages.php' );
		require_once( $this->path_dir . 'includes/class-helpers.php' );
		require_once( $this->path_dir . 'includes/class-options.php' );
		require_once( $this->path_dir . 'includes/class-theme-support.php' );

		require_once( $this->path_dir . 'includes/wp-settings/wp-settings.php' );
		require_once( $this->path_dir . 'includes/wp-settings/wp-settings-fields.php' );

		require_once( $this->path_dir . 'includes/ogp/open-graph-protocol.php' );
		require_once( $this->path_dir . 'includes/customize/control-radio-image.php' );

		require_once( $this->path_dir . 'admin/class-admin-view.php' );
		require_once( $this->path_dir . 'public/class-public-view.php' );
		require_once( $this->path_dir . 'widgets/class-widgets.php' );

		add_action( 'plugins_loaded', function() {

			require_once( $this->path_dir . 'includes/bb-metabox/butterbean.php' );
			require_once( $this->path_dir . 'includes/bb-metabox-extend/butterbean-extend.php' );
		} );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Add `plugin_action_links_` hook to display "Settings" link.
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		/**
		 * Add the Action link for the plugin in the Plugin list screen.
		 *
		 * !important that the plugin file name is always referring to the plugin main file
		 * in the plugin's root folder instead of the sub-folders in order for the function to work.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/prefixplugin_action_links_plugin_file/
		 */
		add_filter( 'plugin_action_links_' . plugin_basename( "{$this->path_dir}{$this->plugin_slug}.php" ), array( $this, 'plugin_action_links' ) );

		add_action( 'init', array( $this->languages, 'load_plugin_textdomain' ) );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, or WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function setups() {

		$this->languages = new Languages( $this->plugin_slug );

		new Admin_View( $this );
		new Public_View( $this );

		new Widgets( $this );

		add_action( 'admin_init', array( $this, 'updates' ) );
	}

	/**
	 * Update the version in the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function updates() {

		$regex = '/(-[alpha|beta|rc\.\d]+)/';
		$current_version = preg_replace( $regex, '', $this->version );

		$previous_version = get_option( $this->option_slug . '_version' );
		$previous_version = preg_replace( $regex, '', $previous_version );

		update_option( $this->option_slug . '_version', $current_version );

		if ( version_compare( $previous_version, $current_version, '<' ) || ! get_option( $this->option_slug . '_previous_version' ) ) {
			update_option( $this->option_slug . '_previous_version', $previous_version );
		}
	}

	/**
	 * Add the action link in Plugin list screen.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @param  array $links WordPress built-in links (e.g. Activate, Deactivate, and Edit).
	 * @return array        Action links with the new one added.
	 */
	public function plugin_action_links( $links ) {

		$markup = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=%2$s' ) ) . '">%1$s</a>';
		$settings = array(
			'settings' => sprintf( $markup, esc_html__( 'Settings', 'ninecodes-social-manager' ), $this->plugin_slug ),
		);

		return array_merge( $settings, $links );
	}

	/**
	 * Get the options saved in the database `wp_options`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $name The option name.
	 * @param string $key  The array key to retrieve from the option.
	 * @return mixed The option value or null if option is not available.
	 */
	public function get_option( $name = '', $key = '' ) {
		/*
		 * The $name parameter is required. If the $name is not set, simply return `null`.
		 */
		if ( empty( $name ) ) {
			return null;
		}

		$option = isset( $this->options[ $name ] ) ? get_option( $this->options[ $name ] ) : null;

		if ( $name && $key ) {
			return isset( $option[ $key ] ) ? $option[ $key ] : null;
		}

		return $option ? $option : null;
	}

	/**
	 * Return the theme support data.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return Theme_Support
	 */
	public function theme_support() {

		static $theme_support;

		if ( is_null( $theme_support ) ) {
			$theme_support = new Theme_Support();
		}

		return $theme_support;
	}
}
