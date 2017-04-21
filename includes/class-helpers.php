<?php
/**
 * This file defines the Helpers class of the plugin.
 *
 * @package SocialManager
 * @subpackage Helpers
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Helpers class that is used to assist in providing artbitrary functionalities.
 *
 * The Helpers class may be used across across both the public-facing side
 * of the site and the admin area without having to instantiate the class.
 *
 * @since 1.0.0
 */
final class Helpers {

	/**
	 * The default attribute prefix.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Change the default attribute from 'ninecodes-social-manager'.
	 * @access public
	 * @var string
	 */
	public static $prefix = 'social-manager';

	/**
	 * Function method to get the social media icons in SVG.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $site The name of social media in lowercase (e.g. 'facebook', 'twitter', 'googleples', etc.).
	 * @return string The icon of selected social media in SVG.
	 */
	public static function get_social_icons( $site = '' ) {

		$path = plugin_dir_url( dirname( __FILE__ ) );
		$prefix = esc_attr( self::get_attr_prefix() );

		$icons = array(
			'facebook'  => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-facebook'/></svg>",
			'twitter'   => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-twitter'/></svg>",
			'instagram' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-instagram'/></svg>",
			'pinterest' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-pinterest'/></svg>",
			'linkedin'  => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-linkedin'/></svg>",
			'googleplus' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-googleplus'/></svg>",
			'youtube' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-youtube'/></svg>",
			'reddit' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-reddit'/></svg>",
			'tumblr' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-tumblr'/></svg>",
			'email' => "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-email'/></svg>",
		);

		/**
		 * Filter all icons.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args 	  An array of arguments.
		 *
		 * @var array
		 */
		$icons = apply_filters( 'ninecodes_social_manager_icons', $icons, 'all', array(
			'attr_prefix' => $prefix,
		) );

		$output = isset( $icons[ $site ] ) ? kses_icon( $icons[ $site ] ) : array_map( __NAMESPACE__ . '\\kses_icon', $icons );

		return $output;
	}

	/**
	 * Function method to get prefix that will be used in the HTML elements
	 * attributes (`class`, `id`, etc.) generated by this plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see Theme_Support
	 *
	 * @return string
	 */
	public static function get_attr_prefix() {

		$prefix = self::$prefix; // Default prefix.
		$custom = null;

		$support = new Theme_Support();
		$support = $support->theme_support();

		if ( isset( $support['attr_prefix'] ) ) { // Alias.
			$custom = $support['attr_prefix'];
		}

		// If the custom prefix is not the same as the default then use it.
		if ( is_string( $custom ) && ! empty( $custom ) && $custom !== self::$prefix ) {
			$prefix = $custom;
		}

		return esc_attr( $prefix );
	}
}
