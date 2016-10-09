<?php

namespace XCo\WPSocialManager;

final class Buttons extends OutputUtilities {

	/**
	 * [$plugin_name description]
	 * @var [type]
	 */
	protected $plugin_name;

	/**
	 * [$plugin_opts description]
	 * @var [type]
	 */
	protected $plugin_opts;

	/**
	 * [$handle description]
	 * @var [type]
	 */
	protected $options;

	/**
	 * [__construct description]
	 * @param array $args [description]
	 */
	public function __construct( array $args ) {

		/**
		 * [$this->handle description]
		 * @var [type]
		 */
		$this->plugin_name = $args[ 'plugin_name' ];

		/**
		 * [$this->plugin_opts description]
		 * @var [type]
		 */
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->hooks();
	}

	/**
	 * [hooks description]
	 * @return [type] [description]
	 */
	protected function hooks() {

		add_action( 'wp_head', array( $this, 'setups' ), -30 );
		add_action( 'wp_footer', array( $this, 'add_template_script' ), -30 );
		add_filter( 'the_content', array( $this, 'add_buttons_content' ), 30, 1 );
		add_filter( 'the_content', array( $this, 'add_buttons_image' ), 30, 1 );
	}

	/**
	 * [setups description]
	 * @return [type] [description]
	 */
	public function setups() {

		/**
		 * [$this->post_id description]
		 * @var [type]
		 */
		$this->post_id = get_the_id();

		/**
		 * [$this->options description]
		 * @var [type]
		 */
		$this->options = (object) array(
			'postMeta' => get_post_meta( $this->post_id, $this->plugin_opts, true ),
			'buttonsContent' => get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttonsImage' => get_option( "{$this->plugin_opts}_buttons_image" )
		);
	}

