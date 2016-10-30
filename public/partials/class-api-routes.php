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
	protected $api_version = '1.0';

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

		$this->namespace = $this->plugin_slug . '/' . $this->api_version;

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

		wp_localize_script( $this->plugin_slug . '-app', 'NineCodesSocialManager', $args );
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
		 * @example http://local.wordpress.dev/wp-json/ninecodes-social-manager/1.0/buttons?id=79
		 *
		 * @uses \WP_REST_Server
		 */
		register_rest_route( $this->namespace, '/buttons', array( array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array( $this, 'response_buttons' ),
				'args' => array(
					'id' => array(
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

		if ( isset( $request['id'] ) ) {

			$response = array(
				'id' => $request['id'],
			);

			$response['content'] = $this->endpoints->get_content_endpoints( $request['id'] );
			$response['image'] = $this->endpoints->get_image_endpoints( $request['id'] );

		} else {

			$response = array(
				'plugin_name' => 'Social Manager by NineCodes',
				'plugin_url' => 'http://wordpress.org/plugins/ninecodes-social-manager',
				'version' => $this->version,
				'contributors' => array(
					'Thoriq Firdaus',
					'Hongkiat Lim',
				),
			);
		}

		return new WP_REST_Response( $response, 200 );
	}
}
