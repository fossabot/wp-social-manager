<?php
/**
 * Class Test_Buttons_Content
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
 * The class to test the "Test_Buttons_Content" class instance.
 *
 * @since 1.1.0
 */
class Test_Buttons_Content extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The Buttons_Content class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Buttons_Content
	 */
	protected $buttons_content;

	/**
	 * The Buttons_Image class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Buttons_Image
	 */
	protected $buttons_image;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		$this->plugin = new Plugin();
		$this->plugin->init();

		$this->buttons_content = new Buttons_Content( $this->plugin );
		$this->buttons_image = new Buttons_Image( $this->plugin );
	}

	/**
	 * Function to test the 'get_buttons_icons'.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_button_icons() {

		$icons = Helpers::get_social_icons();
		$this->assertEquals( $icons, $this->buttons_content->get_buttons_icons() );

		add_filter( 'ninecodes_social_manager_icons', function( $icons, $context, $args ) {

			if ( 'buttons_content' === $context ) {

				unset( $icons['facebook'] );
				unset( $icons['twitter'] );
			}

			return $icons;
		}, 10, 3 );

		// Buttons_Image should have not these removed keys.
		$this->assertArrayNotHasKey( 'facebook', $this->buttons_content->get_buttons_icons() );
		$this->assertArrayNotHasKey( 'twitter', $this->buttons_content->get_buttons_icons() );

		// Buttons_Image should have theses removed keys.
		$this->assertArrayHasKey( 'facebook', $this->buttons_image->get_buttons_icons() );
		$this->assertArrayHasKey( 'twitter', $this->buttons_image->get_buttons_icons() );
	}
}
