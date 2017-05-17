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

		$this->path_dir = plugin_dir_path( dirname( __FILE__ ) );
		$this->path_url = plugin_dir_url( dirname( __FILE__ ) );

		$this->hooks();
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

		add_action( 'butterbean_register', array( $this, 'register_manager' ), -90, 2 );
		add_action( 'butterbean_register', array( $this, 'register_section' ), -90, 2 );
		add_action( 'butterbean_register', array( $this, 'register_setting' ), -90, 2 );
		add_action( 'butterbean_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_head-post.php', array( $this, 'admin_head_enqueues' ), 10 );
		add_action( 'admin_head-post-new.php', array( $this, 'admin_head_enqueues' ), 10 );
		add_action( 'admin_footer', array( $this, 'print_meta_preview_template' ) );
	}

	/**
	 * Registers manager.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $butterbean Instance of the 'ButterBean' object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_manager( $butterbean, $post_type ) {

		$this->post_data();
		$this->post_types( $post_type );

		$butterbean->register_manager( $this->plugin->option->slug(), array(
			'label' => __( 'Social Media', 'ninecodes-social-manager' ),
			'post_type' => array_keys( $this->plugin->option->get_list( 'post_types' ) ),
			'context' => 'normal',
			'priority' => 'low',
			'capability' => 'publish_posts',
		) );

		/**
		 * Fires after the metabox has been registered
		 *
		 * Allows developers to register custom sections, settings, and controls
		 * to the Social Media metabox.
		 *
		 * @param Object $metabox The metabox Object.
		 * @param string $post_type The name of the post where metabox is loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'ninecodes_social_manager_metabox', $butterbean->get_manager( $this->plugin->option->slug() ), $post_type );
	}

	/**
	 * Register Metabox sections
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $butterbean Instance of the `ButterBean` object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_section( $butterbean, $post_type ) {

		$manager = $butterbean->get_manager( $this->plugin->option->slug() );

		if ( $this->is_meta_tags_enabled() ) {
			$manager->register_section( 'meta', array(
				'label' => __( 'Meta', 'ninecodes-social-manager' ),
				'icon' => 'dashicons-editor-code',
			) );
		}

		$manager->register_section( 'button', array(
			'label' => __( 'Button', 'ninecodes-social-manager' ),
			'icon'  => 'dashicons-thumbs-up',
		) );
	}

	/**
	 * Register Metabox settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $butterbean Instance of the `ButterBean` object.
	 * @param string $post_type  The current Post Type slug.
	 * @return void
	 */
	public function register_setting( $butterbean, $post_type ) {

		$manager = $butterbean->get_manager( $this->plugin->option->slug() );
		$enable = $this->get_button_post_types(); // Get the list of post types where button image and content is enabled.

		/**
		 * Register the button content setting to the Button section
		 */
		if ( in_array( $post_type, $enable['button_content'], true ) ) {

			$manager->register_setting( 'button_content', array(
				'type' => 'serialize',
				'default' => 1,
				'sanitize_callback' => 'butterbean_validate_boolean',
			) );

			$manager->register_control( 'button_content', array(
				'type' => 'checkbox',
				'section' => 'button',
				'label' => __( 'Social Media Button on the Content', 'ninecodes-social-manager' ),
				 // translators: %s - the post type label i.e. Post, Page, etc.
				'description' => sprintf( __( 'Display the buttons that allow people to share, like, or save this %s in social media', 'ninecodes-social-manager' ), $this->post_type_label ),
			) );
		}

		if ( in_array( $post_type, $enable['button_image'], true ) ) {

			$manager->register_setting( 'button_image', array(
				'type' => 'serialize',
				'default' => 1,
				'sanitize_callback' => 'butterbean_validate_boolean',
			) );

			$manager->register_control( 'button_image', array(
				'type' => 'checkbox',
				'section' => 'button',
				'label' => __( 'Social Media Button on the Image', 'ninecodes-social-manager' ),
				 // translators: %s - the post type label i.e. Post, Page, etc.
				'description' => sprintf( __( 'Display the social media buttons that allow people to share, like, or save images of this %s in social media', 'ninecodes-social-manager' ), $this->post_type_label ),
			) );
		}

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
			'sanitize_callback' => array( $this, 'sanitize_absint' ),
		) );

		$manager->register_control( 'post_thumbnail', array(
			'type' => 'image',
			'section' => 'meta',
			'label' => __( 'Image', 'ninecodes-social-manager' ),
			// translators: %s - the post type label i.e. Post, Page, etc.
			'description' => sprintf( __( 'Set a custom image URL which should represent this within the social meta tag', 'ninecodes-social-manager' ),
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

		$this->post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // WPCS: CSRF ok.

		$this->post_title = get_post_field( 'post_title', $this->post_id );
		$this->post_content = get_post_field( 'post_content', $this->post_id );

		$featured_image_id = get_post_thumbnail_id( $this->post_id );
		$featured_image = wp_get_attachment_image_src( $featured_image_id, 'large' );

		$this->post_featured_image = array(
			'id' => $featured_image_id,
			'src' => $featured_image[0],
			'width' => $featured_image[1],
			'height' => $featured_image[2],
		);

		$this->post_excerpt = wp_trim_words( strip_shortcodes( $this->post_content ), 30, '...' );
	}

	/**
	 * The function method to retrieve the post type singular name.
	 *
	 * This function exist because the post type slug or name can have special
	 * character like underscore or dash.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $post_type The post type slug / name.
	 * @return void
	 */
	protected function post_types( $post_type ) {

		$objects = get_post_type_object( $post_type );
		$this->post_type_label = strtolower( $objects->labels->singular_name );
	}

	/**
	 * The function utility to get selected post types to display social media buttons.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_button_post_types() {

		$button_content = (array) $this->plugin->helper->get_button_content_status();
		$button_image = (array) $this->plugin->helper->get_button_image_status();

		return array(
			'button_content' => isset( $button_content['post_type'] ) ? $button_content['post_type'] : array(),
			'button_image' => isset( $button_image['post_type'] ) ? $button_image['post_type'] : array(),
		);
	}

	/**
	 * The function utility to check if meta site is enabled
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_meta_tags_enabled() {
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

		$post_types = $this->get_button_post_types();
		$post_types = array_merge( $post_types['button_content'], $post_types['button_image'] );

		if ( empty( $post_types ) && ! $this->is_meta_tags_enabled() ) {
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

		wp_enqueue_style( "{$plugin_slug}-metabox", $this->path_url . 'css/metabox.css' );
		wp_enqueue_script( "{$plugin_slug}-metabox-scripts", $this->path_url . 'js/metabox.min.js', array( 'jquery', 'underscore', 'backbone' ), $plugin_version, true );
		wp_add_inline_script( "{$plugin_slug}-metabox-scripts", "var nineCodesSocialManagerAPI = {
			post: {
				id:\"{$this->post_id}\",
				title:\"{$this->post_title}\",
				excerpt:\"{$this->post_excerpt}\",
				featuredImage:\"{$this->post_featured_image['src']}\"
			}
		}" );
	}

	/**
	 * Function to render the HTML markup that previews the meta value
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function print_meta_preview_template() {
		if ( $this->is_meta_tags_enabled() ) : ?>
		<script type="text/html" id="tmpl-butterbean-control-meta-preview">
		<div id="butterbean-control-meta-preview" class="butterbean-control butterbean-control-static">
			<button type="button" id="button-display-meta-preview" class="button button-large button-preview-meta widefat"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Display Preview', 'ninecodes-social-manager' ); ?></button>
			<div class="meta-preview meta-preview--facebook">
				<div class="meta-preview__image"><img src="{{ data.featuredImage }}"></div>
				<div class="meta-preview-summary">
					<div class="meta-preview-summary__title">{{ data.title }}</div>
					<div class="meta-preview-summary__description">{{ data.excerpt }}</div>
					<ul class="meta-preview-summary__footer">
						<# if ( data.siteName ) { #><li class="meta-preview-summary__site-name>{{ data.siteName }}</li><# } #>
						<# if ( data.authorName ) { #><li class="meta-preview-summary__author">{{ data.authorName }}</li><# } #>
					</ul>
				</div>
			</div>
		</div>
		</script>
	<?php
		endif;
	}

	/**
	 * Print internal styles.
	 *
	 * The styles will make the metabox appearance (e.g. icon size, font size, and color)
	 * consistent following with the WooCommerce metabox styling where ButterBean derived
	 * the inspiration for the metabox UI.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function admin_head_enqueues() {
	?>
		<style id="butterbean-styles">
			.butterbean-manager .butterbean-label { font-weight: 600 }
			.butterbean-manager .butterbean-description { color: #555; }
			.butterbean-manager .butterbean-nav .dashicons { font-size: 1em }
			.butterbean-manager .butterbean-nav li[aria-selected=true] a { font-weight: 400 }
			.butterbean-manager .butterbean-control-select-group select,
			.butterbean-manager .butterbean-control-select select { min-width: 35% }
		</style>
	<?php }
}
