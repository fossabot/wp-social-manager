<?php
/**
 * Class TestPlugin
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
class TestPlugin extends WP_UnitTestCase {

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
	 * @access public
	 * @var string
	 */
	public $plugin_slug = 'ninecodes-social-manager';

	/**
	 * The plugin option prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $option_slug = 'ncsocman';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = new Plugin();
		$this->plugin->initialize();
	}

	/**
	 * Tear down.
	 *
	 * @inheritdoc
	 */
	function tearDown() {
		$this->plugin = null;
		parent::tearDown();
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
		$this->assertEquals( $this->plugin_slug, $this->plugin->get_slug() );
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

		$this->assertTrue( method_exists( $this->plugin, 'get_opts' ),  'Class does not have method \'get_opts\'' );
		$this->assertEquals( $this->option_slug, $this->plugin->get_opts() );
	}

	/**
	 * Test the 'get_theme_support()' method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_theme_support() {

		// Make sure the method exist.
		$this->assertTrue( method_exists( $this->plugin, 'get_theme_support' ),  'Class does not have method \'get_theme_support\'' );

		// Make sure the method returns correct instance.
		$this->assertInstanceOf( '\NineCodes\SocialManager\Theme_Support', $this->plugin->get_theme_support() );
	}

	/**
	 * Test the 'get_view_admin()' method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_view_admin() {

		// Make sure the method exist.
		$this->assertTrue( method_exists( $this->plugin, 'get_view_admin' ),  'Class does not have method \'get_view_admin\'' );

		// Make sure the method returns correct instance.
		$this->assertInstanceOf( '\NineCodes\SocialManager\ViewAdmin', $this->plugin->get_view_admin() );
	}

	/**
	 * Test the 'get_view_public()' method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_view_public() {

		// Make sure the method exist.
		$this->assertTrue( method_exists( $this->plugin, 'get_view_public' ),  'Class does not have method \'get_view_public\'' );

		// Make sure the method returns correct instance.
		$this->assertInstanceOf( '\NineCodes\SocialManager\Public_View', $this->plugin->get_view_public() );
	}

	/**
	 * Test the 'get_widgets()' method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_widgets() {

		// Make sure the method exist.
		$this->assertTrue( method_exists( $this->plugin, 'get_widgets' ),  'Class does not have method \'get_widgets\'' );

		// Make sure the method returns correct instance.
		$this->assertInstanceOf( '\NineCodes\SocialManager\Widgets', $this->plugin->get_widgets() );
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

		$opt = $this->plugin->get_option();
		$this->assertNull( $opt );

		$xyz = $this->plugin->get_option( 'xyz' ); // non-existent name.
		$this->assertNull( $xyz );

		$int = $this->plugin->get_option( 123 ); // integer name.
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

		$buttons_content = $this->plugin->get_option( 'buttons_content', 'includes' );

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

		$buttons_image = $this->plugin->get_option( 'buttons_image', 'includes' );

		// Count, in case we will add more in the future.
		$this->assertEquals( 1, count( $buttons_image ) );

		$this->assertTrue( key_exists( 'pinterest', $buttons_image ) );
	}
}
