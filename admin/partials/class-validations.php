<?php

namespace XCo\WPSocialManager;

/**
 *
 */
class Settings_Validation {

    /**
     * [$plugin_name description]
     * @var [type]
     */
	protected $plugin_name;

    /**
     * [$options description]
     * @var [type]
     */
    protected $options;

	/**
	 * [__construct description]
	 * @param [type] $args [description]
	 */
    public function __construct( $args, $options ) {

		$this->plugin_name = $args[ 'plugin_name' ];
        $this->options = $options;
    }

    /**
     * [sanitize description]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function sanitize( $input ) {
        return $input;
    }

    /**
     * [sanitize_profiles description]
     * @param  [type] $profiles [description]
     * @return [type]           [description]
     */
    public function sanitize_accounts( $input ) {

		$accounts = $input[ 'accounts' ];

    	foreach ( $accounts as $acc => $id ) {
    		$input[ 'accounts' ][ $acc ] = sanitize_text_field( $id );
    	}

    	return $input;
    }

    /**
     * [sanitize_sharing description]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function sanitize_buttons( $input ) {

        $content_options = $this->options[ 'content' ][ 'options' ];
        $image_options = $this->options[ 'image' ][ 'options' ];

        foreach ( $input[ 'content' ] as $key => $value ) {

            $options = $content_options[ $key ][ 'options' ];

            switch ( $key ) {
                case 'postTypes':
                case 'socialSites':

                    $value = array_map( 'sanitize_key', $value );

                    foreach ( $value as $key => $value ) {
                        if ( ! array_key_exists( $value, $options ) ) {
                            continue;
                        }
                        $input[ 'content' ][ $key ] = $value;
                    }

                    break;
            }
        }

        foreach ( $input[ 'image' ] as $key => $value ) {

            switch ( $key ) {
                case 'postTypes':
                case 'socialSites':

                    $value = array_map( 'sanitize_key', $value );
                    $options = $content_options[ $key ][ 'options' ];

                    foreach ( $value as $key => $value ) {
                        if ( ! array_key_exists( $value, $options ) ) {
                            continue;
                        }
                        $input[ 'image' ][ $key ] = $value;
                    }

                    break;
            }
        }

        if ( ! isset( $input[ 'image' ][ 'imageSharing' ] ) ) {
            $input[ 'image' ][ 'imageSharing' ] = 0;
        } else {
            $imageSharing = $input[ 'image' ][ 'imageSharing' ];
            $input[ 'image' ][ 'imageSharing' ] = $imageSharing ? 1 : 0;
        }

        return $input;
    }

    /**
     * [sanitize_meta description]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function sanitize_metas( $input ) {

        $args[ 'site' ] = wp_parse_args( $input[ 'site' ], array(
            'metaEnable' => '',
            'siteName' => '',
            'siteDescription' => '',
            'siteImage' => ''
        ) );

        $enable = $args[ 'site' ][ 'metaEnable' ];
        $input[ 'site' ][ 'metaEnable' ] = $enable ? 1 : 0;

        $input[ 'site' ][ 'siteName' ] = sanitize_text_field( $args[ 'site' ][ 'siteName' ] );
        $input[ 'site' ][ 'siteDescription' ] = wp_kses( $args[ 'site' ][ 'siteDescription' ], array() );
        $input[ 'site' ][ 'siteImage' ] = esc_url_raw( $args[ 'site' ][ 'siteImage' ] );

        return $input;
    }

    public function sanitize_advanced( $input ) {

        $args[ 'general' ] = wp_parse_args( $input[ 'general' ], array(
                'disableStylesheet' => 0,
            ) );

        $disable_stylesheet = $args['general'][ 'disableStylesheet' ];

        $input['general']['disableStylesheet'] = $disable_stylesheet ? 1 : 0;

        return $input;
    }
}