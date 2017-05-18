<?php
/**
 * Public: Button_Content class
 *
 * @package SocialManager
 * @subpackage Public\Button
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
class Button_Content extends Button {

	/**
	 * The Buttons Content view set in the Settings.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var string
	 */
	protected $view;

	/**
	 * The Buttons Content placmeent set in the Settings.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var string
	 */
	protected $placement;

	/**
	 * The response of `get_content_endpoint()` function
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

		$this->view = $this->plugin->option->get( 'button_content', 'view' );
		$this->placement = $this->plugin->option->get( 'button_content', 'placement' );

		$this->hooks();
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

		add_action( 'wp', array( $this, 'get_endpoint' ), -30 );
		add_action( 'wp_footer', array( $this, 'render_tmpl' ), -30 );
	}

	/**
	 * Function to setup the image buttons when it is in HTML mode.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Use $this->endpoint property to access the Endpoint class method.
	 * @access public
	 *
	 * @return array
	 */
	public function get_endpoint() {

		if ( $this->is_active() && 'html' === $this->mode ) {

			$response = $this->endpoint->get_content_endpoint( get_the_id() );
			$this->response = $response['endpoint'];
		}

		return $this->response;
	}

	/**
	 * Append or prepend the social media buttons wrapper element into the content.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Prevent appending the social buttons when the post is not yet published.
	 * @access public
	 *
	 * @param string $content The post content.
	 * @return string The post content added with the social buttons wrapper element.
	 *                The wrapper may be added before or after the content, following the option selected.
	 */
	public function render_button( $content ) {

		if ( empty( $content ) || ! $this->is_active() ) {
			return $content;
		}

		$button = '';
		$post_id = get_the_id();

		$is_html = 'html' === $this->mode && $this->response;
		$is_json = 'json' === $this->mode;

		if ( $is_html || $is_json ) {

			$style = $this->plugin->option->get( 'button_content', 'style' );

			$button .= "<div class=\"{$this->attr_prefix}-button {$this->attr_prefix}-button--content {$this->attr_prefix}-button--{$this->placement} {$this->attr_prefix}-button--{$style}\" id=\"{$this->attr_prefix}-button-{$post_id}\" data-social-manager=\"button-content\">";
		}

		if ( $is_html ) {
			$button .= $this->render_html( $this->response );
		}

		if ( $is_html || $is_json ) {
			$button .= '</div>';
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
	 * @since 1.0.6 - Renamed `data-social-button` to `data-social-manager` of the `span` (wrapper) element.
	 * @access public
	 *
	 * @param array $includes Data to include in the button.
	 * @return string The formatted HTML of the buttons.
	 */
	public function render_html( array $includes ) {

		$list = '';

		if ( ! empty( $includes ) ) :

			$heading = $this->plugin->option->get( 'button_content', 'heading' );
			$heading = esc_html( $heading );

			if ( ! empty( $heading ) ) {
				$list .= "<h4 class=\"{$this->attr_prefix}-button__heading\">{$heading}</h4>";
			}

			$prefix = $this->attr_prefix;
			$list .= "<div class=\"{$this->attr_prefix}-button__list {$this->attr_prefix}-button__list--{$this->view}\">";

			foreach ( $includes as $site => $endpoint ) :

				$icon = $this->get_icons( $site );

				if ( ! $icon || ! $endpoint ) {
					continue;
				}

				$label = $this->get_label( $site, 'content' );
				$list .= $this->render_view( $this->view, 'content', array(
					'attr_prefix' => $prefix,
					'site' => $site,
					'icon' => $icon,
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
	 * @since 1.0.6 - Renamed `data-social-button` to `data-social-manager` of the `span` (wrapper) element.
	 * @access public
	 *
	 * @return void
	 */
	public function render_tmpl() {

		if ( $this->is_active() && 'json' === $this->mode ) :
			if ( wp_script_is( $this->plugin_slug . '-app', 'enqueued' ) ) : ?>

		<script type="text/html" id="tmpl-button-content"><?php

		$heading = $this->plugin->option->get( 'button_content', 'heading' );
		$heading = wp_kses( $heading, array() );

		if ( ! empty( $heading ) ) {

			echo wp_kses( "<h4 class=\"{$this->attr_prefix}-button__heading\">{$heading}</h4>", array(
				'h4' => array(
					'class' => true,
				),
			) );

		} ?><div class="<?php echo esc_attr( $this->attr_prefix ); ?>-button__list <?php echo esc_attr( $this->attr_prefix ); ?>-button__list--<?php echo esc_attr( $this->view ); ?>"><?php

		$prefix = $this->attr_prefix;
		$includes = (array) $this->plugin->option->get( 'button_content', 'include' );

foreach ( $includes as $site => $value ) :

	$icon = $this->get_icons( $site );

	if ( ! $icon || ! $value ) {
		continue;
	}

	$label = $this->get_label( $site, 'content' );
	$list  = $this->render_view( $this->view, 'content', array(
		'attr_prefix' => $prefix,
		'site' => $site,
		'icon' => $icon,
		'label' => $label,
		'endpoint' => "data.endpoints.{$site}",
	));

	echo $list; // WPCS: XSS ok.
		endforeach; ?></div></script>
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
		 * Filter the icons displayed in the social media buttons content.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var array
		 */
		$icons = apply_filters( 'ninecodes_social_manager_icons', $icons, 'button_content', array(
			'attr_prefix' => $this->attr_prefix,
		) );

		$icons = isset( $icons[ $site ] ) ? sanitize_icon( $icons[ $site ] ) : array_map( __NAMESPACE__ . '\\sanitize_icon', $icons );

		return $icons;
	}

	/**
	 * The Utility method to check if buttons content should be generated.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Use $this->metas property to access the Meta class method.
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_active() {

		/**
		 * Check whether the button is active on the Setting level.
		 *
		 * @var bool
		 */
		$status = $this->plugin->helper->get_button_content_status();

		if ( ! $status ) {
			return false;
		}

		/**
		 * Ok, so it appears that the button is active on the upper level,
		 * let's check if it is loaded in the appropriate single page.
		 *
		 * @var bool
		 */
		if ( ! is_singular( $status['post_type'] ) || is_feed() || $this->in_amp() ) {
			return false;
		}

		if ( is_singular() && 'publish' !== $this->get_post_status() ) {
			return false;
		}

		/**
		 * Ok, we have come to this far.
		 * Let's check if it is enabled in the individual post.
		 *
		 * @var bool
		 */
		$post_meta = $this->plugin->meta->get_post_meta( get_the_id(), 'button_content' );

		/**
		 * If it is 'null' we assume that the meta post either not yet created or
		 * the associated key, 'buttons_image', in the meta is not set. So, we
		 * return to the default 'true'.
		 */
		return null === $post_meta;
	}
}
