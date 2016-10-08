<?php

namespace XCo\WPSocialManager;

/**
 *
 */
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
		add_action( 'wp_footer', array( $this, 'add_buttons_list_tmpl' ), -30 );

		add_filter( 'the_content', array( $this, 'add_buttons_content_wrap' ), 30, 1 );
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
	public function add_buttons_content_wrap( $content ) {

		$post_types = $this->options->buttonsContent[ 'postTypes' ];
		$post_meta = $this->options->postMeta[ 'buttons_content' ];

		if ( ! is_singular( array_keys( $post_types ) ) || ! $post_meta ) {
			return $content;
		}

		$place = $this->options->buttonsContent[ 'placement' ];

		if ( ! in_array( $place, array_keys( self::get_button_placements() ), true ) ) {
			return $content;
		}

		if ( 'before' === $place ) {
			$content = $this->button_wrap_content( $this->post_id ) . $content;
		}

		if ( 'after' === $place ) {
			$content = $content . $this->button_wrap_content( $this->post_id );
		}

		return $content;
	}

	/**
	 * [buttons_wrapper description]
	 * @return [type] [description]
	 */
	public function add_buttons_list_tmpl() {
		$this->button_list_content();
	}

	/**
	 * [button_list_content description]
	 * @return [type] [description]
	 */
	protected function button_list_content() {

		$post_types = $this->options->buttonsContent[ 'postTypes' ];
		$post_meta = $this->options->postMeta[ 'buttons_content' ];

		if ( ! is_singular( array_keys( $post_types ) ) || ! $post_meta ) {
			return;
		}

		if ( wp_script_is( $this->plugin_name, 'enqueued' ) ) :

			$heading = $this->options->buttonsContent[ 'heading' ];
			$includes = $this->options->buttonsContent[ 'includes' ];
			$view = $this->options->buttonsContent[ 'view' ];

			var_dump( $includes );

			$prefix = self::get_attr_prefix(); ?>

		<?php if ( is_array( $includes ) && ! empty( $includes ) ) : ?>
		<script type="text/html" id="tmpl-buttons-content">
			<?php if ( ! empty( $heading ) ) : ?>
			<h4 class="<?php echo esc_attr( $prefix ); ?>-buttons__heading"><?php echo esc_html( $heading ); ?></h4>
			<?php endif; ?>
			<ul class="<?php echo esc_attr( $prefix ); ?>-buttons__list <?php echo esc_attr( $prefix ); ?>-buttons__list--<?php echo esc_attr( $view ); ?>">
			<?php foreach ( $includes as $value ) :

				$properties = self::get_social_properties( $value );
				$properties = wp_parse_args( $properties, array(
					'label' => '',
					'url' => '',
					'icon' => ''
				) );

				echo self::button_views( $view, array(
						'site' => $value,
						'icon' => $properties[ 'icon' ],
						'label' => $properties[ 'label' ]
					) );

			endforeach; ?>
			</ul>
		</script>
		<?php endif; // is_array( $includes )
		endif; // wp_script_is
	}

	/**
	 * [button_wrap_content description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	protected function button_wrap_content( $id ) {

		$prefix = self::get_attr_prefix();

		$view = $this->options->buttonsContent[ 'view' ];
		$placement = $this->options->buttonsContent[ 'placement' ];

		return "<div class='{$prefix}-buttons {$prefix}-buttons--content {$prefix}-buttons--content-{$placement}' id='{$prefix}-buttons-{$id}'></div>";
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

		$site = $args[ 'site' ];
		$icon = $args[ 'icon' ];
		$label = $args[ 'label' ];

		$prefix = self::get_attr_prefix();

		$templates = array(
			'icon' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{content.{$site}.endpoint}}' target='_blank'>{$icon}</a></li>",
			'text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{content.{$site}.endpoint}}' target='_blank'>{$label}</a></li>",
			'icon-text' => "<li class='{$prefix}-buttons__item item-{$site}'><a href='{{content.{$site}.endpoint}}' target='_blank'><span class='{$prefix}-buttons__item-icon'>{$icon}</span><span class='{$prefix}-buttons__item-text'>{$label}</span></a></li>"
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}
}