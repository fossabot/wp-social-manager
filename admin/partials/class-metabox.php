<?php

namespace XCo\WPSocialManager;

/**
 * Main ButterBean class.  Runs the show.
 *
 * @since  1.0.0
 * @access public
 */
final class SocialMetaBox {

	/**
	 * [$screen description]
	 * @var [type]
	 */
	protected $screen;

	/**
	 * [$post_id description]
	 * @var [type]
	 */
	protected $post_id;

	/**
	 * [$post_title description]
	 * @var [type]
	 */
	protected $post_title;

	/**
	 * [$post_content description]
	 * @var [type]
	 */
	protected $post_content;

	/**
	 * [$post_excerpt description]
	 * @var [type]
	 */
	protected $post_excerpt;

	/**
	 * [$post_thumbnail description]
	 * @var [type]
	 */
	protected $post_thumbnail;

	/**
	 * [$post_type description]
	 * @var [type]
	 */
	protected $post_type;

	/**
	 * [includes description]
	 * @return [type] [description]
	 */
	public function requires() {

		$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		require_once( $this->plugin_dir . 'butterbean/butterbean.php' );
		require_once( $this->plugin_dir . 'butterbean-extend/butterbean-extend.php' );
	}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function hooks() {

		// Load `ButterBean` library.
		add_action( 'plugins_loaded', array( $this, 'requires' ) );

		// Register managers.
		add_action( 'butterbean_register', array( $this, 'register_manager' ), -90, 2 );

		// Register sections, settings, and controls.
		add_action( 'butterbean_register', array( $this, 'register_section_sharing' ), -90, 2 );
		add_action( 'butterbean_register', array( $this, 'register_section_meta' ), -90, 2 );
	}

	/**
	 * [register_manager description]
	 * @param  [type] $butterbean [description]
	 * @param  [type] $post_type  [description]
	 * @return [type]             [description]
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
				'label' => esc_html__( 'Social', 'wp-social-manager' ),
				'post_type' => array( 'post', 'page', 'product' ),
				'context'   => 'normal',
				'priority'  => 'high'
			)
		);
	}

	/**
	 * Registers managers, sections, controls, and settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $butterbean  Instance of the `ButterBean` object.
	 * @param  string  $post_type
	 * @return void
	 */
	public function register_section_sharing( $butterbean, $post_type ) {

		// Get our custom manager object.
		$manager = $butterbean->get_manager( 'wp_social_manager' );

		// Register a section.
		$manager->register_section(
			'buttons',
			array(
				'label' => 'Buttons',
				'icon'  => 'dashicons-thumbs-up'
			)
		);

		// Register a setting.
		$manager->register_setting(
			'buttons_content',
			array(
				'type' => 'serialize',
				'default' => 1,
				'sanitize_callback' => 'butterbean_validate_boolean'
			)
		);
		$manager->register_control(
			'buttons_content',
			array(
				'type' => 'checkbox',
				'section' => 'buttons',
				'label' => 'Content Social Media Buttons',
				'description' => "Display the buttons that allow people to share, like, or save this {$this->post_type} in social media"
			)
		);

		// Register a setting.
		$manager->register_setting(
			'buttons_image',
			array(
				'type' => 'serialize',
				'default' => 1,
				'sanitize_callback' => 'butterbean_validate_boolean'
			)
		);
		$manager->register_control(
			'buttons_image',
			array(
				'type' => 'checkbox',
				'section' => 'buttons',
				'label' => 'Image Social Media Buttons',
				'description' => "Display the social media buttons that allow people to share, like, or save images of this {$this->post_type} in social media"
			)
		);
	}

	/**
	 * [register_section_meta description]
	 * @param  [type] $butterbean [description]
	 * @param  [type] $post_type  [description]
	 * @return [type]             [description]
	 */
	public function register_section_meta( $butterbean, $post_type ) {

		// Get our custom manager object.
		$manager = $butterbean->get_manager( 'wp_social_manager' );

		$manager->register_section(
			'meta_tags',
			array(
				'label' => 'Metas',
				'icon'  => 'dashicons-editor-code'
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
					'placeholder' => $this->post_title
				),
			)
		);

		$manager->register_setting(
			'post_title',
			array(
				'type' => 'serialize',
				'sanitize_callback' => 'sanitize_text_field'
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
					'class' => 'widefat'
				)
			)
		);

		$manager->register_setting(
			'post_excerpt',
			array(
				'type' => 'serialize',
				'sanitize_callback' => 'wp_kses'
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
				'size'        => 'large'
			)
		);
		$manager->register_setting(
			'post_thumbnail',
			array(
				'type' => 'serialize',
				'sanitize_callback' => array( $this, 'sanitize_absint' )
			)
		);
	}

	/**
	 * [sanitize_absint description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function sanitize_absint( $value ) {
		return $value && is_numeric( $value ) ? absint( $value ) : '';
	}

	/**
	 * [load_post description]
	 * @return [type] [description]
	 */
	protected function load_post() {

		$this->post_id = isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : 0;

		$this->post_title = get_post_field( 'post_title', $this->post_id );
		$this->post_content = get_post_field( 'post_content', $this->post_id );
		$this->post_thumbnail = get_post_thumbnail_id( $this->post_id );

		$this->post_excerpt = wp_trim_words( $this->post_content, 30, '...' );
	}

	/**
	 * [load_post_type description]
	 * @param  [type] $post_type [description]
	 * @return [type]            [description]
	 */
	protected function load_post_type( $post_type ) {

		$objects = get_post_type_object( $post_type );

		$this->post_type = strtolower( $objects->labels->singular_name );
	}

	/**
	 * [admin_head_enqueues description]
	 * @param  [type] $screen [description]
	 * @return [type]         [description]
	 */
	public function admin_head_enqueues( $screen ) { ?>
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
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
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

SocialMetaBox::get_instance();