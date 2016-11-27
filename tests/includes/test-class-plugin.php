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

		add_option( 'ncsocman_buttons_content', array(
			'includes' => array_keys( Options::button_sites( 'content' ) ),
		) );
		add_option( 'ncsocman_buttons_image', array(
			'includes' => array_keys( Options::button_sites( 'image' ) ),
		) );
	}

	/**
	 * Tear down.
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
	 * @return void
	 */
	public function test_plugin_get_opts() {
		$this->assertEquals( $this->option_slug, $this->plugin->get_opts() );
	}

	/**
	 * Test the plugin option.
	 *
	 * TODO Add more test for get_option() that retrieve value from database.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_plugin_get_option() {

		$opt = $this->plugin->get_option();
		$this->assertNull( $opt );

		$xyz = $this->plugin->get_option( 'xyz' ); // non-existent name.
		$this->assertNull( $xyz );

		$int = $this->plugin->get_option( 123 ); // integer name.
		$this->assertNull( $int );

		$buttons_content = $this->plugin->get_option( 'buttons_content', 'includes' );

		$this->assertEquals( 7, count( $buttons_content ) );
		$this->assertTrue( in_array( 'facebook', $buttons_content, true ) );
		$this->assertTrue( in_array( 'twitter', $buttons_content, true ) );
		$this->assertTrue( in_array( 'googleplus', $buttons_content, true ) );
		$this->assertTrue( in_array( 'pinterest', $buttons_content, true ) );
		$this->assertTrue( in_array( 'linkedin', $buttons_content, true ) );
		$this->assertTrue( in_array( 'reddit', $buttons_content, true ) );
		$this->assertTrue( in_array( 'email', $buttons_content, true ) );

		$buttons_image = $this->plugin->get_option( 'buttons_image', 'includes' );

		$this->assertEquals( 1, count( $buttons_image ) );
		$this->assertTrue( in_array( 'pinterest', $buttons_image, true ) );
	}
}
