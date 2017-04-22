<?php
/**
 * Public: Button Interface
 *
 * @package SocialManager
 * @subpackage Public\Button
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 * @since 1.0.6 - Remove Endpoint class as the parent class.
 */
interface Button_Interface {

	/**
	 * Function method that the button.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render();

	/**
	 * Render the button in the content.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 */
	public function render_button( $content );

	/**
	 * The buttons template script to use in JSON mode.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $includes Data to include in the button.
	 * @return void
	 */
	public function render_html( array $includes );

	/**
	 * The buttons template script to use in JSON mode.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_tmpl();
}
