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
 * The class to test the "ThemeSupports" class instance.
 *
 * @since 1.0.0
 */
class TestThemeSupports extends WP_UnitTestCase {


	/**
	 * The Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The ThemeSupports instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var ThemeSupports
	 */
	public $theme_supports;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		$this->plugin = new Plugin();
		$this->theme_supports = new ThemeSupports();
	}

	/**
	 * Test plugin slug name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_name() {

		$plugin_slug = $this->plugin->get_slug();
		$feature_name = $this->theme_supports->get_feature_name();

		$this->assertEquals( $plugin_slug, $feature_name );
	}

	/**
	 * Test the ThemeSupports is() method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports() {

		add_theme_support( $this->theme_supports->get_feature_name() );
		$this->assertTrue( $this->theme_supports->theme_supports() );
	}

	/**
	 * Test the ThemeSupports 'stylesheet' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_stylesheet() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'stylesheet' => false,
		));

		$supports = $this->theme_supports->theme_supports();

		$this->assertArrayHasKey( 'stylesheet', $supports );
		$this->assertFalse( $supports['stylesheet'] );
	}

	/**
	 * Test the ThemeSupports HTML 'buttons-mode' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_buttons_mode_html() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'buttons-mode' => 'html',
		));

		$supports = $this->theme_supports->theme_supports();

		$this->assertArrayHasKey( 'buttons-mode', $supports );
		$this->assertEquals( 'html', $supports['buttons-mode'] );

		$is = $this->theme_supports->is( 'buttons-mode' );
		$this->assertEquals( 'html', $is );
	}

	/**
	 * Test the ThemeSupports JSON 'buttons-mode' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_buttons_mode_json() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'buttons-mode' => 'json',
		));

		$supports = $this->theme_supports->theme_supports();

		$this->assertArrayHasKey( 'buttons-mode', $supports );
		$this->assertEquals( 'json', $supports['buttons-mode'] );

		$is = $this->theme_supports->is( 'buttons-mode' );
		$this->assertEquals( 'json', $is );
	}

	/**
	 * Test the TestThemeSupports 'attr-prefix' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_attr_prefix() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr-prefix' => 'social',
		));

		$supports = $this->theme_supports->theme_supports();

		$this->assertArrayHasKey( 'attr-prefix', $supports );
		$this->assertEquals( 'social', $supports['attr-prefix'] );

		$is = $this->theme_supports->is( 'attr-prefix' );
		$this->assertFalse( $is );
	}
}
