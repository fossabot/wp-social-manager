<?php
/**
 * Public: Buttons Class
 *
 * @package SocialManager
 * @subpackage Public\Button
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 * @since 1.0.6 - Remove Endpoint class as the parent class.
 */
abstract class Button implements Button_Interface {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var string
	 */
	protected $plugin;

	/**
	 * The Endpoint instance.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var Endpoint
	 */
	protected $endpoint;

	/**
	 * The button attribute prefix.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $attr_prefix;

	/**
	 * The button mode, 'json' or 'html'.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $mode;

	/**
	 * Constructor: Initialize the Buttons Class
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Add & instantiate Meta and Endpoint class in the Constructor.
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {

		$this->meta = new Meta( $plugin );
		$this->endpoint = new Endpoint( $plugin, $this->meta );

		$this->plugin = $plugin;

		$this->attr_prefix = $this->get_attr_prefix();
		$this->mode = $this->get_mode();

		$this->render();
	}

	/**
	 * Function method that the button.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	final public function render() {

		add_filter( 'the_content', array( $this, 'render_button' ), 55 );
	}

	/**
	 * Render the button in the content.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string
	 */
	public function render_button( $content ) {
		return $content;
	}

	/**
	 * The buttons template script to use in JSON mode.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $includes Data to include in the button.
	 * @return void
	 */
	public function render_html( array $includes ) {}

	/**
	 * The buttons template script to use in JSON mode.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_tmpl() {}

	/**
	 * Determine and generate the buttons item view.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 - Fix incorrect parameter description.
	 * @since 1.2.0 - Changed method name.
	 * @access public
	 *
	 * @param string $view The button view key (`icon`, `icon-text`, `text`).
	 * @param array  $context Whether the sharing button is for `content` or `image`.
	 * @param array  $args 	{
	 *     The button attributes.
	 *
	 *     @type string $site 	The site unique key (e.g. `facebook`, `twitter`, etc.).
	 *     @type string $icon 	The respective site icon.
	 *     @type string $label 	The site label / text.
	 * }
	 * @return string The formatted HTML list element to display the button.
	 */
	public function render_view( $view, $context, array $args ) {

		if ( ! $view || ! $context || ! is_array( $args ) ) {
			return '';
		}

		if ( ! in_array( $context, array( 'content', 'image' ), true ) ) { // Context value must only be 'content' and 'image'.
			return '';
		}

		$args = wp_parse_args( $args, array(
			'attr_prefix' => '',
			'site' => '',
			'icon' => '',
			'label' => '',
			'endpoint' => '',
		) );

		/**
		 * Check if the $args contain falsy value.
		 *
		 * Each element is required. If any of them contain is empty or contains falsy value,
		 * then return empty string.
		 */
		if ( array_filter( $args, function( $value ) {
			return false === (bool) $value || ! is_string( $value );
		} ) ) {
			return '';
		}

		$prefix = $args['attr_prefix'];
		$site = $args['site'];
		$icon = $args['icon'];
		$label = $args['label'];
		$endpoint = 'json' === $this->mode ? '{{' . $args['endpoint'] . '}}' : $args['endpoint'];

		/**
		 * Get the button style from Customizer.
		 *
		 * @since 1.2.0
		 *
		 * @var string
		 */
		$style = get_theme_mod( "{$this->plugin->option_slug}_button_style", 'default' );

		$templates = array(
			'icon' => "<a class='{$prefix}-buttons__item item-{$style} item-{$site}' href='{$endpoint}' target='_blank' role='button' rel='nofollow'>{$icon}</a>",
			'text' => "<a class='{$prefix}-buttons__item item-{$style} item-{$site}' href='{$endpoint}' target='_blank' role='button' rel='nofollow'>{$label}</a>",
			'icon_text' => "<a class='{$prefix}-buttons__item item-{$style} item-{$site}' href='{$endpoint}' target='_blank' role='button' rel='nofollow'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a>",
		);

		$button_view = isset( $templates[ $view ] ) ? kses_icon( $templates[ $view ] ) : '';

		return 'json' === $this->mode ? "<# if ( {$args['endpoint']} ) { #>" . $button_view . '<# } #>' : $button_view;
	}

	/**
	 * The function utility to get all the icons.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $site The name of social media in lowercase (e.g. 'facebook', 'twitter', 'googleples', etc.).
	 * @return array The list of icon.
	 */
	public function get_icons( $site = '' ) {

		$icons = Helpers::get_social_icons();

		/**
		 * Filter all icons displayed in the social media buttons.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args 	  An array of arguments.
		 *
		 * @var array
		 */
		$icons = apply_filters( 'ninecodes_social_manager_icons', $icons, 'buttons', array(
			'attr_prefix' => $this->attr_prefix,
		) );

		$icons = isset( $icons[ $site ] ) ? kses_icon( $icons[ $site ] ) : array_map( __NAMESPACE__ . '\\kses_icon', $icons );

		return $icons;
	}

	/**
	 * The function utility to get the button label (text)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $site The button site key.
	 * @param string $context The button context, 'content' or 'image'.
	 * @return null|string Return null, if the context is incorrect or the key is unset.
	 */
	public function get_label( $site = '', $context = '' ) {

		if ( in_array( $context, array( 'content', 'image' ), true ) ) {
			$buttons = Options::button_sites( $context );
			return isset( $buttons[ $site ]['label'] ) ? $buttons[ $site ]['label'] : null;
		}

		return null;
	}

	/**
	 * The function utility to get the button mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Whether JSON of HTML
	 */
	public function get_mode() {

		$button_mode = $this->plugin->get_option( 'mode', 'button_mode' );
		$theme_support = $this->plugin->theme_support()->is( 'button_mode' );

		if ( 'json' === $theme_support || 'json' === $button_mode ) {
			return 'json';
		}

		if ( 'html' === $theme_support || 'html' === $button_mode ) {
			return 'html';
		}
	}

	/**
	 * The function utility to get the attribute prefix
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The attribute prefix.
	 */
	public function get_attr_prefix() {
		return Helpers::get_attr_prefix();
	}

	/**
	 * The function utility to check if the content is rendered in AMP endpoint.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function in_amp() {
		return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ? true : false;
	}

	/**
	 * The function to get the post status.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @return boolean|string Returns false if ID is not exist, else the post status.
	 */
	public function get_post_status() {

		$post_id = get_the_id();

		return get_post_status( $post_id );
	}

	/**
	 * Save the DOM and remove the extranouse elements generated.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @param DOMDocument $dom [description].
	 * @return string
	 */
	public function to_html( DOMDocument $dom ) {

		return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(
			array( '<html>', '</html>', '<body>', '</body>' ),
			array( '', '', '', '' ),
			$dom->saveHTML()
		));
	}
}
