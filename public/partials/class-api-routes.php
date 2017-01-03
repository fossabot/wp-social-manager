<?php
/**
 * Public: APIRoutes class
 *
 * @package SocialManager
 * @subpackage Public\APIRoutes
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \WP_REST_Request;
use \WP_REST_Server;
use \WP_REST_Response;

/**
 * The class use for registering custom API Routes using WP-API.
 *
 * @since 1.0.0
 */
class APIRoutes extends Endpoints {

	/**
	 * The API version number.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $api_version = 'v1';

	/**
	 * The API unique namespace.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $namespace;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param ViewPublic $public The ViewPublic class instance.
	 */
	public function __construct( ViewPublic $public ) {
		parent::__construct( $public );

		$this->version = $public->plugin->get_version();
		$this->namespace = 'ninecodes/' . $this->api_version;

		$this->hooks();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function hooks() {

		add_filter( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ) );
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

		$content = (array) $this->plugin->get_option( 'buttons_content', 'post_types' );

		if ( ! (bool) $this->plugin->get_option( 'buttons_image', 'enabled' ) ) {
			$image = array();
		} else {
			$image = (array) $this->plugin->get_option( 'buttons_image', 'post_types' );
		}

		$post_types = array_unique( array_merge( $image, $content ), SORT_REGULAR );

		if ( ! is_singular( $post_types ) ) {
			return;
		}

		$args = array(
			'root' => esc_url( get_rest_url() ),
			'namespace' => esc_html( $this->namespace ),
			'attrPrefix' => esc_attr( Helpers::get_attr_prefix() ),
		);

		$post_id = get_the_id();

		if ( $post_id ) {
			$args['id'] = absint( $post_id );
		}

		wp_localize_script( $this->plugin_slug . '-app', 'nineCodesSocialManagerAPI', $args );
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

		register_rest_route( $this->namespace, '/social-manager', array( array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'response_plugin' ),
			),
		) );

		/**
		 * Register the '/buttons' route.
		 *
		 * This route requires the 'id' parameter that passes
		 * the post ID.
		 *
		 * @example http://local.wordpress.dev/wp-json/ninecodes-social-manager/1.0/buttons?id=79
		 *
		 * @uses \WP_REST_Server
		 */
		register_rest_route( $this->namespace, '/social-manager/buttons/(?P<id>[\d]+)', array( array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'response_buttons' ),
				'args' => array(
					'id' => array(
						'sanitize_callback' => 'absint',
						'validate_callback' => function( $param, $request, $key ) {
							return is_numeric( $param );
						},
					),
					'select' => array(
						'sanitize_callback' => 'sanitize_key',
						'validate_callback' => function( $param, $request, $key ) {
							return is_string( $param ) && ! empty( $param ) && in_array( $param, array( 'content', 'images' ), true );
						},
					),
				),
			),
		) );
	}

	/**
	 * Return the '/social-manager' route response.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Data from the request.
	 * @return WP_REST_Respon
	 */
	public function response_plugin( WP_REST_Request $request ) {

		$response = array(
			'plugin_name' => 'Social Manager',
			'plugin_url' => 'http://wordpress.org/plugins/ninecodes-social-manager',
			'version' => $this->version,
			'contributors' => array(
				'Thoriq Firdaus',
				'Hongkiat Lim',
			),
		);

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Return the '/social-manager/buttons' route response.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request The passed parameters in the route.
	 * @return WP_REST_Response
	 */
	public function response_buttons( WP_REST_Request $request ) {

		$button_id = $request['id'];
		$response = array( 'id' => $button_id );

		if ( isset( $request['select'] ) && ! empty( $request['select'] ) ) {

			$select = $request['select'];

			if ( 'content' === $select ) {
				$response['content'] = array(
					'endpoints' => $this->get_content_endpoints( $button_id ),
				);
			}

			if ( 'images' === $select ) {
				$response['images'] = $this->get_image_endpoints( $button_id );
			}
		} else {

			$response = array_merge( $response, array(
				'content' => $this->get_content_endpoints( $button_id ),
				'images' => $this->get_image_endpoints( $button_id ),
			) );
		}

		return new WP_REST_Response( $response, 200 );
	}

	/**
	 * Function ot get the plugin api namespace.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The name space and the version.
	 */
	public function get_namespace() {
		return $this->namespace;
	}
}
