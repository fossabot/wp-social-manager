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

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \NineCodes\WPSettings;

/**
 * The Fields class is used for registering the new setting field using PepperPlane.
 *
 * @since 1.0.0
 */
final class Fields extends WPSettings\Fields {

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
		add_action( "{$this->screen}_field_include_sites", array( $this, 'field_include_sites' ) );

		// Filters.
		add_filter( "{$this->screen}_field_scripts", array( $this, 'register_scripts' ) );
		add_filter( "{$this->screen}_field_styles", array( $this, 'register_styles' ) );
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
	public function register_scripts( array $scripts ) {

		$scripts['image'] = 'field-image';
		$scripts['text_profile'] = 'field-text-profile';
		$scripts['checkbox_toggle'] = 'field-checkbox-toggle';

		return $scripts;
	}

	/**
	 * Register files (stylesheets or JavaScripts) to load when using the input.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $styles An array of input type.
	 * @return array The input types with the image file name.
	 */
	public function register_styles( array $styles ) {

		$styles['image'] = 'field-image';
		$styles['include_sites'] = 'field-include-sites';

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
				<div id='{$id}-img-placeholder' class='field-image-placeholder'>" . __( 'No Image Selected', 'ninecodes-social-manager' ) . "</div>
			</div>
			<div id='{$id}-img-buttons' class='field-image__buttons'>
				<button type='button' id='{$id}-img-add' class='button add-media-img{$show}' data-input='#{$id}'>" . __( 'Add image', 'ninecodes-social-manager' ) . "</button>
				<button type='button' id='{$id}-img-change' class='button change-media-img{$hide}' data-input='#{$id}'>" . __( 'Change image', 'ninecodes-social-manager' ) . "</button>
				<button type='button' id='{$id}-img-remove' class='button remove-media-img{$hide}' data-input='#{$id}'>" . __( 'Remove image', 'ninecodes-social-manager' ) . '</button>
            </div>';
		echo $html; // WPCS: XSS ok. ?>
	<?php }

	/**
	 * The function callback to render the Input Profile field.
	 *
	 * @since 1.2.0
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
		$args['attr']['data-url'] = trailingslashit( $args['attr']['data-url'] );

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
	 * @since 1.2.0
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

	/**
	 * [field_multicheckbox_button_sites description]
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $args Arguments (e.g. id, section, type, etc.) to render the new interface.
	 * @return void
	 */
	public function field_include_sites( array $args ) {

		$args = $this->get_arguments( $args ); // Escapes all attributes.

		$id = esc_attr( $args['id'] );
		$section = esc_attr( $args['section'] );
		$value = (array) $this->get_option( $args );

		$count = count( $args['options'] );

		if ( 0 === $count ) {
			return;
		} ?>
		<table class="field-include-sites widefat striped">
			<thead>
				<tr>
					<th class="manage-column column-cb check-column" scope="col"><input type="checkbox" class="check-all"></label></th>
					<th class="manage-column column-site" scope="col"><?php esc_html_e( 'Site', 'ninecodes-social-manager' ); ?></th>
					<th class="manage-column column-label" scope="col"><?php esc_html_e( 'Label', 'ninecodes-social-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( (array) $args['options'] as $site => $opt ) :

					if ( ! isset( $opt['label'] ) || ! isset( $opt['name'] ) ) {
						continue;
					}

					$site = sanitize_key( $site );

					$checked = isset( $value[ $site ]['enable'] ) && 'on' === $value[ $site ]['enable'] ? ' checked="checked" ' : '';
					$label   = isset( $value[ $site ]['label'] ) && ! empty( $value[ $site ]['label'] ) ? $value[ $site ]['label'] : $opt['name'];

					/**
					 * Build the checkbox element.
					 *
					 * @var string
					 */
					$checkbox = sprintf( '<input type="checkbox" id="%1$s-%2$s-%3$s-cb" name="%1$s[%2$s][%3$s][enable]" value="on"%4$s%5$s />',
						$section,
						$id,
						$site,
						$checked,
						$args['attr']
					);

					/**
					 * Build the option <label> to select the checkbox.
					 *
					 * @var sting
					 */
					$name = sprintf( '<label for="%1$s-%2$s-%3$s-cb">%4$s</label>', $section, $id, $site, esc_html( $opt['name'] ) );

					/**
					 * Build the option <input> to change the button label.
					 *
					 * @var sting
					 */
					$input = sprintf( '<input type="text" id="%1$s-%2$s-%3$s-input" name="%1$s[%2$s][%3$s][label]" value="%4$s" class="widefat" />',
						$section,
						$id,
						$site,
						esc_attr( $label )
					); ?>
				<tr>
					<th scope="row" class="check-column"><?php echo wp_kses( $checkbox, array(
						'input' => array(
							'id' => true,
							'name' => true,
							'value' => true,
							'type' => true,
							'checked' => true,
							'class' => true,
						),
					) ); ?></th>
					<th><?php echo wp_kses( $name, array(
						'label' => array(
							'for' => true,
						),
					) ); ?></th>
					<td><?php echo wp_kses( $input, array(
						'input' => array(
							'id' => true,
							'name' => true,
							'value' => true,
							'type' => true,
							'class' => true,
						),
					) ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php }
}
