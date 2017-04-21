<?php
/**
 * Public: Button_Image Class
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
 */
class Button_Image extends Button {

	/**
	 * The response of `get_image_endpoint()` function
	 * in HTML buttons modes.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $response;

	/**
	 * Constructor
	 *
	 * Initialize the Buttons abstract class, and render the buttons
	 * in the content.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Change the class parameter to the Plugin instance.
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	function __construct( Plugin $plugin ) {
		parent::__construct( $plugin );

		$this->view = $this->plugin->get_option( 'buttons_image', 'view' );
		$this->hooks();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'wp', array( $this, 'get_endpoint' ), -30 );
		add_action( 'wp_footer', array( $this, 'render_tmpl' ), -30 );
		add_filter( 'the_content', array( $this, 'pre_render_button' ), -55 );
	}

	/**
	 * Function to setup the image buttons when it is in HTML mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_endpoint() {

		if ( 'html' === $this->mode && is_singular() ) {

			$this->response = $this->endpoint->get_image_endpoint( get_the_id() );
		}

		return $this->response;
	}

	/**
	 * Before adding the wrapper and render the button.
	 *
	 * This function allows us, for example, to append attribute to image before the shortocode,
	 * in the content is rendered.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The content with each image wrapped in an element to display
	 *                the social buttons on the images.
	 */
	public function pre_render_button( $content ) {

		if ( empty( $content ) || ! $this->is_button_image() || 'publish' !== $this->get_post_status() ) {
			return $content;
		}

		/**
		 * The DOM Document instance
		 *
		 * @var DOMDocument
		 */
		$dom = new DOMDocument();
		$errors = libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$images = $dom->getElementsByTagName( 'img' );

		if ( 0 === $images->length ) { // If we have at least 1 image.
			return $content;
		}

		$post_id = get_the_id();

		foreach ( $images as $index => $img ) :
			$img->setAttribute( 'data-social-manager', 'content-image-' . $post_id );
		endforeach;

		$content = $this->to_html( $dom );

		libxml_clear_errors();
		libxml_use_internal_errors( $errors );

		return $content;
	}

	/**
	 * Add social wrapper element into the images in the content.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Add `data-social-manager` attribute when the img src match with the src in the endpoints response.
	 * 				- Wrap the image with `<span>` only on HTML Mode.
	 * 				- (HTML Mode) Only wrap the image with `<span>` element when the img src match with the src in the endpoints response.
	 * 				- Use the new method `to_html()` from the parent class to return the content.
	 * 				- Prevent appending the social buttons when the post is not yet published.
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The content with each image wrapped in an element to display
	 *                the social buttons on the images.
	 */
	public function render_button( $content ) {

		if ( empty( $content ) || ! $this->is_button_image() || 'publish' !== $this->get_post_status() ) {
			return $content;
		}

		$is_html = 'html' === $this->mode && $this->response && is_singular();
		$is_json = 'json' === $this->mode;

		if ( $is_html || $is_json ) {

			/**
			 * The DOM Document instance
			 *
			 * @var DOMDocument
			 */
			$dom = new DOMDocument();
			$errors = libxml_use_internal_errors( true );

			$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
			$images = $dom->getElementsByTagName( 'img' );

			if ( 0 === $images->length ) { // If we have at least 1 image.
				return $content;
			}

			$post_id = get_the_id();

			if ( $is_html ) {

				$wrap = $dom->createElement( 'span' );
				$wrap->setAttribute( 'class', "{$this->attr_prefix}-buttons {$this->attr_prefix}-buttons--img {$this->attr_prefix}-buttons--{$post_id}" );
			}

			foreach ( $images as $index => $img ) :

				$wrap_id = absint( $index + 1 );
				$resp_src = $this->response[ $index ]['src'];

				$attributes = array();
				foreach ( $img->attributes as $attr ) {
					$attributes[ $attr->nodeName ] = $attr->nodeValue;
				}

				if ( ! isset( $attributes['data-social-manager'] ) ) {
					continue;
				}

				if ( $is_html && in_array( $resp_src, $attributes, true ) && "content-image-{$post_id}" === $attributes['data-social-manager'] ) {

					$wrap_clone = $wrap->cloneNode();
					$wrap_clone->setAttribute( 'id', "{$this->attr_prefix}-buttons-{$post_id}-img-{$wrap_id}" );

					if ( 'a' === $img->parentNode->nodeName ) {

						$link_parent = $img->parentNode;

						$link_parent->parentNode->replaceChild( $wrap_clone, $link_parent );
						$wrap_clone->appendChild( $link_parent );
					} else {

						$img->parentNode->replaceChild( $wrap_clone, $img );
						$wrap_clone->appendChild( $img );
					}

					$fragment = $dom->createDocumentFragment();
					$fragment->appendXML( $this->render_html( $this->response[ $index ]['endpoints'] ) );
					$wrap_clone->appendChild( $fragment );
				}

			endforeach;

			$content = $this->to_html( $dom );

			libxml_clear_errors();
			libxml_use_internal_errors( $errors );
		} // End if().

		return $content;
	}

