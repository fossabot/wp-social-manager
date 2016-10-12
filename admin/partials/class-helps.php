<?php

namespace XCo\WPSocialManager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	die;

class Helps {

	/**
	 * [$screen description]
	 * @var [type]
	 */
	protected $screen;

	/**
	 * [__construct description]
	 * @param [type] $screen [description]
	 */
    public function __construct( $screen ) {
    	$this->setups( $screen );
    	$this->hooks();
    }

    /**
     * [setups description]
     * @param  [type] $screen [description]
     * @return [type]         [description]
     */
    public function setups( $screen ) {
    	$this->screen = \WP_Screen::get( $screen );
    }

    /**
     * [hooks description]
     * @return [type] [description]
     */
    public function hooks() {
    	add_action( "load-{$this->screen->base}", array( $this, 'register_help_tabs' ) );
    }

    /**
     * [register_help_tab description]
     * @return [type] [description]
     */
    public function register_help_tabs() {

    	$this->screen->add_help_tab( array(
			'title'    => esc_html__( 'Overview', 'wp-sharelog' ),
			'id'       => 'overview',
			'callback' => array( $this, 'help_content' ),
			)
		);

        $this->screen->add_help_tab( array(
            'title'    => esc_html__( 'Accounts', 'wp-sharelog' ),
            'id'       => 'accounts',
            'callback' => array( $this, 'help_content' ),
            )
        );

        $this->screen->add_help_tab( array(
            'title'    => esc_html__( 'Buttons', 'wp-sharelog' ),
            'id'       => 'buttons',
            'callback' => array( $this, 'help_content' ),
            )
        );

        $this->screen->add_help_tab( array(
            'title'    => esc_html__( 'Metas', 'wp-sharelog' ),
            'id'       => 'metas',
            'callback' => array( $this, 'help_content' ),
            )
        );

        $this->screen->add_help_tab( array(
            'title'    => esc_html__( 'Advanced', 'wp-sharelog' ),
            'id'       => 'advanced',
            'callback' => array( $this, 'help_content' ),
            )
        );

        $this->screen->set_help_sidebar( $this->help_sidebar() );
    }

    /**
     * [help_content description]
     * @param  [type] $screen [description]
     * @param  [type] $tab    [description]
     * @return [type]         [description]
     */
    public function help_content( $screen, $tab ) {

        $content = '';

    	if ( 'overview' === $tab['id'] ) {

            $content .= '<p>'. esc_html__( 'Social network sites is one of the staple outlets for websites of any scale to distribute their content as well as to attract incoming visitors to the website.', 'wp-sharelog' ) .'</p>';
            $content .= '<p>'. esc_html__( 'In this screen, you can find a number of options that allow you configure the utilities to optimize this website presence and presentation on popular social network sites such as Facebook, Twitter, Pinterest, LinkedIn, and Google+.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. esc_html__( 'You must click the Save Changes button at the bottom of the screen for the new settings to take effect.', 'wp-social-manager' ) .'</p>';
    	}

        if ( 'accounts' === $tab['id'] ) {
            $content .= '<p>'. esc_html__( 'You might have registered one or more accounts (e.g. Facebook page, a Twitter profile, a Google+ page, etc.) to represent this website presence in the some social network sites.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. sprintf( esc_html__( 'You can add username of these profiles and pages in the Account tab input fields. The added profiles and pages can be displayed through the "Social Profiles" widget which you can find in the %s admin page.', 'wp-social-manager' ), '<a href="'. esc_url( get_admin_url() ) .'widgets.php">Widgets</a>' ).'</p>';
            $content .= '<p>'. esc_html__( 'You must click the Save Changes button at the bottom of the screen for the new settings to take effect.', 'wp-social-manager' ) .'</p>';
        }

        if ( 'buttons' === $tab['id'] ) {
            $content .= '<p>'. esc_html__( 'Social buttons, generally, allow the readers to share or save this website content to social network sites.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. esc_html__( 'In the Buttons setting tab, you can configure several aspects of these buttons including the buttons appearance, the social network sites to include, and the position where these buttons should be displayed within the content.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. esc_html__( 'You must click the Save Changes button at the bottom of the screen for the new settings to take effect.', 'wp-social-manager' ) .'</p>';
        }

        if ( 'metas' === $tab['id'] ) {
            $content .= '<p>'. sprintf( esc_html__( 'In the Metas setting tab, you can configure the social meta tags such as Open Graph and Twitter Cards added in this website %s tag.', 'wp-social-manager' ), '<code>head</code>' ) .'</p>';
            $content .= '<p>'. esc_html__( ' These meta tags may be used to serve customized title, description, image and other things that will represent this website in the social network sites with a more compelling presentation format.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. sprintf( esc_html__( 'If this functionality has been served through a 3rd-party plugin, you may disable it to avoid conflicts with the plugin by unticking the "Enable Meta Tags" option.', 'wp-social-manager' ), '<code>head</code>' ) .'</p>';
            $content .= '<p>'. esc_html__( 'You must click the Save Changes button at the bottom of the screen for the new settings to take effect.', 'wp-social-manager' ) .'</p>';
        }

        if ( 'advanced' === $tab['id'] ) {
            $content .= '<p>'. esc_html__( 'This tabs contains advanced stuff which may require technical comprehension. Change the setting options with cautious. Whenever in doubt leave the options in this tab as is.', 'wp-social-manager' ) .'</p>';
            $content .= '<p>'. esc_html__( 'You must click the Save Changes button at the bottom of the screen for the new settings to take effect.', 'wp-social-manager' ) .'</p>';
        }

        echo wp_kses_post( $content );
    }

    /**
     * [help_sidebar description]
     * @return [type] [description]
     */
    public function help_sidebar() {

        $sidebar  = '<p><strong>'. esc_html__( 'Useful references:', 'wp-social-manager' ) .'</strong></p>';
        $sidebar .= '<p><a href="'. esc_url( 'http://ogp.me/' ) .'" target="_blank">'. esc_html__( 'The Open Graph Protocol', 'wp-social-manager' ) .'</a></p>';
        $sidebar .= '<p><a href="'. esc_url( 'https://dev.twitter.com/cards/overview' ) .'" target="_blank">'. esc_html__( 'Twitter Cards', 'wp-social-manager' ) .'</a></p>';

        return wp_kses_post( $sidebar );
    }
}