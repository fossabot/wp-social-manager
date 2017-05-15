<?php
/**
 * Class Test_Plugin
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load WP_UnitTestCase;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "Plugin" class instance.
 *
 * @since 1.0.0
 */
class Test_Plugin extends WP_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The plugin slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PLUGIN_SLUG = 'ninecodes-social-manager';

	/**
	 * The plugin option prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	const OPTION_SLUG = 'ncsocman';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();
	}

	/**
	 * Test plugin slug name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_slug() {
		$this->assertEquals( self::PLUGIN_SLUG, $this->plugin->plugin_slug );
	}

	/**
	 * Test the plugin option prefix name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_opts() {
		$this->assertEquals( self::OPTION_SLUG, $this->plugin->option_slug );
	}

	/**
	 * Test the plugin option.
	 *
	 * TODO Add more test for get_option() that retrieve value from database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_option() {

		$opt = $this->plugin->option->get();
		$this->assertNull( $opt );

		$xyz = $this->plugin->option->get( 'xyz' ); // non-existent name.
		$this->assertNull( $xyz );

		$int = $this->plugin->option->get( 123 ); // integer name.
		$this->assertNull( $int );
	}

	/**
	 * Test the 'buttons_content' option.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_option_buttons_content() {

		$buttons_content = $this->plugin->option->get( 'button_content', 'include' );

		// Count, in case we will add more in the future.
		$this->assertEquals( 8, count( $buttons_content ) );

		$this->assertTrue( key_exists( 'facebook', $buttons_content ) );
		$this->assertTrue( key_exists( 'twitter', $buttons_content ) );
		$this->assertTrue( key_exists( 'googleplus', $buttons_content ) );
		$this->assertTrue( key_exists( 'pinterest', $buttons_content ) );
		$this->assertTrue( key_exists( 'linkedin', $buttons_content ) );
		$this->assertTrue( key_exists( 'reddit', $buttons_content ) );
		$this->assertTrue( key_exists( 'tumblr', $buttons_content ) );
		$this->assertTrue( key_exists( 'email', $buttons_content ) );
	}

	/**
	 * Test the 'buttons_image' option.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_option_buttons_image() {

		$buttons_image = $this->plugin->option->get( 'button_image', 'include' );

		// Count, in case we will add more in the future.
		$this->assertEquals( 1, count( $buttons_image ) );

		$this->assertTrue( key_exists( 'pinterest', $buttons_image ) );
	}

	/**
	 * Test adding action link to the plugin table.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_action_links() {

		$links = $this->plugin->plugin_action_links( array() );

		$this->assertArrayHasKey( 'settings', $links );
		$this->assertEquals( '<a href="http://example.org/wp-admin/options-general.php?page=ninecodes-social-manager">Settings</a>', $links['settings'] );
	}

	/**
	 * Test the 'updates()' method.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_updates_init() {

		$version = get_option( $this->plugin->option_slug . '_version' );
		$this->assertFalse( $version ); // The method have to be instantiated in the admin hence should False.

		$this->plugin->updates();

		$version = get_option( $this->plugin->option_slug . '_version' );
		$this->assertEquals( $this->plugin->version, $version );

		$prev_version = get_option( $this->plugin->option_slug . '_previous_version' );
		$this->assertEquals( $this->plugin->version, $prev_version );

		// Reset the option.
		delete_option( $this->plugin->option_slug . '_version' );
		delete_option( $this->plugin->option_slug . '_previous_version' );
	}

	/**
	 * Test the 'updates()' with updated value.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_updates_updated() {

		// Current state.
		add_option( $this->plugin->option_slug . '_version', '2.0.0' );
		add_option( $this->plugin->option_slug . '_previous_version', '1.5.0' );

		// Plugin updated.
		$this->plugin->version = '3.0.0';
		$this->plugin->updates();

		$version = get_option( $this->plugin->option_slug . '_version' );
		$this->assertEquals( '3.0.0', $version );

		$prev_version = get_option( $this->plugin->option_slug . '_previous_version' );
		$this->assertEquals( '2.0.0', $prev_version );

		// Reset the option.
		delete_option( $this->plugin->option_slug . '_version' );
		delete_option( $this->plugin->option_slug . '_previous_version' );
	}


	/**
	 * Test the 'updates()' with beta value.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_updates_alpha_beta() {

		// Current state.
		add_option( $this->plugin->option_slug . '_version', '2.0.0-beta.2' );
		add_option( $this->plugin->option_slug . '_previous_version', '2.0.0-alpha.1' );

		// Plugin updated.
		$this->plugin->version = '3.0.0-alpha.1';
		$this->plugin->updates();

		$version = get_option( $this->plugin->option_slug . '_version' );
		$this->assertEquals( '3.0.0-alpha.1', $version );

		$prev_version = get_option( $this->plugin->option_slug . '_previous_version' );
		$this->assertEquals( '2.0.0-beta.2', $prev_version );

		// Reset the option.
		delete_option( $this->plugin->option_slug . '_version' );
		delete_option( $this->plugin->option_slug . '_previous_version' );
	}

	/**
	 * Test the 'theme_support()' method.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_support() {

		$theme_support = $this->plugin->helper->theme_support();
		$this->assertInstanceOf( __NAMESPACE__ . '\\Theme_Support', $theme_support );
	}
}
