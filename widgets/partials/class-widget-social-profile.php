<?php
/**
 * Widget: Social Media Profile
 *
 * @package SocialManager\Widget
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \NineCodes\SocialManager\Helpers;
use \NineCodes\SocialManager\Options;

/**
 * "Social Media Profiles" widget registration class.
 *
 * @link https://developer.wordpress.org/reference/classes/wp_widget/
 *
 * @since 2.0.0
 */
class Widget_Social_Profile extends Widget {

	/**
	 * Base ID of the widget; it has to be lowercase and unique.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $widget_id;

	/**
	 * Name for the widget displayed on the configuration page.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var string
	 */
	protected $widget_title;

	/**
	 * Initialize the class.
	 *
	 * Retrieve the required option, define the widget id, title and description,
	 * and register the widget to WordPress.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct( array(
			'id' => 'profile',
			'name' => __( 'Social Media Profile', 'ninecodes-social-manager' ),
			'description' => __( 'Display list of social media profile and page URLs connected to this website.', 'ninecodes-social-manager' ),
		) );

		$this->profiles = $this->plugin->option()->social_profiles();
		$this->widget_title = __( 'Follow Us', 'ninecodes-social-manager' );
	}

	/**
	 * Run Action and Filter hooks.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 30 );
	}

	/**
	 * Load the stylesheets for the public-facing side.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_styles() {

		if ( ! is_active_widget( false, false, $this->id_base, true ) ) {
			return;
		}

		$script_enqueue = $this->plugin->option()->get( 'enqueue' );

		if ( isset( $script_enqueue['stylesheet'] ) && 'on' === $script_enqueue['stylesheet'] ) {
			wp_enqueue_style( $this->plugin->plugin_slug );
		}
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		$id = $this->get_field_id( 'title' );
		$name = $this->get_field_name( 'title' );
		$title = isset( $instance['title'] ) ? $instance['title'] : $this->widget_title;

		/**
		 * The site profile inputs in the Settings from the user.
		 *
		 * @var array
		 */
		$site_profiles = $this->plugin->option()->get( 'profile' ); ?>

		<div class="<?php echo esc_attr( $this->id_base ); ?>">
			<p>
				<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Title:', 'ninecodes-social-manager' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<?php if ( ! array_filter( $site_profiles ) ) :  ?>
			<p>
			<?php
				/* translators: %s is replaced with the "setting page link" */
				$message = __( 'Please add at least one social media profile of this website in the %s.', 'ninecodes-social-manager' );
				$setting = '<a href="' . admin_url( 'options-general.php?page=ninecodes-social-manager' ) . '">' . __( 'setting page', 'ninecodes-social-manager' ) . '</a>';
				echo wp_kses(sprintf( $message, $setting ), array(
					'a' => array(
						'href' => true,
					),
				)); ?></p>
			<?php else : ?>
			<p>
				<label><?php esc_html_e( 'Includes:', 'ninecodes-social-manager' ); ?></label>
				<br>
			<?php
			foreach ( $site_profiles as $key => $value ) :
				if ( empty( $value ) ) {
					continue;
				}

				$key = esc_attr( sanitize_key( $key ) );
				$id = esc_attr( $this->get_field_id( $key ) );

				$name = esc_attr( $this->get_field_name( 'site' ) );
				$name = esc_attr( "{$name}[{$key}]" );

				$state = isset( $instance['site'][ $key ] ) ? $instance['site'][ $key ] : 1;
				$state = checked( $state, 1, false );

				echo "<input id='{$id}' type='checkbox' class='checkbox' name='{$name}'' value='{$key}' {$state}>"; // WPCS: XSS ok.
				echo "<label for='{$id}'>{$this->profiles[$key]['label']}</label><br>"; // WPCS: XSS ok.
			endforeach; ?>
			</p>

			<p>
				<label><?php esc_html_e( 'View:', 'ninecodes-social-manager' ); ?></label>
				<br />
				<?php
					$id = esc_attr( $this->get_field_id( 'view' ) );
					$name = esc_attr( $this->get_field_name( 'view' ) );
					$views = $this->plugin->option()->button_views(); ?>

				<select name="<?php echo esc_attr( $name ); ?>" class="widefat">
			<?php foreach ( $views as $key => $label ) :
				$key = sanitize_key( $key );

				$state = isset( $instance['view'] ) && ! empty( $instance['view'] ) ? $instance['view'] : 'icon';
				$state = selected( sanitize_key( $state ), $key, false );

				echo "<option value=\"{$key}\" {$state}>" . esc_html( $label ) . '</option>'; // WPCS: XSS ok.
			endforeach; ?>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Style:', 'ninecodes-social-manager' ); ?></label>
				<br />
				<?php
					$name = $this->get_field_name( 'style' );
					$styles = $this->plugin->option()->button_styles( 'widget_social_profile' ); ?>

