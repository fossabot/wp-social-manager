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
	public static $prefix = 'ninecodes-social-manager';

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

		$facebook = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#facebook /></svg>';
		$twitter = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#twitter /></svg>';
		$instagram = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#instagram /></svg>';
		$pinterest = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#pinterest /></svg>';
		$linkedin = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#linkedin /></svg>';
		$googleplus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#googleplus /></svg>';
		$youtube = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#youtube /></svg>';
		$reddit = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#reddit /></svg>';
		$dribbble = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#dribbble /></svg>';
		$behance = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#behance /></svg>';
		$github = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#github /></svg>';
		$codepen = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#codepen /></svg>';
		$email = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><use xlink:href=' . $path . 'public/img/social-sites-icon.svg#email /></svg>';

		$icons = array(
			'facebook' => apply_filters( 'ninecodes_social_manager_icon', $facebook, 'facebook', false ),
			'twitter' => apply_filters( 'ninecodes_social_manager_icon', $twitter, 'twitter', false ),
			'instagram' => apply_filters( 'ninecodes_social_manager_icon', $instagram, 'instagram', false ),
			'pinterest' => apply_filters( 'ninecodes_social_manager_icon', $pinterest, 'pinterest', false ),
			'linkedin' => apply_filters( 'ninecodes_social_manager_icon', $linkedin, 'linkedin', false ),
			'googleplus' => apply_filters( 'ninecodes_social_manager_icon', $googleplus, 'googleplus', false ),
			'youtube' => apply_filters( 'ninecodes_social_manager_icon', $youtube, 'youtube', false ),
			'reddit' => apply_filters( 'ninecodes_social_manager_icon', $reddit, 'reddit', false ),
			'dribbble' => apply_filters( 'ninecodes_social_manager_icon', $dribbble, 'dribbble', false ),
			'behance' => apply_filters( 'ninecodes_social_manager_icon', $behance, 'behance', false ),
			'github' => apply_filters( 'ninecodes_social_manager_icon', $github, 'github', false ),
			'codepen' => apply_filters( 'ninecodes_social_manager_icon', $codepen, 'codepen', false ),
			'email' => apply_filters( 'ninecodes_social_manager_icon', $email, 'email', false ),
		);

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

		$support = new ThemeSupports();
		$support = $support->theme_supports();

		$custom = isset( $support['attr-prefix'] ) ? $support['attr-prefix'] : self::$prefix;

		// If the custom prefix is not the same as the default then use it.
		$prefix = $custom !== self::$prefix ? $custom : self::$prefix;

		return esc_attr( $prefix );
	}
}
