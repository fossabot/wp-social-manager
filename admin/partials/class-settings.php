<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       github.com/tfirdaus
 * @since      1.0.0
 *
 * @package    WP_Social_Manager
 * @subpackage WP_Social_Manager/admin/partials
 */

namespace XCo\WPSocialManager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	die;

class SettingScreenAdmin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * [$plugin_name description]
	 * @var [type]
	 */
	private $option_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * [$screen description]
	 * @var [type]
	 */
	private $screen;

	/**
	 * [$screen_tab description]
	 * @var [type]
	 */
	private $screen_tab;

	/**
	 * [$options description]
	 * @var [type]
	 */
	private $options;

	/**
	 * [$fields description]
	 * @var [type]
	 */
	private $fields;

	/**
	 * [$validation description]
	 * @var [type]
	 */
	private $validation;

	/**
	 * [__construct description]
	 * @param [type] $args [description]
	 */
	public function __construct( $args ) {

		$this->arguments = $args;

		$this->plugin_name = $args[ 'plugin_name' ];
		$this->option_name = $args[ 'option_name' ];
		$this->version = $args[ 'version' ];

		$this->requires();

		$this->load_options();
		$this->load_fields();
		$this->load_settings();

		add_action( 'admin_notices', array( $this, 'setting_notifications' ), 10 );
	}

	/**
	 * [requires description]
	 * @return [type] [description]
	 */
	protected function requires() {

		require_once plugin_dir_path( __FILE__ ) . 'class-fields.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-validations.php';
	}

	/**
	 * [load_options description]
	 * @return [type] [description]
	 */
	protected function load_options() {

		$options = new Options();
		$this->options = $options->get_options();
	}

	/**
	 * [load_fields description]
	 * @return [type] [description]
	 */
	protected function load_fields() {
		$this->fields = new SettingFields();
	}

	/**
	 * [load_screen_tab description]
	 * @return [type] [description]
	 */
	protected function load_screen_tab() {

		$screen_tab = '';

		if ( isset( $_POST['tab'] ) && sanitize_key( $_POST['tab'] ) ) { // WPCS: CSRF ok. input var okay.
			$screen_tab = sanitize_key( $_POST['tab'] ); // WPCS: input var okay.
		} else {
			if ( isset( $_GET['tab'] ) && sanitize_key( $_GET['tab'] ) ) { // WPCS: input var okay.
				$screen_tab = sanitize_key( $_GET['tab'] ); // WPCS: input var okay.
			}
		}

		if ( '' === $screen_tab ) {
			reset( $this->options->tabs );
			$screen_tab = key( $this->options->tabs );
		}

		return (string) $screen_tab;
	}

	/**
	 * Register the Setting page and its sections.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function load_settings() {

		if ( ! is_array( $this->options->tabs ) || empty( $this->options->tabs ) ) {
			return;
		}

		$screen_tab = $this->load_screen_tab();

		$tabs = $this->options->tabs;
		$page = $this->plugin_name;

		foreach ( $tabs as $tab => $data ) {

			if ( $screen_tab && $screen_tab !== $tab ) {
				continue;
			}

			$sanitize = 'sanitize';

			if ( isset( $data[ 'sanitize_callback' ] ) ) {

				$options = $this->options->tabs[ $tab ][ 'sections' ];
				$validation = new Settings_Validation( $this->arguments, $options );
				$sanitize   = array( $validation, $data[ 'sanitize_callback' ] );
			}

			foreach ( $data[ 'sections' ] as $section => $data ) {

				$title = '';
				if ( isset( $data[ 'title' ] ) ) {
					$title = esc_html( $data[ 'title' ] );
				}

				add_settings_section( $section, $title, array( $this, 'render_section' ), $page );

				register_setting( $page, "{$this->option_name}_{$tab}", $sanitize );

				foreach ( $data[ 'options' ] as $option_id => $option ) {

					$option_class = isset( $option[ 'class' ] ) ? $option[ 'class' ] : '';
					$option_label = isset( $option[ 'label' ] ) ? $option[ 'label' ] : '';

					$args = array(
						'tab' => $tab,
						'section' => $section,
						'name' => "{$this->option_name}_{$tab}",
						'id' => $option_id,
						'class' => $option_class,
						'field' => $option
					);

					add_settings_field( $option_id, $option_label, array( $this->fields, 'render_field' ), $page, $section, $args );
				}
			}

			if ( ! $screen_tab ) {
				break;
			}
		}
	}

	/**
	 * Add Setting Section Content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $tab Registered section.
	 * @return void
	 */
	public function render_section( $args ) {

		$screen_tab = $this->load_screen_tab();
		$sections = wp_parse_args( $this->options->tabs[ $screen_tab ][ 'sections' ], array() );

		if ( ! $sections ) {
			return;
		}

		$description = '';
		if ( isset( $sections[ $args[ 'id' ] ][ 'description' ] ) ) {
			$description = $sections[ $args[ 'id' ] ][ 'description' ];
			$description = $description ? "<p>{$description}</p>" : '';
		}

		echo wp_kses_post( $description );
	}

	/**
	 * Add the Setting page and its input fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_screen() {

		$html  = "<div class='wrap {$this->plugin_name}' id='{$this->plugin_name}-screen'>";
		$html .= "<h1>". esc_html( get_admin_page_title() ) ."</h1>";

		if ( is_array( $this->options->tabs ) && count( $this->options->tabs ) >= 1 ) { // Show page tabs.

			$html .= '<nav class="nav-tab-wrapper">';
			$count = 0;

			foreach ( $this->options->tabs as $tab => $data ) {

				$tab = esc_attr( $tab );

				$class = 'nav-tab'; // Set tab class.
				if ( ! isset( $_GET['tab'] ) ) {  // WPCS: input var okay.
					if ( 0 === $count ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( sanitize_key( $_GET['tab'] ) === $tab ) {  // WPCS: input var okay.
						$class .= ' nav-tab-active';
					}
				}

				$tab_link = add_query_arg( array( 'tab' => $tab ) ); // Set tab link.
				if ( isset( $_GET[ 'settings-updated' ] ) ) {  // WPCS: input var okay.
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				$html .= "<a href='{$tab_link}' class='{$class}'>". esc_html( $data[ 'title' ] ) . "</a>"; 				// Output tab.

				++$count;
			}

			$html .= '</nav>';
		}


		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">';

			$screen_tab = $this->load_screen_tab();

			$html .= '<header class="setting-tab-header">';

			if ( isset( $this->options->tabs[ $screen_tab ][ 'title' ] ) ) {

				$title = esc_html( $this->options->tabs[ $screen_tab ][ 'title' ] );
				$title = $title ? "<h1 class='screen-reader-text'>{$title}</h1>" : '';

				$html .= wp_kses( $title, array( 'h1' => array( 'class' => true ) ) );
			}

			if ( isset( $this->options->tabs[ $screen_tab ][ 'description' ] ) ) {

				$description = esc_html( $this->options->tabs[ $screen_tab ][ 'description' ] );
				$description = $description ? "<p class='setting-tab-description'>{$description}</p>" : '';

				$html .= wp_kses( $description, array( 'p' => array( 'class' => true, 'style' => true ) ) );
			}

			$html .= '</header>';

			if ( isset( $this->options->tabs[ $screen_tab ][ 'sections' ] ) &&
				 is_array( $this->options->tabs[ $screen_tab ][ 'sections' ] ) ) {

				$page = $this->plugin_name;

				ob_start();
					settings_fields( $page );
					do_settings_sections( $page );
				$html .= ob_get_clean();

				$html .= '<p class="submit">';
				$html .= "<input type='hidden' name='tab' value={$screen_tab}>";
				$html .= '<input name="submit" type="submit" class="button button-primary" value="'. esc_attr__( 'Save Changes' , 'wp-social-manager' ). '">';
				$html .= '</p>';
			}

		$html .= '</form>';
		$html .= '</div>';

		echo $html; // WPCS: XSS ok.
	}

	/**
	 * [setting_notifications description]
	 * @return [type] [description]
	 */
	public function setting_notifications() {
		settings_errors( $this->plugin_name );
	}
}