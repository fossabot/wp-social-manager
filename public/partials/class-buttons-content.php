<?php
/**
 * Public: ButtonsContent class
 *
 * @package SocialManager
 * @subpackage Public\Buttons
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \WP_Http as WP_HTTP;

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
class ButtonsContent extends Buttons {

	/**
	 * The response of `get_content_endpoints()` function
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

		$this->view = $this->plugin->get_option( 'buttons_content', 'view' );
		$this->placement = $this->plugin->get_option( 'buttons_content', 'placement' );

		$this->hooks();
		$this->render();
	}

	/**
	 * [hooks description]
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

			$response = $this->get_content_endpoints( get_the_id() );
			$this->response = $response['endpoints'];
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

		add_filter( 'the_content', array( $this, 'render_buttons' ), 50 );
	}

	/**
	 * Append or prepend the social media buttons wrapper element into the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The post content added with the social buttons wrapper element.
	 * 				  The wrapper may be added before or after the content, following
	 * 				  the option selected.
	 */
	public function render_buttons( $content ) {

		$button = '';
		$post_id = get_the_id();

		if ( false === $this->is_buttons_content() ) {
			return $content;
		}

		$is_html = 'html' === $this->mode && $this->response && is_singular();
		$is_json = 'json' === $this->mode;

		if ( $is_html || $is_json ) {

			$opening_tag = "<div class='{$this->prefix}-buttons {$this->prefix}-buttons--content {$this->prefix}-buttons--content-{$this->placement}' id='{$this->prefix}-buttons-{$post_id}'>";
			$button .= apply_filters( 'ninecodes_social_manager_buttons_html', $opening_tag,
				'wrap-opening',
				'button-content',
				array(
					'post_id' => $post_id,
					'prefix' => $this->prefix,
					'placement' => $this->placement,
				)
			);
		}

		if ( $is_html ) {
			$button .= $this->buttons_html( $this->response );
		}

		if ( $is_html || $is_json ) {
			$closing_tag = '</div>';
			$button .= apply_filters( 'ninecodes_social_manager_buttons_html', $closing_tag,
				'wrap-closing',
				'button-content',
				array(
					'post_id' => $post_id,
					'prefix' => $this->prefix,
					'placement' => $this->placement,
				)
			);
		}

		if ( 'before' === $this->placement ) {
			$content = preg_replace( '/\s*$^\s*/m', "\n", $button ) . $content;
		}

		if ( 'after' === $this->placement ) {
			$content = $content . preg_replace( '/\s*$^\s*/m', "\n", $button );
		}

		return $content;
	}

	/**
	 * Generate the buttons HTML for content.
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

			$heading = $this->plugin->get_option( 'buttons_content', 'heading' );
			$heading = esc_html( $heading );

			if ( ! empty( $heading ) ) {
				$list .= "<h4 class='{$this->prefix}-buttons__heading'>{$heading}</h4>";
			}

			$list .= "<div class='{$this->prefix}-buttons__list {$this->prefix}-buttons__list--{$this->view}' data-social-buttons='content'>";

			foreach ( $includes as $site => $endpoint ) :

				$icon = $this->get_button_icon( $site );
				$label = $this->get_button_label( $site, 'content' );
				$list .= $this->button_view( $this->view, 'content', array(
					'prefix' => $this->prefix,
					'site' => $site,
					'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, array(
						'site' => $site,
						'prefix' => $this->prefix,
						'context' => 'button-content',
					) ),
					'label' => $label,
					'endpoint' => $endpoint,
				) );

			endforeach;
			$list .= '</div>';
			endif;

		return $list;
	}

	/**
	 * Add the Underscore.js template of the social media buttons.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function buttons_tmpl() {

		if ( $this->is_buttons_content() && 'json' === $this->mode ) :
			if ( wp_script_is( $this->plugin_slug . '-app', 'enqueued' ) ) : ?>

		<script type="text/html" id="tmpl-buttons-content"><?php

		$heading = $this->plugin->get_option( 'buttons_content', 'heading' );
		$heading = wp_kses( $heading, array() );

		if ( ! empty( $heading ) ) {

			echo wp_kses( "<h4 class='{$this->prefix}-buttons__heading'>{$heading}</h4>", array(
				'h4' => array(
					'class' => true,
				),
			) );

		} ?><div class="<?php echo esc_attr( $this->prefix ); ?>-buttons__list <?php echo esc_attr( $this->prefix ); ?>-buttons__list--<?php echo esc_attr( $this->view ); ?>" data-social-buttons="content"><?php

			$includes = (array) $this->plugin->get_option( 'buttons_content', 'includes' ); foreach ( $includes as $site ) :

			$label = $this->get_button_label( $site, 'content' );
			$icon  = $this->get_button_icon( $site );
			$list  = $this->button_view( $this->view, 'content', array(
				'prefix' => $this->prefix,
				'site' => $site,
				'icon' => apply_filters( 'ninecodes_social_manager_icon', $icon, array(
					'site' => $site,
					'prefix' => $this->prefix,
					'context' => 'button-content',
				) ),
				'label' => $label,
				'endpoint' => "{{ data.endpoints.{$site} }}",
			));

			echo $list; // WPCS: XSS ok.
		endforeach; ?></div></script>
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
	protected function is_buttons_content() {

		if ( $this->in_amp() ) {
			return false;
		}

		$post_types = (array) $this->plugin->get_option( 'buttons_content', 'post_types' );

		/**
		 * If post types are not selected.
		 *
		 * NOTE: The social media buttons currently do not support Home and Archive display.
		 * But, we plan to have it in the future.
		 */
		if ( empty( $post_types ) || is_home() || is_archive() ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_content', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		$placement = $this->plugin->get_option( 'buttons_content', 'placement' );

		if ( ! in_array( $placement, array_keys( Options::button_placements() ), true ) ) {
			return false;
		}

		$post_meta = $this->get_post_meta( get_the_id(), 'buttons_content' );

		/**
		 * If it is 'null' we assume that the meta post either not yet created or
		 * the associated key, 'buttons_image', in the meta is not set. So, we
		 * return to the default 'true'.
		 */
		return ( null === $post_meta ) ? true : $post_meta;
	}
}
