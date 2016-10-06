<?php

namespace XCo\WPSocialManager;

/**
 *
 */
final class APIRoutes extends OutputUtilities {

	/**
	 * The unique identifier of the route.
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $namespace;

	/**
	 * The version of the API routes.
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * [$metas description]
	 * @var [type]
	 */
	protected $metas;

	/**
	 * [__construct description]
	 */
	public function __construct( $namespace, Metas $metas ) {

		/**
		 * API version (not the plugin version).
		 * @var string
		 */
		$this->version = '1.0';

		/**
		 * [$this->namespace description]
		 * @var [type]
		 */
		$this->namespace = $namespace;

		/**
		 * [$this->metas description]
		 * @var [type]
		 */
		$this->metas = $metas;
	}

	/**
	 * [localize_scripts description]
	 * @since  1.0.0 [<description>]
	 * @return void
	 */
	public function localize_scripts() {

		wp_localize_script( $this->namespace, 'WPSocialManagerData', array(

			'root'  => esc_url_raw( get_rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		) );
	}

	/**
	 * [register_routes description]
	 * @since  1.0.0 [<description>]
	 * @return [type] [description]
	 */
	public function register_routes() {

		$base = $this->namespace . '/' . $this->version;

		register_rest_route( $base, '/buttons', array( array(

			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'response_buttons' ),
			'args' => array(
				'id' => array(
					'required' => true,
					'sanitize_callback' => 'absint',
					'validate_callback' => function( $param ) {
						return ( $param );
					} )
				) ),
			)
		);
	}

	/**
	 * [get_buttons description]
	 * @return [type] [description]
	 */
	public function response_buttons( $request ) {

		$response = array(
			'content' => false,
			'image' => false
		);

		if ( (bool) $this->metas->get_post_meta( $request[ 'id' ], 'buttons_content' ) ) {
			$response[ 'content' ] = $this->get_buttons_content( $request );
		}

		if ( (bool) $this->metas->get_post_meta( $request[ 'id' ], 'buttons_image' ) ) {
			$response[ 'image' ] = $this->get_buttons_image( $request );
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * [get_button_sites description]
	 * @return [type] [description]
	 */
	protected function get_buttons_content( $request ) {

		$sites = self::get_button_sites( 'content' );

		$content = $this->get_option( 'wp_social_manager_buttons_content' );
		$metas = $this->get_post_metas( $request[ 'id' ] );

		$buttons = array();

		foreach ( $sites as $key => $value ) {

			if ( ! isset( $content[ 'buttonSites' ][ $key ] ) ||
				 ! isset( $sites[ $key ][ 'endpoint' ] ) ) {

					unset( $sites[ $key ] );
					continue;
			}

			$buttons[ $key ][ 'label' ] = $value[ 'label' ];

			switch ( $key ) {

				case 'facebook' :

					$buttons[ $key ][ 'endpoint' ] = add_query_arg(
							array( 'u' => $metas[ 'post_url' ]
						), $value[ 'endpoint' ] );

					break;

				case 'twitter' :

					$profiles = $this->get_option( 'wp_social_manager_profiles' );
					$args = array(
							'text' => $metas[ 'post_title' ],
							'url'  => $metas[ 'post_url' ]
						);

					if ( isset( $profiles[ 'twitter' ] ) && ! empty( $profiles[ 'twitter' ] ) ) {
						$args[ 'via' ] = $profiles[ 'twitter' ];
					}

					$buttons[ $key ][ 'label' ] = add_query_arg( $args, $value[ 'endpoint' ] );

					break;

				case 'googleplus' :

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'url' => $metas[ 'post_url' ]
						), $value[ 'endpoint' ] );
					break;

				case 'googleplus' :

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'url' => $metas[ 'post_url' ]
						), $value[ 'endpoint' ] );

					break;

				case 'pinterest':

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'url' => $metas[ 'post_url' ],
							'description' => $metas[ 'post_title' ],
							'image' => $metas[ 'image' ],
							'is_video' => false
						), $value[ 'endpoint' ]  );
					break;

				case 'reddit':

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'url' => $metas[ 'post_url' ],
							'post_title' => $metas[ 'post_title' ]
						), $value[ 'endpoint' ]  );
					break;

				case 'email':

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'subject' => $metas[ 'post_title' ],
							'body' => $metas[ 'post_description' ]
						), $value[ 'endpoint' ]  );
					break;
			}
		}

		return $buttons;
	}

	/**
	 * [get_buttons_content description]
	 * @return [type] [description]
	 */
	protected function get_buttons_image( $request ) {

		$sites = self::get_button_sites( 'image' );

		$image = $this->get_option( 'wp_social_manager_buttons_image' );
		$metas = $this->get_post_metas( $request[ 'id' ] );

		$buttons = array();

		foreach ( $sites as $key => $value ) {

			$buttons[ $key ][ 'label' ] = $value[ 'label' ];

			switch ( $key ) {

				case 'facebook' :

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'u' => $metas[ 'post_url' ]
						), $value[ 'endpoint' ] );
					break;

				case 'pinterest':

					$buttons[ $key ][ 'endpoint' ] = add_query_arg( array(
							'url' => $metas[ 'post_url' ],
							'description' => $metas[ 'post_title' ],
							'is_video' => false
						), $value[ 'endpoint' ]  );
					break;
			}
		}

		return $buttons;
	}

	/**
	 * [get_meta description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	protected function get_post_metas( $id ) {

		$post_title = $this->metas->post_title( $id );
		$post_description = $this->metas->post_description( $id );
		$post_image = $this->metas->post_image( $id );
		$post_url = $this->metas->post_url( $id );

		return array(
			'post_title' => rawurlencode( $post_title ),
			'post_description' => rawurlencode( $post_description ),
			'post_image' => $post_image,
			'post_url' => rawurlencode( $post_url )
		);
	}

	/**
	 * [get_meta description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	protected function get_option( $key ) {
		return get_option( $key );
	}
}
