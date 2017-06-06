<?php
/**
 * Admin: Metabox Social Meta Section
 *
 * @package SocialManager
 * @subpackage Admin\Metabox
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to register a custom section, social-meta, to the Metabox
 *
 * @since 2.0.0
 */
final class Section_Social_Meta extends \NineCodes\Metabox\Section {

	/**
	 * The type of section.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'social-meta';

	/**
	 * Array of text labels to use for the media upload frame.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var string
	 */
	public $l10n = array();

	/**
	 * Stores the JSON data for the metabox.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var array
	 */
	public $json = array();

	/**
	 * Creates a new section object
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param object $metabox The metabox object.
	 * @param string $name The name of the section.
	 * @param array  $args The section arguments.
	 * @return void
	 */
	public function __construct( $metabox, $name, $args = array() ) {
		parent::__construct( $metabox, $name, $args );

		$this->l10n = array(
			'toggle' => __( 'Toggle Preview', 'ninecodes-social-manager' ),
		);
	}

	/**
	 * Adds custom data to the json array.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		$this->json['l10n'] = $this->l10n;
	}

	/**
	 * Gets the Underscore.js template.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function get_template() {
		\NineCodes\Metabox\get_section_template( $this->type, 'social-meta' );
	}
}
