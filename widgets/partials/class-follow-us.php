<?php

namespace XCo\WPSocialManager;

/**
 * Social Links Widget Class
 */
class WidgetFollowUs extends \WP_Widget {

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
	public function __construct() {

		$options = get_option( 'wp_social_manager_profiles' );

		$this->widget_id = 'wp-social-manager-follow-us';
		$this->widget_title = esc_html__( 'Follow Us', 'wp-social-manager' );

		$this->profiles = isset( $options ) ? $options : array();
		$this->accounts = SettingUtilities::get_social_properties();

		parent::__construct( $this->widget_id, esc_html__( 'Follow Us', 'wp-social-manager' ), array(
			'classname' => $this->widget_id,
			'description' => esc_html__( 'A list of social site URLs connected to this website.', 'wp-social-manager' )
		) );
	}

	/**
	 * [form description]
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( $instance, array(
				'title' => $this->widget_title
			) );

		$id = esc_attr( $this->get_field_id( 'title' ) );
		$name = esc_attr( $this->get_field_name( 'title' ) );
		$value = esc_html( $instance[ 'title' ] ); ?>

		<div class="<?php echo esc_attr( $this->widget_id ); ?>">
			<p>
				<label for="<?php echo $id; ?>"><?php esc_html_e( 'Title:', 'wp-social-manager' ); ?></label>
				<input class="widefat" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>">
			</p>

			<?php if( ! array_filter( $this->profiles ) ) : ?>
			<p>
			<?php
				$message = esc_html__( 'Please add at least one social profile of this website in the %s.', 'wp-social-manager' );
				$setting = '<a href="'.admin_url( 'options-general.php?page=wp-social-manager' ).'">'.esc_html__( 'setting page', 'wp-social-manager' ).'</a>';

				printf( $message, $setting ); ?></p>
			<?php else : ?>

			<p>
				<label><?php esc_html_e( 'Show these sites:', 'wp-social-manager' ); ?></label>
				<br>
				<?php
					foreach ( $this->profiles as $key => $value ) :

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
				<label for="<?php echo $id; ?>"><?php echo esc_html( $this->accounts[$key]['label'] ); ?></label>
				<br>
				<?php endforeach; ?>
			</p>

			<p>
				<label><?php esc_html_e( 'View:', 'wp-social-manager' ); ?></label>
				<br>
				<?php

					$id = esc_attr( $this->get_field_id( 'view' ) );
					$name = esc_attr( $this->get_field_name( 'view' ) );

					$views = SettingUtilities::get_button_views();
					$state = isset( $instance[ 'view' ] ) ? sanitize_key( $instance[ 'view' ] ) : 'icon';

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
		$instance[ 'view' ] = sanitize_key( $input[ 'view' ] );

		foreach ( $this->profiles as $key => $value ) {

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

		$this->enqueue_styles();

		echo $args[ 'before_widget' ]; // WPCS: XSS ok.

			if ( ! empty( $instance[ 'title' ] ) ) {
				$widget_title = wp_kses( apply_filters( 'widget_title', $instance[ 'title' ] ), array() );
				echo $args[ 'before_title' ] . $widget_title . $args[ 'after_title' ]; // WPCS: XSS ok.
			}

			$view = $instance[ 'view' ];

			echo "<ul class='{$this->widget_id}__list {$this->widget_id}__list--{$view}'>";

			foreach ( $this->profiles as $key => $value ) {

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

				$socials = SettingUtilities::get_social_properties( $key );
				$socials = wp_parse_args( $socials, array(
					'label' => '',
					'url' => '',
					'icon' => ''
				) );

				if ( ! $socials[ 'url' ] ) {
					continue;
				}

				$key = sanitize_key( $key );

				echo $this->output_template( $view, array(
					'site' => $key,
					'label' => esc_html( $socials[ 'label' ] ),
					'url' => esc_url( trailingslashit( $socials[ 'url' ] ) . $this->profiles[ $key ] ),
					'icon' => (string) $socials[ 'icon' ]
				) );
			}

			echo "</ul>";
		echo $args[ 'after_widget' ]; // WPCS: XSS ok.
	}

	/**
	 * [output_template description]
	 * @param  [type] $display [description]
	 * @param  [type] $args    [description]
	 * @return [type]          [description]
	 */
	protected function output_template( $view, $args ) {

		$pref = $this->widget_id;

		$args = wp_parse_args( $args, array(
			'site' => '',
			'label' => '',
			'icon' => '',
			'url' => ''
			) );

		$templates = array(
			'icon' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>{$args['icon']}</a></li>",
			'text' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>{$args['label']}</a></li>",
			'icon-text' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>
					<span class='{$pref}__item-icon'>{$args['icon']}</span><span class='{$pref}__item-text'>{$args['label']}</span></a>
				</li>"
		);

		return $templates[ $view ];
	}

	/**
	 * [actions description]
	 * @return [type] [description]
	 */
	protected function enqueue_styles() {

		$url = trailingslashit( plugin_dir_url( realpath( __DIR__ . '/..' ) ) );
		wp_enqueue_style( $this->widget_id, $url . 'public/css/styles-follow-us.css', array(), '0.1.0', 'all' );
	}
}

/**
 * Register the Widget.
 */
add_action( 'widgets_init', function(){
	register_widget( __NAMESPACE__ . '\\WidgetFollowUs' );
} );
