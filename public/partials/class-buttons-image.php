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
	 * Constructor
	 *
	 * Initialize the Buttons abstract class, and render the buttons
	 * in the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Endpoints $endpoints The Endpoints class instance.
	 */
	function __construct( Endpoints $endpoints ) {
		parent::__construct( $endpoints );
		$this->render();
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
		add_filter( 'the_content', array( $this, 'render_buttons' ), 100 );
	}

	/**
	 * Add social wrapper element into the images in the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The content with each image wrapped in an element to display
	 * 				  the social buttons on the images.
	 */
	public function render_buttons( $content ) {

		if ( empty( $content ) ) {
			return $content;
		}

		if ( false === $this->is_buttons_image() ) {
			return $content;
		}

		libxml_use_internal_errors( true );

		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$prefix = Helpers::get_attr_prefix();

		$images = $dom->getElementsByTagName( 'img' );

		$wrap = $dom->createElement( 'span' );

		$wrap->setAttribute( 'class', "{$prefix}-buttons {$prefix}-buttons--img {$prefix}-buttons--{$this->post_id}" );

		if ( $images->length >= 1 ) { // If we have, at least, 1 image.

			foreach ( $images as $id => $img ) :

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

				if ( 'html' === $this->theme_supports->is( 'buttons-mode' ) ||
					 'html' === $this->plugin->get_option( 'modes', 'buttons_mode' ) ) {

					$fragment = $dom->createDocumentFragment();
					$fragment->appendXML( $this->buttons_html() );

					$wrap_clone->appendChild( $fragment );
				}

			endforeach;

			$content = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );
		} // End if().

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
	 * @return string The formatted HTML of the buttons.
	 */
	public function buttons_html() {

		if ( wp_script_is( $this->plugin_slug, 'enqueued' ) ) :

			$list = '';
			$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

			if ( ! empty( $includes ) ) :

				$prefix = Helpers::get_attr_prefix();
				$prefix = esc_attr( $prefix );
				$view = $this->plugin->get_option( 'buttons_image', 'view' );

				$list .= "<span class='{$prefix}-buttons__list {$prefix}-buttons__list--{$view}' data-social-buttons='image'>";

				foreach ( $includes as $site ) :

					$button = Options::button_sites( 'image' );
					$icon = Helpers::get_social_icons( $site );

					$list .= $this->button_view( $view, array(
							'site'  => $site,
							'icon'  => apply_filters( 'ninecodes_social_manager_icon', $icon, $site, 'button-image' ),
							'label' => $button[ $site ],
					), 'image' );
				endforeach;
				$list .= '</span>';
			endif;

			/**
			 * Format the output to be a proper HTML markup,
			 * so it can be safely append into the DOM.
			 */
			$dom = new DOMDocument();
			$dom->loadHTML( mb_convert_encoding( $list, 'HTML-ENTITIES', 'UTF-8' ) );

			return preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );
		endif;
	}

	/**
	 * Add the Underscore.js template of the social media buttons.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 *
	 * @return void
	 */
	public function buttons_tmpl() {

		if ( 'json' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'json' === $this->plugin->get_option( 'modes', 'buttons_mode' ) ) {

			if ( false === $this->is_buttons_image() ) {
				return;
			}

			if ( wp_script_is( $this->plugin_slug, 'enqueued' ) ) :

				$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' ); ?>

	 			<?php if ( ! empty( $includes ) ) :
	 				$prefix = Helpers::get_attr_prefix();
	 				$view = $this->plugin->get_option( 'buttons_image', 'view' ); ?>
	 			<script type="text/html" id="tmpl-buttons-image">
	 				<span class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>" data-social-buttons="image">
	 				<?php foreach ( $includes as $site ) :

						$button = Options::button_sites( 'image' );
						$icon = Helpers::get_social_icons( $site );

						$list = $this->button_view( $view, array(
								'site'  => $site,
								'icon'  => apply_filters( 'ninecodes_social_manager_icon', $icon, $site, 'button-image' ),
								'label' => $button[ $site ],
						), 'image' );

	 					echo $list; // WPCS: XSS ok.
	 				endforeach; ?>
	 				</span>
	 			</script>
	 			<?php endif;
	 			endif;
		}
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

		$enable = (bool) $this->plugin->get_option( 'buttons_image', 'enabled' );

		if ( ! $enable ) {
			return false;
		}

		$post_types = (array) $this->plugin->get_option( 'buttons_image', 'post_types' );

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		$meta = (array) $this->metas->get_post_meta( $this->post_id, 'buttons_image' );

		return $meta;
	}
}
