<?php
/**
 * Class TestButtonsImage
 *
 * @package NineCodes\SocialMediaManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialMediaManager;

/**
 * Load WP_UnitTestCase;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "TestButtonsImage" class instance.
 *
 * @since 1.1.0
 */
class TestButtonsImage extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The ButtonsContent class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var ButtonsContent
	 */
	protected $buttons_content;

	/**
	 * The ButtonsImage class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var ButtonsImage
	 */
	protected $buttons_image;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		$this->plugin = new Plugin();
		$this->plugin->initialize();

		$this->buttons_content = new ButtonsContent( $this->plugin );
		$this->buttons_image = new ButtonsImage( $this->plugin );
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
		$this->assertEquals( $icons, $this->buttons_image->get_button_icons() );

		add_filter( 'ninecodes_social_manager_icons', function( $icons, $args, $subject ) {

			if ( 'ButtonsImage' === $subject ) {

				unset( $icons['facebook'] );
				unset( $icons['twitter'] );
				unset( $icons['dribbble'] );
			}

			return $icons;
		}, 10, 3 );

		// ButtonsImage should have not theses removed keys.
		$this->assertArrayNotHasKey( 'facebook', $this->buttons_image->get_button_icons() );
		$this->assertArrayNotHasKey( 'twitter', $this->buttons_image->get_button_icons() );
		$this->assertArrayNotHasKey( 'dribbble', $this->buttons_image->get_button_icons() );

		// ButtonsContent should have theses removed keys.
		$this->assertArrayHasKey( 'facebook', $this->buttons_content->get_button_icons() );
		$this->assertArrayHasKey( 'twitter', $this->buttons_content->get_button_icons() );
		$this->assertArrayHasKey( 'dribbble', $this->buttons_content->get_button_icons() );
	}
}