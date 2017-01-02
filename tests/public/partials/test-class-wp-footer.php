<?php
/**
 * Class TestWPFooter
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
 * The class to test the "TestWPFooter" class instance.
 *
 * @since 1.0.4
 */
class TestWPFooter extends WP_UnitTestCase {

	/**
	 * The Endpoints class instance.
	 *
	 * @since 1.0.4
	 * @access protected
	 * @var WPFooter
	 */
	protected $wp_footer;

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
		$this->wp_footer = new WPFooter( $public );
	}

	/**
	 * Function to `icon_reference_svg` method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_icon_reference_svg() {

		$this->assertTrue( method_exists( $this->wp_footer, 'icon_reference_svg' ),  'Class does not have method \'icon_reference_svg\'' );

		ob_start();
		$this->wp_footer->icon_reference_svg();
		$buffer = ob_get_clean();

		$this->assertContains( '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" display="none">', $buffer );
	}

	/**
	 * Function to test hooks.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_hooks() {

		$this->assertEquals( -50, has_action( 'wp_footer', array( $this->wp_footer, 'icon_reference_svg' ) ) );
	}
}