	/**
	 * [add_buttons_wrap description]
	 * @param  [type] $content [description]
	 * @return string
	 */
	public function add_buttons_content( $content ) {

		$post_types = (array) $this->options->buttonsContent[ 'postTypes' ];

		if ( empty( $post_types ) || ! is_singular( array_keys( $post_types ) ) ) {
			return $content;
		}

		$includes = (array) $this->options->buttonsImage[ 'includes' ];

		if ( empty( $includes ) ) {
			return $content;
		}

		$place = $this->options->buttonsContent[ 'placement' ];

		if ( ! in_array( $place, array_keys( self::get_button_placements() ), true ) ) {
			return $content;
		}

		if ( ! isset( $this->options->postMeta[ 'buttons_content' ] ) ||
			 ! $this->options->postMeta[ 'buttons_content' ] ) {
			return $content;
		}

		$prefix = self::get_attr_prefix();
		$place = $this->options->buttonsContent[ 'placement' ];

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
	 * [add_buttons_image description]
	 * @param [type] $content [description]
	 */
	public function add_buttons_image( $content ) {

		if ( ! $this->options->buttonsImage[ 'enabled' ] ) {
			return $content;
		}

		$post_types = (array) $this->options->buttonsImage[ 'postTypes' ];

		if ( empty( $post_types ) || ! is_singular( array_keys( $post_types ) ) ) {
			return $content;
		}

		$includes = (array) $this->options->buttonsImage[ 'includes' ];

		if ( empty( $includes ) ) {
			return $content;
		}

		if ( ! isset( $this->options->postMeta[ 'buttons_image' ] ) ||
			 ! $this->options->postMeta[ 'buttons_image' ] ) {
			return $content;
		}

		libxml_use_internal_errors( true );

		$dom = new \DOMDocument();
    	$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

    	$prefix = self::get_attr_prefix();

    	$images = $dom->getElementsByTagName( 'img' );
    	$wrap = $dom->createElement( 'span' );

    	$wrap->setAttribute( 'class', "{$prefix}-buttons {$prefix}-buttons--img {$prefix}-buttons--post-{$this->post_id}" );

    	// If we have, at least, 1 image.
    	if ( $images->length >= 1 ) {
			foreach ( $images as $id => $img ) {

				$wrapClone = $wrap->cloneNode();

				$wrapId = absint( $id + 1 );
				$wrapId = sanitize_key( $wrapId );

				$wrapClone->setAttribute( 'id', "{$prefix}-buttons-{$this->post_id}-img-{$wrapId}" );

				if ( 'a' === $img->parentNode->nodeName ) {

					$linkParent = $img->parentNode;

					$linkParent->parentNode->replaceChild( $wrapClone, $linkParent );
					$wrapClone->appendChild( $linkParent );

				} else {

					$img->parentNode->replaceChild( $wrapClone, $img );
					$wrapClone->appendChild( $img );
				}
			}
    	}

    	$content = preg_replace( '/^<!DOCTYPE.+?>/', '', str_replace( array( '<html>', '</html>', '<body>', '</body>' ), array( '', '', '', '' ), $dom->saveHTML() ) );

    	return $content;
	}

	/**
	 * [buttons_wrapper description]
	 * @return [type] [description]
	 */
	public function add_template_script() {
		$this->buttons_content();
		$this->buttons_image();
	}

	/**
	 * [buttons_content description]
	 * @return [type] [description]
	 */
	protected function buttons_content() {

		$post_types = (array) $this->options->buttonsContent[ 'postTypes' ];

		if ( ! is_singular( array_keys( $post_types ) ) ||
			 ! isset( $this->options->postMeta[ 'buttons_content' ] ) ) {
			return;
		}

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$heading = $this->options->buttonsContent[ 'heading' ];
			$view = $this->options->buttonsContent[ 'view' ];
			$includes = (array) $this->options->buttonsContent[ 'includes' ];

			$prefix = self::get_attr_prefix(); ?>

		<?php if ( ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-content">
			<?php if ( ! empty( $heading ) ) : ?>
			<h4 class="<?php echo esc_attr( $prefix ); ?>-buttons__heading"><?php echo esc_html( $heading ); ?></h4>
			<?php endif; ?>
			<ul class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>">
			<?php foreach ( $includes as $value ) :

				$props = self::get_social_properties( $value );
				$props = wp_parse_args( $props, array(
						'label' => '',
						'url'   => '',
						'icon'  => ''
					) );

				echo self::button_views( $view, array(
							'site' => $value,
							'icon' => $props[ 'icon' ],
							'label' => $props[ 'label' ]
						) );

			endforeach; ?>
			</ul>
		</script>
		<?php endif; // is_array( $includes )
		endif; // wp_script_is
	}

	protected function buttons_image() {

		$post_types = (array) $this->options->buttonsImage[ 'postTypes' ];

		if ( ! is_singular( array_keys( $post_types ) ) ||
			 ! isset( $this->options->postMeta[ 'buttons_content' ] ) ) {
			return;
		}

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$view = $this->options->buttonsImage[ 'view' ];
			$includes = (array) $this->options->buttonsImage[ 'includes' ];

			$prefix = self::get_attr_prefix(); ?>

		<?php if ( ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-image">
			<ul class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>">
			<?php foreach ( $includes as $value ) :

				$props = self::get_social_properties( $value );
				$props = wp_parse_args( $props, array(
					'label' => '',
					'url' => '',
					'icon' => ''
				) );

				echo self::button_views( $view, array(
						'site' => $value,
						'icon' => $props[ 'icon' ],
						'label' => $props[ 'label' ]
					) );

			endforeach; ?>
			</ul>
		</script>
		<?php endif; // is_array( $includes )
		endif; // wp_script_is
	}

	/**
	 * [button_views description]
	 * @param  string $view [description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	protected static function button_views( $view = '', array $args ) {

		if ( empty( $view ) ) {
			return '';
		}

		/**
		 * [$args description]
		 * @var [type]
		 */
		$args = wp_parse_args( $args, array(
			'site'  => '',
			'icon'  => '',
			'label' => ''
		) );

		if ( in_array( '', $args, true ) ) {
			return;
		}

		$site  = $args[ 'site' ];
		$icon  = $args[ 'icon' ];
		$label = $args[ 'label' ];

		$prefix = self::get_attr_prefix();

		$templates = array(
			'icon' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' rel='nofollow'>{$icon}</a></li>",
			'text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' rel='nofollow'>{$label}</a></li>",
			'icon-text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{{$site}.endpoint}}' target='_blank' rel='nofollow'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a></li>"
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}
}