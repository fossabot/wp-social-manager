<?php

namespace XCo\WPSocialManager;

final class SettingsValidation extends OptionUtilities {

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
			'postTypes' => array(),
			'view' => '',
			'placement' => '',
			'includes' => array()
		) );

		$inputs[ 'heading' ] = sanitize_text_field( $inputs[ 'heading' ] );

		$inputs[ 'postTypes' ]  = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'includes' ] = $this->validate_multi_selection( $inputs[ 'includes' ], 'includes', 'content' );

		$inputs[ 'view' ] = $this->validate_selection( $inputs[ 'view' ], 'view' );
		$inputs[ 'placement' ] = $this->validate_selection( $inputs[ 'placement' ], 'placement' );

		return $inputs;
	}

	/**
	 * [setting_buttons_content description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public function setting_buttons_image( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'view' => '',
			'postTypes' => array(),
			'includes' => array()
		) );

		$inputs[ 'postTypes' ] = $this->validate_multi_selection( $inputs[ 'postTypes' ], 'postTypes' );
		$inputs[ 'includes' ] = $this->validate_multi_selection( $inputs[ 'includes' ], 'includes', 'image' );

		$inputs[ 'view' ] = $this->validate_selection( $inputs[ 'view' ], 'view' );

		return $inputs;
	}

	/**
	 * [setting_metas description]
	 * @param  array  $inputs [description]
	 * @return [type]         [description]
	 */
	public function setting_site_metas( $inputs ) {

		$inputs = wp_parse_args( $inputs, array(
			'enabled' => false,
			'name' => '',
			'description' => '',
			'image' => ''
		) );

		$inputs[ 'name' ] = wp_kses( $inputs[ 'name' ] );
		$inputs[ 'description' ] = wp_kses( $inputs[ 'description' ] );
		$inputs[ 'image' ] = absint( $inputs[ 'image' ] );

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
			'view' => self::get_button_views(),
			'placement' => self::get_button_placements()
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

		$preset = array(
			'postTypes' => self::get_post_types(),
			'includes' => self::get_button_sites( $for )
		);

		$selection = array();

		foreach ( $inputs as $key => $value ) {

			$selection[] = sanitize_text_field( $value );

			if ( ! key_exists( $key, $preset[ $ref ] ) ) {
				unset( $inputs[ $key ] );
			}
		}

		return $selection;
	}
}