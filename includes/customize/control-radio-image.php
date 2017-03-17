<?php
/**
 * Customizer Radio Image Control
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package SocialManager
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \WP_Customize_Control;

add_action( 'customize_register', function() {

	/**
	 * The class to register custom, Radio Image, control to Customizer
	 *
	 * @since 1.2.0
	 */
	class Customize_Control_Radio_Image extends WP_Customize_Control {

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @var string
		 */
		protected $plugin_slug = 'ninecodes-social-manager';

		/**
		 * The type of customize control being rendered.
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    string
		 */
		public $type = 'radio_image';

		/**
		 * Loads the jQuery UI Button script and custom scripts/styles.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue() {

			wp_enqueue_script( 'jquery-ui-button' );

			wp_enqueue_script( "{$plugin_slug}-customize-controls", plugin_dir_url( __FILE__ ) . 'js/customize-controls.js', array( 'jquery' ) );
			wp_enqueue_style( "{$plugin_slug}-customize-controls", plugin_dir_url( __FILE__ ) . 'css/customize-controls.css' );
		}

		/**
		 * Add custom JSON parameters to use in the JS template.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function to_json() {
			parent::to_json();

			// We need to make sure we have the correct image URL.
			foreach ( $this->choices as $value => $args ) {
				$this->choices[ $value ]['url'] = esc_url( $args['url'] );
			}

			$this->json['choices'] = $this->choices;
			$this->json['link']    = $this->get_link();
			$this->json['value']   = $this->value();
			$this->json['id']      = $this->id;
		}

		/**
		 * Underscore JS template to handle the control's output.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function content_template() {
		?>
			<# if ( ! data.choices ) {
				return;
			} #>

			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>

			<div class="buttonset">

				<# for ( key in data.choices ) { #>

					<input type="radio" value="{{ key }}" name="_customize-{{ data.type }}-{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( key === data.value ) { #> checked="checked" <# } #> />

					<label for="{{ data.id }}-{{ key }}">
						<span class="screen-reader-text">{{ data.choices[ key ]['label'] }}</span>
						<img src="{{ data.choices[ key ]['url'] }}" alt="{{ data.choices[ key ]['label'] }}" />
					</label>
				<# } #>

			</div><!-- .buttonset -->
		<?php }
	}
}, -30 );
