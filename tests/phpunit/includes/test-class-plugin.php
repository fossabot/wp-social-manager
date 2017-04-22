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

		$this->plugin = new Plugin();
		$this->plugin->init();
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
