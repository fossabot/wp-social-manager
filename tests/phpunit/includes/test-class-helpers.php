<?php
/**
 * Class TestHelpers
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
 * The class to test the "Helpers" class.
 *
 * @since 1.0.0
 */
class TestHelpers extends WP_UnitTestCase {


	/**
	 * Plugin instance.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * The ThemeSupports instance.
	 *
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
	 * Test the Helper that retrieves element attribute prefix.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Update the default attr prefix.
	 * @access public
	 * @return void
	 */
	public function test_get_attr_prefix() {

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );
	}

	/**
	 * Test "Helpers::get_attr_prefix()" method for which the "attr-prefix"
	 * is passed with a string correctly.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_get_attr_prefix_theme_support() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr_prefix' => 'social',
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social', $prefix );

		// Test feature name alias: 'attr-prefix'.
		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr-prefix' => 'social',
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social', $prefix );
	}

	/**
	 * Test "Helpers::get_attr_prefix()" method for which the "attr-prefix"
	 * is passed with an empty string.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Update the default attr prefix.
	 * @access public
	 * @return void
	 */
	public function test_get_attr_prefix_theme_support_empty() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr_prefix' => '',
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );

		// Test feature name alias: 'attr-prefix'.
		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr-prefix' => '',
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );
	}

	/**
	 * Test "Helpers::get_attr_prefix()" method for which the "attr-prefix"
	 * is passed with a falsy value.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Update the default attr prefix.
	 * @access public
	 * @return void
	 */
	public function test_get_attr_prefix_theme_support_false() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr_prefix' => false,
		));
		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );

		// Test feature name alias: 'attr-prefix'.
		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr-prefix' => false,
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );
	}

	/**
	 * Test "Helpers::get_attr_prefix()" method for which the "attr-prefix" is passed with
	 * an integer.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Update the default attr prefix.
	 * @access public
	 * @return void
	 */
	public function test_get_attr_prefix_theme_support_integer() {

		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr_prefix' => 1,
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );

		// Test feature name alias: 'attr-prefix'.
		add_theme_support($this->theme_supports->get_feature_name(), array(
			'attr-prefix' => 1,
		));

		$prefix = Helpers::get_attr_prefix();
		$this->assertEquals( 'social-manager', $prefix );
	}
}
