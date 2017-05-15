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
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $version = '2.0.0-alpha.3';

	/**
	 * The path directory relative to the current file.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	function __construct() {

		$this->option_slug = Options::slug();
		$this->option_names = Options::names();
	}

	/**
	 * Get the plugin slug.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function slug() {
		return self::$plugin_slug;
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

		foreach ( $this->option_names as $key => $option_name ) {
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

		require_once( $this->path_dir . 'includes/wp-settings/class-settings.php' );
		require_once( $this->path_dir . 'includes/wp-settings/class-fields.php' );

		require_once( $this->path_dir . 'includes/ogp/open-graph-protocol.php' );

		require_once( $this->path_dir . 'admin/class-admin-view.php' );
		require_once( $this->path_dir . 'public/class-public-view.php' );
		require_once( $this->path_dir . 'widgets/class-widget.php' );

		add_action( 'plugins_loaded', array( $this, 'requires_when_plugins_loaded' ) );
	}

	/**
	 * Load the required dependencies when plugins are already loaded.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function requires_when_plugins_loaded() {

		require_once( $this->path_dir . 'includes/bb-metabox/butterbean.php' );
		require_once( $this->path_dir . 'includes/bb-metabox-extend/butterbean-extend.php' );
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
		 * @link https://developer.wordpress.org/reference/hooks/prefixplugin_action_links_plugin_file/
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
		$this->option = new Options;
		$this->helper = new Helpers;

		$admin_view = new Admin_View( $this );
		$public_view = new Public_View( $this );

		/**
		 * Register a new image size to serve in the og:image or twitter:image meta tags.
		 *
		 * @link https://blog.bufferapp.com/ideal-image-sizes-social-media-posts
		 */
		add_image_size( 'social-media', 600, 315, true );

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

		$updated_version = $this->version;
		$installed_version = get_option( $this->option->slug() . '_version' );
		$previous_version = get_option( $this->option->slug() . '_previous_version' );

		update_option( $this->option->slug() . '_version', $updated_version ); // Update installed version.

		if ( ! $previous_version ) {
			update_option( $this->option->slug() . '_previous_version', $updated_version );
			return;
		}

		if ( version_compare( $installed_version, $updated_version, '<' ) ) {
			update_option( $this->option->slug() . '_previous_version', $installed_version );
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
	public function plugin_action_links( array $links ) {

		$markup = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=%2$s' ) ) . '">%1$s</a>';
		$settings = array(
			'settings' => sprintf( $markup, __( 'Settings', 'ninecodes-social-manager' ), $this->plugin_slug ),
		);

		return array_merge( $settings, $links );
	}
}
