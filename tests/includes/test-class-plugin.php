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
	public $plugin_opts = 'ncsocman';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = new Plugin();
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
		$this->assertEquals( $this->plugin_opts, $this->plugin->get_opts() );
	}

	/**
	 * Test the plugin option.
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
	}
}
