<?php
/**
 * Admin: Metabox class
 *
 * @package SocialManager
 * @subpackage Admin\Metabox
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The SocialMetaBox class is used for registering new metabox via ButterBean API.
 *
 * @since 1.0.0
 *
 * @link https://github.com/justintadlock/butterbean
 */
final class Metabox {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
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
	 * The plugin directory path to the Metabox extensions.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_ext;

	/**
	 * The plugin directory path to the Metabox custom template.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $path_tmpl;

	/**
	 * The WordPress post ID.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var integer
	 */
	protected $post_id;

	/**
	 * The post title.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $post_title;

	/**
	 * The post content.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $post_content;

	/**
	 * The post description / excerpt.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $post_excerpt;

	/**
	 * The post thumbnail id.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var integer
	 */
	protected $post_featured_image;

	/**
	 * The post type singular name.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 * @return void
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;

		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_url = plugin_dir_url( __FILE__ );

		$this->path_ext = trailingslashit( $this->path_dir . 'metabox' );
		$this->path_tmpl = trailingslashit( $this->path_dir . 'metabox/tmpl' );

		$this->hooks();
	}

	/**
	 * The function to include Metabox extension files
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function includes() {

		require_once( $this->path_ext . 'sections/class-section-social-meta.php' );
	}

	/**
	 * The function to include Metabox custom Underscore.js templates
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $base The file basename: metabox, section, control.
	 * @param string $slug The file slug.
	 * @return string
	 */
	public function includes_section_tmpl( $base, $slug ) {

		if ( 'social-meta' === $slug ) {
			return $this->path_tmpl . 'section-social-meta.php' ;
		}

		return $base;
	}

