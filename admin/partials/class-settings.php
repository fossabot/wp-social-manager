<?php
/**
 * Plugin Settings
 *
 * @package SocialManager
 * @subpackage Admin\Settings
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \NineCodes\WP\Settings as WP;

/**
 * Settings Class
 *
 * The `Settings` class is used to register the option menu, the page,
 * and the control fields to administer the plugin.
 *
 * @since 1.0.0
 */
final class Settings {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The plugin directory path relative to the current file.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * The plugin url path relative to the current file
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_url;

	/**
	 * The admin screen base name
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $screen;

	/**
	 * The setting pages (tabs)
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	public $pages;

	/**
	 * The site title
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $site_title;

	/**
	 * The site tagline
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $site_tagline;

	/**
	 * The document title printed in the <title> tag
	 *
	 * Typically document title consists of the $site_title and $site_tagline
	 * seperated with a notation like dash, mdash, or bullet.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $document_title;

	/**
	 * WP\Settings class instance
	 *
	 * @since 1.0.0
	 * @access public
	 * @var WP\Settings
	 */
	public $settings;

	/**
	 * Validation class instance
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Validation
	 */
	public $validate;

	/**
	 * Fields class instance
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Fields
	 */
	public $fields;

	/**
	 * List of setting fields to register
	 *
	 * @since 1.2.0
	 * @access public
	 * @var array
	 */
	public $setting_fields = array();

	/**
	 * Helps class instance
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Helps
	 */
	public $helps;

	/**
	 * Initialize the class and set its properties
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );
		$this->path_url = plugin_dir_url( dirname( __FILE__ ) );

		$this->requires();
		$this->hooks();
	}

	/**
	 * Load dependencies
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function requires() {
		require_once $this->path_dir . 'partials/class-fields.php';
		require_once $this->path_dir . 'partials/class-helps.php';
	}

	/**
	 * Run Filters and Actions required
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'frontend_setups' ) );
		add_action( 'init', array( $this, 'fields_setups' ) );

		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'setups' ) );
		add_action( 'admin_init', array( $this, 'admin' ) );
	}

	/**
	 * Run the setups for the setting page
	 *
	 * The setups may involve running some Classes, Functions and sometimes WordPress Hooks,
	 * and defining the Class properties value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setups() {

		$this->settings = new WP\Settings( $this->plugin->option->slug() );
		$this->validate = new Validation();

		$fields = new Fields( $this->screen );
		$helps = new Helps( $this->screen );
	}

	/**
	 * Add the Settings menu in the Dashboard
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function menu() {

		$plugin_slug = $this->plugin->slug();

		$menu_title = __( 'Social Media', 'ninecodes-social-manager' );
		$page_title = __( 'Social Media Settings', 'ninecodes-social-manager' );

		$this->screen = add_options_page( $page_title, $menu_title, 'manage_options', $plugin_slug, function() use ( $plugin_slug ) {
			echo wp_kses( "<div class=\"wrap\" id=\"{$plugin_slug}-settings\">", array(
				'div' => array(
					'class' => array(),
					'id' => array(),
				),
			) );
			$this->settings->render_header( array(
				'title' => true,
			) );
			$this->settings->render_form();
			echo '</div>';
		} );

		add_action( "{$this->screen}_enqueue_scripts", array( $this, 'enqueue_scripts' ), 10 );
		add_action( "{$this->screen}_enqueue_styles", array( $this, 'enqueue_styles' ), 10 );
	}

	/**
	 * Render the admin component (Tabs, Sections, and Fields)
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function admin() {
		$this->tabs();
		$this->sections();
		$this->fields();
		$this->init();
	}

	/**
	 * Register the Setting tabs
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array List of tabs id, slug, and title.
	 */
	public function tabs() {

		$tabs = array(
			'account' => __( 'Accounts', 'ninecodes-social-manager' ),
			'button' => __( 'Buttons', 'ninecodes-social-manager' ),
			'meta' => __( 'Meta', 'ninecodes-social-manager' ),
			'advanced' => __( 'Advanced', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the setting tabs
		 *
		 * This filter allows developer to add new tabs on the setting page.
		 *
		 * @since 1.2.0
		 *
		 * @param array $tabs List of registered Tabs in the Setting page.
		 * @var array
		 */
		$tabs = (array) apply_filters( 'ninecodes_social_manager_setting_tabs', $tabs );

		/**
		 * Eliminate empty title
		 *
		 * @var array
		 */
		$tabs = array_filter( array_unique( $tabs, SORT_REGULAR ), function( $value ) {
			return is_string( $value ) && ! empty( $value );
		} );

		/**
		 * Register new the tabs.
		 *
		 * @var array
		 */
		$this->pages = $this->settings->add_pages( $tabs );

		return $this->pages;
	}

	/**
	 * Register the Settings sections
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function sections() {

		$sections = array();

		foreach ( $this->pages as $tab => $title ) {

			if ( ! $tab ) {
				continue;
			}

			switch ( $tab ) {

				case 'account':

					$sections[ $tab ] = array(
						'profile' => array(
							'title' => __( 'Profile', 'ninecodes-social-manager' ),
							// translators: %s will be replaced with "Widget" and the link to the Widget admin page.
							'description' => sprintf( __( 'Add all social media profiles and pages for this website. You can then show your social profile on a %s', 'ninecodes-social-manager' ), '<a href=' . admin_url( 'widgets.php' ) . ' target="_blank">Widget</a>' ),
							'validate_callback' => array( $this->validate, 'setting_profile' ),
						),
					);
					break;

				case 'button':
					$sections[ $tab ] = array(
						'button_content' => array(
							'title' => __( 'Content', 'ninecodes-social-manager' ),
							'description' => __( 'Configure how social media buttons display on your content pages.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_button_content' ),
						),
						'button_image' => array(
							'title' => __( 'Image', 'ninecodes-social-manager' ),
							'description' => __( 'Options to configure the social media buttons shown on the content images.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_button_image' ),
						),
					);
					break;

				case 'meta':
					$sections[ $tab ] = array(
						'meta_site' => array(
							'validate_callback' => array( $this->validate, 'setting_meta_site' ),
						),
					);
					break;

				case 'advanced':
					$sections[ $tab ] = array(
						'enqueue' => array(
							'validate_callback' => array( $this->validate, 'setting_advanced' ),
						),
						'mode' => array(
							'title' => __( 'Mode', 'ninecodes-social-manager' ),
							'description' => __( 'Configure the modes that work best for your website.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_mode' ),
						),
					);
					break;

				default:
					$sections[ $tab ] = array();
					break;

			} // End switch().

			/**
			 * Filter the setting sections.
			 *
			 * This filter allows developer to add or remove new sections on the registered Tabs.
			 *
			 * @since 1.2.0
			 *
			 * @param string $tab The Tab ID.
			 *
			 * @var array
			 */
			$sections = (array) apply_filters( 'ninecodes_social_manager_setting_sections', $sections, $tab );

		} // End foreach().

		$sections = $this->remove_duplicate_sections( $sections );

		foreach ( $sections as $tab => $section ) {
			$this->pages = $this->settings->add_sections( $tab, $section );
		}

		return $sections;
	}

