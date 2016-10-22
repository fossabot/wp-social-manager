<?php
/**
 * Admin: SocialMetaBox class
 *
 * @package 	SocialManager
 * @subpackage 	Admin\Metabox
 */

namespace SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die( 'Shame on you!' ); // Abort.
}

/**
 * The SocialMetaBox class is used for registering new metabox via ButterBean API.
 *
 * @since 1.0.0
 *
 * @link https://github.com/justintadlock/butterbean
 */
final class SocialMetaBox {

	/**
	 * The Plugin class instance.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $plugin;

	/**
	 * The plugin directory path relative to the current file.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $path_dir;

	/**
	 * The WordPress post ID.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	integer
	 */
	protected $post_id;

	/**
	 * The post title.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $post_title;

	/**
	 * The post content.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $post_content;

	/**
	 * The post description / excerpt.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $post_excerpt;

	/**
	 * The post thumbnail id.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	integer
	 */
	protected $post_thumbnail;

	/**
	 * The post type singular name.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $post_type;

	/**
	 * Run WordPress and ButterBean Hooks.
	 *
	 * @since 	1.0.0
	 * @access 	private
	 *
	 * @return 	void
	 */
	private function hooks() {

		// Load `ButterBean` library.
		add_action( 'plugins_loaded', array( $this, 'requires' ) );

		// Register managers.
		add_action( 'butterbean_register', array( $this, 'register_manager' ), -90, 2 );

		// Register sections, settings, and controls.
		add_action( 'butterbean_register', array( $this, 'register_section_buttons' ), -90, 2 );
		add_action( 'butterbean_register', array( $this, 'register_section_meta' ), -90, 2 );
	}

	/**
	 * Load dependencies.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @return 	void
	 */
	public function requires() {

		$this->path_dir = plugin_dir_path( __FILE__ );

		require_once( $this->path_dir . 'butterbean/butterbean.php' );
		require_once( $this->path_dir . 'butterbean-extend/butterbean-extend.php' );
	}

