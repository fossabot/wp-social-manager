<?php
/**
 * Class TestEndpoints
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load Global classes;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "TestEndpoints" class instance.
 *
 * @since 1.0.0
 */
class TestEndpoints extends WP_UnitTestCase {

	/**
	 * The Endpoints class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Endpoints
	 */
	protected $endpoints;

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

		$public = new ViewPublic( $plugin );
		$this->endpoints = new Endpoints( $public );

		// Add the buttons content option value.
		add_option( $plugin->get_opts() . '_buttons_content', array(
			'includes' => array_keys( Options::button_sites( 'content' ) ),
		) );

		// Add the buttons image option value.
		add_option( $plugin->get_opts() . '_buttons_image', array(
			'includes' => array_keys( Options::button_sites( 'image' ) ),
		) );
	}

	/**
	 * Tear down.
	 */
	function tearDown() {
		$this->plugin = null;
		$this->public = null;
		$this->endpoints = null;
		parent::tearDown();
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

		$this->assertTrue( method_exists( $this->endpoints, 'get_content_endpoints' ),  'Class does not have method \'get_content_endpoints\'' );
		$this->assertTrue( method_exists( $this->endpoints, 'get_image_endpoints' ),  'Class does not have method \'get_image_endpoints\'' );
		$this->assertTrue( method_exists( $this->endpoints, 'joint_image_endpoints' ),  'Class does not have method \'joint_image_endpoints\'' );
		$this->assertTrue( method_exists( $this->endpoints, 'get_content_image_srcs' ),  'Class does not have method \'get_content_image_srcs\'' );
		$this->assertTrue( method_exists( $this->endpoints, 'get_post_metas' ),  'Class does not have method \'get_post_metas\'' );
		$this->assertTrue( method_exists( $this->endpoints, 'get_endpoint_base' ),  'Class does not have method \'get_endpoint_base\'' );
	}

	/**
	 * Function to test 'get_content_endpoints()' function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_content_endpoints() {

		// Create a post.
		$post_id = $this->factory->post->create();
		$response = $this->endpoints->get_content_endpoints( $post_id );

		$this->assertArrayHasKey( 'endpoints', $response );

		$endpoints = $response['endpoints'];

		// Count the number, in case we will add more in the future.
		$this->assertEquals( 7, count( $endpoints ) );

		$this->assertArrayHasKey( 'facebook', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['facebook'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['facebook'], 'https://www.facebook.com/sharer/sharer.php' ) );

		$this->assertArrayHasKey( 'twitter', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['twitter'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['twitter'], 'https://twitter.com/intent/tweet' ) );

		$this->assertArrayHasKey( 'googleplus', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['googleplus'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['googleplus'], 'https://plus.google.com/share' ) );

		$this->assertArrayHasKey( 'pinterest', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['pinterest'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['pinterest'], 'https://www.pinterest.com/pin/create/bookmarklet/' ) );

		$this->assertArrayHasKey( 'linkedin', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['linkedin'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['linkedin'], 'https://www.linkedin.com/shareArticle' ) );

		$this->assertArrayHasKey( 'reddit', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['reddit'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['reddit'], 'https://www.reddit.com/submit' ) );

		$this->assertArrayHasKey( 'email', $endpoints );
		$this->assertNotFalse( filter_var( $endpoints['email'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoints['email'], 'mailto:' ) );
	}

	/**
	 * Function to test 'get_image_endpoints()' function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_image_endpoints() {

		$post_id = $this->factory->post->create( array(
			'post_content' => 'This is an image <img src="https://placeholdit.imgix.net/~text?txtsize=33&txt=350%C3%97150&w=350&h=150" width=350" height="150" >.',
		) );
		$response = $this->endpoints->get_image_endpoints( $post_id );

		$this->assertArrayHasKey( 'endpoints', $response );

		$endpoints = $response['endpoints'];

		// We should only have 1 image.
		$this->assertEquals( 1, count( $endpoints ) );

		foreach ( $endpoints as $res ) {

			// Count the number, in case we will add more in the future.
			$this->assertEquals( 1, count( $res ) );

			$this->assertArrayHasKey( 'pinterest', $res );
			$this->assertNotFalse( filter_var( $res['pinterest'], FILTER_VALIDATE_URL ) );
			$this->assertEquals( 0, strpos( $res['pinterest'], 'https://www.pinterest.com/pin/create/bookmarklet/' ) );
		}
	}
}
