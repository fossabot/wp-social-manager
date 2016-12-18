<?php
/**
 * Public: ButtonsImage Class
 *
 * @package SocialManager
 * @subpackage Public\Buttons
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
class ButtonsImage extends Buttons {

	/**
	 * The response of `get_image_endpoints()` function
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
	 * @access public
	 *
	 * @param ViewPublic $public The ViewPublic class instance.
	 */
	function __construct( ViewPublic $public ) {
		parent::__construct( $public );

		$this->view = $this->plugin->get_option( 'buttons_image', 'view' );

		$this->hooks();
		$this->render();
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

		add_action( 'wp', array( $this, 'setups_html' ), -30 );
		add_action( 'wp_footer', array( $this, 'buttons_tmpl' ), -30 );
	}

	/**
	 * Function to setup the image buttons when it is in HTML mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function setups_html() {

		if ( 'html' === $this->mode && is_singular() ) {
			$this->response = $this->get_image_endpoints( get_the_id() );
		}
	}

	/**
	 * Function to render the buttons in the content.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function render() {

		add_filter( 'the_content', array( $this, 'render_buttons' ), 51 );
	}

	/**
	 * Add social wrapper element into the images in the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The content with each image wrapped in an element to display
	 *                the social buttons on the images.
	 */
	public function render_buttons( $content ) {

		$post_id = get_the_id();

		if ( ! $this->is_buttons_image() ) {
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

			if ( 0 !== $images->length ) : // If we have at least 1 image.

				$wrap = $dom->createElement( 'span' );
				$wrap->setAttribute( 'class', "{$this->prefix}-buttons {$this->prefix}-buttons--img {$this->prefix}-buttons--{$post_id}" );

				foreach ( $images as $index => $img ) :

					$wrap_id = absint( $index + 1 );
					$wrap_id = sanitize_key( $wrap_id );

					$wrap_clone = $wrap->cloneNode();
					$wrap_clone->setAttribute( 'id', "{$this->prefix}-buttons-{$post_id}-img-{$wrap_id}" );

					if ( 'a' === $img->parentNode->nodeName ) {

						$link_parent = $img->parentNode;

						$link_parent->parentNode->replaceChild( $wrap_clone, $link_parent );
						$wrap_clone->appendChild( $link_parent );
					} else {

						$img->parentNode->replaceChild( $wrap_clone, $img );
						$wrap_clone->appendChild( $img );
					}

					if ( $is_html ) {

						$fragment = $dom->createDocumentFragment();
						$fragment->appendXML( $this->buttons_html( $this->response[ $index ] ) );
						$wrap_clone->appendChild( $fragment );
					}

				endforeach;

				$content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(
					array( '<html>', '</html>', '<body>', '</body>' ),
					array( '', '', '', '' ),
					$dom->saveHTML()
				));
			endif;

			libxml_clear_errors();
			libxml_use_internal_errors( $errors );
		}

		return $content;
	}

	/**
	 * Generate the buttons HTML for image.
	 *
	 * Used when the "Buttons Mode" is set to 'HTML'.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $includes Data to include in the button.
	 * @return string The formatted HTML of the buttons.
	 */
	public function buttons_html( $includes ) {

		$list = '';

		if ( ! empty( $includes ) ) :

			$list .= "<span class='{$this->prefix}-buttons__list {$this->prefix}-buttons__list--{$this->view}' data-social-buttons='image'>";

			foreach ( $includes as $site => $endpoint ) :
				$label = $this->get_button_label( $site, 'image' );
				$icon  = $this->get_button_icon( $site );
				$list .= $this->button_view( $this->view, 'image', array(
					'prefix' => $this->prefix,
					'site' => $site,
					'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, array(
						'site' => $site,
						'prefix' => $this->prefix,
						'context' => 'button-image',
					) ),
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

		libxml_clear_errors();
		libxml_use_internal_errors( $errors );

		return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(
			array( '<html>', '</html>', '<body>', '</body>' ),
			array( '', '', '', '' ),
			$dom->saveHTML()
		));
	}

	/**
	 * Add the Underscore.js template of the social media buttons.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @return void
	 */
	public function buttons_tmpl() {

		if ( $this->is_buttons_image() && 'json' === $this->mode && wp_script_is( $this->plugin_slug . '-app', 'enqueued' ) ) :
			$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

			if ( ! empty( $includes ) ) : ?><script type="text/html" id="tmpl-buttons-image">
		<span class="<?php echo esc_attr( $this->prefix ); ?>-buttons__list <?php echo esc_attr( $this->prefix ); ?>-buttons__list--<?php echo esc_attr( $this->view ); ?>" data-social-buttons="image"><?php

		foreach ( $includes as $site ) :

			$label = $this->get_button_label( $site, 'image' );
			$icon  = $this->get_button_icon( $site );
			$list  = $this->button_view($this->view, 'image', array(
				'prefix' => $this->prefix,
				'site' => $site,
				'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, array(
					'site' => $site,
					'prefix' => $this->prefix,
					'context' => 'button-image',
				) ),
				'label' => $label,
				'endpoint' => "{{ data.{$site} }}",
			));

			echo $list; // WPCS: XSS ok.
		endforeach; ?></span>
		</script>

		<?php endif;
		endif;
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
		if ( empty( $post_types ) || is_home() || is_archive() ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		$post_meta = $this->get_post_meta( get_the_id(), 'buttons_image' );

		/**
		 * If it is 'null' we assume that the meta post either not yet created or
		 * the associated key, 'buttons_image', in the meta is not set. So, we
		 * return to the default 'true'.
		 */
		return ( null === $post_meta ) ? true : $post_meta;
	}
}
