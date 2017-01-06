<?php
/**
 * Class TestPlugin
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
 * The class to test the "ThemeSupports" class instance.
 *
 * @since 1.0.0
 */
class TestAPIRoutes extends WP_UnitTestCase {

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

		$this->server = $wp_rest_server = new WP_REST_Server;

		// Constructing the plugin classes.
		$plugin = new Plugin();
		$public = new ViewPublic( $plugin );

		$this->routes = new RESTButtonsController( $public );

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

		$this->assertTrue( method_exists( $this->routes, 'hooks' ),  'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->routes, 'localize_scripts' ),  'Class does not have method \'localize_scripts\'' );
		$this->assertTrue( method_exists( $this->routes, 'register_routes' ),  'Class does not have method \'register_routes\'' );
		$this->assertTrue( method_exists( $this->routes, 'response_plugin' ),  'Class does not have method \'response_plugin\'' );
		$this->assertTrue( method_exists( $this->routes, 'response_buttons' ),  'Class does not have method \'response_buttons\'' );
		$this->assertTrue( method_exists( $this->routes, 'get_namespace' ),  'Class does not have method \'get_namespace\'' );
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

		$this->assertEquals( 10, has_filter( 'rest_api_init', array( $this->routes, 'register_routes' ) ) );
		$this->assertEquals( 10, has_filter( 'wp_enqueue_scripts', array( $this->routes, 'localize_scripts' ) ) );
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
		$this->assertEquals( 'ninecodes/v1/social-manager', $this->routes->get_namespace() . '/social-manager' );

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
		$this->routes = null;

		parent::tearDown();
	}
}
