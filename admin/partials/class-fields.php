<?php
/**
 * Admin: Fields class
 *
 * @package SocialManager
 * @subpackage Admin\Fields
 *
 * TODO: Merge these custom fields to WPSettings.
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \NineCodes\WPSettings;

/**
 * The class to register custom fields in the Settings.
 */
abstract class CustomFields extends WPSettings\Fields {

	/**
	 * The admin screen base / ID
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $screen;

	/**
	 * Constructor.
	 *
	 * Initialize the screen ID property, and run the hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $screen The admin screen base / ID.
	 */
	public function __construct( $screen = '' ) {
		if ( ! empty( $screen ) ) {
			$this->screen = $screen;
			$this->hooks();
		}
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {}
}

/**
 * The Fields class is used for registering the new setting field using PepperPlane.
 *
 * @since 1.0.0
 */
final class Fields extends CustomFields {

	/**
	 * The admin screen base / ID
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $screen;

	/**
	 * Constructor.
	 *
	 * Initialize the screen ID property, and run the hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $screen The admin screen base / ID.
	 */
	public function __construct( $screen = '' ) {
		if ( ! empty( $screen ) ) {
			$this->screen = $screen;
			$this->hooks();
		}
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		// Actions.
		add_action( "{$this->screen}_field_image", array( $this, 'field_image' ) );
		add_action( "{$this->screen}_field_text_profile", array( $this, 'field_text_profile' ) );
		add_action( "{$this->screen}_field_checkbox_toggle", array( $this, 'field_checkbox_toggle' ) );

		// Filters.
		add_filter( "{$this->screen}_field_scripts", array( $this, 'register_field_scripts' ) );
		add_filter( "{$this->screen}_field_styles", array( $this, 'register_field_styles' ) );
	}

	/**
	 * Register files (stylesheets or JavaScripts) to load when using the input.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $scripts An array of input type.
	 * @return array The input types with the image file name.
	 */
	public function register_field_scripts( array $scripts ) {

		$scripts['image'] = 'field-image';
		$scripts['text_profile'] = 'field-text-profile';
		$scripts['checkbox_toggle'] = 'field-checkbox-toggle';

		return $scripts;
	}

	/**
	 * Register files (stylesheets or JavaScripts) to load when using the input.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @param array $styles An array of input type.
	 * @return array The input types with the image file name.
	 */
	public function register_field_styles( array $styles ) {

		$styles['image'] = 'field-image';

		return $styles;
	}

	/**
	 * The function callback to render the Image input field.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Arguments (e.g. id, section, type, etc.) to render the new interface.
	 * @return void
	 */
	public function field_image( array $args ) {

		$args = $this->get_arguments( $args ); // Sanitize arguments.

		wp_enqueue_media();

		$id = esc_attr( "{$args['section']}_{$args['id']}" );
		$name = esc_attr( "{$args['section']}[{$args['id']}]" );
		$value = absint( $this->get_option( $args ) );
		$source = $value ? wp_get_attachment_image_src( $value, 'full', true ) : '';

		list( $src, $width, $height ) = $source;

		$img  = ! empty( $source ) ? "<img src='{$src}' width='{$width}' height='{$height}'>" : '';
		$set  = ! empty( $source ) ? ' is-set' : '';
		$show = ! empty( $source ) ? ' hide-if-js' : '';
		$hide = ! empty( $source ) ? '' : ' hide-if-js';

		$html = "<div class='field-image'><input type='hidden' id='{$id}' name='{$name}' value='{$value}'/>
			<div id='{$id}-img-wrap' class='field-image__wrap{$set}'>
				<div id='{$id}-img-elem'>{$img}</div>
				<div id='{$id}-img-placeholder' class='field-image-placeholder'>" . esc_html__( 'No Image Selected', 'ninecodes-social-manager' ) . "</div>
			</div>
			<div id='{$id}-img-buttons' class='field-image__buttons'>
				<button type='button' id='{$id}-img-add' class='button add-media-img{$show}' data-input='#{$id}'>" . esc_html__( 'Add image', 'ninecodes-social-manager' ) . "</button>
				<button type='button' id='{$id}-img-change' class='button change-media-img{$hide}' data-input='#{$id}'>" . esc_html__( 'Change image', 'ninecodes-social-manager' ) . "</button>
				<button type='button' id='{$id}-img-remove' class='button remove-media-img{$hide}' data-input='#{$id}'>" . esc_html__( 'Remove image', 'ninecodes-social-manager' ) . '</button>
            </div>';
		echo $html; // WPCS: XSS ok. ?>
	<?php }

	/**
	 * The function callback to render the Input Profile field.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @param array $args Arguments (e.g. id, section, type, etc.) to render the new interface.
	 * @return void
	 */
	public function field_text_profile( array $args ) {

		if ( ! isset( $args['attr']['data-url'] ) || empty( $args['attr']['data-url'] ) ) {
			return;
		}

		if ( 'http' !== substr( $args['attr']['data-url'], 0, 4 ) ) { // `data-url` attribute must be a URL with HTTP.
			return;
		}

		$args['type'] = 'text'; // Revert the type back to 'text'.
		$args['attr']['class'] = 'field-text-profile code';

		$args  = $this->get_arguments( $args ); // Escapes all attributes.

		$value = (string) esc_attr( $this->get_option( $args ) );
		$error = $this->get_setting_error( $args['id'] );
		$elem  = sprintf( '<input type="%6$s" id="%1$s_%2$s" name="%1$s[%2$s]" value="%3$s"%4$s%5$s/>',
			$args['section'],
			esc_attr( $args['id'] ),
			esc_attr( $value ),
			$args['attr'],
			$error,
			esc_attr( $args['type'] )
		);

		$before = wp_kses_post( $args['before'] );
		$after = wp_kses_post( $args['after'] );
		$description = wp_kses_post( $this->description( $args['description'] ) );

		echo $before . $elem . $after . $description; // XSS ok.
	}

	/**
	 * The function callback to render the Text Checkbox field.
	 *
	 * @since 1.1.3
	 * @access public
	 *
	 * @param array $args Arguments (e.g. id, section, type, etc.) to render the new interface.
	 * @return void
	 */
	public function field_checkbox_toggle( array $args ) {

		if ( ! isset( $args['attr']['data-toggle'] ) || empty( $args['attr']['data-toggle'] ) ) {
			return;
		}

		if ( '.' !== substr( $args['attr']['data-toggle'], 0, 1 ) ) { // `data-toggle` must be a class selector.
			return;
		}

		$args['type'] = 'checkbox';
		$args['attr']['class'] = 'field-checkbox-toggle';

		$args = $this->get_arguments( $args ); // Escapes all attributes.

		$id = esc_attr( $args['id'] );
		$section = esc_attr( $args['section'] );
		$value = esc_attr( $this->get_option( $args ) );

		$checkbox = sprintf( '<input type="checkbox" id="%1$s_%2$s" name="%1$s[%2$s]" value="on"%4$s%5$s />',
			$section,
			$id,
			$value,
			checked( $value, 'on', false ),
			$args['attr']
		);

		$error = $this->get_setting_error( $id, ' style="border: 1px solid red; padding: 2px 1em 2px 0; "' );
		$description = wp_kses_post( $args['description'] );

		$elem = sprintf( '<label for="%1$s_%2$s"%5$s>%3$s %4$s</label>', $section, $id, $checkbox, $description, $error );

		echo $elem; // XSS ok.
	}
}
