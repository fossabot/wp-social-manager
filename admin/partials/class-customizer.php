<?php
/**
 * Admin: Customizer class
 *
 * @package SocialManager
 * @subpackage Admin\Fields
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

		$this->path_dir = plugin_dir_path( __FILE__ );

		$this->plugin = $plugin;
		$this->plugin_slug = $plugin->get_slug();
		$this->option_slug = $plugin->get_opts();

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
		$wp_customize->register_control_type( __NAMESPACE__ . '\\Customize_Control_Radio_Image' );

		// Register Panel: Social Media.
		$wp_customize->add_panel('ninecodes-social-manager', array(
			'capability' => 'edit_theme_options',
			'title' => esc_html__( 'Social Media', 'ninecodes-social-manager' ),
			'priority' => 210,
		));

		// Register Section in the Panel: Buttons.
		$wp_customize->add_section('buttons', array(
			'title' => esc_html__( 'Buttons', 'ninecodes-social-manager' ),
			'panel' => 'ninecodes-social-manager',
		));

		// Register Setting: Button Styles.
		$wp_customize->add_setting('buttons_style', array(
			'default' => 'content-sidebar',
		));

		// Register Control: Button Styles.
		$wp_customize->add_control(
			new Customize_Control_Radio_Image(
				$wp_customize,
				'buttons_style',
				array(
					'label' => esc_html__( 'Styles', 'ninecodes-social-manager' ),
					'section' => 'buttons',
					'choices' => apply_filters( 'ninecodes_social_manager_options', array(
						'colored' => array(
							'label' => esc_html__( 'Colored', 'ninecodes-social-manager' ),
							'url'   => plugin_dir_url( __DIR__ ) . 'img/buttons-style-square.png',
						),
						'square' => array(
							'label' => esc_html__( 'Square', 'ninecodes-social-manager' ),
							'url'   => plugin_dir_url( __DIR__ ) . 'img/buttons-style-square.png',
						),
						'rounded' => array(
							'label' => esc_html__( 'Rounded', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/buttons-style-rounded.png',
						),
						'circular' => array(
							'label' => esc_html__( 'Circular', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/buttons-style-rounded.png',
						),
						'skeuomorphic' => array(
							'label' => esc_html__( 'Skeuomorphic', 'ninecodes-social-manager' ),
							'url' => plugin_dir_url( __DIR__ ) . 'img/buttons-style-skeuomorphic.png',
						),
					), 'button_styles' ),
				)
			)
		);
	}
}
