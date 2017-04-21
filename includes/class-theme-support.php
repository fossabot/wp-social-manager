<?php
/**
 * This file defines the Theme_Support class.
 *
 * @package SocialManager
 * @subpackage Theme_Support
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Theme_Support class to get the theme support arguments passed through
 * 'add_theme_support' function.
 *
 * The Theme_Support class also contains a couple of utility method that
 * will returns a `boolean` for which the theme supports the specified feature.
 *
 * @since 1.0.0
 */
final class Theme_Support {

	/**
	 * The name to check the feature provided by the plugin.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const FEATURE_NAME = 'ninecodes-social-manager';

	/**
	 * The theme supports arguments.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array|boolean
	 */
	public $supports;

	/**
	 * Constructor
	 *
	 * Run the hook that initialize the theme_supports function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Run Actions and Filters.
	 *
	 * The Function methods that have to run inside WordPress Hooks.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'theme_support' ) );
	}

	/**
	 * Function method to fetch the arguments passed in the `add_theme_support`
	 * function.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array|boolean Return an array if the arguments are passed in
	 *                       the `add_theme_support` function, otherwise a boolean.
	 */
	public function theme_support() {

		if ( current_theme_supports( self::FEATURE_NAME ) ) {
			$supports = get_theme_support( self::FEATURE_NAME );

			if ( is_array( $supports ) && ! empty( $supports ) ) {
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
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $feature The feature name.
	 * @return boolean        Return `true` if the theme supports the feature,
	 *                        Otherwise `false`
	 */
	public function is( $feature = '' ) {

		if ( empty( $feature ) ) {
			return false;
		}

		$supports = array(
			'stylesheet' => $this->stylesheet(),
			'attr_prefix' => $this->attr_prefix(),
			'button_mode' => $this->button_mode(),
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
			return (bool) $this->supports['stylesheet'];
		}

		return (bool) $this->attr_prefix();
	}

	/**
	 * Function to check if the theme support the 'attr_prefix' feature.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @return string The attribute prefix.
	 */
	protected function attr_prefix() {

		$supports = (array) $this->supports;
		$default = (string) Helpers::$prefix;

		$prefix = key_exists( 'attr_prefix', $supports ) ? $this->supports['attr_prefix'] : '';

		/**
		 * If the prefix is the same as the attribute prefix,
		 * we can assume that the theme will add custom stylesheet.
		 */
		return ! empty( $prefix ) && $default !== (string) $prefix ? $prefix : false;
	}

	/**
	 * Function to check if the theme support the 'buttons-mode' feature.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean|string String is either 'html' or 'json', false if this feature is not defined.
	 */
	protected function button_mode() {

		$supports = (array) $this->supports;
		$haystack = (array) Options::button_modes();

		$mode = key_exists( 'button_mode', $supports ) ? $this->supports['button_mode'] : '';

		if ( $mode && key_exists( $mode, $haystack ) ) {
			return $mode;
		}

		return false;
	}

	/**
	 * Get the feature name of the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The feature name of the plugin.
	 */
	public function get_feature_name() {
		return self::FEATURE_NAME;
	}
}
