<?php
/**
 * Admin: Customizer class
 *
 * @package SocialManager
 * @subpackage Admin\Customizer
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to register custom fields in the Customizer.
 */
class Customizer {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
		$this->path_dir = plugin_dir_path( __FILE__ );

		add_action( 'customize_register', array( $this, 'customize_social_manager' ), 20 );
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
	public function customize_social_manager( $wp_customize ) {

		// Register the radio image control class as a JS control type.
		$wp_customize->register_control_type( __NAMESPACE__ . '\\Customizer\Control_Radio_Image' );

		// Register Panel: Social Media.
		$wp_customize->add_panel('ninecodes-social-manager', array(
			'capability' => 'edit_theme_options',
			'title' => esc_html__( 'Social Media', 'ninecodes-social-manager' ),
			'priority' => 210,
			'active_callback' => array( $this, 'panel_active_callback' ),
		));

		// Register Section in the Panel: Buttons.
		$wp_customize->add_section('button', array(
			'title' => esc_html__( 'Buttons', 'ninecodes-social-manager' ),
			'panel' => 'ninecodes-social-manager',
		));

		// Register Setting: Button Styles.
		$wp_customize->add_setting("{$this->plugin->option_slug}_button_style", array(
			'default' => 'default',
		));

		// Register Control: Button Styles.
		$wp_customize->add_control(
			new Customizer\Control_Radio_Image(
				$wp_customize,
				"{$this->plugin->option_slug}_button_style",
				array(
					'label' => esc_html__( 'Style', 'ninecodes-social-manager' ),
					'description' => esc_html__( 'Select one the following options to change the social media buttons style', 'ninecodes-social-manager' ),
					'section' => 'button',
					'choices' => apply_filters( 'ninecodes_social_manager_options', array(
						'default' => array(
							'label' => esc_html__( 'Default', 'ninecodes-social-manager' ),
							'url'   => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
						'colored' => array(
							'label' => esc_html__( 'Colored', 'ninecodes-social-manager' ),
							'url'   => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
						'square' => array(
							'label' => esc_html__( 'Square', 'ninecodes-social-manager' ),
							'url'   => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
						'rounded' => array(
							'label' => esc_html__( 'Rounded', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
						'circular' => array(
							'label' => esc_html__( 'Circular', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
						'skeuomorphic' => array(
							'label' => esc_html__( 'Skeuomorphic', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/dummy.png',
						),
					), 'button_styles' ),
				)
			)
		);
	}

	/**
	 * Method to check where the Panel should be active.
	 *
	 * @return [type] [description]
	 */
	public function panel_active_callback() {

		$buttons_image = $this->plugin->get_option( 'buttons_image' );

		$post_types_content = $this->plugin->get_option( 'buttons_content', 'post_types' );
		$post_types_image = isset( $buttons_image['enabled'] ) && 'on' === $buttons_image['enabled'] ? $buttons_image['post_types'] : array();

		$post_types = array_merge( $post_types_content, $post_types_image );

		return is_singular( array_keys( array_filter( $post_types ) ) );
	}
}
