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
