<?php
/**
 * Class Test_Endpoint
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
 * The class to test the "Test_Endpoint" class instance.
 *
 * @since 1.0.0
 */
class Test_Endpoint extends WP_UnitTestCase {

	/**
	 * The Endpoint class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Endpoint
	 */
	protected $endpoint;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		// Setup the plugin.
		$plugin = new Plugin();
		$plugin->init();

		$this->endpoint = new Endpoint( $plugin, new Meta( $plugin ) );
	}

	/**
	 * Tear down.
	 */
	function tearDown() {

		$this->plugin = null;
		$this->public = null;
		$this->endpoint = null;

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

		$this->assertTrue( method_exists( $this->endpoint, 'get_content_endpoint' ),  'Class does not have method \'get_content_endpoint\'' );
		$this->assertTrue( method_exists( $this->endpoint, 'get_image_endpoint' ),  'Class does not have method \'get_image_endpoint\'' );
		$this->assertTrue( method_exists( $this->endpoint, 'joint_image_endpoint' ),  'Class does not have method \'joint_image_endpoint\'' );
		$this->assertTrue( method_exists( $this->endpoint, 'get_content_image_src' ),  'Class does not have method \'get_content_image_src\'' );
		$this->assertTrue( method_exists( $this->endpoint, 'get_post_meta' ),  'Class does not have method \'get_post_meta\'' );
		$this->assertTrue( method_exists( $this->endpoint, 'get_endpoint_base' ),  'Class does not have method \'get_endpoint_base\'' );
	}

	/**
	 * Function to test 'get_content_endpoint()' function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_content_endpoint() {

		// Create a post.
		$post_id = $this->factory->post->create();
		$response = $this->endpoint->get_content_endpoint( $post_id );

		$this->assertArrayHasKey( 'endpoint', $response );

		$endpoint = $response['endpoint'];

		// Count the number, in case we will add more in the future.
		$this->assertEquals( 8, count( $endpoint ) );

		$this->assertArrayHasKey( 'facebook', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['facebook'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['facebook'], 'https://www.facebook.com/sharer/sharer.php' ) );

		$this->assertArrayHasKey( 'twitter', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['twitter'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['twitter'], 'https://twitter.com/intent/tweet' ) );

		$this->assertArrayHasKey( 'googleplus', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['googleplus'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['googleplus'], 'https://plus.google.com/share' ) );

		$this->assertArrayHasKey( 'pinterest', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['pinterest'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['pinterest'], 'https://www.pinterest.com/pin/create/bookmarklet/' ) );

		$this->assertArrayHasKey( 'linkedin', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['linkedin'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['linkedin'], 'https://www.linkedin.com/shareArticle' ) );

		$this->assertArrayHasKey( 'reddit', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['reddit'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['reddit'], 'https://www.reddit.com/submit' ) );

		$this->assertArrayHasKey( 'tumblr', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['tumblr'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['tumblr'], 'http://www.tumblr.com/share/link' ) );

		$this->assertArrayHasKey( 'email', $endpoint );
		$this->assertNotFalse( filter_var( $endpoint['email'], FILTER_VALIDATE_URL ) );
		$this->assertEquals( 0, strpos( $endpoint['email'], 'mailto:' ) );
	}

	/**
	 * Function to test 'get_image_endpoint()' function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_image_endpoint() {

		$image_src = 'https://placeholdit.imgix.net/~text?txtsize=33&txt=350%C3%97150&w=350&h=150';

		$post_id = $this->factory->post->create( array(
			'post_content' => 'This is an image <img src="' . $image_src . '" width=350" height="150" >.',
		) );
		$response = $this->endpoint->get_image_endpoint( $post_id );

		// We should only have 1 image.
		$this->assertEquals( 1, count( $response ) );

		foreach ( $response as $res ) {

			$this->assertArrayHasKey( 'src', $res );
			$this->assertArrayHasKey( 'endpoint', $res );

			$endpoint = $res['endpoint'];

			$this->assertEquals( 1, count( $endpoint ) ); // Count the number, in case we will add more in the future.
			$this->assertArrayHasKey( 'pinterest', $endpoint );
			$this->assertNotFalse( filter_var( $endpoint['pinterest'], FILTER_VALIDATE_URL ) );
			$this->assertEquals( 0, strpos( $endpoint['pinterest'], add_query_arg( 'url', rawurlencode( $image_src ), 'https://www.pinterest.com/pin/create/bookmarklet/' ) ) );
		}
	}
}