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

/**
 * The class to test the "ThemeSupports" class instance.
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
	 * Function to test 'get_content_endpoints()' function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_content_endpoints() {

		$this->assertTrue( method_exists( $this->endpoints, 'get_content_endpoints' ),  'Class does not have method \'get_content_endpoints\'' );

		// Create a post.
		$post_id = $this->factory->post->create();
		$response = $this->endpoints->get_content_endpoints( $post_id );

		// Count the number, in case we will add more in the future.
		$this->assertEquals( 7, count( $response ) );

		$this->assertArrayHasKey( 'facebook', $response );
		$this->assertNotFalse( filter_var( $response['facebook'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['facebook'], 'https://www.facebook.com/sharer/sharer.php' ) );

		$this->assertArrayHasKey( 'twitter', $response );
		$this->assertNotFalse( filter_var( $response['twitter'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['twitter'], 'https://twitter.com/intent/tweet' ) );

		$this->assertArrayHasKey( 'googleplus', $response );
		$this->assertNotFalse( filter_var( $response['googleplus'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['googleplus'], 'https://plus.google.com/share' ) );

		$this->assertArrayHasKey( 'pinterest', $response );
		$this->assertNotFalse( filter_var( $response['pinterest'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['pinterest'], 'https://www.pinterest.com/pin/create/bookmarklet/' ) );

		$this->assertArrayHasKey( 'linkedin', $response );
		$this->assertNotFalse( filter_var( $response['linkedin'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['linkedin'], 'https://www.linkedin.com/shareArticle' ) );

		$this->assertArrayHasKey( 'reddit', $response );
		$this->assertNotFalse( filter_var( $response['reddit'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['reddit'], 'https://www.reddit.com/submit' ) );

		$this->assertArrayHasKey( 'email', $response );
		$this->assertNotFalse( filter_var( $response['email'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $response['email'], 'mailto:' ) );
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

		$this->assertTrue( method_exists( $this->endpoints, 'get_image_endpoints' ),  'Class does not have method \'get_image_endpoints\'' );

		$post_id = $this->factory->post->create( array(
			'post_content' => 'This is an image <img src="https://placeholdit.imgix.net/~text?txtsize=33&txt=350%C3%97150&w=350&h=150" width=350" height="150" >.',
		) );
		$response = $this->endpoints->get_image_endpoints( $post_id );

		// We should only have 1 image.
		$this->assertEquals( 1, count( $response ) );

		foreach ( $response as $res ) {

			// Count the number, in case we will add more in the future.
			$this->assertEquals( 1, count( $res ) );

			$this->assertArrayHasKey( 'pinterest', $res );
			$this->assertNotFalse( filter_var( $res['pinterest'], FILTER_VALIDATE_URL ) );
			$this->assertEquals( 0, strpos( $res['pinterest'], 'https://www.pinterest.com/pin/create/bookmarklet/' ) );
		}
	}
}
