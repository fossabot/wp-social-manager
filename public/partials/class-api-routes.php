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

use \WP_REST_Server;

/**
 * The class use for registering custom API Routes using WP-API.
 *
 * @since 1.0.0
 */
class APIRoutes extends EndPoints {

	/**
	 * The unique identifier of the route.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * The API unique namespace.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $namespace = '';

	/**
	 * Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Meta
	 */
	protected $metas = null;

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
	 * @param Metas $metas 				The class Meta instance.
	 */
	function __construct( array $args, Metas $metas ) {

		parent::__construct( $args, $metas );

		$this->plugin_name = $args['plugin_name'];

		$this->namespace = $this->plugin_name . '/1.0';

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
		$image = $this->options->buttonsImage;

		if ( ! isset( $image['enabled'] ) && false === (bool) $image['enabled'] ) {
			$image['postTypes'] = array();
		}

		$post_types = array_unique( array_merge( (array) $image['postTypes'], (array) $content['postTypes'] ), SORT_REGULAR );

		if ( ! is_singular( $post_types ) ) {
			return;
		}

		$args = array(
			'root' => esc_url( get_rest_url() ),
			'namespace' => esc_html( $this->namespace ),
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
		 *
		 * @uses \WP_REST_Server
		 */
		register_rest_route( $this->namespace, '/buttons', array( array(
				'methods' => WP_REST_Server::READABLE,
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

		$response['content'] = $this->get_content_endpoints( $request['id'] );
		$response['image'] = $this->get_image_endpoints( $request['id'] );

		return new \WP_REST_Response( $response, 200 );
	}
}