				<select name="<?php echo esc_attr( $name ); ?>" class="widefat">
				<?php foreach ( $styles as $key => $label ) :
					$key = sanitize_key( $key );

					$state = isset( $instance['style'] ) && ! empty( $instance['style'] ) ? $instance['style'] : 'rounded';
					$state = selected( sanitize_key( $state ), $key, false );

					echo "<option value=\"{$key}\" {$state}>" . esc_html( $label ) . '</option>'; // WPCS: XSS ok.
				endforeach; ?>
				</select>
			</p>
			<?php endif; // array_filter( $site_profiles ). ?>
		</div>
		<?php
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $input New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $input, $instance ) {

		$instance['title'] = sanitize_text_field( $input['title'] );
		$instance['view'] = sanitize_key( $input['view'] ? $input['view'] : 'icon' );
		$instance['style'] = sanitize_key( $input['style'] ? $input['style'] : 'rounded' );

		/**
		 * The site profile inputs in the Settings from the user.
		 *
		 * @var array
		 */
		$site_profiles = (array) $this->plugin->option()->get( 'profile' );

		foreach ( $site_profiles as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$instance['site'][ $key ] = wp_validate_boolean( $input['site'][ $key ] ) ? 1 : 0;
		}

		return $instance;
	}

	/**
	 * Echoes the widget content.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		echo wp_kses_post( $args['before_widget'] );

		/*
		 * If somehow the widget title is not saved,
		 * fallback to the default.
		 */
		$widget_title = ! isset( $instance['title'] ) ? $this->widget_title : $instance['title'];

		/**
		 * The site profile inputs in the Settings from the user.
		 *
		 * @var array
		 */
		$site_profiles = (array) $this->plugin->option()->get( 'profile' );

		if ( ! empty( $widget_title ) ) {
			$widget_title = apply_filters( 'widget_title', $widget_title );
			echo wp_kses_post( $args['before_title'] . $widget_title . $args['after_title'] );
		}

		$prefix = $this->plugin->helper()->get_attr_prefix();
		$view = isset( $instance['view'] ) ? $instance['view'] : 'icon';
		$style = isset( $instance['style'] ) ? $instance['style'] : 'rounded';

		echo wp_kses("<div class=\"{$prefix}-profiles {$prefix}-profiles--{$view} {$prefix}-profiles--{$style}\">", array(
			'div' => array(
				'class' => true,
			),
		));

		foreach ( $site_profiles as $key => $value ) {
			$site = 0;

			if ( ! isset( $instance['site'][ $key ] ) && ! empty( $value ) ) {
				$site = 1;
			}
			if ( isset( $instance['site'][ $key ] ) && ! empty( $value ) ) {
				$site = $instance['site'][ $key ];
			}

			if ( 0 === $site ) {
				continue;
			}

			$profiles = wp_parse_args($this->profiles[ $key ], array(
				'label' => '',
				'url' => '',
			));

			if ( ! $profiles['url'] ) {
				continue;
			}

			$key = sanitize_key( $key );
			$list = $this->list_views($view, array(
				'site' => $key,
				'label' => esc_html( $profiles['label'] ),
				'url' => tmpl_profile_url( $profiles['url'], $site_profiles[ $key ] ),
				'icon' => $this->plugin->helper()->get_social_icons( $key ),
			));

			echo wp_kses($list, array(
				'a' => array(
					'class' => true,
					'href' => true,
					'target' => true,
				),
				'span' => array(
					'class' => true,
				),
				'svg' => array(
					'xmlns' => true,
					'viewbox' => true,
				),
				'use' => array(
					'xlink:href' => true,
				),
				'path' => array(
					'd' => true,
				),
			));
		} // End foreach().

		echo '</div>';
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Select and generate the widget list view.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param string $view The name of the list view to generate.
	 * @param array  $args Attributes of the list item such as the label, the icon, and the url.
	 * @return string An HTML list element with the attributes to display selected list view.
	 */
	protected function list_views( $view = '', array $args ) {

		if ( empty( $view ) ) {
			return '';
		}

		$prefix = $this->plugin->helper()->get_attr_prefix();
		$args = wp_parse_args($args, array(
			'site' => '',
			'label' => '',
			'icon' => '',
			'url' => '',
		));

		$templates = array(
			'icon' => "<a class='{$prefix}-profiles__item site-{$args['site']}' href='{$args['url']}' target='_blank'>{$args['icon']}</a>",
			'text' => "<a class='{$prefix}-profiles__item site-{$args['site']}' href='{$args['url']}' target='_blank'>{$args['label']}</a>",
			'icon_text' => "<a class='{$prefix}-profiles__item site-{$args['site']}' href='{$args['url']}' target='_blank'><span class='{$prefix}-profiles__item-icon'>{$args['icon']}</span><span class='{$prefix}-profiles__item-text'>{$args['label']}</span></a>",
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}
}

add_action( 'widgets_init', function() {
	register_widget( __NAMESPACE__ . '\\Widget_Social_Profile' );
});
