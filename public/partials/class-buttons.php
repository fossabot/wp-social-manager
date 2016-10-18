<?php
/**
 * Public: Buttons Class
 *
 * @package NineCodes_Social_Manager
 * @subpackage Public\Buttons
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 * The Class that define the social buttons output.
 *
 * @since 1.0.0
 */
final class Buttons extends Endpoints {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_opts = '';

	/**
	 * The Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	protected $metas = null;

	/**
	 * ThemeSupports class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	protected $supports = null;

	/**
	 * Options retrieved from database.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	protected $options = null;

	/**
	 * The current post ID in the loop.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	protected $post_id = null;

	/**
	 * Constructor: Initialize the Buttons Class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array 		$args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 * @param Metas 		$metas 		The Metas class instance.
	 * @param ThemeSupports $supports 	The ThemeSupports class instance.
	 */
	function __construct( array $args, Metas $metas, ThemeSupports $supports ) {

		parent::__construct( $args, $metas );

		$this->plugin_name = $args['plugin_name'];
		$this->plugin_opts = $args['plugin_opts'];

		$this->metas = $metas;
		$this->supports = $supports;

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
	 * Get the current WordPress post ID.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {

		$this->options = (object) array(
			'profiles' => get_option( "{$this->plugin_opts}_profiles" ),
			'buttonsContent' => get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttonsImage' => get_option( "{$this->plugin_opts}_buttons_image" ),
			'modes' => get_option( "{$this->plugin_opts}_modes" ),
		);

		if ( is_singular() ) {
			$this->post_id = get_the_id();
		}
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
		$prefix = parent::get_attr_prefix();

		$button  = "<div class='{$prefix}-buttons {$prefix}-buttons--content {$prefix}-buttons--content-{$place}' id='{$prefix}-buttons-{$this->post_id}'>";
		if ( 'html' === $this->supports->is_theme_support( 'buttons-mode' ) ||
			 'html' === $this->options->modes['buttonsMode'] ) {
			$button .= $this->buttons_content_html();
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

		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$prefix = parent::get_attr_prefix();

		$images = $dom->getElementsByTagName( 'img' );

		$wrap = $dom->createElement( 'span' );

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

				if ( 'html' === $this->supports->is_theme_support( 'buttons-mode' ) ||
					 'html' === $this->options->modes['buttonsMode'] ) {

					$fragment = $dom->createDocumentFragment();
					$fragment->appendXML( $this->buttons_image_html() );

					$wrap_clone->appendChild( $fragment );
				}
			}
		}

		$content = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );

