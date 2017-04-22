<?php
/**
 * Class TestPublic
 *
 * TODO: Add tests for the empty functions in this file.
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
 * The class to test the "TestPublic" class instance.
 *
 * @since 1.0.4
 */
class Test_Public_View extends WP_UnitTestCase {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The Public
	 *
	 * @since 1.0.4
	 * @access protected
	 *
	 * @var Public
	 */
	protected $public;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		// Setup the plugin.
		$this->plugin = new Plugin();
		$this->plugin->init();

		$this->public = new Public_View( $this->plugin );
	}

	/**
	 * Function to test Class methods availability.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( $this->public, 'requires' ),  'Class does not have method \'requires\'' );
		$this->assertTrue( method_exists( $this->public, 'hooks' ),  'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->public, 'setups' ),  'Class does not have method \'setups\'' );
		$this->assertTrue( method_exists( $this->public, 'register_styles' ),  'Class does not have method \'register_styles\'' );
		$this->assertTrue( method_exists( $this->public, 'register_scripts' ),  'Class does not have method \'register_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'enqueue_styles' ),  'Class does not have method \'enqueue_styles\'' );
		$this->assertTrue( method_exists( $this->public, 'enqueue_scripts' ),  'Class does not have method \'enqueue_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'is_load_stylesheet' ),  'Class does not have method \'is_load_stylesheet\'' );
		$this->assertTrue( method_exists( $this->public, 'is_load_scripts' ),  'Class does not have method \'is_load_scripts\'' );
		$this->assertTrue( method_exists( $this->public, 'is_json_mode' ),  'Class does not have method \'is_json_mode\'' );
		$this->assertTrue( method_exists( $this->public, 'is_buttons_active' ),  'Class does not have method \'is_buttons_active\'' );
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

		$this->assertEquals( 10, has_action( 'init', array( $this->public, 'setups' ) ) );
		$this->assertEquals( -10, has_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_styles' ) ) );
		$this->assertEquals( -10, has_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) ) );
	}

	/**
	 * Function to test 'register_styles' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_register_styles() {

		$this->public->register_styles();
		$this->assertTrue( wp_style_is( $this->plugin->plugin_slug, 'registered' ) );
	}

	/**
	 * Function to test 'register_scripts' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_register_scripts() {

		$this->public->register_scripts();

		$this->assertTrue( wp_script_is( $this->plugin->plugin_slug . '-app', 'registered' ) );
		$this->assertTrue( wp_script_is( $this->plugin->plugin_slug, 'registered' ) );
	}

	/**
	 * Function to test 'enqueue_styles' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_enqueue_styles() {

		$this->assertFalse( wp_style_is( $this->plugin->plugin_slug, 'enqueued' ) );
	}

	/**
	 * Function to test 'is_load_stylesheet' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_load_stylesheet() {

		// Create a single post.
		$post_id = $this->factory()->post->create();

		// Set the default value.
		update_option( $this->plugin->options['enqueue'], array(
			'enable_stylesheet' => 'on',
		) );

		/**
		 * ============================================================
		 * Stylesheet is disabled through the add_theme_support.
		 * ============================================================
		 */
		add_theme_support( 'ninecodes-social-manager', array(
			'stylesheet' => true,
		) );
		do_action( 'init' ); // The `theme_supports` method is run through the 'init' Action.

		$this->assertFalse( $this->public->is_load_stylesheet() );

		add_theme_support( 'ninecodes-social-manager', array(
			'stylesheet' => false,
		) );
		do_action( 'init' ); // The `theme_supports` method is run through the 'init' Action.

		$this->assertTrue( $this->public->is_load_stylesheet() );

		/**
		 * ============================================================
		 * The Social Buttons is not active.
		 * - Social Buttons Content post type is not selected.
		 * - Social Buttons Image is disabled.
		 * ============================================================
		 */
		update_option( $this->plugin->options['button_image'], array(
			'enable' => '',
			'post_type' => array(
				'post' => false,
				'page' => false,
			),
		) );
		update_option( $this->plugin->options['button_content'], array(
			'post_type' => array(
				'post' => false,
				'page' => false,
			),
		) );

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertFalse( $this->public->is_load_stylesheet() );

		/**
		 * ============================================================
		 * The Social Buttons Content is not active,
		 * But, the Social Buttons Image is set in enabled and
		 * should be shown in 'post'.
		 * ============================================================
		 */
		update_option( $this->plugin->options['button_image'], array(
			'enable' => 'on',
			'post_type' => array(
				'post' => 'on',
			),
		) );

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( $this->public->is_load_stylesheet() );

		/**
		 * ============================================================
		 * Stylesheet is enabled in the 'Settings'.
		 * ============================================================
		 */
		// Stylesheet is enabled.
		$this->assertTrue( $this->public->is_load_stylesheet() );

		// Enable Stylesheet.
		update_option( $this->plugin->options['enqueue'], array(
			'enable_stylesheet' => '',
		) );
		$this->assertFalse( $this->public->is_load_stylesheet() );
	}

	/**
	 * Function to test 'is_load_scripts' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_load_scripts() {

		// The Home / Archive Page should not load the sripts (at least, for the time being).
		$this->assertFalse( $this->public->is_load_scripts() );

		/**
		 * Test if the function is loaded in a single post.
		 *
		 * Function should return 'false' when loaded in a single
		 * post.
		 */
		$post_id = $this->factory()->post->create();
		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( $this->public->is_load_scripts() );

		/**
		 * Test if the function is loaded in a single post,
		 * but both button content and button image are disable.
		 *
		 * Function should return 'false' when loaded in a single
		 * post.
		 */
		$post_id = $this->factory()->post->create();
		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		update_option( $this->plugin->options['button_content'], array(
			'post_type' => array(
				'post' => '',
			),
		) );
		update_option( $this->plugin->options['button_image'], array(
			'enable' => '',
			'post_type' => array(
				'post' => 'on',
			),
		) );

		$this->assertFalse( $this->public->is_load_scripts() );

		/**
		 * Test if the function is loaded in a single post,
		 * but both button content and button image are disable.
		 *
		 * !Button image is disabled by not selecting any post_type.
		 *
		 * Function should return 'false' when loaded in a single
		 * post.
		 */
		$post_id = $this->factory()->post->create();
		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		update_option( $this->plugin->options['button_content'], array(
			'post_type' => array(
				'post' => '',
			),
		) );
		update_option( $this->plugin->options['button_image'], array(
			'enable' => 'on',
			'post_type' => array(
				'post' => '',
			),
		) );

		$this->assertFalse( $this->public->is_load_scripts() );
	}

	/**
	 * Function to test 'is_json_mode' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_json_mode() {

		// Default mode is HTML.
		$this->assertFalse( $this->public->is_json_mode() );

		/**
		 * Test if the button_mode in theme_support is set to `json`
		 * Function should return 'true' for the mode has been switched to 'json'.
		 */
		add_theme_support( $this->plugin->theme_support()->get_feature_name(), array(
			'button_mode' => 'json',
		));
		$this->plugin->theme_support()->theme_support(); // Init Theme_Support.
		$this->assertTrue( $this->public->is_json_mode() );

		// Reset `button_mode` to `html`.
		add_theme_support( $this->plugin->theme_support()->get_feature_name(), array(
			'button_mode' => 'html',
		));
		$this->plugin->theme_support()->theme_support(); // Init Theme_Support.
		$this->assertFalse( $this->public->is_json_mode() );

		// Test if the Mode setting is set to `json`.
		update_option( $this->plugin->options['mode'], array(
			'button_mode' => 'json',
		) );
		$this->assertTrue( $this->public->is_json_mode() );
	}

	/**
	 * Function to test 'is_json_mode' method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_is_buttons_active() {}
}
