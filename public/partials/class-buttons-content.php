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

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
class ButtonsContent extends Buttons {

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

		if ( false === $this->is_buttons_content() ) {
			return $content;
		}

		$place = $this->plugin->get_option( 'buttons_content', 'placement' );
		$prefix = Helpers::get_attr_prefix();

		$button = "<div class='{$prefix}-buttons {$prefix}-buttons--content {$prefix}-buttons--content-{$place}' id='{$prefix}-buttons-{$this->post_id}'>";

		if ( 'html' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'html' === $this->plugin->get_option( 'modes', 'buttons_mode' ) ) {
			$button .= $this->buttons_html();
		}
		$button .= '</div>';

		if ( 'before' === $place ) {
			$content = preg_replace( '/\s*$^\s*/m', "\n", $button ) . $content;
		}

		if ( 'after' === $place ) {
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
	 * @return string The formatted HTML of the buttons.
	 */
	public function buttons_html() {

		if ( wp_script_is( $this->plugin_slug, 'enqueued' ) ) :

			$list = '';

			$view = $this->plugin->get_option( 'buttons_content', 'view' );
			$includes = (array) $this->plugin->get_option( 'buttons_content', 'includes' );

			$prefix = Helpers::get_attr_prefix();

			if ( ! empty( $includes ) ) :

				$heading = $this->plugin->get_option( 'buttons_content', 'heading' );
				$heading = esc_html( $heading );

				if ( ! empty( $heading ) ) :
					$list .= "<h4 class='{$prefix}-buttons__heading'>{$heading}</h4>";
				endif;

				$list .= "<span class='{$prefix}-buttons__list {$prefix}-buttons__list--{$view}' data-social-buttons='content'>";

				foreach ( $includes as $site ) :

					$button = Options::button_sites( 'content' );
					$icon = Helpers::get_social_icons( $site );

					$list .= $this->button_view( $view, array(
								'site'  => $site,
								'icon'  => $icon,
								'label' => $button[ $site ],
					), 'content' );
				endforeach;
				$list .= '</span>';
				endif;

			return $list;
		endif;
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

		if ( 'json' === $this->theme_supports->is( 'buttons-mode' ) ||
			 'json' === $this->plugin->get_option( 'modes', 'buttons_mode' ) ) {

			if ( false === $this->is_buttons_content() ) {
				return;
			}

			if ( wp_script_is( $this->plugin_slug, 'enqueued' ) ) :

				$heading = $this->plugin->get_option( 'buttons_content', 'heading' );
				$view = $this->plugin->get_option( 'buttons_content', 'view' );
				$includes = (array) $this->plugin->get_option( 'buttons_content', 'includes' );

				$prefix = Helpers::get_attr_prefix(); ?>

	 			<?php if ( ! empty( $includes ) ) : ?>
	 			<script type="text/html" id="tmpl-buttons-content">
	 				<?php if ( ! empty( $heading ) ) : ?>
	 				<h4 class="<?php echo esc_attr( $prefix ); ?>-buttons__heading"><?php echo esc_html( $heading ); ?></h4>
	 				<?php endif; ?>
	 				<span class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>" data-social-buttons="content">
	 				<?php foreach ( $includes as $site ) :

	 					$button = Options::button_sites( 'content' );
	 					$icon = Helpers::get_social_icons( $site );

	 					$list = $this->button_view( $view, array(
	 								'site'  => $site,
	 								'icon'  => $icon,
	 								'label' => $button[ $site ],
	 					), 'content' );

	 					echo $list; // WPCS: XSS ok.
	 				endforeach; ?>
	 				</span>
	 			</script>
	 			<?php endif;
	 			endif;
		}// End if().
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

		$post_types = (array) $this->plugin->get_option( 'buttons_content', 'post_types' );

		if ( empty( $post_types ) || ! is_singular( $post_types ) ) {
			return false;
		}

		$includes = (array) $this->plugin->get_option( 'buttons_content', 'includes' );

		if ( empty( $includes ) ) {
			return false;
		}

		$place = $this->plugin->get_option( 'buttons_content', 'placement' );

		if ( ! in_array( $place, array_keys( Options::button_placements() ), true ) ) {
			return false;
		}

		$meta = $this->metas->get_post_meta( $this->post_id, 'buttons_content' );

		return ( null === $meta ) ? true : $meta;
	}
}
