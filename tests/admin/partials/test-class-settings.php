<?php
/**
 * Class TestSettings
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
 * The class to test the "Settings" class.
 *
 * TODO: Add tests for the Settings default values.
 *
 * @since 1.0.0
 */
class TestSettings extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		$this->plugin = new Plugin();
		$this->plugin_slug = $this->plugin->get_slug();

		$this->settings = new Settings( $this->plugin );
	}

	/**
	 * Ensure all hooks are present
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_settings_hooks() {

		$this->assertEquals( 10, has_action( 'init', array( $this->settings, 'frontend_setups' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->settings, 'setting_menu' ) ) );

		$this->assertEquals( 10, has_action( 'admin_init', array( $this->settings, 'setting_setups' ) ) );
		$this->assertEquals( 15, has_action( 'admin_init', array( $this->settings, 'setting_pages' ) ) );
		$this->assertEquals( 20, has_action( 'admin_init', array( $this->settings, 'setting_sections' ) ) );
		$this->assertEquals( 25, has_action( 'admin_init', array( $this->settings, 'setting_fields' ) ) );
		$this->assertEquals( 30, has_action( 'admin_init', array( $this->settings, 'setting_init' ) ) );
	}

	/**
	 * Function to test the setting is set up with proper classes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_setting_setups() {

		$this->settings->setting_setups();

		$this->assertInstanceOf( '\NineCodes\WPSettings\Settings', $this->settings->settings );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Validation', $this->settings->validate );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Fields', $this->settings->fields );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Helps', $this->settings->helps );
	}
}
