<?php

namespace NineCodes\SocialManager;

final class ThemeSupports {

	public $supports;

	public function __construct() {
		$this->hooks();
	}

	protected function hooks() {
		add_action( 'init', array( $this, 'theme_supports' ) );
	}

	public function theme_supports() {

		if ( current_theme_supports( 'wp-social-manager' ) ) {

			$supports = get_theme_support( 'wp-social-manager' );

			if ( is_array( $supports ) ) {
				$this->supports = $supports[0];
			} else {
				$this->supports = $supports;
			}

			return $this->supports;
		}

		return false;
	}

	/**
	 * [is description]
	 *
	 * @param  [type] $feature [description]
	 * @return boolean          [description]
	 */
	function is( $feature = '' ) {

		if ( empty( $feature ) ) {
			return false;
		}

		$supports = array(
			'stylesheet' => $this->stylesheet(),
			'buttons-mode' => $this->buttons_mode(),
		);

		return isset( $supports[ $feature ] ) ? $supports[ $feature ] : false;
	}

    /**
     * [stylesheet description]
     * @return [type] [description]
     */
	protected function stylesheet() {

		/*
		 * If set to 'true' it means the theme load its own stylesheet,
		 * to style the plugin output.
		 */
		if ( isset( $this->supports['stylesheet'] ) ) {
			$stylesheet = (bool) $this->supports['stylesheet'];
			return $stylesheet;
		}

		/*
		 * If the prefix is the same as the attribute prefix,
		 * we can assume that the theme will add custom stylesheet.
		 */
		if ( isset( $this->supports['attr-prefix'] ) ) {
			$prefix = $this->supports['attr-prefix'] !== Helpers::$prefix ? true : false;
			return $prefix;
		}
	}

	protected function buttons_mode() {

		$mode = false;

		if ( isset( $this->supports['buttons-mode'] ) ) {

			$yep = (string) $this->supports['buttons-mode'];
			$haystack = (array) Options::buttons_modes();

			if ( key_exists( $yep, $haystack ) ) {
				$mode = $yep;
			}
		}

		return $mode;
	}
}
