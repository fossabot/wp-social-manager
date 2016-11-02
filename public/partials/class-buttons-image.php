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
	 * Get apis URL response.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object
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

		add_filter( 'the_content', array( $this, 'render_buttons' ), 90 );
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

		if ( ! $this->is_buttons_image() ) {
			return $content;
		}

		$dom = new DOMDocument();
		$errors = libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$images = $dom->getElementsByTagName( 'img' );

		if ( 0 !== $images->length ) : // If we have at least 1 image.

			$prefix = $this->get_button_attr_prefix();

			$wrap = $dom->createElement( 'span' );
			$wrap->setAttribute( 'class', "{$prefix}-buttons {$prefix}-buttons--img {$prefix}-buttons--{$this->post_id}" );

			foreach ( $images as $index => $img ) :
				$wrap_id = absint( $index + 1 );
				$wrap_id = sanitize_key( $wrap_id );

				$wrap_clone = $wrap->cloneNode();
				$wrap_clone->setAttribute( 'id', "{$prefix}-buttons-{$this->post_id}-img-{$wrap_id}" );

				if ( 'a' === $img->parentNode->nodeName ) {
					$link_parent = $img->parentNode;

					$link_parent->parentNode->replaceChild( $wrap_clone, $link_parent );
					$wrap_clone->appendChild( $link_parent );
				} else {
					$img->parentNode->replaceChild( $wrap_clone, $img );
					$wrap_clone->appendChild( $img );
				}

				if ( $this->is_buttons_image() && 'html' === $this->get_buttons_mode() ) :
					$response = wp_remote_get( trailingslashit( get_rest_url() ) . $this->plugin_slug . '/1.0/buttons?id=' . $this->post_id );

					if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
						$body = wp_remote_retrieve_body( $response );
						$this->response = json_decode( $body );

						$fragment = $dom->createDocumentFragment();
						$fragment->appendXML( $this->buttons_html( $this->response->images[ $index ] ) );

						$wrap_clone->appendChild( $fragment );
					}
				endif;
			endforeach;

			$content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(
				array( '<html>', '</html>', '<body>', '</body>' ),
				array( '', '', '', '' ),
				$dom->saveHTML()
			));
		endif;

		libxml_clear_errors();
		libxml_use_internal_errors( $errors );

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
	 * @param array $includes Sites to include in the buttons.
	 * @return string The formatted HTML of the buttons.
	 */
	public function buttons_html( $includes ) {

		if ( 'html' === $this->get_buttons_mode() && wp_script_is( $this->plugin_slug, 'enqueued' ) ) :
			$list = '';

			if ( ! empty( $includes ) ) :

				$prefix = $this->get_button_attr_prefix();
				$view   = $this->plugin->get_option( 'buttons_image', 'view' );

				$list .= "<span class='{$prefix}-buttons__list {$prefix}-buttons__list--{$view}' data-social-buttons='image'>";

				foreach ( $includes as $site => $endpoint ) :
					$label = $this->get_button_label( $site, 'image' );
					$icon  = $this->get_button_icon( $site );
					$list .= $this->button_view( $view, 'image', array(
						'prefix' => $prefix,
						'site' => $site,
						'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, $site, 'button-image' ),
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
		endif;
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

		if ( $this->is_buttons_image() && 'json' === $this->get_buttons_mode() ) :
			if ( wp_script_is( $this->plugin_slug . '-app', 'enqueued' ) ) :
				$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' ); ?>

			<?php if ( ! empty( $includes ) ) :

				$prefix = $this->get_button_attr_prefix();
				$view = $this->plugin->get_option( 'buttons_image', 'view' ); ?>

			<script type="text/html" id="tmpl-buttons-image">
				<span class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>" data-social-buttons="image">

				<?php foreach ( $includes as $site ) :

					$label = $this->get_button_label( $site, 'image' );
					$icon  = $this->get_button_icon( $site );
					$list  = $this->button_view($view, 'image', array(
						'prefix' => $prefix,
						'site' => $site,
						'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, $site, 'button-image' ),
						'label' => $label,
						'endpoint' => "{{data.{$site}}}",
					));

					echo $list; // WPCS: XSS ok.
				endforeach; ?>
				</span>
			</script>
			<?php endif;
			endif;
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

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_image', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		$meta = $this->metas->get_post_meta( $this->post_id, 'buttons_image' );

		/**
		 * If it is 'null' we assume that the meta post either not yet created or
		 * the associated key, 'buttons_image', in the meta is not set. So, we
		 * return to the default 'true'.
		 */
		return ( null === $meta ) ? true : $meta;
	}
}
