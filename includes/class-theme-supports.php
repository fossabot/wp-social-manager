<?php
/**
 * This file defines the ThemeSupports class.
 *
 * @package 	NineCodes\SocialManager
 * @subpackage 	ThemeSupports
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die( 'Shame on you!' ); // Abort.
}

/**
 * The ThemeSupports class to get the theme support arguments passed through
 * 'add_theme_support' function.
 *
 * The ThemeSupports class also contains a couple of utility method that
 * will returns a `boolean` for which the theme supports the specified feature.
 *
 * @since 1.0.0
 */
final class ThemeSupports {

	/**
	 * The name to check the feature provided by the plugin.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 * @var 	string
	 */
	protected $feature = 'wp-social-manager';

	/**
	 * The theme supports arguments.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var 	array|boolean
	 */
	public $supports;

	/**
	 * Constructor
	 *
	 * Run the hook that initialize the theme_supports function.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	void
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Run Actions and Filters.
	 *
	 * The Function methods that have to run inside WordPress Hooks.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @return 	void
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'theme_supports' ) );
	}

	/**
	 * Function method to fetch the arguments passed in the
	 * 'add_theme_support' function.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return 	array|boolean 	Return an array if the arguments are passed in
	 * 							the function, otherwise a boolean.
	 */
	public function theme_supports() {

		if ( current_theme_supports( $feature ) ) {

			$supports = get_theme_support( $feature );

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
	 * Utility function to check if the theme supports a defined
	 * feature.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @param  	string $feature 	The feature name.
	 * @return 	boolean         	Return `true` if the theme supports the feature,
	 * 								Otherwise 'false'
	 */
	public function is( $feature = '' ) {

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
	 * Function to check if the theme support the 'stylesheet' feature.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function stylesheet() {

		/**
		 * If set to 'true' it means the theme load its own stylesheet,
		 * to style the plugin output.
		 */
		if ( isset( $this->supports['stylesheet'] ) ) {
			$stylesheet = (bool) $this->supports['stylesheet'];
			return $stylesheet;
		}

		/**
		 * If the prefix is the same as the attribute prefix,
		 * we can assume that the theme will add custom stylesheet.
		 */
		if ( isset( $this->supports['attr-prefix'] ) ) {
			$prefix = Helpers::$prefix !== $this->supports['attr-prefix'] ? true : false;
			return $prefix;
		}
	}

	/**
	 * Function to check if the theme support the 'buttons-mode' feature.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
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
