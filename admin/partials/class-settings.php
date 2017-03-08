<?php
/**
 * Admin: Settings class
 *
 * @package SocialManager
 * @subpackage Admin\Settings
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \NineCodes\WPSettings;

/**
 * The Settings class is used to register the option menu, the option page,
 * and the input fields that will allow users to configure the plugin.
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
	 * The plugin slug (unique identifier).
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The plugin option name or meta key prefix.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $option_slug;

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $version;

	/**
	 * The ThemeSupports class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var ThemeSupports
	 */
	protected $theme_supports;

	/**
	 * The plugin directory path relative to the current file.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_dir;

	/**
	 * The plugin url path relative to the current file.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_url;

	/**
	 * The admin screen base name.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $screen;

	/**
	 * The setting pages (tabs).
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	public $pages;

	/**
	 * The site title.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $site_title;

	/**
	 * The site tagline.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $site_tagline;

	/**
	 * The document title printed in the <title> tag.
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
	 * WPSettings\Settings class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var WPSettings\Settings
	 */
	public $settings;

	/**
	 * Validation class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Validation
	 */
	public $validate;

	/**
	 * Fields class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Fields
	 */
	public $fields;

	/**
	 * List of setting fields to register.
	 *
	 * @since 1.1.3
	 * @access public
	 * @var array
	 */
	public $setting_fields = array();

	/**
	 * Helps class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Helps
	 */
	public $helps;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;

		$this->plugin_slug = $plugin->get_slug();
		$this->option_slug = $plugin->get_opts();
		$this->version = $plugin->get_version();
		$this->theme_supports = $plugin->get_theme_supports();

		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );
		$this->path_url = plugin_dir_url( dirname( __FILE__ ) );

		$this->requires();
		$this->hooks();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function requires() {

		require_once( $this->path_dir . 'partials/class-fields.php' );
		require_once( $this->path_dir . 'partials/class-helps.php' );
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'frontend_setups' ) );

		add_action( 'init', array( $this, 'setting_fields_profiles' ), 15 );
		add_action( 'init', array( $this, 'setting_fields_buttons_content' ), 15 );
		add_action( 'init', array( $this, 'setting_fields_buttons_image' ), 15 );
		add_action( 'init', array( $this, 'setting_fields_metas_site' ), 15 );
		add_action( 'init', array( $this, 'setting_fields_enqueue' ), 15 );
		add_action( 'init', array( $this, 'setting_fields_modes' ), 15 );

		add_action( 'admin_menu', array( $this, 'setting_menu' ) );
		add_action( 'admin_init', array( $this, 'setting_setups' ) );
		add_action( 'admin_init', array( $this, 'setting_tabs' ), 15 );
		add_action( 'admin_init', array( $this, 'setting_sections' ), 20 );
		add_action( 'admin_init', array( $this, 'setting_fields' ), 25 );
		add_action( 'admin_init', array( $this, 'setting_init' ), 30 );
	}

	/**
	 * Run the setups for the setting page.
	 *
	 * The setups may involve running some Classes, Functions and sometimes WordPress Hooks,
	 * and defining the Class properties value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setting_setups() {

		$this->settings = new WPSettings\Settings( $this->option_slug );
		$this->validate = new Validation();

		$this->fields = new Fields( $this->screen );
		$this->helps = new Helps( $this->screen );
	}

	/**
	 * Function method that adds a new option page for the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setting_menu() {

		$menu_title = esc_html__( 'Social Media', 'ninecodes-social-manager' );
		$page_title = esc_html__( 'Social Media Settings', 'ninecodes-social-manager' );

		$this->screen = add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_slug, function() {
			echo wp_kses( "<div class='wrap' id='{$this->plugin_slug}-settings'>", array(
					'div' => array(
						'class' => array(),
						'id' => array(),
					),
			) );
			$this->settings->render_header( array( 'title' => true ) );
			$this->settings->render_form();
			echo '</div>';
		} );

		add_action( "admin_print_styles-{$this->screen}", array( $this, 'print_setting_styles' ), 20, 1 );
		add_action( "{$this->screen}_enqueue_scripts", array( $this, 'enqueue_scripts' ), 10, 1 );
		add_action( "{$this->screen}_enqueue_styles", array( $this, 'enqueue_styles' ), 10, 1 );
	}

	/**
	 * Function method to register the setting page pages or tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array List of tabs id, slug, and title.
	 */
	public function setting_tabs() {

		$tabs = array(
			'accounts' => esc_html__( 'Accounts', 'ninecodes-social-manager' ),
			'buttons' => esc_html__( 'Buttons', 'ninecodes-social-manager' ),
			'metas' => esc_html__( 'Metas', 'ninecodes-social-manager' ),
			'advanced' => esc_html__( 'Advanced', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the setting tabs.
		 *
		 * This filter allows developer to add new tabs on the setting page.
		 *
		 * @since 1.1.3
		 *
		 * @param array $tabs List of registered Tabs in the Setting page.
		 * @var array
		 */
		$tabs = (array) apply_filters( 'ninecodes_social_manager_setting_tabs', $tabs );

		/**
		 * Eliminate empty title.
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
		$tabs = $this->settings->add_pages( $tabs );

		return $this->tabs = $tabs;
	}

	/**
	 * Function method to register sections within the setting pages (tabs).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function setting_sections() {

		$sections = array();

		foreach ( $this->tabs as $tab => $title ) {

			if ( empty( $tab ) ) {
				continue;
			}

			switch ( $tab ) {

				case 'accounts':
					$sections[ $tab ] = array(
						'profiles' => array(
							'title' => esc_html__( 'Profiles', 'ninecodes-social-manager' ),
							'description' => esc_html__( 'Add all social media profiles and pages for this website.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_profiles' ),
						),
					);
					break;

				case 'buttons':
					$sections[ $tab ] = array(
						'buttons_content' => array(
							'title' => esc_html__( 'Content', 'ninecodes-social-manager' ),
							'description' => esc_html__( 'Configure how social media buttons display on your content pages.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_buttons_content' ),
						),
						'buttons_image' => array(
							'title' => esc_html__( 'Image', 'ninecodes-social-manager' ),
							'description' => esc_html__( 'Options to configure the social media buttons shown on the content images.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_buttons_image' ),
						),
					);
					break;

				case 'metas':
					$sections[ $tab ] = array(
						'metas_site' => array(
							'validate_callback' => array( $this->validate, 'setting_site_metas' ),
						),
					);
					break;

				case 'advanced':
					$sections[ $tab ] = array(
						'enqueue' => array(
							'validate_callback' => array( $this->validate, 'setting_advanced' ),
						),
						'modes' => array(
							'title' => esc_html__( 'Modes', 'ninecodes-social-manager' ),
							'description' => esc_html__( 'Configure the modes that work best for your website.', 'ninecodes-social-manager' ),
							'validate_callback' => array( $this->validate, 'setting_modes' ),
						),
					);
					break;

				default:
					$sections[ $tab ] = array();
					break;
			}

			/**
			 * Filter the setting sections.
			 *
			 * This filter allows developer to add or remove new sections on the registered Tabs.
			 *
			 * @since 1.1.3
			 *
			 * @param string $tab The Tab ID.
			 *
			 * @var array
			 */
			$sections = (array) apply_filters( 'ninecodes_social_manager_setting_sections', $sections, $tab );
		}

		$sections = $this->remove_duplicate_sections( $sections );

		foreach ( $sections as $tab => $section ) {
			$this->tabs = $this->settings->add_sections( $tab, $section );
		}

		return $sections;
	}

	/**
	 * Fields: Profiles & Pages.
	 *
	 * Add the social media profiles and pages related to this website.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array The array of fields added in the Profiles section.
	 */
	public function setting_fields_profiles() {

		$setting_fields = array();

		foreach ( Options::social_profiles() as $slug => $props ) {

			$props = wp_parse_args( $props, array(
				'label' => '',
				'url' => '',
				'description' => '',
			) );

			if ( empty( $props['label'] ) ||
				 empty( $props['url'] ) ||
				 empty( $props['description'] ) ) {
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
		 * the Profiles section in "Accounts" (tab) > "Profiles" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'profiles', 'accounts' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_profiles", $setting_fields );

		/**
		 * Regiter the fields in "Accounts" > "Profiles".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'accounts', 'profiles', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Fields: Buttons Content.
	 *
	 * The setting fields to configure the social media buttons that
	 * allows people to share, like, or save content of this site.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array
	 */
	public function setting_fields_buttons_content() {

		$setting_fields = array(
			'includes' => array(
				'label' => esc_html__( 'Buttons to include', 'ninecodes-social-manager' ),
				'type' => 'multicheckbox',
				'options' => Options::button_sites( 'content' ),
				'default' => array_map( function( $value ) {
					return 'on';
				}, Options::button_sites( 'content' ) ),
			),
			'post_types' => array(
				'type' => 'multicheckbox',
				'label' => esc_html__( 'Buttons Visibility', 'ninecodes-social-manager' ),
				'description' => wp_kses( sprintf( __( 'Select the %s that are allowed to show the social media buttons.', 'ninecodes-social-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">' . esc_html__( 'Post Types', 'ninecodes-social-manager' ) . '</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'options' => Options::post_types(),
				'default' => array( 'post' => 'on' ),
			),
			'view' => array(
				'label' => esc_html__( 'Buttons Views', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Select the social media buttons appearance shown in the content.', 'ninecodes-social-manager' ),
				'type' => 'radio',
				'options' => Options::button_views(),
				'default' => 'icon',
			),
			'placement' => array(
				'type' => 'radio',
				'label' => esc_html__( 'Buttons Placements', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Select the location to show the social media buttons in the content.', 'ninecodes-social-manager' ),
				'options' => Options::button_placements(),
				'default' => 'after',
			),
			'heading' => array(
				'type' => 'text',
				'label' => esc_html__( 'Buttons Header', 'ninecodes-social-manager' ),
				'description' => sprintf( esc_html__( 'Set the heading shown before the buttons (e.g. %s).', 'ninecodes-social-manager' ), '<code>Share on:</code>' ),
				'default' => esc_html__( 'Share on:', 'ninecodes-social-manager' ),
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in "Buttons" (tab) > "Buttons Content" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'buttons_content', 'buttons' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_buttons_content", $setting_fields );

		/**
		 * Register the fields in "Buttons" > "Buttons Content".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'buttons', 'buttons_content', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Fields: Buttons Image.
	 *
	 * The setting fields to configure the social media buttons shown
	 * on the content images.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array
	 */
	public function setting_fields_buttons_image() {

		$setting_fields = array(
			'enabled' => array(
				'label' => esc_html__( 'Buttons Image Display', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Show the social media buttons on images in the content', 'ninecodes-social-manager' ),
				'type' => 'checkbox_toggle',
				'attr' => array(
					'data-toggle' => '.sharing-image-setting',
				),
			),
			'includes' => array(
				'label' => esc_html__( 'Buttons to include', 'ninecodes-social-manager' ),
				'type' => 'multicheckbox',
				'options' => Options::button_sites( 'image' ),
				'default' => array_map( function( $value ) {
					return 'on';
				}, Options::button_sites( 'image' ) ),
				'class' => 'sharing-image-setting hide-if-js',
			),
			'post_types' => array(
				'label' => esc_html__( 'Buttons Visibility', 'ninecodes-social-manager' ),
				'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the social media buttons on the images of the content.', 'ninecodes-social-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">' . esc_html__( 'Post Types', 'ninecodes-social-manager' ) . '</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'type' => 'multicheckbox',
				'options' => Options::post_types(),
				'default' => array( 'post' => 'on' ),
				'class' => 'sharing-image-setting hide-if-js',
			),
			'view' => array(
				'label' => esc_html__( 'Buttons Views', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Select the social media buttons appearance shown on the images of the content.', 'ninecodes-social-manager' ),
				'type' => 'radio',
				'options' => Options::button_views(),
				'default' => 'icon',
				'class' => 'sharing-image-setting hide-if-js',
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in "Buttons" (tab) > "Buttons Image" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'buttons_image', 'buttons' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_buttons_image", $setting_fields );

		/**
		 * Register the fields in "Buttons" > "Buttons Image".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'buttons', 'buttons_image', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Fields: Metas Site.
	 * The setting fields to configure the meta data and the meta tags.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array
	 */
	public function setting_fields_metas_site() {

		$setting_fields = array(
			'enabled' => array(
				'type' => 'checkbox_toggle',
				'label' => esc_html__( 'Enable Meta Tags', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Generate social media meta tags on this website', 'ninecodes-social-manager' ),
				'default' => 'on',
				'attr' => array(
					'data-toggle' => '.meta-site-setting',
				),
			),
			'name' => array(
				'type' => 'text',
				'label' => esc_html__( 'Site Name', 'ninecodes-social-manager' ),
				'legend' => esc_html__( 'Site Name', 'ninecodes-social-manager' ),
				'description' => sprintf( esc_html__( 'The website name or brand as it should appear within the social media meta tags (e.g. %s)', 'ninecodes-social-manager' ), '<code>iMDB</code>, <code>TNW</code>, <code>HKDC</code>' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->site_title,
				),
			),
			'title' => array(
				'type' => 'text',
				'label' => esc_html__( 'Site Title', 'ninecodes-social-manager' ),
				'legend' => esc_html__( 'Site Title', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'The title of this website as it should appear within the social media meta tags.', 'ninecodes-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->document_title,
				),
			),
			'description' => array(
				'type' => 'textarea',
				'label' => esc_html__( 'Site Description', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'A one to two sentence describing this website that should appear within the social media meta tags.', 'ninecodes-social-manager' ),
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
				'label' => esc_html__( 'Site Image', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'An image URL which should represent this website within the social media meta tags (e.g. Open Graph, Twitter Cards, etc.)', 'ninecodes-social-manager' ),
			),
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in "Metas" (tab) > "Metas Site" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'metas_site', 'metas' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_metas", $setting_fields );

		/**
		 * Register the fields in "Metas" > "Metas Site".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'metas', 'metas_site', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Fields: Enqueue.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array
	 */
	public function setting_fields_enqueue() {

		$setting_fields = array();

		if ( $this->theme_supports->is( 'stylesheet' ) ) :

			$setting_fields['enable_stylesheet'] = array(
				'label' => esc_html__( 'Enable Stylesheet', 'ninecodes-social-manager' ),
				'type' => 'content',
				'content' => esc_html__( 'The Theme being used in this website has included the styles in its own stylesheet.', 'ninecodes-social-manager' ),
			);
		else :

			$setting_fields['enable_stylesheet'] = array(
				'label' => esc_html__( 'Enable Stylesheet', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Load the plugin stylesheet to apply essential styles.', 'ninecodes-social-manager' ),
				'default' => 'on',
				'type' => 'checkbox',
			);
		endif;

		/**
		 * The Filter hook to allow developer adding new field type
		 * in "Metas" (tab) > "Metas Site" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
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
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_enqueue", $setting_fields );

		/**
		 * Register the fields in "Advanced" > "Enqueue".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'advanced', 'enqueue', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Fields: Buttons Mode
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @see Options
	 *
	 * @return array
	 */
	public function setting_fields_modes() {

		$setting_fields = array();

		if ( ! (bool) $this->theme_supports->is( 'buttons_mode' ) ) :

			$setting_fields['buttons_mode'] = array(
				'label' => esc_html__( 'Buttons Mode', 'ninecodes-social-manager' ),
				'description' => esc_html__( 'Select the mode to render the social media buttons.', 'ninecodes-social-manager' ),
				'type' => 'radio',
				'options' => Options::buttons_modes(),
				'default' => 'html',
			);

		endif;

		$setting_fields['link_mode'] = array(
			'label' => esc_html__( 'Link Mode', 'ninecodes-social-manager' ),
			'description' => esc_html__( 'Select the link mode to append when the content or the image is shared.', 'ninecodes-social-manager' ),
			'type' => 'radio',
			'options' => Options::link_modes(),
			'default' => 'permalink',
		);

		/**
		 * The Filter hook to allow developer adding new field type
		 * in "Advanced" (tab) > "Modes" (section).
		 *
		 * @since 1.1.3
		 *
		 * @param string $tab_id 	 The tab id.
		 * @param string $section_id The section id.
		 * @var array
		 */
		$setting_fields = (array) apply_filters( 'ninecodes_social_manager_setting_fields', $setting_fields, 'modes', 'advanced' );

		/**
		 * Removes duplicate values from an array.
		 *
		 * @var array
		 */
		$setting_fields = array_unique( $setting_fields, SORT_REGULAR );

		/**
		 * Feed the fields default value to `get_option()`
		 *
		 * @since 1.1.3
		 */
		$this->option_defaults( "{$this->option_slug}_modes", $setting_fields );

		/**
		 * Register the fields in "Advanced" > "Enqueue".
		 *
		 * @var array {
		 *		@type string $tab 	  		 The tab ID.
		 * 		@type string $section 		 The section ID.
		 *		@type array  $setting_fields The fields data.
		 * }
		 */
		$this->setting_fields[] = array( 'advanced', 'modes', $setting_fields );

		return $setting_fields;
	}

	/**
	 * Register fields.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @return array
	 */
	public function setting_fields() {

		foreach ( $this->setting_fields as $key => $value ) {

			list( $tab, $section, $fields ) = $value;

			$setting_fields = $this->settings->add_fields( $tab, $section, $fields );
		}

		return $this->tabs = $setting_fields;
	}

	/**
	 * Initialize and render the setting screen with the registered
	 * tabs, sections, and fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setting_init() {

		$this->settings->init( $this->screen, $this->tabs );
	}

	/**
	 * Function to internal styles in the setting page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function print_setting_styles() {
		?>
		<style id="<?php echo esc_attr( "{$this->plugin_slug}-internal-styles" ); ?>">
			.wrap > form > h2 {
				margin-bottom: 0.72em;
				margin-top: 1.68em;
			}
			.wrap > form > div.notice {
				margin-top: 1.68em;
			}
			.branch-4-5 .wrap > .nav-tab-wrapper,
			.branch-4-6 .wrap > .nav-tab-wrapper {
				border-bottom: 1px solid #ccc;
				margin: 0;
				padding-top: 9px;
				padding-bottom: 0;
				line-height: inherit;
			}
			.wrap .field-image-control {
				margin-top: 0.867em;
			}
			.wrap .field-image-placeholder {
				width: 100%;
				max-width: 590px;
				position: relative;
				text-align: center;
				padding: 2em 0;
				line-height: 1.3;
				border: 1px dashed #b4b9be;
				box-sizing: border-box;
			}
		</style>
	<?php }

	/**
	 * Function to enqueue JavaScripts in the setting page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args An array of the JavaScripts file name.
	 * @return void
	 */
	public function enqueue_scripts( array $args ) {

		foreach ( $args as $key => $file ) {

			$file = is_string( $file ) && ! empty( $file ) ? "{$file}" : 'scripts';
			wp_enqueue_script( "{$this->plugin_slug}-{$file}", "{$this->path_url}js/{$file}.min.js", array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		}
	}

	/**
	 * Function to enqueue stylesheets in the setting page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args An array of the stylesheets file name.
	 * @return void
	 */
	public function enqueue_styles( array $args ) {

		foreach ( $args as $name => $file ) {

			$file = is_string( $file ) && ! empty( $file ) ? "{$file}" : 'styles';

			wp_enqueue_style( "{$this->plugin_slug}-{$file}", "{$this->path_url}css/{$file}.min.css", array(), $this->version );

			if ( 'image-upload' === $file ) {
				wp_style_add_data( "{$this->plugin_slug}-{$file}", 'rtl', 'replace' );
			}
		}
	}

	/**
	 * Setups the front ends.
	 *
	 * This function method run functions that will otherwise won't be
	 * accessible if they are run via the 'admin_init' Action Hook.
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
	 * The function method to construct the document title.
	 *
	 * The 'wp_get_document_title' function does not return a proper value
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
	 * The utility function to remove duplicate keys in the Tabs and Sections.
	 *
	 * NOTE This functionality should be merged to `wp-settings`.
	 *
	 * @since 1.1.3
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
	 * Sort out the tabs for possible duplicate values in the Tabs and Sections.
	 *
	 * NOTE This functionality should be merged to `wp-settings`.
	 *
	 * @since 1.1.3
	 * @access protected
	 *
	 * @param string $key  	The key in the array to search.
	 * @param string $value The value in the array to compare.
	 * @param array  $arr 	The array.
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
	 * The utility function to remove duplicate keys in the Tabs and Sections.
	 *
	 * NOTE This functionality should be merged to `wp-settings`.
	 *
	 * @since 1.1.3
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
	 * Enable the 'get_option' to return default value along with the saved value in the database.
	 *
	 * @since 1.1.3
	 * @access protected
	 *
	 * @param string $option_slug The option key.
	 * @param array  $fields      The fields data.
	 * @return void
	 */
	protected function option_defaults( $option_slug = '', array $fields ) {

		$default = array();
		foreach ( $fields as $key => $value ) {
			$default[ $key ] = isset( $value['default'] ) ? $value['default'] : null;
		}

		$options = get_option( $option_slug );
		$value = $options ? wp_parse_args_recursive( $options, $default ) : $default;

		add_filter( "default_option_{$option_slug}", function( $v, $o ) use ( $value ) {
			return $value;
		}, 10, 2 );

		add_filter( "option_{$option_slug}", function( $v, $o ) use ( $value ) {
			return $value;
		}, 10, 2 );
	}
}
