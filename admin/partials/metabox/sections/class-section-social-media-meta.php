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
final class Section_Social_Media_Meta extends \NineCodes\Metabox\Section {

	/**
	 * The type of section.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'social-media-meta';

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
	 * @param object $manager The manager object.
	 * @param string $name The name of the section.
	 * @param array  $args The section arguments.
	 * @return void
	 */
	public function __construct( $manager, $name, $args = array() ) {
		parent::__construct( $manager, $name, $args );

		$this->l10n = array(
			'toggle' => __( 'Toggle Preview', 'ninecodes-social-manager' ),
		);

		$this->setups();
	}

	/**
	 * Run the setup
	 *
	 * The setups may involve running some Classes, Functions, andn sometimes WordPress Hooks
	 * that are required to run or add functionalities in the plugin.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setups() {

		$this->post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // WPCS: CSRF ok.
		$this->meta = new Meta;
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

		$site_url = explode( '.', $this->meta->get_site_url() );
		$site_url = array_reverse( $site_url );

		$author = $this->meta->get_post_author( $this->post_id );
		$author = __( 'by', 'ninecodes-social-manager' ) . ' ' . $author['display_name'];

		$this->json['l10n'] = $this->l10n;
		$this->json['meta'] = array(
			'site_name' => "{$site_url[1]}.{$site_url[0]}", // e.g. wordpress.com.
			'document_title' => html_entity_decode( $this->meta->get_post_document_title( $this->post_id ) ),
			'title' => $this->meta->get_post_title( $this->post_id ),
			'image' => $this->meta->get_post_image( $this->post_id ),
			'description' => $this->meta->get_post_description( $this->post_id ),
			'author' => $author,
		);
	}

	/**
	 * Gets the Underscore.js template.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function get_template() {
		\NineCodes\Metabox\get_section_template( $this->type, 'social-media-meta' );
	}
}
