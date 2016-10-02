<?php

namespace XCo\WPSocialManager;

final class SettingsExtend extends \PepperPlaneFields {

	/**
	 * [$page_hook description]
	 * @var [type]
	 */
	protected $page_hook;

	/**
	 * [__construct description]
	 */
	public function __construct( $page_hook ) {

		$this->page_hook = $page_hook;

		$this->actions();
		$this->filters();
	}

	/**
	 * [actions description]
	 * @return [type] [description]
	 */
	protected function actions() {

		add_action( "{$this->page_hook}_add_extra_field", array( $this, 'callback_image' ) );
	}

	/**
	 * [filters description]
	 * @return [type] [description]
	 */
	protected function filters() {

		add_filter( "{$this->page_hook}_field_scripts", array( $this, 'register_field_files' ) );
		add_filter( "{$this->page_hook}_field_styles", array( $this, 'register_field_files' ) );
	}

	/**
	 * [load_script description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function register_field_files( array $args ) {
		$args[ 'image' ] = 'imageUpload';
		return $args;
	}

	/**
	 * [callback_image description]
	 * @return [type] [description]
	 */
	public function callback_image( $args ) {

		$args  = $this->get_arguments( $args );

		$id    = esc_attr( "{$args['section']}_{$args['id']}" );
		$name  = esc_attr( "{$args['section']}[{$args['id']}]" );
		$value = esc_url( $this->get_option( $args ) );

		$img  = ! empty( $value ) ? "<img src='{$value}'>" : '';
		$set  = ! empty( $value ) ? ' is-set' : '';
		$show = ! empty( $value ) ? ' hide-if-js' : '';
		$hide = ! empty( $value ) ? '' : ' hide-if-js';

		$html = "<input type='hidden' id='{$id}' name='{$name}'' value='{$value}'/>
			<div id='{$id}-wrap' class='field-image-wrap{$set}'>
				<div id='{$id}-img'>{$img}</div>
				<div id='{$id}-placeholder' class='field-image-placeholder'>". esc_html__( 'No Image Selected', 'wp-social-manager' ) ."</div>
			</div>
			<div id='{$id}-control' class='field-image-control'>
				<button type='button' id='{$id}-add' class='button add-media{$show}' data-input='#{$id}'>". esc_html__( 'Add image', 'wp-social-manager' ) ."</button>
				<button type='button' id='{$id}-change' class='button change-media{$hide}' data-input='#{$id}'>". esc_html__( 'Change image', 'wp-social-manager' ) ."</button>
				<button type='button' id='{$id}-remove' class='button remove-media{$hide}' data-input='#{$id}'>". esc_html__( 'Remove image', 'wp-social-manager' ) ."</button>
			</div>";

		echo $html; ?>

	<?php }
}