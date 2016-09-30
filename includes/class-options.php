<?php

namespace XCo\WPSocialManager;

class Options {

	/**
	 * [$tabs description]
	 * @var [type]
	 */
	public $tabs;

	/**
	 * [$post_types description]
	 * @var [type]
	 */
	protected $post_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->register_tabs();
	}

	/**
	 * [get_options description]
	 * @return [type] [description]
	 */
	public function get_options() {
		return $this;
	}

	/**
	 * [load_post_types description]
	 * @return [type] [description]
	 */
	protected function load_post_types() {

		$post_types = array();

		$types = get_post_types();
		$types = get_post_types( array(
			'public' => true,
		) );

		foreach ( $types as $key => $value ) {
			switch ( $key ) {
				case 'attachment':
				case 'revision':
				case 'nav_menu_item':
				case 'deprecated_log':
					unset( $types[ $key ] );
					break;
			}
		}

		foreach ( $types as $key => $value ) {
			$post_types[ $key ] = get_post_type_object( $key )->label;
		}

		return $post_types;
	}

	/**
	 * [get_site_logo description]
	 * @return [type] [description]
	 */
	protected function get_site_logo() {

		$logo = get_theme_mod( 'custom_logo' );
		$logo = wp_get_attachment_image_src( $logo , 'full' );

		return $logo[0] ? $logo[0] : '';
	}

	/**
	 * [register_tabs description]
	 * @return [type] [description]
	 */
	protected function register_tabs() {

		$this->tabs[ 'accounts' ] = array(
			'title' => esc_html_x( 'Accounts', 'name of a setting tab', 'wp-social-manager' ),
			'description' => esc_html__( 'Social media profiles and pages connected to this website.', 'wp-social-manager' ),
			'sanitize_callback' => 'sanitize_accounts'
		);
		$this->tab_accounts();


		$this->tabs[ 'buttons' ] = array(
			'title' => esc_html_x( 'Buttons', 'name of a setting tab', 'wp-social-manager' ),
			'description' => esc_html__( 'Configure the social media buttons shown on this website.', 'wp-social-manager' ),
			'sanitize_callback' => 'sanitize_buttons'
		);
		$this->tab_buttons();


		$this->tabs[ 'metas' ] = array(
			'title' => esc_html_x( 'Metas', 'name of a setting tab', 'wp-social-manager' ),
			'description' => esc_html__( 'Social meta tags provides information to richly represent web page in social sites like Facebook, Twitter, and Pinterest.', 'wp-social-manager' ),
			'sanitize_callback' => 'sanitize_metas'
		);
		$this->tab_metas();


		$this->tabs[ 'advanced' ] = array(
			'title' => esc_html_x( 'Advanced', 'name of a setting tab', 'wp-social-manager' ),
			'description' => esc_html__( 'Advanced stuff going on here. If in doubt, leave the following options as they are.', 'wp-social-manager' ),
			'sanitize_callback' => 'sanitize_advanced'
		);
		$this->tab_advanced();
	}

	/**
	 * [tab_accounts description]
	 * @return [type] [description]
	 */
	protected function tab_accounts() {
		$this->tabs[ 'accounts' ][ 'sections' ][ 'accounts' ] = array(
			'options' => self::accounts()
		);
	}

	/**
	 * [tab_metas description]
	 * @return [type] [description]
	 */
	protected function tab_metas() {
		$this->tabs[ 'metas' ][ 'sections' ][ 'site' ] = array(
			'options' => self::metas()
		);
	}

	/**
	 * [tab_buttons(description]
	 * @return [type] [description]
	 */
	protected function tab_buttons() {

		$this->tabs[ 'buttons' ][ 'sections' ][ 'content' ] = array(
				'title' => esc_html_x( 'Content', 'name of a setting tab', 'wp-sharing-manager' ),
				'description' => esc_html__( 'Options to configure the social media buttons that enable sharing, saving, or liking the content.', 'wp-sharing-manager' ),
				'options' => self::buttons( 'content' )
			);
		$this->tabs[ 'buttons' ][ 'sections' ][ 'image' ] = array(
				'title' => esc_html_x( 'Image', 'name of a setting tab', 'wp-sharing-manager' ),
				'description' => esc_html__( 'Options to configure the social media buttons shown on the content images.', 'wp-sharing-manager' ),
				'options' => self::buttons( 'image' )
			);
	}

	/**
	 * [tab_advanced description]
	 * @return [type] [description]
	 */
	protected function tab_advanced() {

		$this->tabs[ 'advanced' ][ 'sections' ][ 'general' ] = array(
			'options' => self::advanced()
		);
	}

	/**
	 * [accounts description]
	 * @return array [description]
	 */
	public static function accounts() {

		return array(
			'facebook' => array(
				'label' => 'Facebook',
				'legend' => 'Facebook Profile or Page Username',
				'description' => sprintf( esc_html__( 'Facebook profile or page (e.g. %s)', 'wp-social-manager' ), '<code>zuck</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://www.facebook.com/',
				'icon' => get_social_icon( 'facebook' )
			),
			'twitter' => array(
				'label' => 'Twitter',
				'legend' => 'Twitter Username',
				'description' => sprintf( esc_html__( 'Twitter profile without the %1$s (e.g. %2$s)', 'wp-social-manager' ), '<code>@</code>', '<code>jack</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://twitter.com/',
				'icon' => get_social_icon( 'twitter' )
			),
			'instagram' => array(
				'label' => 'Instagram',
				'legend' => 'Instagram Username',
				'description' => sprintf( esc_html__( 'Instagram profile (e.g. %s)', 'wp-social-manager' ), '<code>victoriabeckham</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://instagram.com/',
				'icon' => get_social_icon( 'instagram' )
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'legend' => 'Pinterest Username',
				'description' => sprintf( esc_html__( 'Pinterest profile (e.g. %s)', 'wp-social-manager' ), '<code>ohjoy</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://pinterest.com/',
				'icon' => get_social_icon( 'pinterest' )
			),
			'googleplus' => array(
				'label' => 'Google+',
				'legend' => 'Google+ Page or Profile Username',
				'description' => sprintf( esc_html__( 'Google+ page or profile without the %1$s (e.g. %2$s)', 'wp-social-manager' ), '<code>+</code>', '<code>MarquesBrownlee</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://plus.google.com/',
				'icon' => get_social_icon( 'googleplus' )
			),
			'youtube' => array(
				'label' => 'Youtube',
				'legend' => 'Youtube Channel',
				'description' => sprintf( esc_html__( 'Youtube channel (e.g. %s)', 'wp-social-manager' ), '<code>BuzzFeedVideo</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://www.youtube.com/user/',
				'icon' => get_social_icon( 'youtube' )
			),
			'dribbble' => array(
				'label' => 'Dribbble',
				'legend' => 'Dribbble Portfolio',
				'description' => sprintf( esc_html__( 'Dribbble username (e.g. %s)', 'wp-social-manager' ), '<code>simplebits</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://dribbble.com/',
				'icon' => get_social_icon( 'dribbble' )
			),
			'behance' => array(
				'label' => 'Behance',
				'legend' => 'Behance Portfolio',
				'description' => sprintf( esc_html__( 'Behance username (e.g. %s)', 'wp-social-manager' ), '<code>amocci</code>' ),
				'type' => 'text',
				'attr' => array(
					'class' => 'code'
				),
				'baseURL' => 'https://www.behance.net/',
				'icon' => get_social_icon( 'behance' )
			),
		);
	}

	/**
	 * [buttons description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public static function buttons( $name ) {

		$buttons = array(
			'content' => array(
				'postTypes' => array(
					'label' => esc_html__( 'Show the buttons in', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Post Types', 'wp-sharing-manager' ),
					'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the sharing buttons.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
					'type' => 'checkboxes',
					'options' => self::load_post_types(),
					'default' => array( 'post' ),
				),
				'buttonType' => array(
					'label' => esc_html__( 'Show the buttons as', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Button Type', 'wp-sharing-manager' ),
					'description' => esc_html__( 'The social media button appearance in the content.', 'wp-sharing-manager' ),
					'type' => 'radio',
					'options' => array(
						'icon'      => esc_html__( 'Icon Only', 'wp-sharing-manager' ),
						'text'      => esc_html__( 'Text Only', 'wp-sharing-manager' ),
						'icon-text' => esc_html__( 'Icon and Text', 'wp-sharing-manager' ),
					),
					'default' => 'icon'
				),
				'buttonLocation' => array(
					'label'       => esc_html__( 'Place the buttons', 'wp-sharing-manager' ),
					'legend'      => esc_html__( 'Button Location', 'wp-sharing-manager' ),
					'description' => esc_html__( 'Location in the content to show the sharing buttons.', 'wp-sharing-manager' ),
					'type'        => 'radio',
					'options'     => array(
						'before' => esc_html__( 'Before the content', 'wp-sharing-manager' ),
						'after'  => esc_html__( 'After the content', 'wp-sharing-manager' ),
					),
					'default' => 'after'
				),
				'socialSites' => array(
					'label'   => esc_html__( 'Include these buttons', 'wp-sharing-manager' ),
					'legend'  => esc_html__( 'Include these buttons', 'wp-sharing-manager' ),
					'type'    => 'checkboxes',
					'options' => array(
						'facebook' => 'Facebook',
						'twitter' => 'Twitter',
						'googleplus' => 'Google+',
						'pinterest' => 'Pinterest',
						'reddit' => 'Reddit',
						'whatsapp' => 'WhatsApp',
						'email' => 'Email'
					),
					'default' => array( 'facebook', 'twitter' )
				),
			),
			'image' => array(
				'imageSharing' => array(
					'label' => esc_html__( 'Image Sharing Display', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Image Sharing Display', 'wp-sharing-manager' ),
					'description' => esc_html__( 'Show the social sharing buttons on images in the content', 'wp-sharing-manager' ),
					'type' => 'checkbox',
					'attr' => array(
						'class' => 'toggle-switch-control',
						'data-toggle-target' => '.sharing-image-control'
					)
				),
				'postTypes' => array(
					'label' => esc_html__( 'Show the buttons in', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Post Types', 'wp-sharing-manager' ),
					'description' => wp_kses( sprintf( __( 'List of %s that are allowed to show the sharing buttons on the images of the content.', 'wp-sharing-manager' ), '<a href="https://codex.wordpress.org/Post_Types" target="_blank">'. esc_html__( 'Post Types', 'wp-sharing-manager' ) .'</a>' ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
					'type' => 'checkboxes',
					'class' => 'sharing-image-control',
					'options' => self::load_post_types(),
					'default' => array( 'post' ),
				),
				'buttonType' => array(
					'label' => esc_html__( 'Show the buttons as', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Button Type', 'wp-sharing-manager' ),
					'description' => esc_html__( 'The sharing button appearance on the image.', 'wp-sharing-manager' ),
					'type' => 'radio',
					'class' => 'sharing-image-control',
					'options' => array(
						'icon'      => esc_html__( 'Icon Only', 'wp-sharing-manager' ),
						'text'      => esc_html__( 'Text Only', 'wp-sharing-manager' ),
						'icon-text' => esc_html__( 'Icon and Text', 'wp-sharing-manager' ),
					),
					'default' => 'icon',
				),
				'socialSites' => array(
					'label' => esc_html__( 'Include these sites', 'wp-sharing-manager' ),
					'legend' => esc_html__( 'Social Sites', 'wp-sharing-manager' ),
					'type' => 'checkboxes',
					'class' => 'sharing-image-control',
					'options' => array(
							'facebook'  => 'Facebook',
							'pinterest' => 'Pinterest',
						),
					'default' => array( 'pinterest' )
				),
			)
		);

		return $buttons[ $name ];
	}

	/**
	 * [metas description]
	 * @return [type] [description]
	 */
	public static function metas() {

		return array(
			'metaEnable' => array(
				'label' => esc_html__( 'Enable Meta Tags', 'wp-social-manager' ),
				'legend' => esc_html__( 'Enable Meta Tags', 'wp-social-manager' ),
				'description' => esc_html__( 'Generate social meta tags on this website', 'wp-social-manager' ),
				'type' => 'checkbox',
				'default' => true,
				'attr' => array(
					'class' => 'toggle-switch-control',
					'data-toggle-target' => '.meta-tags-control'
				)
			),
			'siteName' => array(
				'label' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'legend' => esc_html__( 'Site Name', 'wp-social-manager' ),
				'description' => esc_html__( 'The name of this website as it should appear within the Open Graph meta tag', 'wp-social-manager' ),
				'class' => 'meta-tags-control',
				'type' => 'text',
				'attr' => array(
					'placeholder' => get_bloginfo( 'name' )
				)
			),
			'siteDescription' => array(
				'label' => esc_html__( 'Site Description', 'wp-social-manager' ),
				'class' => 'meta-tags-control',
				'legend' => esc_html__( 'Description', 'wp-social-manager' ),
				'description' => esc_html__( 'A one to two sentence description of this website that should appear within the Open Graph meta tag', 'wp-social-manager' ),
				'type' => 'textarea',
				'attr' => array(
					'class' => 'regular-text',
					'placeholder' => get_bloginfo( 'description' )
				)
			),
			'siteImage' => array(
				'class' => 'meta-tags-control',
				'label' => esc_html__( 'Site Image', 'wp-social-manager' ),
				'legend' => esc_html__( 'Image', 'wp-social-manager' ),
				'description' => esc_html__( 'An image URL which should represent this website within the Open Graph meta tag', 'wp-social-manager' ),
				'type' => 'image',
				'default' => self::get_site_logo()
			),
		);
	}

	/**
	 * [advanced description]
	 * @return [type] [description]
	 */
	public static function advanced() {

		return array(
			'disableStylesheet' => array(
				'label' => esc_html__( 'Enable Stylesheet', 'wp-sharing-manager' ),
				'legend' => esc_html__( 'Enable Stylesheet', 'wp-sharing-manager' ),
				'description' => esc_html__( 'Load the plugin stylesheet to apply essential styles.', 'wp-sharelog' ),
				'default' => 1,
				'type' => 'checkbox'
			)
		);
	}
}