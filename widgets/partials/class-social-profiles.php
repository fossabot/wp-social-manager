<?php
/**
 * Widget: WidgetSocialProfiles class
 *
 * @package SocialManager
 * @subpackage Widgets\SocialProfiles
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \WP_Widget;

/**
 * "Social Profiles" widget registration class.
 *
 * @link https://developer.wordpress.org/reference/classes/wp_widget/
 *
 * @since 1.0.0
 */
final class WidgetSocialProfiles extends WP_Widget {

	/**
	 * Base ID of the widget; it has to be lowercase and unique.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $widget_id;

	/**
	 * Name for the widget displayed on the configuration page.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $widget_title;

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $option_slug = 'ncsocman';

	/**
	 * Profile and Page usernames saved in the option.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $options = array();

	/**
	 * Social properties, such as the URLs and labels.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $properties = array();

	/**
	 * Initialize the class.
	 *
	 * Retrieve the required option, define the widget id, title and description,
	 * and register the widget to WordPress.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin_slug = $plugin->get_slug();
		$this->option_slug = $plugin->get_opts();

		$options = get_option( "{$this->option_slug}_profiles" );
		$this->options = ! empty( $options ) ? $options : array();

		$this->profiles = Options::social_profiles();

		$this->widget_id = "{$this->plugin_slug}-profiles";
		$this->widget_title = esc_html__( 'Follow Us', 'ninecodes-social-manager' );

		parent::__construct($this->widget_id, esc_html__( 'Social Media Profiles', 'ninecodes-social-manager' ), array(
			'classname' => $this->widget_id,
			'description' => esc_html__( 'Display list of social media profile and page URLs connected to this website.', 'ninecodes-social-manager' ),
		));
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		$id = $this->get_field_id( 'title' );
		$name = $this->get_field_name( 'title' );
		$title = isset( $instance['title'] ) ? $instance['title'] : $this->widget_title; ?>

		<div class="<?php echo esc_attr( $this->widget_id ); ?>">
			<p>
				<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Title:', 'ninecodes-social-manager' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<?php if ( ! array_filter( $this->options ) ) :  ?>
			<p>
			<?php
				$message = esc_html__( 'Please add at least one social media profile of this website in the %s.', 'ninecodes-social-manager' );
				$setting = '<a href="' . admin_url( 'options-general.php?page=ninecodes-social-manager' ) . '">' . esc_html__( 'setting page', 'ninecodes-social-manager' ) . '</a>';
				echo wp_kses( sprintf( $message, $setting ), array(
					'a' => array(
						'href' => true,
					),
				) ); ?></p>
			<?php else : ?>

			<p>
				<label><?php esc_html_e( 'Include these', 'ninecodes-social-manager' ); ?></label>
				<br>
			<?php
			foreach ( $this->options as $key => $value ) :

				if ( empty( $value ) ) {
					continue;
				}

				$key = esc_attr( sanitize_key( $key ) );
				$id = esc_attr( $this->get_field_id( $key ) );

				$name = esc_attr( $this->get_field_name( 'site' ) );
				$name = esc_attr( "{$name}[{$key}]" );

				$state = isset( $instance['site'][ $key ] ) ? $instance['site'][ $key ] : 1;
				$state = checked( $state, 1, false );

				echo "<input id='{$id}' type='checkbox' class='checkbox' name='{$name}'' value='{$key}' {$state}>";
				echo "<label for='{$id}'>{$this->profiles[$key]['label']}</label><br>";

			endforeach; ?>
			</p>

			<p>
				<label><?php esc_html_e( 'View:', 'ninecodes-social-manager' ); ?></label>
				<br>
				<?php

					$id = esc_attr( $this->get_field_id( 'view' ) );
					$name = esc_attr( $this->get_field_name( 'view' ) );
					$views = Options::button_views();

				foreach ( $views as $key => $label ) :

					$key = sanitize_key( $key );

					$state = isset( $instance['view'] ) && ! empty( $instance['view'] ) ? $instance['view'] : 'icon';
					$state = checked( sanitize_key( $state ), $key, false );

					echo "<input id='{$id}-{$key}' type='radio' name='{$name}' value='{$key}' {$state}>";
					echo "<label for='{$id}-{$key}'>" . esc_html( $label ) . '</label><br>';
				endforeach; ?>
			</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $input    New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $input, $instance ) {

		$instance['title'] = sanitize_text_field( $input['title'] );
		$instance['view'] = sanitize_key( $input['view'] ? $input['view'] : 'icon' );

		foreach ( $this->options as $key => $value ) {
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
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args 	  Display arguments including 'before_title', 'after_title',
	 * 						  'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		wp_enqueue_style( $this->plugin_slug );

		echo $args['before_widget']; // WPCS : XSS ok.

		/*
		 * If somehow the widget title is not saved,
		 * fallback to the default.
		 */
		$widget_title = ! isset( $instance['title'] ) ? $this->widget_title : $instance['title'];

		if ( ! empty( $widget_title ) ) {
				$widget_title = wp_kses( apply_filters( 'widget_title', $widget_title ), array() );
				echo $args['before_title'] . $widget_title . $args['after_title']; // WPCS : XSS ok, sanitization ok.
		}

		$view = isset( $instance['view'] ) ? $instance['view'] : 'icon';

		echo "<div class='{$this->widget_id}__list {$this->widget_id}__list--{$view}'>"; // WPCS : XSS ok.

		foreach ( $this->options as $key => $value ) {
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
			$list = self::list_views($view, array(
					'site' => $key,
					'label' => esc_html( $profiles['label'] ),
					'url' => esc_url( trailingslashit( $profiles['url'] ) . $this->options[ $key ] ),
					'icon' => Helpers::get_social_icons( $key ),
			));

			echo wp_kses( $list, array(
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
			) );
		} // End foreach().

		echo '</div>';

		echo $args['after_widget']; // WPCS : XSS ok.
	}

	/**
	 * Select and generate the widget list view.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $view The name of the list view to generate.
	 * @param array  $args Attributes of the list item such as the label, the icon, and the url.
	 * @return string An HTML list element with the attributes to display selected list view.
	 */
	protected static function list_views( $view = '', array $args ) {

		if ( empty( $view ) ) {
			return '';
		}

		$prefix = Helpers::get_attr_prefix();
		$args = wp_parse_args($args, array(
				'site' => '',
				'label' => '',
				'icon' => '',
				'url' => '',
		));

		$templates = array(
			'icon' => "<a class='{$prefix}-profiles__item item-{$args['site']}' href='{$args['url']}' target='_blank'>{$args['icon']}</a>",
			'text' => "<a class='{$prefix}-profiles__item item-{$args['site']}' href='{$args['url']}' target='_blank'>{$args['label']}</a>",
			'icon-text' => "<a class='{$prefix}-profiles__item item-{$args['site']}' href='{$args['url']}' target='_blank'><span class='{$prefix}-profiles__item-icon'>{$args['icon']}</span><span class='{$prefix}-profiles__item-text'>{$args['label']}</span></a>",
		);

		return isset( $templates[ $view ] ) ? $templates[ $view ] : '';
	}
}
