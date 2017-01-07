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
class Helpers {

	/**
	 * The default attribute prefix.
	 *
	 * @since 1.0.0
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
	 * @param string $name The name of social media in lowercase (e.g. 'facebook', 'twitter', 'googleples', etc.).
	 * @return string The icon of selected social media in SVG.
	 */
	final public static function get_social_icons( $name = '' ) {

		$path = plugin_dir_url( dirname( __FILE__ ) );
		$prefix = esc_attr( self::get_attr_prefix() );

		$facebook = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-facebook'/></svg>";
		$twitter = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-twitter'/></svg>";
		$instagram = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-instagram'/></svg>";
		$pinterest = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-pinterest'/></svg>";
		$linkedin = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-linkedin'/></svg>";
		$googleplus = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-googleplus'/></svg>";
		$youtube = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-youtube'/></svg>";
		$reddit = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-reddit'/></svg>";
		$dribbble = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-dribbble'/></svg>";
		$behance = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-behance'/></svg>";
		$github = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-github'/></svg>";
		$codepen = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-codepen'/></svg>";
		$email = "<svg aria-hidden='true'><use xlink:href='#{$prefix}-icon-email'/></svg>";

		$icons = array(
			'facebook' => apply_filters( 'ninecodes_social_manager_icon', $facebook, array(
				'site' => 'facebook',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'twitter' => apply_filters( 'ninecodes_social_manager_icon', $twitter, array(
				'site' => 'twitter',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'instagram' => apply_filters( 'ninecodes_social_manager_icon', $instagram, array(
				'site' => 'instagram',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'pinterest' => apply_filters( 'ninecodes_social_manager_icon', $pinterest, array(
				'site' => 'pinterest',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'linkedin' => apply_filters( 'ninecodes_social_manager_icon', $linkedin, array(
				'site' => 'linkedin',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'googleplus' => apply_filters( 'ninecodes_social_manager_icon', $googleplus, array(
				'site' => 'googleplus',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'youtube' => apply_filters( 'ninecodes_social_manager_icon', $youtube, array(
				'site' => 'youtube',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'reddit' => apply_filters( 'ninecodes_social_manager_icon', $reddit, array(
				'site' => 'reddit',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'dribbble' => apply_filters( 'ninecodes_social_manager_icon', $dribbble, array(
				'site' => 'dribbble',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'behance' => apply_filters( 'ninecodes_social_manager_icon', $behance, array(
				'site' => 'behance',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'github' => apply_filters( 'ninecodes_social_manager_icon', $github, array(
				'site' => 'github',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'codepen' => apply_filters( 'ninecodes_social_manager_icon', $codepen, array(
				'site' => 'codepen',
				'prefix' => $prefix,
				'context' => false,
			) ),
			'email' => apply_filters( 'ninecodes_social_manager_icon', $email, array(
				'site' => 'email',
				'prefix' => $prefix,
				'context' => false,
			) ),
		);

		/**
		 * Hooks to filter all the icons at once.
		 *
		 * @var array
		 */
		$icons = apply_filters( 'ninecodes_social_manager_icons', $icons, array(
			'prefix' => 'prefix',
			'context' => false,
		) );

		$output = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons;

		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_html['svg'] = array(
			'xmlns' => true,
			'viewbox' => true,
		);
		$allowed_html['path'] = array(
			'd' => true,
		);
		$allowed_html['use'] = array(
			'xlink:href' => true,
		);

		return wp_kses( $output, $allowed_html );
	}

	/**
	 * Function method to get prefix that will be used in the HTML elements
	 * attributes (`class`, `id`, etc.) generated by this plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see ThemeSupports
	 *
	 * @return string
	 */
	public static function get_attr_prefix() {

		$custom = null;

		$support = new ThemeSupports();
		$support = $support->theme_supports();

		if ( isset( $support['attr-prefix'] ) && ! empty( $support['attr-prefix'] ) ) {
			$custom = $support['attr-prefix'];
		}

		// If the custom prefix is not the same as the default then use it.
		$prefix = is_string( $custom ) && $custom !== self::$prefix ? $custom : self::$prefix;

		return esc_attr( $prefix );
	}
}