	/**
	 * Run the setups.
	 *
	 * The setups may involve running some Classes, Functions, and sometimes WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param  	Plugin $plugin The Plugin class instance.
	 * @return 	void
	 */
	public function setups( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Registers manager.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param 	object $butterbean  Instance of the 'ButterBean' object.
	 * @param 	string $post_type   The current Post Type slug.
	 * @return 	void
	 */
	public function register_manager( $butterbean, $post_type ) {

		// Load internal post and post type objects.
		$this->load_post();
		$this->load_post_type( $post_type );

		// Load internal styles or scripts.
		add_action( 'admin_head-post.php', array( $this, 'admin_head_enqueues' ), 10 );
		add_action( 'admin_head-post-new.php', array( $this, 'admin_head_enqueues' ), 10 );

		// Register our custom manager.
		$butterbean->register_manager(
			'wp_social_manager',
			array(
				'label'      => esc_html__( 'Social', 'wp-social-manager' ),
				'post_type'  => array( 'post', 'page', 'product' ),
				'context'    => 'normal',
				'priority'   => 'high',
				'capability' => 'publish_posts',
			)
		);
	}

	/**
	 * Registers sections.
	 *
	 * @since  	1.0.0
	 * @access 	public
	 *
	 * @param 	object $butterbean  Instance of the `ButterBean` object.
	 * @param 	string $post_type   The current Post Type slug.
	 * @return 	void
	 */
	public function register_section_buttons( $butterbean, $post_type ) {

		// Get our custom manager object.
		$manager = $butterbean->get_manager( 'wp_social_manager' );

		// Register a section.
		$manager->register_section(
			'buttons',
			array(
				'label' => 'Buttons',
				'icon'  => 'dashicons-thumbs-up',
			)
		);

		$post_types = (array) $this->plugin->get_option( 'buttons_content', 'post_types' );

		if ( in_array( $post_type, $post_types, true ) ) {

			// Register a setting.
			$manager->register_setting(
				'buttons_content',
				array(
					'type' => 'serialize',
					'default' => 1,
					'sanitize_callback' => 'butterbean_validate_boolean',
				)
			);
			$manager->register_control(
				'buttons_content',
				array(
					'type' => 'checkbox',
					'section' => 'buttons',
					'label' => 'Content Social Media Buttons',
					'description' => "Display the buttons that allow people to share, like, or save this {$this->post_type} in social media",
				)
			);
		}

		$enabled = (bool) $this->plugin->get_option( 'buttons_image', 'enabled' );
		$post_types = (array) $this->plugin->get_option( 'buttons_image', 'post_types' );

		if ( in_array( $post_type, $post_types, true ) && $enabled ) {

			// Register a setting.
			$manager->register_setting(
				'buttons_image',
				array(
					'type' => 'serialize',
					'default' => 1,
					'sanitize_callback' => 'butterbean_validate_boolean',
				)
			);
			$manager->register_control(
				'buttons_image',
				array(
					'type' => 'checkbox',
					'section' => 'buttons',
					'label' => 'Image Social Media Buttons',
					'description' => "Display the social media buttons that allow people to share, like, or save images of this {$this->post_type} in social media",
				)
			);
		}
	}

	/**
	 * Register the "Meta" section tab.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param 	object $butterbean  Instance of the `ButterBean` object.
	 * @param 	string $post_type   The current Post Type slug.
	 * @return 	void
	 */
	public function register_section_meta( $butterbean, $post_type ) {

		$meta_enabled = (bool) $this->plugin->get_option( 'metas_site', 'enabled' );

		if ( ! $meta_enabled ) {
			return;
		}

		// Get our custom manager object.
		$manager = $butterbean->get_manager( 'wp_social_manager' );

		$manager->register_section(
			'meta_tags',
			array(
				'label' => 'Metas',
				'icon'  => 'dashicons-editor-code',
			)
		);

		$manager->register_control(
			'post_title',
			array(
				'type' => 'text',
				'section' => 'meta_tags',
				'label' => 'Title',
				'description' => "Set a customized title of this {$this->post_type} as it should appear within the social meta tag",
				'attr' => array(
					'class' => 'widefat',
					'placeholder' => $this->post_title,
				),
			)
		);

		$manager->register_setting(
			'post_title',
			array(
				'type' => 'serialize',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$manager->register_control(
			'post_excerpt',
			array(
				'type' => 'textarea',
				'section' => 'meta_tags',
				'label' => 'Description',
				'description' => "Set a one to two customized description of this {$this->post_type} that should appear within the social meta tag",
				'attr' => array(
					'placeholder' => $this->post_excerpt,
					'class' => 'widefat',
				),
			)
		);

		$manager->register_setting(
			'post_excerpt',
			array(
				'type' => 'serialize',
				'sanitize_callback' => 'wp_kses',
			)
		);

		// Image upload control.
		$manager->register_control(
			'post_thumbnail',
			array(
				'type'        => 'image',
				'section'     => 'meta_tags',
				'label'       => 'Image',
				'description' => "Set a custom image URL which should represent this {$this->post_type} within the social meta tag",
				'size'        => 'large',
			)
		);
		$manager->register_setting(
			'post_thumbnail',
			array(
				'type' => 'serialize',
				'sanitize_callback' => array( $this, 'sanitize_absint' ),
			)
		);
	}

	/**
	 * Sanitize function for integers.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param  	integer $value 	The value to sanitize.
	 * @return 	integer|null 	The sanitized value or empty string.
	 */
	public function sanitize_absint( $value ) {
		return $value && is_numeric( $value ) ? absint( $value ) : null;
	}

	/**
	 * The function method to retrive the post title, content, excerpt, and thumbnail id.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @link 	https://developer.wordpress.org/reference/functions/get_post_field/
	 *
	 * @return 	void
	 */
	protected function load_post() {

		$this->post_id = isset( $_GET['post'] ) ? $this->sanitize_absint( $_GET['post'] ) : 0;

		$this->post_title = get_post_field( 'post_title', $this->post_id );
		$this->post_content = get_post_field( 'post_content', $this->post_id );
		$this->post_thumbnail = get_post_thumbnail_id( $this->post_id );

		$this->post_excerpt = wp_trim_words( $this->post_content, 30, '...' );
	}

	/**
	 * The function method to retrieve the post type singular name.
	 *
	 * This function exist because the post type slug or name can have special
	 * character like underscore or dash.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @param 	string $post_type The post type slug / name.
	 * @return 	void
	 */
	protected function load_post_type( $post_type ) {

		$objects = get_post_type_object( $post_type );

		$this->post_type = strtolower( $objects->labels->singular_name );
	}

	/**
	 * Print internal styles.
	 *
	 * The styles will make the metabox appearance (e.g. icon size, font size, and color)
	 * consistent following with the WooCommerce metabox styling where ButterBean derived
	 * the inspiration for the metabox UI.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	void
	 */
	public function admin_head_enqueues() {
	?>
		<style id="butterbean-styles">
			.butterbean-manager .butterbean-label { font-weight: 600 }
			.butterbean-manager .butterbean-description { color: #555; }
			.butterbean-manager .butterbean-nav .dashicons { font-size: 1em }
			.butterbean-manager .butterbean-nav li[aria-selected=true] a { font-weight: 400 }
		</style>
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  Plugin $plugin The Plugin class instance.
	 * @return object
	 */
	public static function get_instance( Plugin $plugin ) {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setups( $plugin );
			$instance->hooks();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}
}
