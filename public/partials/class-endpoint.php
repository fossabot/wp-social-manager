<?php
/**
 * Public: EndPoints class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package SocialManager
 * @subpackage Public
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 * The class used for creating the social media endpoint URLs.
 *
 * @since 1.0.0
 */
class Endpoint {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The Meta class instance.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var Meta
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 * @param Meta   $meta The Meta class instance.
	 */
	function __construct( Plugin $plugin, Meta $meta ) {

		$this->plugin = $plugin;
		$this->meta = $meta;
	}

	/**
	 * Get the buttons content endpoint urls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id The WordPress post ID.
	 * @return array An array of sites with their label / name and button endpoint url.
	 *
	 * TODO add inline docs referring to each site endpoint documentation page.
	 */
	public function get_content_endpoint( $post_id ) {

		$post_id = absint( $post_id );
		$meta = $this->get_post_meta( $post_id );

		$output = array();

		if ( ! $meta['post_url'] || ! $meta['post_title'] ) {
			return $output;
		}

		$includes = (array) $this->plugin->get_option( 'button_content', 'include' );
		$sites = Options::button_sites( 'content' );

		foreach ( $sites as $site => $label ) {  // Exclude site which is not enabled.

			if ( ! in_array( $site, array_keys( $includes ), true ) ||
				 ! isset( $includes[ $site ]['enable'] ) ||
				 'on' !== $includes[ $site ]['enable'] ) {

				unset( $sites[ $site ] );
			}
		}

		foreach ( $sites as $site => $value ) {

			if ( ! isset( $value['endpoint'] ) || empty( $value['endpoint'] ) ) {
				continue;
			}

			$endpoint = $value['endpoint'];

			switch ( $site ) {
				case 'facebook':
					$output[ $site ] = add_query_arg(
						array(
							'u' => $meta['post_url'],
						),
						$endpoint
					);

					break;

				case 'twitter':
					$profiles = $this->plugin->get_option( 'profile', 'twitter' );

					$args = array(
						'text' => $meta['post_title'],
						'url'  => $meta['post_url'],
					);

					if ( isset( $profiles ) && ! empty( $profiles ) ) {
						$args['via'] = $profiles;
					}

					$output[ $site ] = add_query_arg( $args, $endpoint );

					break;

				case 'googleplus':
					$output[ $site ] = add_query_arg(
						array(
							'url' => $meta['post_url'],
						),
						$endpoint
					);

					break;

				case 'linkedin':
					$output[ $site ] = add_query_arg(
						array(
							'mini' => true,
							'title' => $meta['post_title'],
							'summary' => $meta['post_description'],
							'url' => $meta['post_url'],
							'source' => rawurlencode( get_site_url() ),
						),
						$endpoint
					);

					break;

				case 'pinterest':
					$output[ $site ] = add_query_arg(
						array(
							'url' => $meta['post_url'],
							'description' => $meta['post_title'],
							'is_video' => false,
							'media' => $meta['post_image'],
						),
						$endpoint
					);

					break;

				case 'reddit':
					$output[ $site ] = add_query_arg(
						array(
							'url' => $meta['post_url'],
							'post_title' => $meta['post_title'],
						),
						$endpoint
					);

					break;

				case 'tumblr':

					$output[ $site ] = add_query_arg(
						array(
							'url' => $meta['post_url'],
							'name' => $meta['post_title'],
							'description' => substr( $meta['post_description'], 0, 30 ) . '...',
						),
						$endpoint
					);

					break;

				case 'email':
					$output[ $site ] = add_query_arg(
						array(
							'subject' => $meta['post_title'],
							'body' => $meta['post_description'],
						),
						$endpoint
					);

					break;

				default:

					/**
					 * Filter to add endpoint for custom site.
					 *
					 * @since 1.2.0
					 *
					 * @param mixed  $value The value return from the filter.
					 * @param string $site The site slug.
					 * @param string $context Whether for 'content' or 'image'
					 * @param array  $args The button arguments to construct the endpoint URLs.
					 */
					$output[ $site ] = apply_filters( 'ninecodes_social_manager_button_endpoint', null, $site, 'content', array(
						'endpoint' => $endpoint,
						'post_title' => $meta['post_title'],
						'post_description' => $meta['post_description'],
						'post_url' => $meta['post_url'],
						'post_image' => $meta['post_image'],
					) );
					break;
			} // End switch().
		} // End foreach().

		return array(
			'endpoint' => $output,
		);
	}

