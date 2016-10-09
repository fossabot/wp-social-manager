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

final class Settings extends OptionUtilities {

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
	 * [$document_title description]
	 * @var [type]
	 */
	protected $site_title;

	/**
	 * [$document_title description]
	 * @var [type]
	 */
	protected $site_tagline;

	/**
	 * [$document_title description]
	 * @var [type]
	 */
	protected $document_title;

	/**
	 * [__construct description]
	 * @param array $args [description]
	 */
	public function __construct( array $args ) {

		$this->args = $args;

		$this->version = $args[ 'version' ];
		$this->plugin_name = $args[ 'plugin_name' ];
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->path_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
		$this->path_url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );

		$this->requires();
		$this->hooks();
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	protected function requires() {

		require_once( $this->path_dir . 'partials/pepperplane/pepperplane.php' );
		require_once( $this->path_dir . 'partials/pepperplane/pepperplane-fields.php' );
		require_once( $this->path_dir . 'partials/pepperplane/pepperplane-install.php' );

		require_once( $this->path_dir . 'partials/class-extends.php' );
	}

	/**
	 * [setups description]
	 * @return [type] [description]
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'frontend_setups' ) );

		add_action( 'admin_menu', array( $this, 'setting_menu' ) );

		add_action( 'admin_init', array( $this, 'setting_setups' ), 10 );
		add_action( 'admin_init', array( $this, 'setting_pages' ), 15 );
		add_action( 'admin_init', array( $this, 'setting_sections' ), 15 );
		add_action( 'admin_init', array( $this, 'setting_fields' ), 15 );
		add_action( 'admin_init', array( $this, 'setting_init' ), 20 );

		add_action( "{$this->plugin_opts}_admin_enqueue_scripts", array( $this, 'enqueue_scripts' ), 10, 1 );
		add_action( "{$this->plugin_opts}_admin_enqueue_styles", array( $this, 'enqueue_styles' ), 10, 1 );
	}

	/**
	 * [setting_setups description]
	 * @return [type] [description]
	 */
	public function setting_setups() {

		$fields = new \PepperPlaneFields( get_settings_errors() );
		$settings = new \PepperPlane( $this->plugin_opts, $fields );

		$extends = new SettingsExtend( $this->plugin_opts );
		$validate = new SettingsValidation();

		$this->settings = $settings;
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
				$this->settings->render_form();
			echo "</div>";
		} );

		add_action( "admin_print_styles-{$this->screen}", array( $this, 'print_setting_styles' ), 20, 1 );
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
					'description' => esc_html__( 'Options to configure the social media buttons that allows people to share, like, or save content of this site.', 'wp-sharing-manager' ),
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
			'validate_callback' => array( $this->validate, 'setting_site_metas' )
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
					'data-load-script' => 'preview-profile',
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
					'description' => wp_kses( sprintf( __( 'Select the %s that are allowed to show the social media buttons.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
					'options' => self::get_post_types(),
					'default' => array( 'post' ),
				),
				array(
					'id' => 'view',
					'label' => esc_html__( 'Buttons View', 'wp-sharing-manager' ),
					'description' => esc_html__( 'Select the social media buttons visual appearance displayed in the content.', 'wp-sharing-manager' ),
					'type' => 'radio',
					'options' => self::get_button_views(),
					'default' => 'icon'
				),
				array(
					'id' => 'placement',
					'type' => 'radio',
					'label' => esc_html__( 'Buttons Placement', 'wp-sharing-manager' ),
					'description' => esc_html__( 'Select the location to show the social media buttons in the content.', 'wp-sharing-manager' ),
					'options' => self::get_button_placements(),
					'default' => 'after',
				),
				array(
					'id' => 'heading',
					'type' => 'text',
					'label' => esc_html__( 'Buttons Heading', 'wp-sharing-manager' ),
					'description' => sprintf( esc_html__( 'Set the heading title shown before the buttons (e.g. %s).', 'wp-sharing-manager' ), '<code>Share on:</code>' )
				),
				array(
					'id' => 'includes',
					'label' => esc_html__( 'Include these', 'wp-sharing-manager' ),
					'type' => 'multicheckbox',
					'options' => self::get_button_sites( 'content' ),
					'default' => array_keys( self::get_button_sites( 'content' ) )
				)
		) );

		/**
		 * [$this->pages description]
		 * @var [type]
		 */
		$this->pages = $this->settings->add_fields( 'buttons', 'buttons_image', array(
			array(
				'id' => 'enabled',
				'label' => esc_html__( 'Image Buttons Display', 'wp-sharing-manager' ),
				'description' => esc_html__( 'Show the social media buttons on images in the content', 'wp-sharing-manager' ),
				'type' => 'checkbox',
				'attr' => array(
					'class' => 'toggle-control',
					'data-load-script' => 'toggle-control',
					'data-toggle' => '.sharing-image-setting',
				)
			),
			array(
				'id' => 'postTypes',
				'label' => esc_html__( 'Show the buttons in', 'wp-sharing-manager' ),
				'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the social media buttons on the images of the content.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
				'type' => 'multicheckbox',
				'options' => self::get_post_types(),
				'default' => array( 'post' ),
				'class' => 'sharing-image-setting hide-if-js'
			),
			array(
				'id' => 'view',
				'label' => esc_html__( 'Buttons View', 'wp-sharing-manager' ),
				'description' => esc_html__( 'The social media button visual appearance in the content.', 'wp-sharing-manager' ),
				'type' => 'radio',
				'options' => self::get_button_views(),
				'default' => 'icon',
				'class' => 'sharing-image-setting hide-if-js',
			),
			array(
				'id' => 'includes',
				'label' => esc_html__( 'Include these', 'wp-sharing-manager' ),
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
				'id' => 'enabled',
				'type' => 'checkbox',
				'label' => esc_html__( 'Enable Meta Tags', 'wp-social-manager' ),
				'description' => esc_html__( 'Generate social media meta tags on this website', 'wp-social-manager' ),
				'default' => 'on',
				'attr' => array(
					'class' => 'toggle-control',
					'data-load-script' => 'toggle-control',
					'data-toggle' => '.meta-site-setting',
				)
			),
			array(
				'id' => 'name',
				'type' => 'text',
				'label' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'legend' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'description' => sprintf( esc_html__( 'The website name or brand as it should appear within the social media meta tags (e.g. %s)', 'wp-social-manager' ), '<code>iMDB</code>, <code>TNW</code>, <code>HKDC</code>' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->site_title
				)
			),
			array(
				'id' => 'title',
				'type' => 'text',
				'label' => esc_html__( 'Site Title', 'wp-social-manager' ),
				'legend' => esc_html__( 'Site Title', 'wp-social-manager' ),
				'description' => esc_html__( 'The title of this website as it should appear within the social media meta tags.', 'wp-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'placeholder' => $this->document_title
				)
			),
			array(
				'id' => 'description',
				'type' => 'textarea',
				'label' => esc_html__( 'Site Description', 'wp-social-manager' ),
				'description' => esc_html__( 'A one to two sentence describing this website that should appear within the social media meta tags.', 'wp-social-manager' ),
				'class' => 'meta-site-setting',
				'attr' => array(
					'rows' => '4',
					'cols' => '80',
					'placeholder' => $this->site_tagline
				)
			),
			array(
				'id' => 'image',
				'type' => 'image',
				'class' => 'meta-site-setting',
				'label' => esc_html__( 'Site Image', 'wp-social-manager' ),
				'description' => esc_html__( 'An image URL which should represent this website within the social media meta tags (e.g. Open Graph, Twitter Cards, etc.)', 'wp-social-manager' )
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
		$this->settings->install();
	}

	/**
	 * [setting_styles description]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function print_setting_styles() { ?>
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
	 * [field_scripts description]
	 * @return [type] [description]
	 */
	public function enqueue_scripts( array $args ) {

		foreach ( $args as $key => $file ) {
			$file = is_string( $file ) && ! empty( $file ) ? "{$file}" : "scripts";
			wp_enqueue_script( "{$this->plugin_name}-{$file}", "{$this->path_url}js/{$file}.js", array( 'jquery', 'underscore', 'backbone' ), $this->version, true );
		}

		wp_enqueue_media();
	}

	/**
	 * [setting_styles description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function enqueue_styles( array $args ) {

		foreach ( $args as $name => $suffix ) {
			$file = is_string( $suffix ) && ! empty( $suffix ) ? "styles-{$suffix}" : "styles";
			wp_enqueue_style( "{$this->plugin_name}-{$suffix}", "{$this->path_url}css/{$file}.css", array(), $this->version );
		}
	}

	/**
	 * [_document_title description]
	 * @return [type] [description]
	 */
	public function frontend_setups() {
		$this->wp_get_document_title();
	}

	/**
	 * [wp_get_document_title description]
	 * @return [type] [description]
	 */
	protected function wp_get_document_title() {

		$title[ 'site' ] = get_bloginfo( 'name', 'display' );
		$title[ 'tagline' ] = get_bloginfo( 'description', 'display' );

		$this->site_title = $title[ 'site' ];
		$this->site_tagline = $title[ 'tagline' ];

		$sep   = apply_filters( 'document_title_separator', '-' );
		$title = apply_filters( 'document_title_parts', $title );

		$title = implode( " $sep ", array_filter( $title ) );
		$title = wptexturize( $title );
		$title = convert_chars( $title );
		$title = esc_html( $title );
		$title = capital_P_dangit( $title );

		$this->document_title = $title;
	}
}