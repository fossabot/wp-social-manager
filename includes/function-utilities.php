<?php
/**
 * The file that defines utility functions.
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
function kses_icon( $data ) {

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
 * A function utility to check whether an array is
 * associative or sequential.
 *
 * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 *
 * @since 1.2.0
 *
 * @param array $arr The array.
 * @return boolean
 */
function is_array_associative( array $arr ) {

	if ( array() === $arr ) {
		return false;
	}

	return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

/**
 * Merge args recursively.
 *
 * @since 1.2.0
 *
 * @param array $a Value to merge with $defaults.
 * @param array $b Array that serves as the defaults.
 * @return array
 */
function wp_parse_args_recursive( &$a, $b ) {

	$a = (array) $a;
	$b = (array) $b;
	$result = $b;

	foreach ( $a as $k => &$v ) {
		if ( is_array( $v ) && isset( $result[ $k ] ) ) {
			$result[ $k ] = wp_parse_args_recursive( $v, $result[ $k ] );
		} else {
			$result[ $k ] = $v;
		}
	}

	return $result;
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
		return trailingslashit( esc_url( $url ) ) . '{{data.profile}}';
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
 * @param string $tmpl_url The URL template in Mustache ({{ }}) for mat.
 * @return void
 */
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
function tmpl_profile_url( $tmpl_url = '', $profile_id = '' ) {

	$url = preg_replace( '/(?P<profile>\{{\s?data.profile\s?}})/s', '%s', $tmpl_url );

	return sprintf( $url, $profile_id );
}

/**
 * Insert an array into another array before/after a certain key
 *
 * @since 1.2.0
 *
 * @param array  $array The initial array.
 * @param array  $pairs The array to insert.
 * @param string $key The certain key.
 * @param string $position Wether to insert the array before or after the key.
 * @return array
 */
function array_insert( $array, $pairs, $key, $position = 'after' ) {

	$key_pos = array_search( $key, array_keys( $array ), true );

	if ( 'after' === $position ) {
		$key_pos++;
	}

	if ( false !== $key_pos ) {
		$result = array_slice( $array, 0, $key_pos );
		$result = array_merge( $result, $pairs );
		$result = array_merge( $result, array_slice( $array, $key_pos ) );
	} else {
		$result = array_merge( $array, $pairs );
	}

	return $result;
}