	/**
	 * Setups fields configuration
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function fields_setups() {

		$this->fields_profile();
		$this->fields_button_content();
		$this->fields_button_image();
		$this->fields_meta_site();
		$this->fields_enqueue();
		$this->fields_mode();
	}

	/**
	 * Fields of Profiles and Pages
	 *
	 * Add the social media profiles and pages related to this website.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array The array of fields added in the Profiles section.
	 */
	public function fields_profile() {

		$setting_fields = array();

		foreach ( $this->plugin->option->get_list( 'social_profiles' ) as $slug => $props ) {

			$props = wp_parse_args( $props, array(
				'label' => '',
				'url' => '',
				'description' => '',
			) );

			if ( empty( $props['label'] ) || empty( $props['url'] ) || empty( $props['description'] ) ) {
				continue;
			}

			// Field / input unique name.
			$name = sanitize_key( $slug );

			$setting_fields[ $name ] = array(
				'type' => 'text_profile',
				'label' => $props['label'],
				'description' => $props['description'],
				'attr' => array(
					'data-url' => $props['url'],
				),
			);
		}

		/**
		 * The Filter hook to allow developer to add new field type in
		 * the Profiles section in Accounts (tab) > Profiles (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'profile', 'account' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'profile' ), $setting_fields );

		/**
		 * Regiter the fields in Accounts > Profiles.
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'account', 'profile', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Buttons Content Fields
	 *
	 * The setting fields to configure the social media buttons that
	 * allows people to share, like, or save content of this site.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array
	 */
	public function fields_button_content() {

		/**
		 * The list of buttons sites registered in the Content.
		 *
		 * @var array
		 */
		$button_sites = $this->plugin->option->get_list( 'button_sites', array( 'content' ) );

		$setting_fields = array(
			'include' => array(
				'label' => __( 'Button to include', 'ninecodes-social-manager' ),
				'type' => 'checkboxtable',
				'options' => array_map( function( $value ) {
					return array(
						'name' => $value['name'],
						'label' => $value['label'],
					);
				}, $button_sites ), // Return the key => label.
				'default' => array_map( function( $value ) {
					return array(
						'enable' => 'on',
						'label' => $value['label'],
					);
				}, $button_sites ),  // Return the key => 'on'.
			),
			'post_type' => array(
				'type' => 'multicheckbox',
				'label' => __( 'Button Visibility', 'ninecodes-social-manager' ),
				// translators: %s will be replaced with "Post Types" pointing to https://codex.wordpress.org/Post_Types.
				'description' => wp_kses( sprintf( __( 'Select the %s that are allowed to show the social media buttons.', 'ninecodes-social-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">' . __( 'Post Types', 'ninecodes-social-manager' ) . '</a>' ), array(
					'a' => array(
						'href' => array(),
						'target' => array(),
					),
				) ),
				'options' => $this->plugin->option->get_list( 'post_types' ),
				'default' => array(
					'post' => 'on',
				),
			),
			'view' => array(
				'label' => __( 'Button View', 'ninecodes-social-manager' ),
				'description' => __( 'Select the social media buttons appearance shown in the content.', 'ninecodes-social-manager' ),
				'type' => 'select',
				'options' => $this->plugin->option->get_list( 'button_views' ),
				'default' => 'icon',
			),
			'placement' => array(
				'type' => 'select',
				'label' => __( 'Button Placement', 'ninecodes-social-manager' ),
				'description' => __( 'Select the location to show the social media buttons in the content.', 'ninecodes-social-manager' ),
				'options' => $this->plugin->option->get_list( 'button_placements' ),
				'default' => 'after',
			),
			'style' => array(
				'type' => 'select',
				'label' => __( 'Button Style', 'ninecodes-social-manager' ),
				// translators: %s will be replaced with "<code>Share on:</code>".
				'description' => sprintf( __( 'Change the style of the social media button of the content.', 'ninecodes-social-manager' ), '<code>Share on:</code>' ),
				'options' => $this->plugin->option->get_list( 'button_styles', array( 'content' ) ),
				'default' => 'rounded',
			),
			'heading' => array(
				'type' => 'text',
				'label' => __( 'Button Header', 'ninecodes-social-manager' ),
				// translators: %s will be replaced with "<code>Share on:</code>".
				'description' => sprintf( __( 'Set the heading shown before the buttons (e.g. %s).', 'ninecodes-social-manager' ), '<code>Share on:</code>' ),
				'default' => __( 'Share on:', 'ninecodes-social-manager' ),
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in Buttons (tab) > Buttons Content (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'button_content', 'button' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'button_content' ), $setting_fields );

		/**
		 * Register the fields in Buttons > Buttons Content.
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'button', 'button_content', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Buttons Image Fields
	 *
	 * The setting fields to configure the social media buttons shown
	 * on the content images.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array
	 */
	public function fields_button_image() {

		/**
		 * The list of buttons sites registered in the Image.
		 *
		 * @var array
		 */
		$button_sites = $this->plugin->option->get_list( 'button_sites', array( 'image' ) );

		$setting_fields = array(
			'enable' => array(
				'label' => __( 'Enable Button Image', 'ninecodes-social-manager' ),
				'description' => __( 'Show the social media buttons on images in the content', 'ninecodes-social-manager' ),
				'type' => 'checkbox',
				'attr' => array(
					'data-selector-toggle' => '.sharing-image-setting',
				),
			),
			'include' => array(
				'label' => __( 'Button to include', 'ninecodes-social-manager' ),
				'type' => 'checkboxtable',
				'options' => array_map( function( $value ) {
					return array(
						'name' => $value['name'],
						'label' => $value['label'],
					);
				}, $button_sites ),
				'default' => array_map( function( $value ) {
					return array(
						'enable' => 'on', // Default selected.
						'label' => $value['label'],
					);
				}, $button_sites ),
				'class' => 'sharing-image-setting hide-if-js',
			),
			'post_type' => array(
				'label' => __( 'Button Visibility', 'ninecodes-social-manager' ),

				// translators: %s will be replaced with a link pointing to https://codex.wordpress.org/Post_Types.
				'description' => sprintf( __( 'List of %s that are allowed to show the social media buttons on the images of the content.', 'ninecodes-social-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">' . __( 'Post Types', 'ninecodes-social-manager' ) . '</a>' ),
				'type' => 'multicheckbox',
				'options' => $this->plugin->option->get_list( 'post_types' ),
				'default' => array(
					'post' => 'on',
				),
				'class' => 'sharing-image-setting hide-if-js',
			),
			'view' => array(
				'label' => __( 'Button View', 'ninecodes-social-manager' ),
				'description' => __( 'Select the social media buttons appearance shown on the images of the content.', 'ninecodes-social-manager' ),
				'type' => 'select',
				'options' => $this->plugin->option->get_list( 'button_views' ),
				'default' => 'icon',
				'class' => 'sharing-image-setting hide-if-js',
			),
			'style' => array(
				'type' => 'select',
				'label' => __( 'Button Style', 'ninecodes-social-manager' ),
				// translators: %s will be replaced with "<code>Share on:</code>".
				'description' => sprintf( __( 'Change the style of the social media button shown on the image.', 'ninecodes-social-manager' ), '<code>Share on:</code>' ),
				'options' => $this->plugin->option->get_list( 'button_styles', array( 'image' ) ),
				'default' => 'rounded',
				'class' => 'sharing-image-setting hide-if-js',
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in Buttons (tab) > Buttons Image (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'button_image', 'button' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'button_image' ), $setting_fields );

		/**
		 * Register the fields in "Buttons" > "Buttons Image".
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'button', 'button_image', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Meta Site Fields
	 *
	 * The setting fields to configure the meta data and the meta tags.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array
	 */
	public function fields_meta_site() {

		$setting_fields = array(
			'enable' => array(
				'type' => 'checkbox',
				'label' => __( 'Generate Meta Tags', 'ninecodes-social-manager' ),
				'description' => __( 'Generate social media meta tags on this website', 'ninecodes-social-manager' ),
				'default' => 'on',
				'attr' => array(
					'data-selector-toggle' => '.meta-site-setting',
				),
			),
			'name' => array(
				'type' => 'text',
				'label' => __( 'Site Name', 'ninecodes-social-manager' ),
				'legend' => __( 'Site Name', 'ninecodes-social-manager' ),
				// translators: the %s will be replaced with list of brand / site name examples.
				'description' => sprintf( __( 'The website name or brand as it should appear within the social media meta tags (e.g. %s)', 'ninecodes-social-manager' ), '<code>iMDB</code>, <code>TNW</code>, <code>HKDC</code>' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->site_title,
				),
			),
			'title' => array(
				'type' => 'text',
				'label' => __( 'Site Title', 'ninecodes-social-manager' ),
				'legend' => __( 'Site Title', 'ninecodes-social-manager' ),
				'description' => __( 'The title of this website as it should appear within the social media meta tags.', 'ninecodes-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->document_title,
				),
			),
			'description' => array(
				'type' => 'textarea',
				'label' => __( 'Site Description', 'ninecodes-social-manager' ),
				'description' => __( 'A one to two sentence describing this website that should appear within the social media meta tags.', 'ninecodes-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'rows' => '4',
					'cols' => '80',
					'placeholder' => $this->site_tagline,
				),
			),
			'image' => array(
				'type' => 'image',
				'class' => 'meta-site-setting',
				'label' => __( 'Site Image', 'ninecodes-social-manager' ),
				'description' => __( 'An image URL which should represent this website within the social media meta tags (e.g. Open Graph, Twitter Cards, etc.)', 'ninecodes-social-manager' ),
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in Meta (tab) > Meta Site (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'meta_site', 'meta' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'meta_site' ), $setting_fields );

		/**
		 * Register the fields in "Meta" > "Meta Site".
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'meta', 'meta_site', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Enqueue Fields
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array
	 */
	public function fields_enqueue() {

		$setting_fields = array();

		if ( $this->plugin->helper->is_theme_support( 'stylesheet' ) ) :

			$setting_fields['stylesheet'] = array(
				'label' => __( 'Load Stylesheet', 'ninecodes-social-manager' ),
				'type' => 'content',
				'content' => __( 'The Theme being used in this website has included the styles in its own stylesheet.', 'ninecodes-social-manager' ),
			);
		else :

			$setting_fields['stylesheet'] = array(
				'label' => __( 'Load Stylesheet', 'ninecodes-social-manager' ),
				'description' => __( 'Load the plugin stylesheet to apply essential styles.', 'ninecodes-social-manager' ),
				'default' => 'on',
				'type' => 'checkbox',
			);
		endif;

		/**
		 * The Filter hook to allow developer adding new field type
		 * in Meta (tab) > Meta Site (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'enqueue', 'advanced' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'enqueue' ), $setting_fields );

		/**
		 * Register the fields in Advanced > Enqueue.
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'advanced', 'enqueue', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Button Mode Fields
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @see Options
	 *
	 * @return array
	 */
	public function fields_mode() {

		$setting_fields = array();

		if ( ! (bool) $this->plugin->helper->is_theme_support( 'button_mode' ) ) :

			$setting_fields['button_mode'] = array(
				'label' => __( 'Button Mode', 'ninecodes-social-manager' ),
				'description' => __( 'Select the mode to render the social media buttons.', 'ninecodes-social-manager' ),
				'type' => 'radio',
				'options' => $this->plugin->option->get_list( 'button_modes' ),
				'default' => 'html',
			);

		endif;

		$setting_fields['link_mode'] = array(
			'label' => __( 'Link Mode', 'ninecodes-social-manager' ),
			'description' => __( 'Select the link mode to append when the content or the image is shared.', 'ninecodes-social-manager' ),
			'type' => 'radio',
			'options' => $this->plugin->option->get_list( 'link_modes' ),
			'default' => 'permalink',
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in Advanced (tab) > Modes (section).
		 *
		 * @since 1.2.0
		 *
		 * @param string $tab_id The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'mode', 'advanced' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to get_option()
		 *
		 * @since 1.2.0
		 */
		$this->option_default( $this->plugin->option->name( 'mode' ), $setting_fields );

		/**
		 * Register the fields in Advanced > Enqueue.
		 *
		 * @var array {
		 *  @type string $tab The tab ID.
		 *  @type string $section The section ID.
		 *  @type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'advanced', 'mode', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Register Fields
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array
	 */
	public function fields() {

		$setting_fields = array();

		foreach ( $this->setting_fields as $key => $value ) {

			list( $tab, $section, $fields ) = $value;
			$setting_fields = $this->settings->add_fields( $tab, $section, $fields );
		}

		$this->pages = $setting_fields;

		return $setting_fields;
	}

	/**
	 * Initialize and render the setting screen with the registered tabs, sections, and fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() {

		$this->settings->init( $this->screen, $this->pages );
	}

	/**
	 * Function to enqueue JavaScripts in the setting page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args An array of the JavaScripts file name.
	 * @return void
	 */
	public function enqueue_scripts( array $args ) {

		$plugin_slug = $this->plugin->slug();
		$plugin_version = $this->plugin->version;

		wp_enqueue_script( "{$plugin_slug}-settings-scripts", $this->path_url . 'assets/js/scripts.min.js', array(
			'jquery',
			'underscore',
			'backbone',
			'wp-util',
		), $plugin_version, true );

		foreach ( $args as $key => $file ) {

			$file = is_string( $file ) && ! empty( $file ) ? "{$file}" : '';

			if ( empty( $file ) ) {
				continue;
			}

			wp_enqueue_script( "{$plugin_slug}-settings-{$file}", $this->path_url . 'assets/js/{$file}.min.js', array( "{$plugin_slug}-scripts" ), $plugin_version, true );
		}
	}

	/**
	 * Function to enqueue stylesheets in the setting page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args An array of the stylesheets file name.
	 * @return void
	 */
	public function enqueue_styles( array $args ) {

		$plugin_slug = $this->plugin->slug();
		$plugin_version = $this->plugin->version;

		wp_enqueue_style( "{$plugin_slug}-settings", $this->path_url . 'assets/css/style.css', array(), $plugin_version );

		foreach ( $args as $name => $file ) {

			$file = is_string( $file ) && ! empty( $file ) ? $file : '';

			if ( empty( $file ) ) {
				continue;
			}

			wp_style_add_data( "{$plugin_slug}-settings-{$file}", 'rtl', 'replace' );
		}
	}

	/**
	 * Setups the front ends
	 *
	 * This function method run functions that will otherwise won't be
	 * accessible if they are run via the admin_init Action Hook.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function frontend_setups() {
		$this->wp_get_document_title();
	}

	/**
	 * The function method to construct the document title
	 *
	 * The wp_get_document_title function does not return a proper value
	 * when run inside the setting pages hence this function.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function wp_get_document_title() {

		$title['site'] = get_bloginfo( 'name', 'display' );
		$title['tagline'] = get_bloginfo( 'description', 'display' );

		$this->site_title = $title['site'];
		$this->site_tagline = $title['tagline'];

		$sep   = apply_filters( 'document_title_separator', '-' );
		$title = apply_filters( 'document_title_parts', $title );

		$title = implode( " $sep ", array_filter( $title ) );
		$title = wptexturize( $title );
		$title = convert_chars( $title );
		$title = esc_html( $title );
		$title = capital_P_dangit( $title );

		$this->document_title = $title;
	}

	/**
	 * The utility function to remove duplicate keys in the Tabs and Sections
	 *
	 * TODO: Move this function to the WP-Settings-API
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $sections The array.
	 * @return array
	 */
	public function remove_duplicate_sections( array $sections ) {

		$arr = array();
		foreach ( $sections as $tab => $section ) {

			if ( ! is_array( $section ) || empty( $section ) ) {
				continue;
			}

			foreach ( $section as $id => $s ) { // $id: Section ID.

				if ( ! is_array( $s ) || empty( $s ) ) {
					continue;
				}

				$arr[] = array_merge( array(
					'tab' => $tab,
					'id' => $id,
				), $s );
			}
		}

		/**
		 * Section with the same id added later must be removed.
		 *
		 * @var array
		 */
		$arr = $this->remove_duplicate_values( 'id', $arr );

		$sections = array();
		foreach ( $arr as $val ) {

			$tab = $val['tab'];
			$id  = $val['id'];

			unset( $val['tab'] );
			unset( $val['id'] );

			$sections[ $tab ][ $id ] = $val;
		}

		return $sections;
	}

	/**
	 * Sort out the tabs for possible duplicate values in the Tabs and Sections
	 *
	 * TODO: Move this function to the WP-Settings-API
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @param string $key The key in the array to search.
	 * @param string $value The value in the array to compare.
	 * @param array  $arr The array.
	 * @return array
	 */
	protected function search_duplicate_values( $key = '', $value = '', array $arr ) {

		$keys = array();

		foreach ( $arr as $i => $v ) {
			if ( $v[ $key ] === $value ) {
				$keys[ $i ] = $value;
			}
		}

		if ( 2 <= count( $keys ) ) { // There should at least be two elements in the array.
			return $keys;
		}

		return $keys;
	}

	/**
	 * The utility function to remove duplicate keys in the Tabs and Sections
	 *
	 * TODO: Move this function to the WP-Settings-API
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @param string $key The key in the array to search.
	 * @param array  $arr The array.
	 * @return array
	 */
	protected function remove_duplicate_values( $key = '', array $arr ) {

		$remove_keys = array();

		foreach ( $arr as $i => $v ) {

			$duplicate_keys = $this->search_duplicate_values( $key, $v[ $key ], $arr ); // Find possible duplicates.

			if ( is_array( $duplicate_keys ) && $duplicate_keys ) {
				$remove_keys = array_slice( $duplicate_keys, 1, null, true ); // Exclude the first array.
			}

			if ( isset( $remove_keys[ $i ] ) && $remove_keys[ $i ] === $arr[ $i ][ $key ] ) {
				unset( $arr[ $i ] );
			}
		}

		return array_values( $arr );
	}

	/**
	 * Enable the get_option() to return default value along with the saved value in the database
	 *
	 * TODO: Move this function to the WP-Settings-API
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @param string $option_name The option name.
	 * @param array  $fields The fields data.
	 * @return void
	 */
	protected function option_default( $option_name = '', array $fields ) {

		$default = array();
		foreach ( $fields as $key => $value ) {
			$default[ $key ] = isset( $value['default'] ) ? $value['default'] : '';
		}

		/**
		 * Create the option if it is not there yet.
		 *
		 * @var mixed
		 */
		$option = get_option( $option_name );
		if ( false === $option ) {
			add_option( $option_name, $default );
		}

		add_filter( "option_{$option_name}", function( $option ) use ( $default ) {
			$value = $option ? wp_parse_args_recursive( $option, $default ) : $default;
			return $value;
		}, 10 );
	}
}
