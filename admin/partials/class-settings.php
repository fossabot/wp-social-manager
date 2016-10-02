<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       github.com/tfirdaus
 * @since      1.0.0
 *
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/admin/partials
 */

namespace XCo\WPSocialManager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	die;

final class Settings extends SettingUtilities {

	/**
	 * [$args description]
	 * @var [type]
	 */
	protected $args;

	/**
	 * [$plugin_dir description]
	 * @var [type]
	 */
	protected $plugin_dir;

	/**
	 * [$screen description]
	 * @var [type]
	 */
	protected $screen;

	/**
	 * [$settings description]
	 * @var [type]
	 */
	protected $settings;

	/**
	 * [$fields description]
	 * @var [type]
	 */
	protected $fields;

	/**
	 * [__construct description]
	 * @param array $args [description]
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->version = $args[ 'version' ];
		$this->plugin_name = $args[ 'plugin_name' ];
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->plugin_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
		$this->plugin_url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );

		$this->requires();
		$this->actions();
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	protected function requires() {

		require_once( $this->plugin_dir . 'partials/pepperplane/pepperplane.php' );
		require_once( $this->plugin_dir . 'partials/pepperplane/pepperplane-fields.php' );
	}

		/**
	 * [setups description]
	 * @return [type] [description]
	 */
	protected function actions() {

		add_action( 'admin_menu', array( $this, 'setting_menu' ) );

		add_action( 'admin_init', array( $this, 'setting_setup' ), 10 );
		add_action( 'admin_init', array( $this, 'setting_pages' ), 11 );
		add_action( 'admin_init', array( $this, 'setting_sections' ), 11 );
		add_action( 'admin_init', array( $this, 'setting_fields' ), 11 );
		add_action( 'admin_init', array( $this, 'setting_init' ), 12 );

		add_action( "{$this->plugin_opts}_admin_enqueue_scripts", array( $this, 'setting_scripts' ), 10, 1 );
	}

	/**
	 * [setting_setup description]
	 * @return [type] [description]
	 */
	public function setting_setup() {

		$errors = get_settings_errors();
		$fields = new \PepperPlaneFields( $errors );

		$settings = new \PepperPlane( $fields, $this->plugin_name, $this->plugin_opts );
		$this->settings = $settings;

		$validate = new SettingValidation();
		$this->validate = $validate;
	}

	/**
	 * [setting_menu description]
	 * @return [type] [description]
	 */
	public function setting_menu() {

		$menu_title = esc_html__( 'Social', 'wp-social-manager' );
		$page_title = esc_html__( 'Social Settings', 'wp-social-manager' );

		$this->screen = add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_name, function() {
			echo "<div class='wrap' id='{$this->plugin_name}-wrap'>";
				$this->settings->render_header( array( 'title' => false ) );
				// echo $this->settings->debug;
				$this->settings->render_form();
			echo "</div>";
		} );

