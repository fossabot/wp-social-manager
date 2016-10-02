<?php

namespace XCo\WPSocialManager;

final class SettingValidation extends SettingUtilities {

	/**
	 * [sanitize_profiles description]
	 * @param  [type] $profiles [description]
	 * @return [type]           [description]
	 */
	public function setting_usernames( array $inputs ) {

		foreach ( $inputs as $key => $username ) {

			$inputs[ $key ] = sanitize_text_field( $username );

			if ( 2 >= strlen( $inputs[ $key ] ) && 0 !== strlen( $inputs[ $key ] ) ) {
				$inputs[ $key ] = '';
				add_settings_error( $key, 'social-username-length', esc_html__( 'A username generally should contains at least 3 characters (or more).', 'wp-social-plugin' ), 'error' );
			}
		}

		return $inputs;
	}

	/**
	 * [sanitize_sharing description]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	public function setting_buttons_content( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'buttonType' => '',
			'buttonLocation' => '',
			'postTypes' => array(),
			'buttonSites' => array()
		) );

		$inputs[ 'postTypes' ]  = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'buttonSites' ] = $this->validate_multi_selection( $inputs[ 'buttonSites' ], 'buttonSites' );
		$inputs[ 'buttonType' ] = $this->validate_selection( $inputs[ 'buttonType' ], 'buttonType' );
		$inputs[ 'buttonLocation' ] = $this->validate_selection( $inputs[ 'buttonLocation' ], 'buttonLocation' );

		return $inputs;
	}

	/**
	 * [setting_buttons_content description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public function setting_buttons_image( array $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'imageSharing' => '',
			'buttonType' => '',
			'postTypes' => array(),
			'buttonSites' => array()
		) );

		$inputs[ 'postTypes' ] = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'buttonSites' ] = $this->validate_multi_selection( $inputs[ 'buttonSites' ], 'buttonSites', 'image' );
		$inputs[ 'buttonType' ] = $this->validate_selection( $inputs[ 'buttonType' ], 'buttonType' );

		return $inputs;
	}

	/**
	 * [validate_button_sites description]
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	protected function validate_selection( $input, $ref ) {

		$selection = array(
			'buttonType' => self::get_button_types(),
			'buttonLocation' => self::get_button_locations()
		);

		$input = sanitize_key( (string) $input );

		if ( ! key_exists( $input, $selection[ $ref ] ) )
			$input = current( array_keys( $selection[ $ref ] ) );

		return $input;
	}

	/**
	 * [validate_post_types description]
	 * @param  [type] $inputs [description]
	 * @return [type]         [description]
	 */
	protected function validate_multi_selection( $inputs, $ref, $for = '' ) {

		$selection = array(
			'postTypes' => self::get_post_types(),
			'buttonSites' => self::get_button_sites( $for )
		);

		foreach ( $inputs as $key => $value ) {

			$inputs[ $key ] = sanitize_text_field( $value );

			if ( ! key_exists( $key, $selection[ $ref ] ) ) {
				unset( $inputs[ $key ] );
			}
		}

		return $inputs;
	}
}