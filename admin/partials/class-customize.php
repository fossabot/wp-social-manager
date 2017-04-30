<?php
/**
 * Admin: Customizer class
 *
 * @package SocialManager
 * @subpackage Admin\Customizer
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to register custom fields in the Customizer.
 */
class Customize {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( __FILE__ );
		$this->path_url = plugin_dir_url( __FILE__ );

		$this->hooks();
	}

	/**
	 * Load dependencies.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function requires() {
		require_once( $this->path_dir . 'customize/class-customize-control-radio-image.php' );
	}

	/**
	 * Run Actions and Filters.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'customize_register', array( $this, 'requires' ) );
		add_action( 'customize_register', array( $this, 'register_panel' ) );
		add_action( 'customize_register', array( $this, 'register_section' ) );
		add_action( 'customize_register', array( $this, 'register_setting' ) );
	}

	/**
	 * This hooks into 'customize_register' (available as of WP 3.4) and allows
	 * you to add new sections and controls to the Theme Customize screen.
	 *
	 * Note: To enable instant preview, we have to actually write a bit of custom
	 * javascript. See live_preview() for more.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @see add_action('customize_register', $func ).
	 * @param \WP_Customize_Manager $wp_customize The Customizer instance.
	 *
	 * @return void
	 */
	public function register_panel( $wp_customize ) {

		$wp_customize->add_panel( $this->plugin->option_slug,
			array(
				'capability' => 'edit_theme_options',
				'title' => __( 'Social Media', 'ninecodes-social-manager' ),
				'priority' => 210,
				'active_callback' => array( $this, 'panel_active_callback' ),
			)
		);
	}

	/**
	 * Register the section.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @see add_action('customize_register', $func ).
	 * @param \WP_Customize_Manager $wp_customize The Customizer instance.
	 *
	 * @return void
	 */
	public function register_section( $wp_customize ) {

		$sections = apply_filters( 'ninecodes_social_manager_customize',
			array(
				'style' => array(
					'title' => __( 'Styles', 'ninecodes-social-manager' ),
				),
		), 'section' );

		foreach ( (array) $sections as $section => $args ) {

			$args = wp_parse_args( $args, array(
				'panel' => $this->plugin->option_slug,
			) );

			$wp_customize->add_section( $section, $args );
		}
	}

	/**
	 * Register the control.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @see add_action('customize_register', $func ).
	 * @param \WP_Customize_Manager $wp_customize The Customizer instance.
	 *
	 * @return void
	 */
	public function register_setting( $wp_customize ) {

		$wp_customize->register_control_type( __NAMESPACE__ . '\\Customize_Control_Radio_Image' );

		// Register Setting: Button Styles.
		$wp_customize->add_setting("{$this->plugin->option_slug}_style_button_content", array(
			'default' => 'rounded',
		));

		// Register Control: Button Styles.
		$wp_customize->add_control(
			new Customize_Control_Radio_Image(
				$wp_customize,
				"{$this->plugin->option_slug}_style_button_content",
				array(
					'label' => __( 'Button Content', 'ninecodes-social-manager' ),
					'description' => __( 'Select the style of the social media button in the content.', 'ninecodes-social-manager' ),
					'section' => 'style',
					'choices' => apply_filters( 'ninecodes_social_manager_options', array(
						'rounded' => array(
							'label' => __( 'Rounded', 'ninecodes-social-manager' ),
							'url' => $this->path_url . 'customize/img/dummy.png',
						),
						'square' => array(
							'label' => __( 'Square', 'ninecodes-social-manager' ),
							'url'   => $this->path_url . 'customize/img/dummy.png',
						),
						'circular' => array(
							'label' => __( 'Circular', 'ninecodes-social-manager' ),
							'url' => $this->path_url . 'customize/img/dummy.png',
						),
					), 'style_button_content' ),
				)
			)
		);

		$wp_customize->add_setting("{$this->plugin->option_slug}_style_button_image", array(
			'default' => 'rounded',
		));

		$wp_customize->add_control(
			new Customize_Control_Radio_Image(
				$wp_customize,
				"{$this->plugin->option_slug}_style_button_image",
				array(
					'label' => __( 'Button Image', 'ninecodes-social-manager' ),
					'description' => __( 'Select the style of social media button on the images.', 'ninecodes-social-manager' ),
					'section' => 'style',
					'choices' => apply_filters( 'ninecodes_social_manager_options', array(
						'square' => array(
							'label' => __( 'Square', 'ninecodes-social-manager' ),
							'url'   => $this->path_url . 'customize/img/dummy.png',
						),
						'rounded' => array(
							'label' => __( 'Rounded', 'ninecodes-social-manager' ),
							'url' => $this->path_url . 'customize/img/dummy.png',
						),
						'circular' => array(
							'label' => __( 'Circular', 'ninecodes-social-manager' ),
							'url' => $this->path_url . 'customize/img/dummy.png',
						),
					), 'style_button_image' ),
				)
			)
		);
	}

	/**
	 * Method to check where the Panel should be active.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function panel_active_callback() {

		$buttons_image = $this->plugin->get_option( 'button_image' );

		$post_types_content = $this->plugin->get_option( 'button_content', 'post_type' );
		$post_types_image = isset( $buttons_image['enable'] ) && 'on' === $buttons_image['enable'] ? $buttons_image['post_type'] : array();

		$post_types = array_merge( $post_types_content, $post_types_image );

		return is_singular( array_keys( array_filter( $post_types ) ) );
	}
}