		add_action( "admin_print_styles-{$this->screen}", array( $this, 'setting_styles' ), 20, 1 );
	}

	/**
	 * [setting_init description]
	 * @return [type] [description]
	 */
	public function setting_pages() {

		$this->pages = $this->settings->add_pages( array(
				array(
					'id' => 'accounts',
					'slug' => 'accounts',
					'title' => esc_html__( 'Accounts', 'wp-social-manager' )
				),
				array(
					'id' => 'buttons',
					'slug' => 'buttons',
					'title' => esc_html__( 'Buttons', 'wp-social-manager' )
					),
				array(
					'id' => 'metas',
					'slug' => 'metas',
					'title' => esc_html__( 'Metas', 'wp-social-manager' )
				),
				array(
					'id' => 'advanced',
					'slug' => 'advanced',
					'title' => esc_html__( 'Advanced', 'wp-social-manager' )
				),
			)
		);
	}

	/**
	 * [setting_sections description]
	 * @return [type] [description]
	 */
	public function setting_sections() {

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_section( 'accounts', array(
				'id' => 'profiles',
				'title' => esc_html__( 'Profiles & Pages', 'wp-social-manager' ),
				'description' => esc_html__( 'Add the social media profiles and pages related to this website.', 'wp-social-manager' ),
				'validate_callback' => array( $this->validate, 'setting_usernames' )
			)
		);

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_sections( 'buttons', array(
				array(
					'id' => 'buttons_content',
					'title' => esc_html__( 'Content', 'wp-social-manager' ),
					'description' => esc_html__( 'Options to configure the social media buttons that enable sharing, saving, or liking the content.', 'wp-sharing-manager' ),
					'validate_callback' => array( $this->validate, 'setting_buttons_content' )
				),
				array(
					'id' => 'buttons_image',
					'title' => esc_html__( 'Image', 'wp-social-manager' ),
					'description' => esc_html__( 'Options to configure the social media buttons shown on the content images.', 'wp-sharing-manager' ),
					'validate_callback' => array( $this->validate, 'setting_buttons_image' )
				)
			)
		);

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_section( 'metas', array(
			'id' => 'metas_site',
			'validate_callback' => array( $this->validate, 'setting_metas' )
			)
		);

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_section( 'advanced', array(
			'id' => 'advanced',
			'validate_callback' => array( $this->validate, 'setting_advanced' )
			)
		);
	}

	/**
	 * [setting_fields description]
	 * @return void [description]
	 */
	public function setting_fields() {

		/**
		 * [$key description]
		 * @var [type]
		 */
		foreach ( self::get_social_profiles() as $key => $value ) {

			$props = self::get_social_properties( $key );
			$label = isset( $value[ 'label' ] ) ? $value[ 'label' ] : '';
			$description = isset( $value[ 'description' ] ) ? $value[ 'description' ] : '';

			$profiles = array(
				'id' => sanitize_key( $key ),
				'type' => 'text',
				'label' => $label,
				'description' => $description,
				'after' => '<p class="account-profile-preview hide-if-js"><code></code></p>',
				'attr' => array(
					'class' => "account-profile-control code",
					'data-js-enabled' => true,
					'data-url' => $props[ 'url' ]
				)
			);

			$this->pages = $this->settings->add_fields( 'accounts', 'profiles', array( $profiles ) );
		}

		/**
		 * [setting_init description]
		 * @param  [type] $this->screen [description]
		 * @return [type]               [description]
		 */
		$this->pages = $this->settings->add_fields( 'buttons', 'buttons_content', array(
				array(
					'id' => 'postTypes',
					'type' => 'multicheckbox',
					'label' => esc_html__( 'Show the buttons in', 'wp-sharing-manager' ),
					'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the sharing buttons.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
					'options' => self::get_post_types(),
					'default' => 'post',
				),
				array(
					'id' => 'buttonType',
					'label' => esc_html__( 'Show the buttons as', 'wp-sharing-manager' ),
					'description' => esc_html__( 'The social media button appearance in the content.', 'wp-sharing-manager' ),
					'type' => 'radio',
					'options' => self::get_button_types(),
					'default' => 'icon'
				),
				array(
					'id' => 'buttonLocation',
					'type' => 'radio',
					'label' => esc_html__( 'Place the buttons', 'wp-sharing-manager' ),
					'description' => esc_html__( 'Location in the content to show the sharing buttons.', 'wp-sharing-manager' ),
					'options' => self::get_button_locations(),
					'default' => 'after',
				),
				array(
					'id' => 'buttonSites',
					'label' => esc_html__( 'Include these buttons', 'wp-sharing-manager' ),
					'type' => 'multicheckbox',
					'options' => self::get_button_sites(),
					'default' => array_keys( self::get_button_sites() )
				)
		) );

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_fields( 'buttons', 'buttons_image', array(
			array(
				'id' => 'imageSharing',
				'label' => esc_html__( 'Image Sharing Display', 'wp-sharing-manager' ),
				'description' => esc_html__( 'Show the social sharing buttons on images in the content', 'wp-sharing-manager' ),
				'type' => 'checkbox',
				'attr' => array(
					'class' => 'toggle-control',
					'data-js-enabled' => true,
					'data-toggle-target' => '.sharing-image-setting',
				)
			),
			array(
				'id' => 'postTypes',
				'label' => esc_html__( 'Show the buttons in', 'wp-sharing-manager' ),
				'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the sharing buttons on the images of the content.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'type' => 'multicheckbox',
				'options' => self::get_post_types(),
				'default' => array( 'post' ),
				'class' => 'sharing-image-setting hide-if-js'
			),
			array(
				'id' => 'buttonType',
				'label' => esc_html__( 'Show the buttons as', 'wp-sharing-manager' ),
				'description' => esc_html__( 'The social media button appearance in the content.', 'wp-sharing-manager' ),
				'type' => 'radio',
				'options' => self::get_button_types(),
				'default' => 'icon',
				'class' => 'sharing-image-setting hide-if-js',
			),
			array(
				'id' => 'buttonSites',
				'label' => esc_html__( 'Include these buttons', 'wp-sharing-manager' ),
				'type' => 'multicheckbox',
				'options' => self::get_button_sites( 'image' ),
				'default' => array_keys( self::get_button_sites( 'image' ) ),
				'class' => 'sharing-image-setting hide-if-js',
			)
		) );

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_fields( 'metas', 'metas_site', array(
			array(
				'id' => 'metaEnable',
				'type' => 'checkbox',
				'label' => esc_html__( 'Enable Meta Tags', 'wp-social-manager' ),
				'description' => esc_html__( 'Generate social meta tags on this website', 'wp-social-manager' ),
				'default' => 'on',
				'attr' => array(
					'class' => 'toggle-control',
					'data-js-enabled' => true,
					'data-toggle-target' => '.meta-site-setting',
				)
			),
			array(
				'id' => 'siteName',
				'type' => 'text',
				'label' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'legend' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'description' => esc_html__( 'The name of this website as it should appear within the Open Graph meta tag', 'wp-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => get_bloginfo( 'name' )
				)
			),
			array(
				'id' => 'siteDescription',
				'type' => 'textarea',
				'label' => esc_html__( 'Site Description', 'wp-social-manager' ),
				'description' => esc_html__( 'A one to two sentence description of this website that should appear within the Open Graph meta tag', 'wp-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'rows' => '5',
					'cols' => '80',
					'placeholder' => get_bloginfo( 'description' )
				)
			)
		) );

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_field( 'advanced', 'advanced', array(
			'id' => 'disableStylesheet',
			'label' => esc_html__( 'Enable Stylesheet', 'wp-sharing-manager' ),
			'description' => esc_html__( 'Load the plugin stylesheet to apply essential styles.', 'wp-sharelog' ),
			'default' => 'on',
			'type' => 'checkbox'
		) );
	}

	/**
	 * [setting_init description]
	 * @access public
	 * @return [type] [description]
	 */
	public function setting_init() {

		$this->settings->init( $this->screen, $this->pages );
	}

	/**
	 * [setting_styles description]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function setting_styles() { ?>
		<style id="<?php echo esc_attr( "{$this->plugin_name}-internal-styles" ); ?>">
			.wrap > form > h2 {
				margin-bottom: 0.72em;
				margin-top: 1.68em;
			}
			.wrap > form > div.notice {
				margin-top: 1.68em;
			}
			.wrap > .nav-tab-wrapper {
				margin: 1.5em 0 1em;
    			border-bottom: 1px solid #ccc;
			}
			.wrap .form-table .account-profile-preview {
				margin-top: 0.5em;
			}
		</style>
	<?php }

	/**
	 * [field_scripts description]
	 * @return [type] [description]
	 */
	public function setting_scripts() {

		wp_enqueue_script( "{$this->plugin_name}-setting", "{$this->plugin_url}js/scripts.js", array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		wp_enqueue_media();
	}
}