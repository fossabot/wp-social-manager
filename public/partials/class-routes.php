<?php
/**
 * Public: APIRoutes class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Public\Routes
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class use for registering custom API Routes using WP-API.
 *
 * @since 1.0.0
 */
final class APIRoutes extends OutputUtilities {

	/**
	 * The unique identifier of the route.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_opts;

	/**
	 * The version of the API routes.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $version;

	/**
	 * Options required to define the routes.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var object
	 */
	protected $options;

	/**
	 * Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Meta
	 */
	protected $metas;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name    The unique identifier of this plugin.
	 *     @type string $plugin_opts    The unique identifier or prefix for database names.
	 *     @type string $version        The plugin version number.
	 * }
	 * @param Metas $metas The class Meta instance.
	 */
	public function __construct( array $args, Metas $metas ) {

		$this->plugin_name = $args['plugin_name'];

		$this->plugin_opts = $args['plugin_opts'];

		$this->namespace = $this->plugin_name . '/1.0';

		$this->options = (object) array(
			'profiles'       => $this->get_option( "{$this->plugin_opts}_profiles" ),
			'buttonsContent' => $this->get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttonsImage'   => $this->get_option( "{$this->plugin_opts}_buttons_image" ),
		);

		$this->metas = $metas;
	}

	/**
	 * Localize a script.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @todo Print the localize script in the homagepage and archives (Category, Tag, etc.).
	 *
	 * @return mixed Return false if viewed outside the specified singular posts.
	 */
	public function localize_scripts() {

		$content = $this->options->buttonsContent;
		$image   = $this->options->buttonsImage;

		if ( ! isset( $image['enabled'] ) && false === (bool) $image['enabled'] ) {
			$image['postTypes'] = array();
		}

		$post_types = array_unique( array_merge( (array) $image['postTypes'], (array) $content['postTypes'] ), SORT_REGULAR );

		if ( ! is_singular( $post_types ) ) {
			return;
		}

		$args = array(
			'root'       => esc_url( get_rest_url() ),
			'namespace'  => esc_html( $this->namespace ),
			'attrPrefix' => esc_attr( self::get_attr_prefix() ),
		);

		$post_id = get_the_id();

		if ( $post_id ) {
			$args['id'] = absint( $post_id );
		}

		wp_localize_script( $this->plugin_name, 'wpSocialManager', $args );
	}

	/**
	 * Registers a REST API route.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_rest_route/
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_routes() {

		/**
		 * Register the '/buttons' route.
		 *
		 * This route requires the 'id' parameter that passes
		 * the post ID.
		 *
		 * @example http://local.wordpress.dev/wp-json/wp-social-manager/1.0/buttons?id=79
		 */
		register_rest_route( $this->namespace, '/buttons', array( array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'response_buttons' ),
				'args' => array(
					'id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
						'validate_callback' => function( $param ) {
							return ( $param );
						},
					),
				),
			),
		) );
	}

	/**
	 * Return the '/buttons' route response.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $request The passed parameters in the route.
	 * @return WP_REST_Response A REST response object.
	 */
	public function response_buttons( $request ) {

		$response = array(
			'id' => $request['id'],
		);

		$response['content'] = $this->buttons_content_response( $request );
		$response['image'] = $this->buttons_image_response( $request );

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Get the Buttons Content Response.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @todo add inline docs referring to each site endpoint documentation page.
	 *
	 * @param array $request The passed parameters in the route.
	 * @return array         An array of sites with their label / name and button endpoint url.
	 */
	protected function buttons_content_response( $request ) {

		$sites   = self::get_button_sites( 'content' );

		$content = $this->options->buttonsContent;
		$metas   = $this->get_post_metas( $request['id'] );

		$buttons = array();

		foreach ( $sites as $key => $value ) {

			if ( ! in_array( $key, $content['includes'], true ) ||
				 ! isset( $sites[ $key ]['endpoint'] ) ) {

					unset( $sites[ $key ] );
					continue;
			}

			$buttons[ $key ]['label'] = $value['label'];

			switch ( $key ) {

				case 'facebook' :

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array( 'u' => $metas['post_url'] ),
						$value['endpoint']
					);

					break;

				case 'twitter' :

					$profiles = $this->options->profiles;

					$args = array(
						'text' => $metas['post_title'],
						'url'  => $metas['post_url'],
					);

					if ( isset( $profiles['twitter'] ) && ! empty( $profiles['twitter'] ) ) {
						$args['via'] = $profiles['twitter'];
					}

					$buttons[ $key ]['endpoint'] = add_query_arg( $args, $value['endpoint'] );

					break;

				case 'googleplus' :

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array( 'url' => $metas['post_url'] ),
						$value['endpoint']
					);

					break;

				case 'linkedin' :

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array(
							'mini' => true,
							'title' => $metas['post_title'],
							'summary' => $metas['post_description'],
							'url' => $metas['post_url'],
							'source' => $metas['post_url'],
						),
						$value['endpoint']
					);

					break;

				case 'pinterest':

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'description' => $metas['post_title'],
							'is_video' => false,
						),
						$value['endpoint']
					);

					break;

				case 'reddit':

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'post_title' => $metas['post_title'],
						),
						$value['endpoint']
					);

					break;

				case 'email':

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array(
							'subject' => $metas['post_title'],
							'body' => $metas['post_description'],
						),
						$value['endpoint']
					);

					break;

				default:

					$buttons[ $key ]['endpoint'] = false;
					break;
			} // End switch().
		} // End foreach().

		return $buttons;
	}

	/**
	 * Get the Buttons Image Response.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @todo add inline docs referring to each site endpoint documentation page.
	 *
	 * @param array $request The passed parameters in the route.
	 * @return array         An array of sites with their label / name and button endpoint url.
	 */
	protected function buttons_image_response( $request ) {

		$sites = self::get_button_sites( 'image' );

		$image = $this->options->buttonsImage;
		$metas = $this->get_post_metas( $request['id'] );

		$buttons = array();

		foreach ( $sites as $key => $value ) {

			$buttons[ $key ]['label'] = $value['label'];

			switch ( $key ) {

				case 'pinterest':

					$buttons[ $key ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'description' => $metas['post_title'],
							'is_video' => false,
						),
						$value['endpoint']
					);

					break;
			}
		}

		return $buttons;
	}

	/**
	 * Get a collection of post metas to add in the site endpoint parameter.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param  integer $id The WordPress post ID.
	 * @return array       An array of post meta.
	 */
	protected function get_post_metas( $id ) {

		$post_title = $this->metas->post_title( $id );
		$post_description = $this->metas->post_description( $id );
		$post_url = $this->metas->post_url( $id );

		return array(
			'post_title' => rawurlencode( $post_title ),
			'post_description' => rawurlencode( $post_description ),
			'post_url' => rawurlencode( $post_url ),
		);
	}

	/**
	 * Method to retrieve an option inside the class.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param  string $key The option name.
	 * @return mixed       The option data.
	 */
	protected function get_option( $key ) {
		return get_option( $key );
	}
}
