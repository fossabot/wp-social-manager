<?php

namespace XCo\WPSocialManager;

/**
 * Social Links Widget Class
 */
class WidgetSocialProfiles extends \WP_Widget {

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
	 * [$widget_title description]
	 * @var [type]
	 */
	protected $widget_id;

	/**
	 * [$widget_title description]
	 * @var string
	 */
	protected $widget_title;

	/**
	 * [$options description]
	 * @var [type]
	 */
	protected $options = array();

	/**
	 * [$profiles description]
	 * @var [type]
	 */
	protected $profiles = array();

	/**
	 * [__construct description]
	 */
	public function __construct( array $args ) {

		$options = get_option( "{$args['plugin_opts']}_profiles" );

		$this->widget_id = "{$args['plugin_name']}-profiles";
		$this->widget_title = esc_html__( 'Follow Us', 'wp-social-manager' );

		$this->options = isset( $options ) ? $options : array();
		$this->properties = OptionUtilities::get_social_properties();

		parent::__construct( $this->widget_id, esc_html__( 'Follow Us', 'wp-social-manager' ), array(
			'classname' => $this->widget_id,
			'description' => esc_html__( 'List of social profile and page URLs connected to this website.', 'wp-social-manager' )
		) );
	}

	/**
	 * [form description]
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function form( $instance ) {

		$id    = esc_attr( $this->get_field_id( 'title' ) );
		$name  = esc_attr( $this->get_field_name( 'title' ) );
		$title = esc_html( isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $this->widget_title ); ?>

		<div class="<?php echo esc_attr( $this->widget_id ); ?>">
			<p>
				<label for="<?php echo $id; ?>"><?php esc_html_e( 'Title:', 'wp-social-manager' ); ?></label>
				<input class="widefat" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="text" value="<?php echo $title; ?>">
			</p>

			<?php if( ! array_filter( $this->options ) ) : ?>
			<p>
			<?php
				$message = esc_html__( 'Please add at least one social profile of this website in the %s.', 'wp-social-manager' );
				$setting = '<a href="'.admin_url( 'options-general.php?page=wp-social-manager' ).'">'.esc_html__( 'setting page', 'wp-social-manager' ).'</a>';

				printf( $message, $setting ); ?></p>
			<?php else : ?>

			<p>
				<label><?php esc_html_e( 'Include these:', 'wp-social-manager' ); ?></label>
				<br>
				<?php
					foreach ( $this->options as $key => $value ) :

					if ( empty( $value ) ) {
						continue;
					}

					$key = sanitize_key( $key );
					$id = esc_attr( $this->get_field_id( $key ) );

					$name = esc_attr( $this->get_field_name( 'site' ) );
					$name = "{$name}[{$key}]";

					$state = isset( $instance[ 'site' ][ $key ] ) ? $instance[ 'site' ][ $key ] : 1;
					$state = checked( $state, 1, false ); ?>

				<input id="<?php echo $id; ?>" type="checkbox" class="checkbox" name="<?php echo $name; ?>" value="<?php echo $key; ?>" <?php echo $state; ?>>
				<label for="<?php echo $id; ?>"><?php echo esc_html( $this->properties[$key]['label'] ); ?></label>
				<br>
				<?php endforeach; ?>
			</p>

			<p>
				<label><?php esc_html_e( 'View:', 'wp-social-manager' ); ?></label>
				<br>
				<?php

					$id = esc_attr( $this->get_field_id( 'view' ) );
					$name = esc_attr( $this->get_field_name( 'view' ) );

					$views = OptionUtilities::get_button_views();
					$state = isset( $instance[ 'view' ] ) && ! empty( $instance[ 'view' ] ) ? $instance[ 'view' ] : 'icon';
					$state = sanitize_key( $state );

					foreach ( $views as $key => $label ) : $key = sanitize_key( $key ); ?>
						<input id="<?php echo "{$id}-{$key}";?>" type="radio" name="<?php echo $name;?>" value="<?php echo $key;?>" <?php checked( $state, $key, true );?>>
						<label for="<?php echo "{$id}-{$key}";?>"><?php echo esc_html( $label );?></label>
						<br>
					<?php endforeach; ?>
			</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * [update description]
	 * @param  [type] $input   [description]
	 * @param  [type] $intance [description]
	 * @return [type]          [description]
	 */
	public function update( $input, $instance ) {

		$instance[ 'title' ] = sanitize_text_field( $input[ 'title' ] );
		$instance[ 'view' ]  = sanitize_key( $input[ 'view' ] ? $input[ 'view' ] : 'icon' );

		foreach ( $this->options as $key => $value ) {

			if ( empty( $value ) ) {
				continue;
			}

			$instance[ 'site' ][ $key ] = wp_validate_boolean( $input[ 'site' ][ $key ] ) ? 1 : 0;
		}

		return $instance;
	}

