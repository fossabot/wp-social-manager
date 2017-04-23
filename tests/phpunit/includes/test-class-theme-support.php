<?php
/**
 * Class Test_Theme_Support
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
 * The class to test the "Theme_Support" class instance.
 *
 * @since 1.0.0
 */
class Test_Theme_Support extends WP_UnitTestCase {

	/**
	 * The Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The Theme_Support instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Theme_Support
	 */
	public $theme_support;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->theme_support = new Theme_Support();
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
		$this->assertEquals( $this->plugin->plugin_slug, $this->theme_support->get_feature_name() );
	}

	/**
	 * Test the Theme_Support is() method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_support() {

		add_theme_support( $this->theme_support->get_feature_name() );
		$this->assertTrue( $this->theme_support->theme_support() );
	}

	/**
	 * Test the Theme_Support 'stylesheet' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_stylesheet() {

		add_theme_support($this->theme_support->get_feature_name(), array(
			'stylesheet' => true,
		));

		$supports = $this->theme_support->theme_support();

		$this->assertArrayHasKey( 'stylesheet', $supports );
		$this->assertTrue( $supports['stylesheet'] );

		$is = $this->theme_support->is( 'stylesheet' );
		$this->assertTrue( $is );
	}

	/**
	 * Test the Theme_Support HTML 'button_mode' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_buttons_mode_html() {

		add_theme_support($this->theme_support->get_feature_name(), array(
			'button_mode' => 'html',
		));

		$supports = $this->theme_support->theme_support();

		$this->assertArrayHasKey( 'button_mode', $supports );
		$this->assertEquals( 'html', $supports['button_mode'] );

		$is = $this->theme_support->is( 'button_mode' );
		$this->assertEquals( 'html', $is );

		/**
		 * Feature name alias: `buttons-mode`.
		 */
		add_theme_support($this->theme_support->get_feature_name(), array(
			'button-mode' => 'html',
		));

		$is = $this->theme_support->is( 'button-mode' );
		$this->assertFalse( $is );
	}

	/**
	 * Test the Theme_Support JSON 'button_mode' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_buttons_mode_json() {

		add_theme_support($this->theme_support->get_feature_name(), array(
			'button_mode' => 'json',
		));

		$supports = $this->theme_support->theme_support();

		$this->assertArrayHasKey( 'button_mode', $supports );
		$this->assertEquals( 'json', $supports['button_mode'] );

		$is = $this->theme_support->is( 'button_mode' );
		$this->assertEquals( 'json', $is );

		/**
		 * Feature name alias: `buttons-mode` is deprecated as of 2.0.0.
		 */
		add_theme_support($this->theme_support->get_feature_name(), array(
			'button-mode' => 'json',
		));

		$is = $this->theme_support->is( 'button-mode' );
		$this->assertFalse( $is );
	}

	/**
	 * Test the TestTheme_Support 'attr_prefix' feature.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_theme_supports_attr_prefix() {

		add_theme_support($this->theme_support->get_feature_name(), array(
			'attr_prefix' => 'social',
		));

		$supports = $this->theme_support->theme_support();

		$this->assertArrayHasKey( 'attr_prefix', $supports );
		$this->assertEquals( 'social', $supports['attr_prefix'] );

		$this->assertEquals( 'social', $this->theme_support->is( 'attr_prefix' ) );

		/**
		 * The `stylesheet` support should turn to true since we are now set a custom
		 * attribute prefix.
		 */
		$this->assertTrue( $this->theme_support->is( 'stylesheet' ) );

		/**
		 * Alias feature name: `attr-prefix` is deprecated as of 2.0.0.
		 */
		add_theme_support($this->theme_support->get_feature_name(), array(
			'attr-prefix' => 'social',
		));

		$supports = $this->theme_support->theme_support();

		$this->assertFalse( $this->theme_support->is( 'attr-prefix' ) );
		$this->assertFalse( $this->theme_support->is( 'stylesheet' ) );

		add_theme_support($this->theme_support->get_feature_name(), array(
			'attr_prefix' => '',
		));
	}
}