	/**
	 * Get the complete endpoint url for buttons on the image.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @todo add inline docs referring to each site endpoint documentation page.
	 *
	 * @param integer $post_id The WordPress post ID.
	 * @return array An array of sites with their label / name and button endpoint url.
	 */
	public function get_image_endpoint( $post_id ) {

		$post_id = absint( $post_id );
		$meta = $this->get_post_meta( $post_id );

		$output = array();
		$buttons = array();

		if ( ! $meta['post_url'] || ! $meta['post_title'] ) {
			return $output;
		}

		/**
		 * Get the sites enabled in the "Settings > Social Media".
		 *
		 * @var array
		 */
		$includes = (array) $this->plugin->get_option( 'button_image', 'include' );
		$sites = Options::button_sites( 'image' );

		foreach ( $sites as $site => $label ) { // Exclude site which is not enabled.

			if ( ! in_array( $site, array_keys( $includes ), true ) ||
				 ! isset( $includes[ $site ]['enable'] ) ||
				 'on' !== $includes[ $site ]['enable'] ) {

				unset( $sites[ $site ] );
			}
		}

		foreach ( $sites as $site => $value ) {

			$label = $value['label'];
			$endpoint = $value['endpoint'];

			$button['site'] = $site;
			$button['label'] = $label;
			$button['endpoint'] = $endpoint;
			$button['post_url'] = $meta['post_url'];
			$button['post_title'] = $meta['post_title'];

			$buttons = array_merge( $buttons, array( $button ) );
		}

		$content = get_post_field( 'post_content', $post_id );
		$image_src = $this->get_content_image_src( $content );

		foreach ( $image_src as $src ) {
			$output = array_merge( $output, array_map( array( $this, 'joint_image_endpoint' ), $buttons, array( $src ) ) );
		}

		return $output;
	}

	/**
	 * Function to merge each image src to the button endpoint URL.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array  $button {
	 *              The site button properties.
	 *              @type string $site       The button site unique key e.g. facebook, twitter, etc.
	 *              @type string $label      The button label or text.
	 *              @type string $endpoint   The site endpoint URL of the button.
	 *              @type string $post_url   The post URL.
	 *              @type string $post_title The post title.
	 * }
	 * @param string $src The image source URL.
	 * @return array The button endpoint URL with the image src added.
	 */
	protected function joint_image_endpoint( $button, $src ) {

		$urls = array();

		if ( ! isset( $button['site'] ) ) {
			return $urls;
		}

		$site = $button['site'];
		$endpoint = array(
			'pinterest' => add_query_arg(
				array(
					'url' => $button['post_url'],
					'description' => $button['post_title'],
					'is_video' => false,
					'media' => $src,
				),
				$button['endpoint']
			),
		);

		if ( isset( $endpoint[ $site ] ) ) {
			$urls[ $site ] = $endpoint[ $site ];
		}

		return array(
			'src' => $src,
			'endpoint' => $urls,
		);
	}

	/**
	 * Function to get image sources in the post content.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param integer $content The post content.
	 * @return array List of image source URL.
	 */
	protected function get_content_image_src( $content ) {

		$dom = new DOMDocument();
		$errors = libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$images = $dom->getElementsByTagName( 'img' );
		$source = array();

		if ( 0 === $images->length ) {
			return $source;
		}

		foreach ( $images as $key => $img ) {
			$source[] = $img->getAttribute( 'src' );
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $errors );

		return $source;
	}

	/**
	 * Get a collection of post metas to add in the site endpoint parameter.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param integer $post_id The WordPress post ID.
	 * @return array An array of post meta.
	 */
	protected function get_post_meta( $post_id ) {

		$charset = get_bloginfo( 'charset' );

		$post_id = absint( $post_id );
		$post_title = $this->meta->get_post_title( $post_id );
		$post_description = $this->meta->get_post_description( $post_id );
		$post_image = $this->meta->get_post_image( $post_id );
		$post_url = $this->meta->get_post_url( $post_id );

		if ( 'shortlink' === $this->plugin->get_option( 'mode', 'link_mode' ) ) {
			$post_url = wp_get_shortlink( $post_id );
		}

		return array(
			'post_title' => rawurlencode( html_entity_decode( $post_title, ENT_COMPAT, $charset ) ),
			'post_description' => rawurlencode( html_entity_decode( $post_description, ENT_COMPAT, $charset ) ),
			'post_url' => rawurlencode( $post_url ),
			'post_image' => isset( $post_image['src'] ) ? rawurlencode( $post_image['src'] ) : '',
		);
	}
}
