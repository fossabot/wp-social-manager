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
class APIRoutes {

	/**
	 * The API version number.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $api_version = 'v1';

	/**
	 * The unique identifier of the route.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_slug;

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
	 * @param Endpoints $endpoints The Endpoints class instance.
	 */
	function __construct( Endpoints $endpoints ) {

		$this->endpoints = $endpoints;

		$this->plugin = $endpoints->plugin;
		$this->plugin_slug = $endpoints->plugin->get_slug();
		$this->version = $endpoints->plugin->get_version();
		$this->theme_supports = $endpoints->plugin->get_theme_supports();

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
	protected function hooks() {

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

		wp_localize_script( $this->plugin_slug . '-app', 'nineCodesSocialManager', $args );
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
		register_rest_route( $this->namespace, '/social-manager/buttons', array( array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'response_buttons' ),
				'args' => array(
					'id' => array(
						'validate_callback' => function( $id ) {
							return is_numeric( $id );
						},
						'required' => true,
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

		return new WP_REST_Response( array(
			'plugin_name' => 'Social Manager by NineCodes',
			'plugin_url' => 'http://wordpress.org/plugins/ninecodes-social-manager',
			'version' => $this->version,
			'contributors' => array(
				'Thoriq Firdaus',
				'Hongkiat Lim',
			),
		), 200 );
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

		$button_id = absint( $request['id'] );

		$response = array(
			'id' => $button_id,
			'content' => $this->endpoints->get_content_endpoints( $button_id ),
			'images' => $this->endpoints->get_image_endpoints( $button_id ),
		);

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