	/**
	 * Generate the buttons HTML for image.
	 *
	 * Used when the "Buttons Mode" is set to 'HTML'.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Renamed `data-social-buttons` to `data-social-manager` of the `span` (wrapper) element.
	 * @access public
	 *
	 * @param array $includes Data to include in the button.
	 * @return string The formatted HTML of the buttons.
	 */
	public function render_html( array $includes ) {

		$list = '';

		if ( ! empty( $includes ) ) :

			$list .= "<span class='{$this->attr_prefix}-buttons__list {$this->attr_prefix}-buttons__list--{$this->view}' data-social-manager=\"button-image\">";

			$prefix = $this->attr_prefix;

			foreach ( $includes as $site => $endpoint ) :

				$icon = $this->get_buttons_icons( $site );

				if ( ! $icon || ! $endpoint ) {
					continue;
				}

				$label = $this->get_buttons_label( $site, 'image' );
				$list .= $this->buttons_view( $this->view, 'image', array(
					'attr_prefix' => $prefix,
					'site' => $site,
					'icon' => $icon,
					'label' => $label,
					'endpoint' => $endpoint,
				));
			endforeach;

			$list .= '</span>';
		endif;

		/**
		 * Format the output to be a proper HTML markup,
		 * so it can be safely append into the DOM.
		 */
		$dom = new DOMDocument();
		$errors = libxml_use_internal_errors( true );
		$dom->loadHTML( mb_convert_encoding( $list, 'HTML-ENTITIES', 'UTF-8' ) );

		$list = $this->to_html( $dom );

		libxml_clear_errors();
		libxml_use_internal_errors( $errors );

		return $list;
	}

	/**
	 * Add the Underscore.js template of the social media buttons.
	 *
	 * @since   1.0.0
	 * @since 	1.0.6 - Renamed `data-social-buttons` to `data-social-manager` of the `span` (wrapper) element.
	 * @access  public
	 *
	 * @return void
	 */
	public function render_tmpl() {

		if ( $this->is_button_image() && 'json' === $this->mode && wp_script_is( $this->plugin_slug . '-app', 'enqueued' ) ) :
			$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

			if ( ! empty( $includes ) ) : ?><script type="text/html" id="tmpl-buttons-image">
<span class="<?php echo esc_attr( $this->attr_prefix ); ?>-buttons__list <?php echo esc_attr( $this->attr_prefix ); ?>-buttons__list--<?php echo esc_attr( $this->view ); ?>" data-social-manager="buttons-image"><?php

$prefix = $this->attr_prefix;
foreach ( $includes as $site => $value ) :

	$label = $this->get_buttons_label( $site, 'image' );
	$icon  = $this->get_buttons_icons( $site );

	if ( ! $icon ) {
		continue;
	}

	$list = $this->buttons_view($this->view, 'image', array(
		'attr_prefix' => $prefix,
		'site' => $site,
		'icon' => $icon,
		'label' => $label,
		'endpoint' => "data.endpoints.{$site}",
	));

	echo $list; // WPCS: XSS ok.
endforeach; ?></span>
</script>

		<?php endif;
		endif;
	}

	/**
	 * Override parent buttons icon method.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $site The name of social media in lowercase (e.g. 'facebook', 'twitter', 'googleples', etc.).
	 * @return array The list of icon.
	 */
	public function get_icons( $site = '' ) {

		$icons = parent::get_icons();

		/**
		 * Filter the icons displayed in the social media buttons image.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args 	  An array of arguments.
		 *
		 * @var array
		 */
		$icons = apply_filters( 'ninecodes_social_manager_icons', $icons, 'buttons_image', array(
			'attr_prefix' => $this->attr_prefix,
		) );

		$icons = isset( $icons[ $site ] ) ? kses_icon( $icons[ $site ] ) : array_map( __NAMESPACE__ . '\\kses_icon', $icons );

		return $icons;
	}

	/**
	 * The Utility method to check if buttons content should be generated.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_button_image() {

		if ( $this->in_amp() ) {
			return false;
		}

		$enable = (bool) $this->plugin->get_option( 'buttons_image', 'enabled' );

		if ( ! $enable ) {
			return false;
		}

		$post_types = (array) $this->plugin->get_option( 'buttons_image', 'post_types' );

		/**
		 * NOTE: The social media buttons currently do not support Home and Archive display.
		 * But, we plan to have it in the future.
		 */
		if ( ! is_singular( array_keys( array_filter( $post_types ) ) ) ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		/**
		 * Get the post meta value whether the Social Buttons Image is enabled or not.
		 *
		 * @since 1.0.6 - Use the $this->metas property to utilitze the Meta instance methods.
		 * @var boolean
		 */
		$post_meta = $this->metas->get_post_meta( get_the_id(), 'buttons_image' );

		/**
		 * If it is 'null' we assume that the meta post either not yet created or
		 * the associated key, 'buttons_image', in the meta is not set. So, we
		 * return to the default 'true'.
		 */
		return ( null === $post_meta ) ? true : $post_meta;
	}
}
