<?php
/**
 * Admin: SettingsValidation class
 *
 * @package SocialManager
 * @subpackage Admin\Validation
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class Validation, as the name said, is used for validating inputs
 * in the setting page.
 *
 * @since 1.0.0
 */
final class Validation {

	/**
	 * Function to sanitize the username inputs in "Profiles" section.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $input Unsanitized inputs being saved.
	 * @return array Sanitized inputs.
	 */
	public function setting_profile( $input ) {

		/**
		 * Return early, if the value is not an array or the value
		 * is not an Associative array.
		 */
		if ( ! is_array( $input ) || ! is_array_associative( $input ) ) {
			return array();
		}

		$output = array();
		$profiles = Options::get_list( 'social_profiles' );

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
	public function setting_button_content( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'view' => '',
			'placement' => '',
			'heading' => '',
			'include' => array(),
			'post_type' => array(),
		) );

		$inputs['view'] = $this->validate_selection( $inputs['view'], Options::get_list( 'button_views' ) );
		$inputs['placement'] = $this->validate_selection( $inputs['placement'], Options::get_list( 'button_placements' ) );
		$inputs['style'] = $this->validate_selection( $inputs['style'], Options::get_list( 'button_styles', array( 'content' ) ) );

		$inputs['heading'] = sanitize_text_field( $inputs['heading'] );

		$inputs['include'] = $this->validate_include_sites( $inputs['include'], Options::get_list( 'button_sites', array( 'content' ) ) );
		$inputs['post_type'] = $this->validate_multicheckbox( $inputs['post_type'], Options::get_list( 'post_types' ) );

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
	public function setting_button_image( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enable' => '',
			'view' => '',
			'post_type' => array(),
			'include' => array(),
		) );

		$inputs['enable'] = $this->validate_checkbox( $inputs['enable'] );
		$inputs['view'] = $this->validate_selection( $inputs['view'], Options::get_list( 'button_views' ) );
		$inputs['style'] = $this->validate_selection( $inputs['style'], Options::button_styles( 'image' ) );

		$inputs['post_type'] = $this->validate_multicheckbox( $inputs['post_type'], Options::post_types() );
		$inputs['include'] = $this->validate_include_sites( $inputs['include'], Options::button_sites( 'image' ) );

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
	public function setting_meta_site( $inputs ) {

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
	public function setting_advanced( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'stylesheet' => '',
		) );

		$inputs['stylesheet'] = $this->validate_checkbox( $inputs['stylesheet'] );

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
	public function setting_mode( $inputs ) {

		$inputs['button_mode'] = $this->validate_selection( $inputs['button_mode'], Options::button_modes() );
		$inputs['link_mode'] = $this->validate_selection( $inputs['link_mode'], Options::link_modes() );

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
	public function validate_selection( $input, $options ) {

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
	public function validate_checkbox( $input ) {
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
	public function validate_multicheckbox( $inputs, $options ) {

		$selection = array();

		foreach ( $options as $key => $label ) {
			$key = sanitize_key( $key );
			$selection[ $key ] = key_exists( $key, $inputs ) ? 'on' : false;
		}

		return $selection;
	}

	/**
	 * Utility function to sanitize multiple selection inputs for "Button to include" option.
	 *
	 * "Button to include" option has two inputs namely the checkbox and the label text input.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array  $inputs Unsanitized inputs being saved.
	 * @param string $options Options reference to check against the incoming input.
	 * @return array Sanitized inputs.
	 */
	public function validate_include_sites( $inputs, $options ) {

		$selection = array();

		foreach ( $options as $key => $data ) {

			$site = sanitize_key( $key );

			if ( ! key_exists( $site, $inputs ) ) {
				$selection[ $site ]['enable'] = 'on';
				$selection[ $site ]['label'] = $data['label'];
			} else {
				$selection[ $site ]['enable'] = isset( $inputs[ $site ]['enable'] ) ? 'on' : false;

				if ( isset( $inputs[ $site ]['label'] ) && ! empty( $inputs[ $site ]['label'] ) ) {
					$selection[ $site ]['label'] = sanitize_text_field( $inputs[ $site ]['label'] );
				} else {
					$selection[ $site ]['label'] = sanitize_text_field( $data['label'] );
				}
			}
		}

		return $selection;
	}
}