	/**
	 * [widget description]
	 * @param  [type] $args     [description]
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function widget( $args, $instance ) {

		echo $args[ 'before_widget' ]; // WPCS: XSS ok.

			/**
			 * If somehow the widget title is not saved fallback to the default.
			 * @var string.
			 */
			$widget_title = ! isset( $instance[ 'title' ] ) ? $this->widget_title : $instance[ 'title' ];

			if ( ! empty( $widget_title ) ) {
				$widget_title = wp_kses( apply_filters( 'widget_title', $widget_title ), array() );
				echo $args[ 'before_title' ] . $widget_title . $args[ 'after_title' ]; // WPCS: XSS ok.
			}

			$view = isset( $instance[ 'view' ] ) ? $instance[ 'view' ] : 'icon';

			echo "<ul class='{$this->widget_id}__list {$this->widget_id}__list--{$view}'>";

			foreach ( $this->options as $key => $value ) {

				$site = 0;

				if ( !isset( $instance[ 'site' ][ $key ] ) && ! empty( $value ) ) {
					$site = 1;
				}

				if ( isset( $instance[ 'site' ][ $key ] ) && ! empty( $value ) ) {
					$site = $instance[ 'site' ][ $key ];
				}

				if ( 0 === $site ) {
					continue;
				}

				$properties = OptionUtilities::get_social_properties( $key );
				$properties = wp_parse_args( $properties, array(
					'label' => '',
					'url' => '',
					'icon' => ''
				) );

				if ( ! $properties[ 'url' ] ) {
					continue;
				}

				$key = sanitize_key( $key );

				echo self::button_views( $view, array(
						'site' => $key,
						'label' => esc_html( $properties[ 'label' ] ),
						'url' => esc_url( trailingslashit( $properties[ 'url' ] ) . $this->options[ $key ] ),
						'icon' => (string) $properties[ 'icon' ]
					) );
			}

			echo "</ul>";

		echo $args[ 'after_widget' ]; // WPCS: XSS ok.
	}

	/**
	 * [button_view description]
	 * @param  [type] $display [description]
	 * @param  [type] $args    [description]
	 * @return [type]          [description]
	 */
	protected static function button_views( $view = '', array $args ) {

		if ( empty( $view ) ) {
			return '';
		}

		$prefix = OutputUtilities::get_attr_prefix();
		$args   = wp_parse_args( $args, array(
				'site' => '',
				'label' => '',
				'icon' => '',
				'url' => ''
			) );

		$templates = array(
			'icon' => "<li class='{$prefix}-profiles__item item-{$args['site']}'><a href='{$args['url']}' target='_blank'>{$args['icon']}</a></li>",
			'text' => "<li class='{$prefix}-profiles__item item-{$args['site']}'><a href='{$args['url']}' target='_blank'>{$args['label']}</a></li>",
			'icon-text' => "<li class='{$prefix}-profiles__item item-{$args['site']}'><a href='{$args['url']}' target='_blank'><span class='{$prefix}-profiles__item-icon'>{$args['icon']}</span><span class='{$prefix}-profiles__item-text'>{$args['label']}</span></a></li>"
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}
}