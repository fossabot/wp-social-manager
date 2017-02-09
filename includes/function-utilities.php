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

	return wp_kses( $data, $allowed_html, array( 'http', 'https' ) );
}

/**
 * A function utility to check whether an array is
 * associative or sequential.
 *
 * @link http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
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