	/**
	 * Run the hooks to register the metabox
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function hooks() {

		if ( ! $this->is_active() ) {
			return;
		}

		add_action( 'ninecodes_metabox_register', array( $this, 'includes' ), 10, 2 );
		add_action( 'ninecodes_metabox_register', array( $this, 'register_section_type' ), 10, 2 );
		add_action( 'ninecodes_metabox_register', array( $this, 'register_manager' ), 10, 2 );
		add_action( 'ninecodes_metabox_register', array( $this, 'register_section' ), 10, 2 );
		add_action( 'ninecodes_metabox_register', array( $this, 'register_setting' ), 10, 2 );
		add_action( 'ninecodes_metabox_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'ninecodes_metabox_pre_section_template', array( $this, 'includes_section_tmpl' ), 10, 2 );
	}

	/**
	 * Registers section types.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param object $manager Instance of the 'ButterBean' object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_section_type( $manager, $post_type ) {

		$manager->register_section_type( 'social-meta', __NAMESPACE__ . '\\Section_Social_Meta' );
	}

	/**
	 * Registers manager.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $manager Instance of the 'ButterBean' object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_manager( $manager, $post_type ) {

		$this->post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // WPCS: CSRF ok.
		$this->post_type = $post_type;

		$this->post_data();

		$manager->register_manager( $this->plugin->option->slug(), array(
			'label' => __( 'Social Media', 'ninecodes-social-manager' ),
			'post_type' => array_keys( $this->plugin->option->get_list( 'post_types' ) ),
			'context' => 'normal',
			'priority' => 'high',
			'capability' => 'publish_posts',
		) );
	}

	/**
	 * Register Metabox sections
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $manager Instance of the `ButterBean` object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_section( $manager, $post_type ) {

		$manager = $manager->get_manager( $this->plugin->option->slug() );

		$manager->register_section( 'meta', array(
			'type' => 'social-meta',
			'label' => __( 'Meta', 'ninecodes-social-manager' ),
			'icon' => 'dashicons-editor-code',
			'active_callback' => array( $this, 'is_meta_tags_enabled' ),
		) );

		$manager->register_section( 'button', array(
			'label' => __( 'Button', 'ninecodes-social-manager' ),
			'icon' => 'dashicons-thumbs-up',
			'active_callback' => array( $this, 'is_button_enabled' ),
		) );
	}

	/**
	 * Register Metabox settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $manager Instance of the `ButterBean` object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_setting( $manager, $post_type ) {

		$manager = $manager->get_manager( $this->plugin->option->slug() );

		$manager->register_setting( 'button_content', array(
			'type' => 'serialize',
			'default' => 1,
			'sanitize_callback' => 'absint',
		) );

		$manager->register_control( 'button_content', array(
			'type' => 'checkbox',
			'section' => 'button',
			'label' => __( 'Social Media Button on the Content', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Display the buttons that allow people to share, like, or save this %s in social media', 'ninecodes-social-manager' ), $this->post_type_label ),
			'active_callback' => array( $this, 'is_button_content_enabled' ),
		) );

		$manager->register_setting( 'button_image', array(
			'type' => 'serialize',
			'default' => 1,
			'sanitize_callback' => 'absint',
		) );

		$manager->register_control( 'button_image', array(
			'type' => 'checkbox',
			'section' => 'button',
			'label' => __( 'Social Media Button on the Image', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Display the social media buttons that allow people to share, like, or save images of this %s in social media', 'ninecodes-social-manager' ), $this->post_type_label ),
			'active_callback' => array( $this, 'is_button_image_enabled' ),
		) );

		$manager->register_setting( 'post_title', array(
			'type' => 'serialize',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$manager->register_control( 'post_title', array(
			'type' => 'text',
			'section' => 'meta',
			'label' => __( 'Title', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Set a customized title of this %s as it should appear within the social meta tag', 'ninecodes-social-manager' ), $this->post_type_label ),
			'attr' => array(
				'class' => 'widefat',
				'placeholder' => $this->post_title,
			),
		) );

		$manager->register_setting( 'post_excerpt', array(
			'type' => 'serialize',
			'sanitize_callback' => 'wp_kses',
		) );

		$manager->register_control( 'post_excerpt', array(
			'type' => 'textarea',
			'section' => 'meta',
			'label' => __( 'Description', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Set a one to two customized description of this %s that should appear within the social meta tag', 'ninecodes-social-manager' ), $this->post_type_label ),
			'attr' => array(
				'placeholder' => strip_shortcodes( $this->post_excerpt ),
				'class' => 'widefat',
			),
		) );

		$manager->register_setting( 'post_thumbnail', array(
			'type' => 'serialize',
			'sanitize_callback' => 'absint',
		) );

		$manager->register_control( 'post_thumbnail', array(
			'type' => 'image',
			'section' => 'meta',
			'label' => __( 'Image', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Set a custom image URL which should represent this %s within the social meta tag', 'ninecodes-social-manager' ),
			$this->post_type_label ),
			'size' => 'large',
		) );
	}

	/**
	 * The function method to retrive the post title, content, excerpt, and thumbnail id.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_post_field/
	 *
	 * @return void
	 */
	protected function post_data() {

		$this->post_title = $this->plugin->meta->get_post_title( $this->post_id );
		$this->post_excerpt = $this->plugin->meta->get_post_description( $this->post_id );
		$this->post_featured_image = $this->plugin->meta->get_post_image( $this->post_id );

		$post_type_objects = get_post_type_object( $this->post_type );
		$this->post_type_label = strtolower( $post_type_objects->labels->singular_name );
	}

	/**
	 * Function callback to enable the "Button" section
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_button_enabled() {

		$button_content = (bool) $this->is_button_content_enabled();
		$button_image = (bool) $this->is_button_image_enabled();

		return $button_content || $button_image;
	}

	/**
	 * Function callback to enable the "Button Content" control
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_button_content_enabled() {

		$status = (array) $this->plugin->helper->get_button_content_status();
		$post_type = isset( $status['post_type'] ) ? $status['post_type'] : array();

		return in_array( $this->post_type, $post_type, true );
	}

	/**
	 * Function callback to enable the "Button Image" control
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_button_image_enabled() {

		$status = (array) $this->plugin->helper->get_button_image_status();
		$post_type = isset( $status['post_type'] ) ? $status['post_type'] : array();

		return in_array( $this->post_type, $post_type, true );
	}

	/**
	 * The function utility to check if meta site is enabled
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_meta_tags_enabled() {
		return $this->plugin->helper->is_meta_tags_enabled();
	}

	/**
	 * The function utility to check if the meta box should be rendered
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_active() {

		if ( ! $this->is_button_enabled() && ! $this->is_meta_tags_enabled() ) {
			return false;
		}

		return true;
	}

	/**
	 * Function to enqueue scripts and styles
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$plugin_slug = $this->plugin->slug();
		$plugin_version = $this->plugin->version;

		wp_enqueue_script( "{$plugin_slug}-metabox", $this->path_url . 'assets/js/metabox.min.js', array( 'backbone', 'wp-util' ), $plugin_version, true );
	}
}
