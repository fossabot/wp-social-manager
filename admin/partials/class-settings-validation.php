<?php

namespace XCo\WPSocialManager;

final class SettingsValidation extends SettingUtilities {

	/**
	 * [sanitize_profiles description]
	 * @param  [type] $profiles [description]
	 * @return [type]           [description]
	 */
	public function setting_usernames( $inputs ) {

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
	public function setting_buttons_content( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'buttonView' => '',
			'buttonPlacement' => '',
			'postTypes' => array(),
			'buttonSites' => array()
		) );

		$inputs[ 'postTypes' ]  = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'buttonSites' ] = $this->validate_multi_selection( $inputs[ 'buttonSites' ], 'buttonSites' );
		$inputs[ 'buttonView' ] = $this->validate_selection( $inputs[ 'buttonView' ], 'buttonView' );
		$inputs[ 'buttonPlacement' ] = $this->validate_selection( $inputs[ 'buttonLocation' ], 'buttonLocation' );

		return $inputs;
	}

	/**
	 * [setting_buttons_content description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public function setting_buttons_image( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'imageSharing' => false,
			'buttonView' => '',
			'postTypes' => array(),
			'buttonSites' => array()
		) );

		$inputs[ 'postTypes' ] = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'buttonSites' ] = $this->validate_multi_selection( $inputs[ 'buttonSites' ], 'buttonSites', 'image' );
		$inputs[ 'buttonView' ] = $this->validate_selection( $inputs[ 'buttonView' ], 'buttonView' );

		return $inputs;
	}

	/**
	 * [setting_metas description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public function setting_site_metas( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'metaEnable' => false,
			'name' => '',
			'description' => '',
			'image' => ''
		) );

		$inputs[ 'name' ] = wp_kses( $inputs[ 'name' ] );
		$inputs[ 'description' ] = wp_kses( $inputs[ 'description' ] );
		$inputs[ 'image' ] = esc_url( $inputs[ 'image' ] );

		return $inputs;
	}

	/**
	 * [setting_advanced description]
	 * @return [type] [description]
	 */
	public function setting_advanced( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'disableStylesheet' => false
		) );

		return $inputs;
	}

	/**
	 * [validate_button_sites description]
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	protected function validate_selection( $input, $ref ) {

		$selection = array(
			'buttonView' => self::get_button_views(),
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