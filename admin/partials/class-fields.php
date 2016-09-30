<?php

namespace XCo\WPSocialManager;

/**
 * SettingFields Main Class
 */
class SettingFields {

	public function __construct() {}

	/**
	 * [setup description]
	 * @return [type] [description]
	 */
	public function setup( $setting ) {

		/**
		 * [$setting description]
		 * @var [type]
		 */
		$setting = wp_parse_args( $setting, array(
			'tab' => '',
			'section' => '',
			'name' => '',
			'id' => '',
			'class' => '',
			'field' => array()
		) );

		/**
		 * [$field description]
		 * @var [type]
		 */
		$field = wp_parse_args( $setting[ 'field' ], array(
			'type' => '',
			'label' => '',
			'legend' => '',
			'description' => '',
			'default' => '',
			'options' => array(),
			'attr' => array(),
		) );
	}

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param  array   $field Field data such as the id, type, label, description, and option
	 * @param  boolean $post  Whether post or not.
	 * @param  boolean $echo  Whether to echo the field HTML or return it.
	 *
	 * @return string  The formatted HTML of the settings control.
	 */
	public function render_field( $setting = array() ) {

		/**
		 * [$setting description]
		 * @var [type]
		 */
		$setting = wp_parse_args( $setting, array(
			'tab' => '',
			'section' => '',
			'name' => '',
			'id' => '',
			'class' => '',
			'field' => array()
		) );

		/**
		 * [$field description]
		 * @var [type]
		 */
		$field = wp_parse_args( $setting[ 'field' ], array(
			'type' => '',
			'label' => '',
			'legend' => '',
			'description' => '',
			'default' => '',
			'options' => array(),
			'attr' => array(),
		) );

		$attrType = esc_attr( $field['type'] );
		$attrName = esc_attr( "{$setting['name']}[{$setting['section']}][{$setting['id']}]" );
		$attrID = esc_attr( "{$setting['tab']}-{$setting['section']}-{$setting['id']}" );

		$attrs = $this->control_attributes( $field['attr'], $attrType );
		$data  = $this->options( $setting, $field['default'] );

		$description = wp_kses_post( $field['description'] );

		$html = '<fieldset class="setting-field">';

		$html .= $field[ 'legend' ] ? '<legend class="screen-reader-text">'. esc_html( $field['legend'] ) .'</legend>' : '';

		switch ( $attrType ) {

			case 'text':
			case 'url':
			case 'email':
			case 'password':
			case 'number':
			case 'hidden':

				$data = esc_attr( $data );

				$html .= "<input id='{$attrID}' {$attrs} type='{$attrType}' name='{$attrName}' value='{$data}'>";

				break;

			case 'textarea':

				$data = esc_textarea( $data );

				$html .= "<textarea id='{$attrID}' {$attrs} rows='5' cols='50' name='{$attrName}'>{$data}</textarea>";

				break;

			case 'image' :

				$data = esc_url( $data );

				$img = $data ? "<img src='{$data}'>" : '';

				$is_set = $data ? ' is-set' : '';
				$init_show = $data ? ' hide-if-js' : '';
				$init_hide = $data ? '' : ' hide-if-js';

				$button_label_add =

				$html .=
				"<input id='{$attrID}' {$attrs} type='hidden' name='{$attrName}' value='{$data}'>
				<div id='{$attrID}-wrap' class='field-image-wrap{$is_set}'>
					<div id='{$attrID}-img'>{$img}</div>
					<div id='{$attrID}-img-placeholder' class='field-image-placeholder'>". esc_html__( 'No Image Selected', 'wp-social-manager' ) ."</div>
				</div>
				<div id='{$attrID}-control' class='field-image-control'>
					<button type='button' id='{$attrID}-add' class='button button-add-media{$init_show}' data-input='#{$attrID}'>". esc_html__( 'Add image', 'wp-social-manager' ) ."</button>
					<button type='button' id='{$attrID}-change' class='button button-change-media{$init_hide}' data-input='#{$attrID}'>". esc_html__( 'Change image', 'wp-social-manager' ) ."</button>
					<button type='button' id='{$attrID}-remove' class='button button-remove-media{$init_hide}' data-input='#{$attrID}'>". esc_html__( 'Remove image', 'wp-social-manager' ) ."</button>
				</div>";
				break;

			case 'checkbox':

				$data = (int) $data;
				$html .= "<label for='{$attrID}'><input id='{$attrID}' {$attrs} type='checkbox' name='{$attrName}' value='1'". checked( $data, 1, false ) .">{$description}</label>";

				break;

			case 'checkboxes':

				foreach ( (array) $field['options'] as $key => $label ) {

					$label   = esc_html( $label );
					$key     = esc_attr( sanitize_key( $key ) );
					$checked = in_array( $key, $data ) ? 1 : 0;

					$html .= "<label for='{$attrID}-{$key}'><input id='{$attrID}-{$key}' {$attrs} type='checkbox' name='{$attrName}[]' value='{$key}' ". checked( $checked, 1, false ) .">{$label}</label><br>";
				}

				break;

			case 'radio':

				$options = (array) $field['options'];
				$count = count( $options );

				foreach ( $options as $key => $label ) {

					$label = esc_html( $label );
					$key = esc_attr( sanitize_key( $key ) );
					$checked = $key === $data || $count < 2 ? 1 : 0;

					$html .= "<label for='{$attrID}-{$key}'><input id='{$attrID}-{$key}' type='radio' name='{$attrName}' value='{$key}'". checked( $checked, 1, false ) ." {$attrs}>{$label}</label><br>";
				}

				break;

			case 'select':

				$html .= "<select id='{$attrID}' {$attrs} name='{$attrName}'>";
				foreach ( $field['options'] as $key => $label ) {

					$key = esc_attr( sanitize_key( $key ) );
					$selected = ( $key === $data ) ? true : false;
					$label = esc_html( $label );

					$html .= "<option ". selected( $selected, true, false ) ." value='{$key}'>{$label}</option>";
				}
				$html .= '</select>';

				break;
		}

		if ( $description && 'checkbox' !== $attrType  ) {
			$html .= "<p class='description'>{$description}</p>";
		}

		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * [get_control_attrs description]
	 * @param  [type] $attrs [description]
	 * @param  [type] $type  [description]
	 * @return [type]        [description]
	 */
	protected function control_attributes( $attrs, $type ) {

		$forbid = array( 'id', 'name', 'type', 'value', 'checked' );
		foreach ( $forbid as $f ) {
			unset( $attrs[ $f ] );
		}

		$attrs = array_map( 'esc_attr', $attrs );
		$types = array( 'text', 'email', 'password', 'url', 'number', 'image' );

		if ( in_array( $type, $types, true ) ) {

			$class  = 'regular-text';
			$class .= ( $type === 'url' ) ? ' code' : '';

			if ( isset( $attrs[ 'class' ] ) ) {
				$attrs[ 'class' ] = "{$class} {$attrs['class']}";
			} else {
				$attrs[ 'class' ] = $class;
			}
		}

		$attribute = '';
		foreach ( $attrs as $a => $v ) {
			$attribute .= " {$a}='{$v}'";
		}

		return $attribute;
	}

	/**
	 * [get_data description]
	 * @param  [type] $setting [description]
	 * @return [type]       [description]
	 */
	public function options( $setting, $default ) {

		$option = get_option( $setting[ 'name' ] );

		if ( ! $option ) {
			return $default;
		}

		if ( isset( $option[ $setting[ 'section' ] ][ $setting[ 'id' ] ] ) ) {
			return $option[ $setting[ 'section' ] ][ $setting[ 'id' ] ];
		}
	}
}