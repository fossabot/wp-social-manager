<?php
/**
 * Class TestPlugin
 *
 * @package NineCodes\SocialMediaManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialMediaManager;

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

		add_option( 'ncsocman_buttons_content', array(
			'includes' => array_keys( Options::button_sites( 'content' ) ),
		) );
		add_option( 'ncsocman_buttons_image', array(
			'includes' => array_keys( Options::button_sites( 'image' ) ),
		) );
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
	 * Test the 'get_theme_supports()' method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_plugin_get_theme_supports() {

		// Make sure the method exist.
		$this->assertTrue( method_exists( $this->plugin, 'get_theme_supports' ),  'Class does not have method \'get_theme_supports\'' );

		// Make sure the method returns correct instance.
		$this->assertInstanceOf( '\NineCodes\SocialMediaManager\ThemeSupports', $this->plugin->get_theme_supports() );
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
		$this->assertInstanceOf( '\NineCodes\SocialMediaManager\ViewAdmin', $this->plugin->get_view_admin() );
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
		$this->assertInstanceOf( '\NineCodes\SocialMediaManager\ViewPublic', $this->plugin->get_view_public() );
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
		$this->assertInstanceOf( '\NineCodes\SocialMediaManager\Widgets', $this->plugin->get_widgets() );
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
		$this->assertEquals( 7, count( $buttons_content ) );

		$this->assertTrue( in_array( 'facebook', $buttons_content, true ) );
		$this->assertTrue( in_array( 'twitter', $buttons_content, true ) );
		$this->assertTrue( in_array( 'googleplus', $buttons_content, true ) );
		$this->assertTrue( in_array( 'pinterest', $buttons_content, true ) );
		$this->assertTrue( in_array( 'linkedin', $buttons_content, true ) );
		$this->assertTrue( in_array( 'reddit', $buttons_content, true ) );
		$this->assertTrue( in_array( 'email', $buttons_content, true ) );
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

		$this->assertTrue( in_array( 'pinterest', $buttons_image, true ) );
	}
}
