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

		$options = get_option( 'wp_social_manager_accounts' );

		$this->widget_id = 'wp-social-manager-follow';
		$this->widget_title = esc_html__( 'Follow Us', 'wp-social-manager' );

		$this->options  = isset( $options[ 'accounts' ] ) ? (array) $options[ 'accounts' ] : array();
		$this->accounts = Options::accounts();

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

			<?php if( ! array_filter( $this->options ) ) : ?>
			<p>
			<?php
				$message = esc_html__( 'Please set at least one social profile of this website in the %s.', 'wp-social-manager' );
				$setting = '<a href="'.admin_url( 'options-general.php?page=wp-social-manager' ).'">'.esc_html__( 'setting page', 'wp-social-manager' ).'</a>';

				printf( $message, $setting ); ?></p>
			<?php else : ?>

			<p>
				<label><?php esc_html_e( 'Show these sites:', 'wp-social-manager' ); ?></label>
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
				<label for="<?php echo $id; ?>"><?php echo esc_html( $this->accounts[$key]['label'] ); ?></label>
				<br>
				<?php endforeach; ?>
			</p>

			<p>
				<label><?php esc_html_e( 'Display as:', 'wp-social-manager' ); ?></label>
				<br>
				<?php
					$id = esc_attr( $this->get_field_id( 'display' ) );
					$name = esc_attr( $this->get_field_name( 'display' ) );
					$state = isset( $instance[ 'display' ] ) ? $instance[ 'display' ] : 'icon'; ?>

				<input id="<?php echo "{$id}-icon"; ?>" type="radio" name="<?php echo $name; ?>" value="icon" <?php checked( $state, 'icon', true ); ?>>
				<label for="<?php echo "{$id}-icon"; ?>"><?php esc_html_e( 'Icon Only', 'wp-social-manager' ); ?></label>
				<br>
				<input id="<?php echo "{$id}-text"; ?>" type="radio" name="<?php echo $name; ?>" value="text" <?php checked( $state, 'text', true ); ?>>
				<label for="<?php echo "{$id}-text"; ?>"><?php esc_html_e( 'Text Only', 'wp-social-manager' ); ?></label>
				<br>
				<input id="<?php echo "{$id}-icon-text"; ?>" type="radio" name="<?php echo $name; ?>" value="icon-text" <?php checked( $state, 'icon-text', true ); ?>>
				<label for="<?php echo "{$id}-icon-text"; ?>"><?php esc_html_e( 'Icon and Text', 'wp-social-manager' ); ?></label>
				<br>
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
		$instance[ 'display' ] = sanitize_key( $input[ 'display' ] );

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

		if ( ! empty( $instance[ 'title' ] ) ) {
			$widget_title = wp_kses( apply_filters( 'widget_title', $instance[ 'title' ] ), array() );
			echo $args[ 'before_title' ] . $widget_title . $args[ 'after_title' ]; // WPCS: XSS ok.
		}

		$display = $instance[ 'display' ];

		echo "<ul class='{$this->widget_id}__list {$this->widget_id}__list--{$display}'>";

		foreach ( $this->options as $key => $value ) {

			$site = 0;

			if ( !isset( $instance[ 'site' ][ $key ] ) && !empty( $value ) ) {
				$site = 1;
			}

			if ( isset( $instance[ 'site' ][ $key ] ) && !empty( $value ) ) {
				$site = $instance[ 'site' ][ $key ];
			}

			if ( 0 === $site ) {
				continue;
			}

			$account = get_social_account( $key );
			$account = wp_parse_args( $account, array(
				'lable' => '',
				'baseURL' => '',
				'icon' => ''
			) );

			if ( !$account[ 'baseURL' ] ) {
				continue;
			}

			$key = sanitize_key( $key );

			echo $this->output_template( $display, array(
				'site' => $key,
				'label' => esc_html( $account[ 'label' ] ),
				'url' => esc_url( trailingslashit( $account[ 'baseURL' ] ) . $this->options[ $key ] ),
				'icon' => (string) $account[ 'icon' ]
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
	protected function output_template( $display, $args ) {

		$pref = $this->widget_id;

		$args = wp_parse_args( $args, array(
			'site' => '',
			'label' => '',
			'icon' => '',
			'url' => ''
			) );

		$template = array(
			'icon' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>{$args['icon']}</a></li>",
			'text' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>{$args['label']}</a></li>",
			'icon-text' => "<li class='{$pref}__item {$pref}__item--{$args['site']}'><a class='{$pref}__url' href='{$args['url']}' target='_blank'>
					<span class='{$pref}__item-icon'>{$args['icon']}</span><span class='{$pref}__item-text'>{$args['label']}</span></a>
				</li>"
		);

		return $template[ $display ];
	}
}

/**
 * Register the Widget.
 */
add_action( 'widgets_init', function(){
	register_widget( __NAMESPACE__ . '\\WidgetFollowUs' );
} );