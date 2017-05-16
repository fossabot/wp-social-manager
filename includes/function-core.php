<?php
/**
 * The file that defines core functions.
 *
 * @package SocialManager
 */

namespace NineCodes\SocialManager;

/**
 * Filter the SVG output.
 *
 * @since 1.1.2
 *
 * @param string $data The HTML string.
 * @return string Sanitized HTML.
 */
function sanitize_icon( $data ) {

	$allowed_html = array(
		'a' => array(
			'href' => true,
			'class' => true,
			'id' => true,
			'target' => true,
			'rel' => true,
			'role' => true,
		),
		'div' => array(
			'class' => true,
			'id' => true,
			'style' => true,
			'role' => true,
		),
		'span' => array(
			'class' => true,
			'id' => true,
			'style' => true,
			'role' => true,
		),
		'i' => array(
			'class' => true,
			'id' => true,
			'style' => true,
			'role' => true,
		),
		'img' => array(
			'src' => true,
			'alt' => true,
			'width' => true,
			'height' => true,
			'class' => true,
			'id' => true,
			'style' => true,
		),
		'svg' => array(
			'xmlns' => true,
			'viewbox' => true,
			'width' => true,
			'height' => true,
			'class' => true,
			'id' => true,
			'style' => true,
			'aria-hidden' => true,
		),
		'path' => array(
			'd' => true,
			'fill-rule' => true,
		),
		'use' => array(
			'xlink:href' => true,
		),
	);

	return wp_kses( $data, $allowed_html, wp_allowed_protocols() );
}

/**
 * Function to sanitize URL template string
 *
 * @since 2.0.0
 *
 * @param string $url The URL string.
 * @return string
 */
function sanitize_profile_url( $url = '' ) {

	preg_match( '/(?P<profile>\{{\s?data.profile\s?}})/s', $url, $match );

	if ( ! isset( $match['profile'] ) || empty( $match['profile'] ) ) {
		$url = trailingslashit( esc_url( $url ) );
		return "{$url}{{data.profile}}";
	}

	$pattern = preg_replace( '/(?P<profile>\{{\s?data.profile\s?}})/s', '%s', $url );
	$url = sprintf( esc_url( $pattern ), '{{data.profile}}' );

	return $url;
}

/**
 * Function to return the profile URL template into proper URL
 *
 * @since 2.0.0
 *
 * @param string $tmpl_url The social media URL pattern.
 * @param string $profile_id The social media profile id or username.
 *
 * @return string
 */
function translate_profile_url( $tmpl_url = '', $profile_id = '' ) {

	$url = preg_replace( '/(?P<profile>\{{\s?data.profile\s?}})/s', '%s', $tmpl_url );

	return sprintf( $url, $profile_id );
}
