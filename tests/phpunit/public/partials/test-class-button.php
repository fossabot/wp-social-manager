<?php
/**
 * Class Test_Button
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
 * The class to test the "Button" class instance.
 *
 * @since 1.1.0
 */
class Test_Button extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	function tearDown() {

		$this->plugin = null;
		parent::tearDown();
	}

	/**
	 * T
	 *
	 * @return void
	 */
	public function test_get_label_method() {

		$button = $this->getMockForAbstractClass( Button::class, array( $this->plugin ) );

		// Bad: The site should be registered in the `Options::button_sites()`.
		$this->assertEquals( '', $button->get_label( 'ello', 'content' ) );

		// Bad: The context arguments should be 'image' or 'content'.
		$this->assertEquals( '', $button->get_label( 'facebook', 'image-content' ) );

		$this->assertEquals( 'Facebook', $button->get_label( 'facebook', 'content' ) ); // Facebook default label.
		$this->assertEquals( 'Twitter', $button->get_label( 'twitter', 'content' ) ); // Twitter default label.
		$this->assertEquals( 'Google+', $button->get_label( 'googleplus', 'content' ) ); // Google+ default label.
		$this->assertEquals( 'Pinterest', $button->get_label( 'pinterest', 'content' ) ); // Pinterest default label.
		$this->assertEquals( 'LinkedIn', $button->get_label( 'linkedin', 'content' ) ); // LinkedIn default label.
		$this->assertEquals( 'Reddit', $button->get_label( 'reddit', 'content' ) ); // Reddit default label.
		$this->assertEquals( 'Tumblr', $button->get_label( 'tumblr', 'content' ) ); // Tumblr default label.
		$this->assertEquals( 'Email', $button->get_label( 'email', 'content' ) ); // Email default label.

		$this->assertEquals( '', $button->get_label( 'facebook', 'image' ) ); // Facebook for Image is not yet supported.
		$this->assertEquals( 'Pinterest', $button->get_label( 'pinterest', 'image' ) ); // Pinterest default label.
	}
}
