<?php
/**
 * Class Test_REST_Button
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load Global classes;
 */
use \WP_UnitTestCase;
use \WP_REST_Server;
use \WP_REST_Request;

/**
 * The class to test the "Test_REST_Button" class instance.
 *
 * @since 1.0.0
 */
class Test_REST_Button extends WP_UnitTestCase {

	/**
	 * The APIRoutes
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var APIRoutes
	 */
	protected $routes;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		global $wp_rest_server;

		$plugin = new Plugin();
		$wp_rest_server = new WP_REST_Server;

		$this->server = $wp_rest_server;
		$this->rest_buttons = new REST_Buttons( $plugin );

		do_action( 'rest_api_init' );
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

		$this->assertTrue( method_exists( $this->rest_buttons, 'hooks' ),  'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'localize_scripts' ),  'Class does not have method \'localize_scripts\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'register_routes' ),  'Class does not have method \'register_routes\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'get_plugin_info' ),  'Class does not have method \'get_item\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'get_item' ),  'Class does not have method \'get_item\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'prepare_item_for_response' ),  'Class does not have method \'prepare_item_for_response\'' );
		$this->assertTrue( method_exists( $this->rest_buttons, 'get_namespace' ),  'Class does not have method \'get_namespace\'' );
	}

	/**
	 * Function to test hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_hooks() {

		$this->assertEquals( 10, has_filter( 'rest_api_init', array( $this->rest_buttons, 'register_routes' ) ) );
		$this->assertEquals( 10, has_filter( 'wp_enqueue_scripts', array( $this->rest_buttons, 'localize_scripts' ) ) );
	}

	/**
	 * Function to test the route url is available.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 *
	 * TODO: Test post buttons with the parameters.
	 */
	public function test_routes() {

		// Test namespace name.
		$this->assertEquals( 'ninecodes/v1/social-manager', $this->rest_buttons->get_namespace() . '/social-manager' );

		// Make sure we have the following routes registered.
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( '/ninecodes/v1/social-manager', $routes );
		$this->assertArrayHasKey( '/ninecodes/v1/social-manager/buttons/(?P<id>[\d]+)', $routes );
	}

	/**
	 * TearDown.
	 *
	 * @inheritdoc
	 */
	public function tearDown() {

		global $wp_rest_server;
		$wp_rest_server = null;
		$this->rest_buttons = null;

		parent::tearDown();
	}
}
