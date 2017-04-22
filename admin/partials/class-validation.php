<?php
/**
 * Admin: SettingsValidation class
 *
 * @package SocialManager
 * @subpackage Admin\Validation
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class Validation, as the name said, is used for validating inputs
 * in the setting page.
 *
 * @since 1.0.0
 */
class Validation {

	/**
	 * Function to sanitize the username inputs in "Profiles" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $input Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_profile( $input ) {

		/**
		 * Return early, if the value is not an array or the value
		 * is not an Associative array.
		 */
		if ( ! is_array( $input ) || ! is_array_associative( $input ) ) {
			return array();
		}

		$output = array();
		$profiles = Options::social_profiles();

		foreach ( $input as $key => $username ) {

			$slug = sanitize_key( $key );

			if ( array_key_exists( $slug, $profiles ) ) {
				$output[ $slug ] = is_string( $username ) ? sanitize_text_field( $username ) : '';
			}
		}

		return $output;
	}

	/**
	 * Function to sanitize the inputs in the "Buttons Content" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_button_content( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'view' => '',
			'placement' => '',
			'heading' => '',
			'include' => array(),
			'post_type' => array(),
		) );

		$inputs['view'] = $this->validate_radio( $inputs['view'], Options::button_views() );
		$inputs['placement'] = $this->validate_radio( $inputs['placement'], Options::button_placements() );
		$inputs['heading'] = sanitize_text_field( $inputs['heading'] );

		$inputs['include'] = $this->validate_multicheckbox( $inputs['include'], Options::button_sites( 'content' ) );
		$inputs['post_type'] = $this->validate_multicheckbox( $inputs['post_type'], Options::post_types() );

		return $inputs;
	}

	/**
	 * Function to sanitize the inputs in the "Buttons Image" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_button_image( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enable' => '',
			'view' => '',
			'post_type' => array(),
			'include' => array(),
		) );

		$inputs['enable'] = $this->validate_checkbox( $inputs['enable'] );
		$inputs['view'] = $this->validate_radio( $inputs['view'], Options::button_views() );
		$inputs['post_type'] = $this->validate_multicheckbox( $inputs['post_type'], Options::post_types() );
		$inputs['include'] = $this->validate_multicheckbox( $inputs['include'], Options::button_sites( 'image' ) );

		return $inputs;
	}

	/**
	 * Function to sanitize the inputs in the "Meta" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_meta_site( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enable' => '',
			'name' => '',
			'description' => '',
			'image' => null,
		) );

		$inputs['enable'] = $this->validate_checkbox( $inputs['enable'] );
		$inputs['name'] = sanitize_text_field( $inputs['name'] );
		$inputs['description'] = sanitize_text_field( $inputs['description'] );
		$inputs['image'] = absint( $inputs['image'] );

		return $inputs;
	}

	/**
	 * Function to sanitize the inputs in the "Advanced" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_advanced( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enable_stylesheet' => '',
		) );

		$inputs['enable_stylesheet'] = $this->validate_checkbox( $inputs['enable_stylesheet'] );

		return $inputs;
	}

	/**
	 * Function to sanitize the inputs in the "Modes" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	final public function setting_mode( $inputs ) {

		$inputs['button_mode'] = $this->validate_radio( $inputs['button_mode'], Options::button_modes() );
		$inputs['link_mode'] = $this->validate_radio( $inputs['link_mode'], Options::link_modes() );

		return $inputs;
	}

	/**
	 * Utility function to sanitize a redio input.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $input Unsanitized inputs being saved.
	 * @param array  $options The list of options set in the setting.
	 * @return string Sanitized input.
	 */
	final public function validate_radio( $input, $options ) {

		if ( array_key_exists( $input, $options ) ) {
			return sanitize_key( $input );
		} else {
			return sanitize_key( key( $options ) ); // Return the first key in the options.
		}
	}

	/**
	 * Utility function to validate a checkbox input.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $input Ideally it should an empty string or 'on'.
	 * @return string Return 'on' if the input is checked, otherwise an empty string.
	 */
	final public function validate_checkbox( $input ) {
		$check = (bool) $input;
		return $check ? 'on' : false;
	}

	/**
	 * Utility function to sanitize multiple selection inputs.
	 *
	 * Typically the inputs are coming from a multicheckbox or multiselect input type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $inputs Unsanitized inputs being saved.
	 * @param string $options Options reference to check against the incoming input.
	 * @return array Sanitized inputs.
	 */
	final public function validate_multicheckbox( array $inputs, $options ) {

		$selection = array();

		foreach ( $options as $key => $label ) {
			$key = sanitize_key( $key );
			$selection[ $key ] = key_exists( $key, $inputs ) ? 'on' : false;
		}

		return $selection;
	}
}
