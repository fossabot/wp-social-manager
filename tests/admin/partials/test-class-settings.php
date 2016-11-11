<?php
/**
 * Class TestPlugin
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
	}

	/**
	 * Ensure all hooks are present
	 *
	 * @return void
	 */
	public function test_settings_hooks() {

		$settings = new Settings( $this->plugin );
		$plugin_opts = $this->plugin->get_opts();

		$this->assertEquals( 10, has_action( 'init', array( $settings, 'frontend_setups' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $settings, 'setting_menu' ) ) );
		$this->assertEquals( 10, has_action( 'admin_init', array( $settings, 'setting_setups' ) ) );
		$this->assertEquals( 15, has_action( 'admin_init', array( $settings, 'setting_pages' ) ) );
		$this->assertEquals( 20, has_action( 'admin_init', array( $settings, 'setting_sections' ) ) );
		$this->assertEquals( 25, has_action( 'admin_init', array( $settings, 'setting_fields' ) ) );
		$this->assertEquals( 30, has_action( 'admin_init', array( $settings, 'setting_init' ) ) );
		$this->assertEquals( 10, has_action( "{$plugin_opts}_admin_enqueue_scripts", array( $settings, 'enqueue_scripts' ) ) );
		$this->assertEquals( 10, has_action( "{$plugin_opts}_admin_enqueue_styles", array( $settings, 'enqueue_styles' ) ) );
	}
}
