<?php
/**
 * Class Test_User
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
 * The class to test the "User" class.
 *
 * @since 1.0.2
 */
class Test_User extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();

		$this->user = new User( $this->plugin );
	}

	/**
	 * Ensure all hooks are present
	 *
	 * @since 1.0.2
	 * @access public
	 *
	 * @return void
	 */
	public function test_users_hooks() {

		$this->assertEquals( -30, has_action( 'load-user-edit.php', array( $this->user, 'load_page' ) ) );
		$this->assertEquals( -30, has_action( 'load-profile.php', array( $this->user, 'load_page' ) ) );

		$this->assertEquals( -30, has_action( 'show_user_profile', array( $this->user, 'add_social_profiles' ) ) );
		$this->assertEquals( -30, has_action( 'edit_user_profile', array( $this->user, 'add_social_profiles' ) ) );
		$this->assertEquals( -30, has_action( 'personal_options_update', array( $this->user, 'save_social_profiles' ) ) );
		$this->assertEquals( -30, has_action( 'edit_user_profile_update', array( $this->user, 'save_social_profiles' ) ) );
	}

	/**
	 * Test `load_page()` method.
	 *
	 * @since 1.0.2
	 * @access public
	 *
	 * @return void
	 */
	public function test_load_page() {

		do_action( 'load-profile.php' );

		$this->assertEquals( -30, has_action( 'admin_enqueue_scripts', array( $this->user, 'enqueue_scripts' ) ) );
	}

	/**
	 * Test `enqueue_scripts()` method.
	 *
	 * @since 1.0.2
	 * @access public
	 *
	 * @return void
	 */
	public function test_enqueue_scripts() {

		$this->user->enqueue_scripts();

		$this->assertTrue( wp_script_is( $this->plugin->slug() . '-preview-profile' ) );
	}
}
