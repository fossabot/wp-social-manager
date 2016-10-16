<?php
/**
 * Public: Buttons Class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Public\Buttons
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
final class Buttons extends OutputUtilities {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_opts;

	/**
	 * The current post ID in the loop.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var integer
	 */
	protected $post_id;

	/**
	 * Related options to render the social buttons.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $options;

	/**
	 * Constructor: Initialize the Buttons Class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 */
	function __construct( array $args ) {

		$this->plugin_name = $args['plugin_name'];
		$this->plugin_opts = $args['plugin_opts'];

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
		add_action( 'wp_footer', array( $this, 'add_template_script' ), -30 );
		add_filter( 'the_content', array( $this, 'add_buttons_content' ), 100, 1 );
		add_filter( 'the_content', array( $this, 'add_buttons_image' ), 100, 1 );
	}

	/**
	 * Setup the buttons.
	 *
	 * The setups may involve running some Classes, Functions,
	 * and sometimes WordPress Hooks that are required to render
	 * the social buttons.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {

		$this->post_id = get_the_id();

		$this->options = (object) array(
			'postMeta'       => get_post_meta( $this->post_id, $this->plugin_opts, true ),
			'buttonsContent' => get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttonsImage'   => get_option( "{$this->plugin_opts}_buttons_image" ),
		);
	}

	/**
	 * Append or prepend the social media buttons wrapper element into the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  	string $content The post content.
	 * @return 	string 			The post content added with the social buttons wrapper
	 *                       	element. The wrapper may be added before or after the content,
	 *                       	following the option selected.
	 */
	public function add_buttons_content( $content ) {

		if ( false === $this->is_buttons_content() ) {
			return $content;
		}

		$place = $this->options->buttonsContent['placement'];
		$prefix = self::get_attr_prefix();

		$wrapper = "<div class='{$prefix}-buttons {$prefix}-buttons--content {$prefix}-buttons--content-{$place}' id='{$prefix}-buttons-{$this->post_id}'></div>";

		if ( 'before' === $place ) {
			$content = $wrapper . $content;
		}

		if ( 'after' === $place ) {
			$content = $content . $wrapper;
		}

		return $content;
	}

	/**
	 * Add social wrapper element into the images in the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  	string $content The post content.
	 * @return  string 			The content with each image wrapped in an element
	 *                        	to display the social buttons on the images.
	 */
	public function add_buttons_image( $content ) {

		if ( empty( $content ) ) {
			return $content;
		}

		if ( false === $this->is_buttons_image() ) {
			return $content;
		}

		libxml_use_internal_errors( true );

		$dom = new \DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$prefix = self::get_attr_prefix();

		$images = $dom->getElementsByTagName( 'img' );

		$wrap   = $dom->createElement( 'span' );

		$wrap->setAttribute( 'class', "{$prefix}-buttons {$prefix}-buttons--img {$prefix}-buttons--{$this->post_id}" );

		// If we have, at least, 1 image.
		if ( $images->length >= 1 ) {
			foreach ( $images as $id => $img ) {

				$wrap_clone = $wrap->cloneNode();

				$wrap_id = absint( $id + 1 );
				$wrap_id = sanitize_key( $wrap_id );

				$wrap_clone->setAttribute( 'id', "{$prefix}-buttons-{$this->post_id}-img-{$wrap_id}" );

				if ( 'a' === $img->parentNode->nodeName ) {

					$link_parent = $img->parentNode;

					$link_parent->parentNode->replaceChild( $wrap_clone, $link_parent );
					$wrap_clone->appendChild( $link_parent );

				} else {

					$img->parentNode->replaceChild( $wrap_clone, $img );
					$wrap_clone->appendChild( $img );
				}
			}
		}

		$content = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );

		return $content;
	}

	/**
	 * Add the Underscore.js template of the social media buttons.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return void
	 */
	public function add_template_script() {
		$this->buttons_content_tmpl();
		$this->buttons_image_tmpl();
	}

	/**
	 * Build the template for the social buttons shown in content
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @link http://devdocs.io/underscore/index#template
	 *
	 * @return void
	 */
	protected function buttons_content_tmpl() {

		if ( false === $this->is_buttons_content() ) {
			return;
		}

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$heading  = $this->options->buttonsContent['heading'];
			$view     = $this->options->buttonsContent['view'];
			$includes = (array) $this->options->buttonsContent['includes'];

			$prefix = self::get_attr_prefix(); ?>

		<?php if ( ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-content">
			<?php if ( ! empty( $heading ) ) : ?>
			<h4 class="<?php echo esc_attr( $prefix ); ?>-buttons__heading"><?php echo esc_html( $heading ); ?></h4>
			<?php endif; ?>
			<ul class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>" data-social-buttons="content">
			<?php foreach ( $includes as $site ) :

				$props = self::get_social_properties( $site );
				$icon  = self::get_social_icons( $site );
				$list  = self::list_views( $view, array(
							'site'  => $site,
							'icon'  => $icon,
							'label' => $props['label'],
				) );

				echo $list;
			endforeach; ?>
			</ul>
		</script>
		<?php endif;
		endif;
	}

	/**
	 * Build the template for the social buttons shown in images of the content.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @link http://devdocs.io/underscore/index#template
	 *
	 * @return void
	 */
	protected function buttons_image_tmpl() {

		if ( false === $this->is_buttons_image() ) {
			return;
		}

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$view = $this->options->buttonsImage['view'];
			$includes = (array) $this->options->buttonsImage['includes'];

			$prefix = self::get_attr_prefix(); ?>

		<?php if ( ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-image">
			<ul class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>"
				data-social-buttons="image">
			<?php foreach ( $includes as $site ) :

				$props = self::get_social_properties( $site );
				$icon  = self::get_social_icons( $site );
				$list  = self::list_views( $view, array(
						'site'  => $site,
						'icon'  => apply_filters( 'wp_social_manager_icon', $icon, $site, 'button-image' ),
						'label' => $props['label'],
				) );

				echo $list;
			endforeach; ?>
			</ul>
		</script>
		<?php endif;
		endif;
	}

	/**
	 * Determine and generate the buttons item view.
	 *
	 * @since 	1.0.0
	 * @access 	protected
	 *
	 * @param  string $view One of the buttons view key name,
	 *                      as defined in the OptionsUtility Class.
	 * @param  array  $args The button attributes such as the 'site' name, button label,
	 *                      and the button icon.
	 * @return string       The formatted HTML list element to display the button.
	 */
	protected static function list_views( $view = '', array $args ) {

		if ( empty( $view ) ) {
			return '';
		}

		$args = wp_parse_args( $args, array(
			'site'  => '',
			'icon'  => '',
			'label' => '',
		) );

		if ( in_array( '', $args, true ) ) {
			return;
		}

		$site  = $args['site'];
		$icon  = $args['icon'];
		$label = $args['label'];

		$prefix = self::get_attr_prefix();

		$templates = array(
			'icon' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' role='button'>{$icon}</a></li>",
			'text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' role='button'>{$label}</a></li>",
			'icon-text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' role='button'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a></li>",
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}

	/**
	 * The Utility method to check if buttons content should be generated.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_buttons_content() {

		$post_types = (array) $this->options->buttonsContent['postTypes'];

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		$includes = (array) $this->options->buttonsContent['includes'];

		if ( empty( $includes ) ) {
			return false;
		}

		$place = $this->options->buttonsContent['placement'];

		if ( ! in_array( $place, array_keys( self::get_button_placements() ), true ) ) {
			return false;
		}

		$meta = (array) $this->options->postMeta;

		return isset( $meta['buttons_content'] ) ? $meta['buttons_content'] : true;
	}

	/**
	 * The Utility method to check if buttons content should be generated.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_buttons_image() {

		$enable = (bool) $this->options->buttonsImage['enabled'];

		if ( ! $enable ) {
			return false;
		}

		$post_types = (array) $this->options->buttonsImage['postTypes'];

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		$includes = (array) $this->options->buttonsImage['includes'];

		if ( empty( $includes ) ) {
			return false;
		}

		$meta = (array) $this->options->postMeta;

		return isset( $meta['buttons_image'] ) ? $meta['buttons_image'] : true;
	}
}