		return $content;
	}

	/**
	 * Generate the buttons HTML for content.
	 *
	 * Used when the "Buttons Mode" is set to 'HTML'.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string The formatted HTML of the buttons.
	 */
	protected function buttons_content_html() {

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$list = '';

			$view = $this->options->buttonsContent['view'];
			$includes = (array) $this->options->buttonsContent['includes'];
			$prefix = parent::get_attr_prefix();

			if ( ! empty( $includes ) ) :

				$heading = $this->options->buttonsContent['heading'];
				$heading = esc_html( $heading );

				if ( ! empty( $heading ) ) :
					$list .= "<h4 class='{$prefix}-buttons__heading'>{$heading}</h4>";
				endif;

				$list .= "<span class='{$prefix}-buttons__list {$prefix}-buttons__list--{$view}' data-social-buttons='content'>";

				foreach ( $includes as $site ) :
					$props = parent::get_social_properties( $site );
					$icon = parent::get_social_icons( $site );
					$list .= $this->button_view( $view, array(
							'site'  => $site,
							'icon'  => $icon,
							'label' => $props['label'],
					), 'content' );
				endforeach;
				$list .= '</span>';
				endif;

			return $list;
		endif;
	}

	/**
	 * Generate the buttons HTML for image.
	 *
	 * Used when the "Buttons Mode" is set to 'HTML'.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string The formatted HTML of the buttons.
	 */
	protected function buttons_image_html() {

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$list = '';
			$includes = (array) $this->options->buttonsImage['includes'];

			if ( ! empty( $includes ) ) :

				$prefix = parent::get_attr_prefix();
				$prefix = esc_attr( $prefix );
				$view = $this->options->buttonsImage['view'];

				$list .= "<span class='{$prefix}-buttons__list {$prefix}-buttons__list--{$view}' data-social-buttons='image'>";

				foreach ( $includes as $site ) :

					$props = parent::get_social_properties( $site );
					$icon = parent::get_social_icons( $site );

					$list .= $this->button_view( $view, array(
								'site'  => $site,
								'icon'  => apply_filters( 'wp_social_manager_icon', $icon, $site, 'button-image' ),
								'label' => $props['label'],
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
	public function add_template_script() {

		if ( 'json' === $this->supports->is_theme_support( 'buttons-mode' ) ||
			 'json' === $this->options->modes['buttonsMode'] ) {
			$this->buttons_content_tmpl();
			$this->buttons_image_tmpl();
		}
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
			$view = $this->options->buttonsContent['view'];
			$includes = (array) $this->options->buttonsContent['includes'];

			$prefix = parent::get_attr_prefix(); ?>

		<?php if ( ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-content">
			<?php if ( ! empty( $heading ) ) : ?>
			<h4 class="<?php echo esc_attr( $prefix ); ?>-buttons__heading"><?php echo esc_html( $heading ); ?></h4>
			<?php endif; ?>
			<span class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>" data-social-buttons="content">
			<?php foreach ( $includes as $site ) :

				$props = parent::get_social_properties( $site );
				$icon = parent::get_social_icons( $site );
				$list = $this->button_view( $view, array(
							'site'  => $site,
							'icon'  => $icon,
							'label' => $props['label'],
				), 'content' );

				echo $list; // WPCS: XSS ok.
			endforeach; ?>
			</span>
		</script>
		<?php endif;
		endif;
	}

	/**
	 * Build the template for the social buttons shown in images of the content.
	 *
	 * @since 1.0.0
	 * @access protected
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

			$includes = (array) $this->options->buttonsImage['includes']; ?>

		<?php if ( ! empty( $includes ) ) :
			$prefix = parent::get_attr_prefix();
			$view = $this->options->buttonsImage['view']; ?>
		<script type="text/html" id="tmpl-buttons-image">
			<span class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>"
				data-social-buttons="image">
			<?php foreach ( $includes as $site ) :

				$props = parent::get_social_properties( $site );
				$icon = parent::get_social_icons( $site );

				$list = $this->button_view( $view, array(
							'site'  => $site,
							'icon'  => apply_filters( 'wp_social_manager_icon', $icon, $site, 'button-image' ),
							'label' => $props['label'],
				), 'image' );

				echo $list; // WPCS: XSS ok.
			endforeach; ?>
			</span>
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
	protected function button_view( $view = '', array $args, $context = '' ) {

		if ( empty( $view ) ||
			 empty( $args ) ||
			 empty( $context ) ) {
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

		$prefix = parent::get_attr_prefix();
		$url = $this->get_endpoint_url( $site, $context );

		if ( empty( $url ) ) {
			return '';
		}

		$templates = array(
			'icon' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button'>{$icon}</a>",
			'text' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button'>{$label}</a>",
			'icon-text' => "<a class='{$prefix}-buttons__item item-{$site}' href='{$url}' target='_blank' role='button'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a>",
		);

		$allowed_html = wp_kses_allowed_html( 'post' );

		/**
		 * Add permision to SVG.
		 * <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3.998v3h-2a1 1 0 0 0-1 1v2h3v3h-3v7h-3v-7h-2v-3h2v-2.5a3.5 3.5 0 0 1 3.5-3.5H19zm1-2H4c-1.105 0-1.99.895-1.99 2l-.01 16c0 1.104.895 2 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z"/></svg>'
		 */
		$allowed_html['svg'] = array(
			'xmlns' => true,
			'viewBox' => true,
		);
		$allowed_html['path'] = array(
			'd' => true,
			'viewBox' => true,
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
	protected function get_endpoint_url( $site, $context ) {

		if ( ! $site || ! in_array( $context, array( 'content', 'image' ), true ) ) {
			return '';
		}

		$url = '';

		if ( 'json' === $this->supports->is_theme_support( 'buttons-mode' ) ||
			 'json' === $this->options->modes['buttonsMode'] ) {
			$url = "{{{$site}.endpoint}}";
		}

		if ( 'html' === $this->supports->is_theme_support( 'buttons-mode' ) ||
			 'html' === $this->options->modes['buttonsMode'] ) {

			$urls = array();

			switch ( $context ) {
				case 'content':
					$urls = $this->get_content_endpoints( $this->post_id );
					break;

				case 'image':
					$urls = $this->get_image_endpoints( $this->post_id );
					break;
			}

			$url = $urls[ $site ]['endpoint'];
		}

		return $url;
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

		$meta = (bool) $this->metas->get_post_meta( $this->post_id, 'buttons_content' );

		return $meta;
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

		$meta = (array) $this->metas->get_post_meta( $this->post_id, 'buttons_image' );

		return $meta;
	}
}
