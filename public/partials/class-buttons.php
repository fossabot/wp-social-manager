<?php
/**
 * Public: Buttons Class
 *
 * @package NineCodes_Social_Manager
 * @subpackage Public\Buttons
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 *
 * @since 1.0.0
 */
interface ButtonsInterface {
	public function buttons_tmpl(); // The buttons have to have template script.
	public function buttons_html(); // The buttons have to have template script.
}

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
abstract class Buttons implements ButtonsInterface {

	/**
	 * [$plugin description]
	 * @var [type]
	 */
	protected $plugin;

	/**
	 * [$post_id description]
	 * @var [type]
	 */
	protected $post_id;

	/**
	 * Constructor: Initialize the Buttons Class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function __construct( Endpoints $endpoints ) {

		$this->endpoints = $endpoints;

		$this->metas = $endpoints->metas;
		$this->plugin = $endpoints->plugin;

		$this->plugin_slug = $endpoints->plugin->get_slug();
		$this->plugin_opts = $endpoints->plugin->get_opts();
		$this->theme_supports = $endpoints->plugin->get_theme_supports();

		$this->hooks();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function hooks() {

		add_action( 'wp_head', array( $this, 'setups' ), -30 );
		add_action( 'wp_footer', array( $this, 'buttons_tmpl' ), -30 );
	}

	/**
	 * Setup the buttons.
	 *
	 * The setups may involve running some Classes, Functions,
	 * and sometimes WordPress Hooks that are required to render
	 * the social buttons.
	 *
	 * Get the current WordPress post ID.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {

		if ( is_singular() ) {
			$this->post_id = get_the_id();
		}
	}

	public function buttons_tmpl() {}
	public function buttons_html() {}

	/**
	 * Determine and generate the buttons item view.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @param  string $view 	The button view key (`icon`, `icon-text`, `text`).
	 * @param  array  $args 	{
	 *     The button attributes.
	 *
	 *     @type string $site 	The site unique key (e.g. `facebook`, `twitter`, etc.).
	 *     @type string $icon 	The respective site icon.
	 *     @type string $label 	The site label / text.
	 * }
	 * @param  array  $context 	The button attributes such as the 'site' name, button label,
	 *                      	and the button icon.
	 * @return string       	The formatted HTML list element to display the button.
	 */
	protected function button_view( $view, array $args, $context = '' ) {

		if ( empty( $view ) || empty( $args ) || empty( $context ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'site' => '',
			'icon' => '',
			'label' => '',
		) );

		if ( in_array( '', $args, true ) ) {
			return;
		}

		$site = $args['site'];
		$icon = $args['icon'];
		$label = $args['label'];

		$prefix = Helpers::get_attr_prefix();
		$url = $this->get_button_url( $site, $context );

		if ( empty( $url ) ) {
			return '';
		}

		$templates = array(
			'icon' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button' rel='nofollow'>{$icon}</a>",
			'text' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button' rel='nofollow'>{$label}</a>",
			'icon-text' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button' rel='nofollow'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a>",
		);

		$allowed_html = wp_kses_allowed_html( 'post' );
		$allowed_html['svg'] = array(
			'xmlns' => true,
			'viewbox' => true,
		);
		$allowed_html['path'] = array(
			'd' => true,
		);

		return isset( $templates[ $view ] ) ? wp_kses( $templates[ $view ], $allowed_html ) : '';
	}

	/**
	 * The function method to generate the buttons endpoint URLs
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @param  string $site    	The site key or slug (e.g. `facebook`, `twitter`, etc.).
	 * @param  string $context 	The button context; `content` or `image`.
	 * @return string 			The endpoint of the site specified in `$site`.
	 */
	public function get_button_url( $site, $context ) {

		if ( ! $site || ! $context ) {
			return '';
		}

		$buttons_mode = $this->plugin->get_option( 'modes', 'buttons_mode' );

		if ( 'json' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'json' === $buttons_mode ) {
			return "{{{$site}.endpoint}}";
		}

		if ( 'html' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'html' === $buttons_mode ) {

			$urls = array();

			switch ( $context ) {
				case 'content':
					$urls = $this->endpoints->get_content_endpoints( $this->post_id );
					break;

				case 'image':
					$urls = $this->endpoints->get_image_endpoints( $this->post_id );
					break;
			}

			return $urls[ $site ]['endpoint'];
		}
	}
}
