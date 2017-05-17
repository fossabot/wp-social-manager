<?php
/**
 * Class Test_Settings
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
class Test_Settings extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		$this->plugin = new Plugin();
		$this->plugin->init();

		$this->settings = new Settings( $this->plugin );
		$this->settings->setups();

		parent::setUp();
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
		$this->assertEquals( 10, has_action( 'init', array( $this->settings, 'fields_setups' ) ) );

		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->settings, 'menu' ) ) );
		$this->assertEquals( 10, has_action( 'admin_init', array( $this->settings, 'setups' ) ) );
		$this->assertEquals( 10, has_action( 'admin_init', array( $this->settings, 'admin' ) ) );
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

		$this->assertInstanceOf( '\NineCodes\WP\Settings\Settings', $this->settings->settings );
		$this->assertInstanceOf( '\NineCodes\SocialManager\Validation', $this->settings->validate );
	}

	/**
	 * Function to test the Tabs.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Add test for filter hook 'ninecodes_social_manager_setting_tabs'.
	 * @access public
	 *
	 * @return void
	 */
	public function test_tabs() {

		// Default Tabs.
		$tabs = $this->settings->tabs();

		/**
		 * Test the filter hook to add a new Tabs in the setting page with valid value.
		 *
		 * @since 1.2.0
		 */
		add_filter('ninecodes_social_manager_setting_tabs', function ( $tabs ) {

			// Valid (Good example).
			$tabs['new_tab'] = 'New Tab';

			// Invalid (Bad examples).
			$tabs['new_tab2'] = '';
			$tabs['new_tab3'] = null;
			$tabs['new_tab4'] = false;
			$tabs['new_tab5'] = -1;
			$tabs['new_tab6'] = 10;
			$tabs['new_tab7'] = array( 1,2,3,4,5 );
			$tabs['new_tab8'] = 'New Tab'; // Duplicate.
			$tabs['new tab9'] = 'New Tab 9'; // Bad Key.
			$tabs['New Tab10'] = 'New Tab 10'; // Bad Key.
			$tabs['new-tab11'] = 'New Tab 11'; // With dash, must be be an underscore.

			return $tabs;
		});

		$this->assertArrayHasKey( 'new_tab', $this->settings->tabs() );
		$this->assertEquals( array_merge( $tabs, array(
			'new_tab' => 'New Tab',
			'newtab9' => 'New Tab 9',
			'newtab10' => 'New Tab 10',
			'new_tab11' => 'New Tab 11',
		) ), $this->settings->tabs() );
	}

	/**
	 * Test the function that removes duplicate sections
	 *
	 * @return void
	 */
	function test_remove_duplicate_sections() {

		$sections = $this->settings->remove_duplicate_sections( array(
			'tab1' => array(
				'section1' => array(
					'title' => 'Section 1 Title',
					'description' => 'Section 1 Desc.',
				),
				'section2' => array(
					'title' => 'Section 2 Title',
					'description' => 'Section 2 Desc.',
				),
			),
			'tab2' => array(
				'section2' => array(
					'title' => 'Yet Another Section 2 Title',
					'description' => 'Yet Another Section 2 Desc.',
				), // Duplicate.
				'section3' => array(
					'title' => 'Section 3 Title',
					'description' => 'Section 3 Desc.',
				),
				'section4' => array(), // Bad.
				'section5' => null,    // Bad.
				'section6' => false,   // Bad.
			),
			'tab3' => array(), // Bad.
			'tab4' => null,    // Bad.
			'tab5' => false,   // Bad.
		) );

		$this->assertEquals( array(
			'tab1' => array(
				'section1' => array(
					'title' => 'Section 1 Title',
					'description' => 'Section 1 Desc.',
				),
				'section2' => array(
					'title' => 'Section 2 Title',
					'description' => 'Section 2 Desc.',
				),
			),
			'tab2' => array(
				'section3' => array(
					'title' => 'Section 3 Title',
					'description' => 'Section 3 Desc.',
				),
			),
		), $sections );
	}
}
