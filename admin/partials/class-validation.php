<?php
/**
 * Admin: SettingsValidation class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Admin\Validation
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to validate setting inputs.
 *
 * @since 1.0.0
 */
final class SettingsValidation extends OptionUtilities {

	/**
	 * The function method to sanitize username inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $inputs Unsanitized inputs being saved.
	 * @return array         Inputs sanitized
	 */
	public function setting_usernames( array $inputs ) {

		foreach ( $inputs as $key => $username ) {

			$inputs[ $key ] = sanitize_text_field( $username );

			if ( 2 >= strlen( $inputs[ $key ] ) && 0 !== strlen( $inputs[ $key ] ) ) {
				$inputs[ $key ] = '';
				add_settings_error( $key, 'social-username-length', esc_html__( 'A username generally should contains at least 3 characters (or more).', 'wp-social-manager' ), 'error' );
			}
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
	 * @return array         Inputs sanitized
	 */
	public function setting_buttons_content( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'postTypes' => array(),
			'view' => '',
			'placement' => '',
			'heading' => '',
			'includes' => array(),
		) );

		$inputs['heading'] = sanitize_text_field( $inputs['heading'] );

		$inputs['postTypes'] = $this->validate_multi_selection( $inputs['postTypes'], 'postTypes' );
		$inputs['includes'] = $this->validate_multi_selection( $inputs['includes'], 'includes', 'content' );
		$inputs['view'] = $this->validate_selection( $inputs['view'], 'view' );
		$inputs['placement'] = $this->validate_selection( $inputs['placement'], 'placement' );

		return $inputs;
	}

	/**
	 * The function method to sanitize the "Buttons Image" inputs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $inputs Unsanitized inputs being saved.
	 * @return array         Inputs sanitized
	 */
	public function setting_buttons_image( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'view' => '',
			'postTypes' => array(),
			'includes' => array(),
		) );

		$inputs['postTypes'] = $this->validate_multi_selection( $inputs['postTypes'], 'postTypes' );
		$inputs['includes'] = $this->validate_multi_selection( $inputs['includes'], 'includes', 'image' );

		$inputs['view'] = $this->validate_selection( $inputs['view'], 'view' );

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
	public function setting_site_metas( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'name' => '',
			'description' => '',
			'image' => '',
		) );

		$inputs['name'] = wp_kses( $inputs['name'], array() );
		$inputs['description'] = wp_kses( $inputs['description'], array() );
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
	 * @return array        	 Inputs sanitized
	 */
	public function setting_advanced( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enableStylesheet' => false,
		) );

		return $inputs;
	}

	/**
	 * The function method to sanitize the Mode section in the "Advanced" tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $inputs Unsanitized inputs being saved.
	 * @return array        Inputs sanitized
	 */
	public function setting_advanced_modes( $inputs ) {

		$inputs['buttonsMode'] = $this->validate_selection( $inputs['buttonsMode'], 'modes' );

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
	 * @param  string $input Unsanitized inputs being saved.
	 * @param  array  $ref   Key reference of the preset / acceptable selection to validate
	 * 						 against the incoming input.
	 * @return string        Inputs sanitized
	 */
	protected function validate_selection( $input, $ref ) {

		/**
		 * Preset selection.
		 *
		 * @var array
		 */
		$preset = array(
			'view' => self::get_button_views(),
			'placement' => self::get_button_placements(),
			'modes' => self::get_button_modes(),
		);

		$input = sanitize_key( (string) $input );

		// The input must be matched with presets to pass the validation.
		if ( ! key_exists( $input, $preset[ $ref ] ) ) {
			$input = current( array_keys( $preset[ $ref ] ) ); }

		return $input;
	}

	/**
	 * The utility function to sanitize multiple selection inputs.
	 *
	 * Typically the inputs are coming from a multicheckbox or multiselect input type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array  $inputs Unsanitized inputs being saved.
	 * @param  string $ref    Key reference of the preset / acceptable selection to validate
	 * 						  against the incoming input.
	 * @param  string $for    Optional. Key reference to get the preset selection in 2-level nested arrays.
	 * @return array          Inputs sanitized
	 */
	protected function validate_multi_selection( array $inputs, $ref, $for = '' ) {

		$preset = array(
			'postTypes' => self::get_post_types(),
			'includes' => self::get_button_sites( $for ),
		);

		$selection = array();

		foreach ( $inputs as $key => $value ) {

			$selection[] = sanitize_text_field( $value );

			// The input must be matched with presets to pass the validation.
			if ( ! key_exists( $key, $preset[ $ref ] ) ) {
				unset( $inputs[ $key ] );
			}
		}

		return $selection;
	}
}
