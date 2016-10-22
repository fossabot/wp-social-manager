<?php
/**
 * Admin: SettingsValidation class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Admin\Validation
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to validate setting inputs.
 *
 * @since 1.0.0
 */
class Validation {

	/**
	 * The function method to sanitize username inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  mixed $inputs Unsanitized inputs being saved.
	 * 						 Sometimes in a form of an array or null.
	 * @return array         Sanitized inputs.
	 */
	final public function setting_profiles( $inputs ) {

		foreach ( $inputs as $slug => $username ) {

			$inputs[ $slug ] = sanitize_text_field( $username );

			if ( 2 >= strlen( $inputs[ $slug ] ) && 0 !== strlen( $inputs[ $slug ] ) ) :

				$inputs[ $slug ] = '';

				add_settings_error( $slug,
					'social-username-length',
					esc_html__( 'A username generally should contains at least 3 characters (or more).', 'wp-social-manager' ),
					'error'
				);
			endif;
		}

		return $inputs;
	}

	/**
	 * The function method to sanitize the "Buttons Content" inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $inputs Unsanitized inputs being saved.
	 * @return array         Sanitized inputs.
	 */
	final public function setting_buttons_content( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'view' => '',
			'placement' => '',
			'heading' => '',
			'includes' => array(),
			'post_types' => array(),
		) );

		$inputs['heading'] = sanitize_text_field( $inputs['heading'] );

		$inputs['view'] = $this->validate_radio( $inputs['view'], Options::button_views() );
		$inputs['placement'] = $this->validate_radio( $inputs['placement'], Options::button_placements() );

		$inputs['post_types'] = $this->validate_multicheckbox( $inputs['post_types'], Options::post_types() );
		$inputs['includes'] = $this->validate_multicheckbox( $inputs['includes'], Options::button_sites( 'content' ) );

		return $inputs;
	}

	/**
	 * The function method to sanitize the "Buttons Image" inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $inputs Unsanitized inputs being saved.
	 * @return array         Inputs sanitized.
	 */
	final public function setting_buttons_image( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'view' => '',
			'post_types' => array(),
			'includes' => array(),
		) );

		$inputs['view'] = $this->validate_radio( $inputs['view'], Options::button_views() );

		$inputs['post_types'] = $this->validate_multicheckbox( $inputs['post_types'], Options::post_types() );
		$inputs['includes'] = $this->validate_multicheckbox( $inputs['includes'], Options::button_sites( 'image' ) );

		return $inputs;
	}

	/**
	 * The function method to sanitize the "Metas" inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $inputs Unsanitized inputs being saved.
	 * @return array         Inputs sanitized
	 */
	final public function setting_site_metas( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'name' => '',
			'description' => '',
			'image' => null,
		) );

		$inputs['enabled'] = ( 'on' === $inputs['enabled'] ) ? 'on' : '';
		$inputs['name'] = sanitize_text_field( $inputs['name'] );
		$inputs['description'] = sanitize_text_field( $inputs['description'] );
		$inputs['image'] = absint( $inputs['image'] );

		return $inputs;
	}

	/**
	 * The function method to sanitize the "Advanced" inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|null $inputs Unsanitized inputs being saved.
	 * @return array        	 Sanitized inputs.
	 */
	final public function setting_advanced( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enable_stylesheet' => false,
		) );

		$inputs['enable_stylesheet'] = ( 'on' === $inputs['enable_stylesheet'] ) ? 'on' : '';

		return $inputs;
	}

	/**
	 * The function method to sanitize the Mode section in the "Advanced" tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|null $inputs Unsanitized inputs being saved.
	 * @return array        	 Sanitized Inputs.
	 */
	final public function setting_modes( $inputs ) {

		$inputs['buttons_mode'] = $this->validate_radio( $inputs['buttons_mode'], Options::buttons_modes() );
		$inputs['link_mode'] = $this->validate_radio( $inputs['link_mode'], Options::link_modes() );

		return $inputs;
	}

	/**
	 * The utility function to sanitize a single selection input.
	 *
	 * A single selection input typically is delivered through the checkbox
	 * or the radio input type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $input 	Unsanitized inputs being saved.
	 * @param  array  $options  The list of options set in the setting.
	 * @return string        	Sanitized input.
	 */
	final protected function validate_radio( $input, $options ) {

		if ( array_key_exists( $input, $options ) ) {
			return sanitize_key( $input );
		} else {
			return sanitize_key( key( $options ) ); // Return the first key in the options.
		}
	}

	/**
	 * The utility function to sanitize multiple selection inputs.
	 *
	 * Typically the inputs are coming from a multicheckbox or multiselect input type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array  $inputs 	Unsanitized inputs being saved.
	 * @param  string $options  Key reference of the preset / acceptable selection to validate
	 * 							against the incoming input.
	 * @return array          	Inputs sanitized
	 */
	final protected function validate_multicheckbox( array $inputs, $options ) {

		$selection = array();

		foreach ( $inputs as $key => $value ) {

			$selection[] = sanitize_key( $key );

			if ( ! array_key_exists( $key, $options[ $key ] ) ) {
				unset( $inputs[ $key ] );
			}
		}

		return $selection;
	}
}
