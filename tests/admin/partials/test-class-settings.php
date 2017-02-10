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
	 * @return void
	 */
	public function test_setting_tabs() {

		// Default Tabs.
		$tabs = $this->settings->setting_tabs();

		/**
		 * Test the filter hook to add a new Tabs in the setting page with valid value.
		 *
		 * @since 1.1.3
		 */
		add_filter('ninecodes_social_manager_setting_tabs', function ( $tabs_extra ) {

			// Valid (Good example).
			$tabs_extra = array(
				'id' => 'new_tab',
				'slug' => 'new-tab',
				'title' => 'New Tab',
			);

			return $tabs_extra;
		});

		$this->assertEquals(array_merge($tabs, array(
			array(
				'id' => 'new_tab',
				'slug' => 'new-tab',
				'title' => 'New Tab',
			),
		)), $this->settings->setting_tabs());

		/**
		 * Test the filter hook to add a new Tabs in the setting page with some invalid values.
		 *
		 * NOTE The "id" is missing.
		 *
		 * @since 1.1.3
		 */
		add_filter('ninecodes_social_manager_setting_tabs', function ( $tabs_extra ) {

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
		});

		$this->assertEquals(array_merge($tabs, array(
			array(
				'id' => 'new_tab_2',
				'slug' => 'new-tab-2',
				'title' => 'New Tab 2',
			),
		)), $this->settings->setting_tabs());
	}

	/**
	 * Function to test the Setting Sections.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return void
	 */
	public function test_setting_sections() {

		$tabs     = $this->settings->setting_tabs();
		$sections = $this->settings->setting_sections();

		/**
		 * Test the filter hook to add a new Section in the setting page with valid value.
		 *
		 * @since 1.1.3
		 */
		add_filter('ninecodes_social_manager_setting_sections', function ( $sections_extra, $tab_id ) {

			if ( 'accounts' === $tab_id ) {
				// Valid (Good example).
				$sections_extra = array(
					'id' => 'new_section',
					'title' => 'New Section', // Optional.
				);
			}

			return $sections_extra;
		}, 10, 2);

		// Insert the new array on index 1, since the array key is reset.
		array_splice($sections, 1, 0, array( array(
				'tab' => 'accounts',
				'id' => 'new_section',
				'title' => 'New Section',
				'description' => '',
			),
		));

		$this->assertEquals( $sections, $this->settings->setting_sections() );

		// Introduce a couple of new tabs.
		add_filter( 'ninecodes_social_manager_setting_tabs', function( $tabs ) {

		    $tabs = array(

		        // Valid.
		        array(
		            'id' => 'integration',
		            'slug' => 'integration',
		            'title' => 'Integration',
		        ),
		        array(
		            'id' => 'woo',
		            'slug' => 'woocommerce',
		            'title' => 'WooCommerce',
		        ),
		    );

		    return $tabs;
		}, 20 );

		$this->settings->setting_tabs(); // Reload Tabs.

		/**
		 * Test the filter hook to add a new Section in the setting page with some invalid values.
		 *
		 * Sections ID must be unique.
		 *
		 * @since 1.1.3
		 */
		add_filter('ninecodes_social_manager_setting_sections', function ( $sections, $tab_id ) {

			switch ( $tab_id ) {
		        case 'woo':
		            $sections = array(

		                // With Duplicate IDs.
		                array(
		                    'id' => 'enqueue',
		                    'title' => esc_html__( 'Enqueue', 'ninecodes-social-manager' ),
		                ),
		                array(
		                    'id' => 'modes',
		                    'title' => esc_html__( 'Modes', 'ninecodes-social-manager' ),
		                    'description' => esc_html__( 'Configure the modes that work best for your website.', 'ninecodes-social-manager' ),
		                ),

		                // Valid.
		                array(
		                    'id' => 'woo_meta',
		                    'title' => 'Meta Tags in WooCommerce',
		                    'description' => 'Configure meta tags for your product pages.',
		                ),
		            );
		            break;

		        case 'integration':

		            $sections = array(

		                // With Duplicate IDs.
		                array(
		                    'id' => 'metas_site',
		                    'title' => 'Metas Site',
		                ),

		                // Valid.
		                array(
		                    'id' => 'social_media_intergration',
		                    'title' => 'Social Media',
		                    'description' => 'Input the App ID to integrate with Social Media.',
		                ),
		            );
		            break;
		    }

			return $sections;
		}, 20, 2);

		$this->assertEquals( array_merge( $sections, array(
			array(
				'tab' => 'integration',
				'id' => 'social_media_intergration',
				'title' => 'Social Media',
				'description' => 'Input the App ID to integrate with Social Media.',
			),
			array(
				'tab' => 'woo',
				'id' => 'woo_meta',
				'title' => 'Meta Tags in WooCommerce',
				'description' => 'Configure meta tags for your product pages.',
			),
		) ), $this->settings->setting_sections() );
	}
}
