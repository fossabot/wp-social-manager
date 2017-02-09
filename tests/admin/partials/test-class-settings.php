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
		$this->settings->setting_setups();
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
		$this->assertEquals( 15, has_action( 'admin_init', array( $this->settings, 'setting_tabs' ) ) );
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

		$this->assertInstanceOf( '\NineCodes\WPSettings\Settings', $this->settings->settings );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Validation', $this->settings->validate );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Fields', $this->settings->fields );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Helps', $this->settings->helps );
	}

	/**
	 * Function to test the Tabs.
	 *
	 * @since 1.0.0
	 * @since 1.1.3 Add test for filter hook 'ninecodes_social_manager_setting_tabs'.
	 * @access public
	 *
	 * @return [type] [description]
	 */
	public function test_setting_tabs() {

		// Default Tabs.
		$tabs = $this->settings->setting_tabs();

		/**
		 * Test the filter hook to add a new Tabs in the setting page with valid value.
		 *
		 * @since 1.1.3
		 */
		add_filter( 'ninecodes_social_manager_setting_tabs', function( $tabs_extra ) {

			// Valid (Good example).
			$tabs_extra = array(
				'id' => 'new_tab',
				'slug' => 'new-tab',
				'title' => 'New Tab',
			);

			return $tabs_extra;
		});

		$this->assertEquals( array_merge( $tabs, array(
			array(
				'id' => 'new_tab',
				'slug' => 'new-tab',
				'title' => 'New Tab',
			),
		) ), $this->settings->setting_tabs() );

		/**
		 * Test the filter hook to add a new Tabs in the setting page with some invalid values.
		 *
		 * NOTE The "id" is missing.
		 *
		 * @since 1.1.3
		 */
		add_filter( 'ninecodes_social_manager_setting_tabs', function( $tabs_extra ) {

			// Valid (Good example).
			return array(

				// With duplicates.
				array(
					'id' => 'accounts', // Duplicate ID.
					'slug' => 'accounts-2',
					'title' => 'Accounts 2',
				),
				array(
					'id' => 'buttons-2',
					'slug' => 'buttons', // Duplicate slug.
					'title' => 'Buttons 2',
				),
				array(
					'id' => 'metas', // Duplicate ID.
					'slug' => 'metas', // Duplicate slug.
					'title' => 'Metas 2',
				),
				array(
					'id' => 'advanced-2',
					'slug' => 'advanced-2',
					'title' => 'Advanced', // Duplicate title.
				),
				array(
					'id' => 'advanced-2',  // Duplicate ID.
					'slug' => 'advanced-3',
					'title' => 'Advanced', // Duplicate title.
				),

				// Bad arrays.
				array(
					'id' => 'new_tab_3',
					'slug' => 'new-tab-3',
				),
				array(
					'id' => 'new_tab_4',
					'title' => 'New Tab 4',
				),
				array(
					'slug' => 'new-tab-5',
					'title' => 'New Tab 5',
				),

				// Valid.
				array(
					'id' => 'new_tab_2',
					'slug' => 'new-tab-2',
					'title' => 'New Tab 2',
				),
			);
		} );

		$this->assertEquals( array_merge( $tabs, array(
			array(
				'id' => 'new_tab_2',
				'slug' => 'new-tab-2',
				'title' => 'New Tab 2',
			),
		) ), $this->settings->setting_tabs() );
	}
}
