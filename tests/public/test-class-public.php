<?php
/**
 * Class TestPublic
 *
 * TODO: Add tests for the empty functions in this file.
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
 * The class to test the "TestPublic" class instance.
 *
 * @since 1.0.4
 */
class TestPublic extends WP_UnitTestCase {

	/**
	 * The Public
	 *
	 * @since 1.0.4
	 * @access protected
	 *
	 * @var Public
	 */
	protected $public;

	/**
	 * The plugin slug.
	 *
	 * @since 1.0.4
	 * @access protected
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		// Setup the plugin.
		$plugin = new Plugin();
		$plugin->initialize();

		$this->plugin_slug = $plugin->get_slug();
		$this->public = new ViewPublic( $plugin );
	}

	/**
	 * Function to test Class methods availability.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( $this->public, 'requires' ),  'Class does not have method \'requires\'' );
		$this->assertTrue( method_exists( $this->public, 'hooks' ),  'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->public, 'setups' ),  'Class does not have method \'setups\'' );
		$this->assertTrue( method_exists( $this->public, 'register_styles' ),  'Class does not have method \'register_styles\'' );
		$this->assertTrue( method_exists( $this->public, 'register_scripts' ),  'Class does not have method \'register_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'enqueue_styles' ),  'Class does not have method \'enqueue_styles\'' );
		$this->assertTrue( method_exists( $this->public, 'enqueue_scripts' ),  'Class does not have method \'enqueue_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'is_load_stylesheet' ),  'Class does not have method \'is_load_stylesheet\'' );
		$this->assertTrue( method_exists( $this->public, 'is_load_scripts' ),  'Class does not have method \'is_load_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'is_json_mode' ),  'Class does not have method \'is_json_mode\'' );
		$this->assertTrue( method_exists( $this->public, 'is_buttons_active' ),  'Class does not have method \'is_buttons_active\'' );
	}

	/**
	 * Function to test hooks.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_hooks() {

		$this->assertEquals( 10, has_action( 'init', array( $this->public, 'setups' ) ) );
		$this->assertEquals( -10, has_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_styles' ) ) );
		$this->assertEquals( -10, has_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) ) );
	}

	/**
	 * Function to test 'register_styles' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_register_styles() {

		$this->public->register_styles();
		$this->assertTrue( wp_style_is( $this->plugin_slug, 'registered' ) );
	}

	/**
	 * Function to test 'register_scripts' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_register_scripts() {}

	/**
	 * Function to test 'enqueue_styles' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_enqueue_styles() {}

	/**
	 * Function to test 'is_load_stylesheet' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_load_stylesheet() {}

	/**
	 * Function to test 'is_load_scripts' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_load_scripts() {}

	/**
	 * Function to test 'is_json_mode' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_json_mode() {}

	/**
	 * Function to test 'is_json_mode' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_buttons_active() {}
}
