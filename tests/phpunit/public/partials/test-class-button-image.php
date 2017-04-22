<?php
/**
 * Class Test_Button_Image
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
 * The class to test the "Test_Button_Image" class instance.
 *
 * @since 1.1.0
 */
class Test_Button_Image extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The Button_Content class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Button_Content
	 */
	protected $button_content;

	/**
	 * The Button_Image class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Button_Image
	 */
	protected $button_image;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		$this->plugin = new Plugin();
		$this->plugin->init();

		$this->button_content = new Button_Content( $this->plugin );
		$this->button_image = new Button_Image( $this->plugin );
	}

	/**
	 * Test the Button_Content class methods.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_button_image_methods() {

		$this->assertTrue( method_exists( $this->button_image, 'render_button' ), 'Class does not have method \'render_button\'' );
		$this->assertTrue( method_exists( $this->button_image, 'render_html' ), 'Class does not have method \'render_html\'' );
		$this->assertTrue( method_exists( $this->button_image, 'render_tmpl' ), 'Class does not have method \'render_tmpl\'' );
		$this->assertTrue( method_exists( $this->button_image, 'render_view' ), 'Class does not have method \'render_view\'' );
		$this->assertTrue( method_exists( $this->button_image, 'get_icons' ), 'Class does not have method \'get_icons\'' );
		$this->assertTrue( method_exists( $this->button_image, 'get_label' ), 'Class does not have method \'get_label\'' );
		$this->assertTrue( method_exists( $this->button_image, 'get_mode' ), 'Class does not have method \'get_mode\'' );
		$this->assertTrue( method_exists( $this->button_image, 'get_attr_prefix' ), 'Class does not have method \'get_attr_prefix\'' );
		$this->assertTrue( method_exists( $this->button_image, 'get_post_status' ), 'Class does not have method \'get_post_status\'' );
		$this->assertTrue( method_exists( $this->button_image, 'in_amp' ), 'Class does not have method \'in_amp\'' );
		$this->assertTrue( method_exists( $this->button_image, 'to_html' ), 'Class does not have method \'to_html\'' );
	}

	/**
	 * Function to test the `get_icon`.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_icons() {

		$icons = Helpers::get_social_icons();
		$this->assertEquals( $icons, $this->button_image->get_icons() );

		add_filter( 'ninecodes_social_manager_icons', function( $icons, $context, $args ) {

			if ( 'buttons_image' === $context ) {

				unset( $icons['facebook'] );
				unset( $icons['twitter'] );
			}

			return $icons;
		}, 10, 3 );

		// Button_Image should have not theses removed keys.
		$this->assertArrayNotHasKey( 'facebook', $this->button_image->get_icons() );
		$this->assertArrayNotHasKey( 'twitter', $this->button_image->get_icons() );

		// Button_Content should have theses removed keys.
		$this->assertArrayHasKey( 'facebook', $this->button_content->get_icons() );
		$this->assertArrayHasKey( 'twitter', $this->button_content->get_icons() );
	}
}
