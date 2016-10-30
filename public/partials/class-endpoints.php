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

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 * The class used for creating the social media endpoint URLs.
 *
 * @since 1.0.0
 */
class Endpoints {

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $plugin;

	/**
	 * The Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	public $metas;

	/**
	 * [$image_srcs description]
	 * @var [type]
	 */
	protected $image_srcs;

	/**
	 * Constructor.
	 *
	 * Run the WordPress Hooks, add meta tags in the 'head' tag.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Plugin $plugin The Plugin class instance.
	 * @param Metas  $metas The Meta class instance.
	 */
	function __construct( Plugin $plugin, Metas $metas ) {

		$this->metas = $metas;
		$this->plugin = $plugin;
	}

	/**
	 * Get the buttons content endpoint urls.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @todo add inline docs referring to each site endpoint documentation page.
	 *
	 * @param array $post_id The WordPress post ID.
	 * @return array An array of sites with their label / name and button endpoint url.
	 */
	public function get_content_endpoints( $post_id ) {

		$metas = $this->get_post_metas( $post_id );

		if ( in_array( '', $metas, true ) ) {
			return;
		}

		$sites = Options::button_sites( 'content' );
		$includes = $this->plugin->get_option( 'buttons_content', 'includes' );

		$urls = array();

		foreach ( $sites as $slug => $label ) {

			$endpoint = self::get_endpoint_base( 'content', $slug );

			if ( ! $endpoint ) {
				unset( $sites[ $slug ] );
			}

			$urls[ $slug ]['label'] = $label;

			switch ( $slug ) {

				case 'facebook' :

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array( 'u' => $metas['post_url'] ),
						$endpoint
					);

					break;

				case 'twitter' :

					$profiles = $this->plugin->get_option( 'profiles', 'twitter' );

					$args = array(
						'text' => $metas['post_title'],
						'url'  => $metas['post_url'],
					);

					if ( isset( $profiles ) && ! empty( $profiles ) ) {
						$args['via'] = $profiles;
					}

					$urls[ $slug ]['endpoint'] = add_query_arg( $args, $endpoint );

					break;

				case 'googleplus' :

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array( 'url' => $metas['post_url'] ),
						$endpoint
					);

					break;

				case 'linkedin' :

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array(
							'mini' => true,
							'title' => $metas['post_title'],
							'summary' => $metas['post_description'],
							'url' => $metas['post_url'],
							'source' => $metas['post_url'],
						),
						$endpoint
					);

					break;

				case 'pinterest':

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'description' => $metas['post_title'],
							'is_video' => false,
						),
						$endpoint
					);

					break;

				case 'reddit':

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'post_title' => $metas['post_title'],
						),
						$endpoint
					);

					break;

				case 'email':

					$urls[ $slug ]['endpoint'] = add_query_arg(
						array(
							'subject' => $metas['post_title'],
							'body' => $metas['post_description'],
						),
						$endpoint
					);

					break;

				default:

					$urls[ $slug ]['endpoint'] = false;
					break;
			} // End switch().
		} // End foreach().

		return $urls;
	}

	/**
	 * Get the buttons image endpoint urls.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @todo add inline docs referring to each site endpoint documentation page.
	 *
	 * @param integer $post_id  The WordPress post ID.
	 * @return array An array of sites with their label / name and button endpoint url.
	 */
	public function get_image_endpoints( $post_id ) {

		$metas = $this->get_post_metas( $post_id );

		if ( in_array( '', $metas, true ) ) {
			return;
		}

		$sites = Options::button_sites( 'image' );

		$urls = array();
		$buttons = array();

		foreach ( $sites as $site => $label ) {

			$endpoint = self::get_endpoint_base( 'image', $site );

			if ( ! $endpoint ) {
				unset( $sites[ $site ] );
			}

			$button['site'] = $site;
			$button['label'] = $label;
			$button['endpoint'] = $endpoint;
			$button['url'] = $metas['post_url'];
			$button['title'] = $metas['post_title'];

			$buttons = array_merge( $buttons, array( $button ) );
		}

		$srcs = $this->get_content_image_srcs( $post_id );

		foreach ( $srcs as $key => $src ) {
			$urls[] = array_map( array( $this, 'joint_image_endpoints' ), $buttons, array( $src ) );
		}

		return $urls;
	}

	protected function joint_image_endpoints( $button, $src ) {

		$url = array();

		if ( ! isset( $button['site'] ) ) {
			return $url;
		}

		$site = $button['site'];
		$endpoints = array(
			'facebook' => array(),
			'twitter' => array(),
			'pinterest' => array(
				'label' => $button['label'],
				'endpoint' => add_query_arg( array(
					'url' => $button['url'],
					'description' => $button['title'],
					'is_video' => false,
					'media' => $src
				), $button['endpoint'] )
			)
		);

		if ( isset( $endpoints[ $site ] ) ) {
			$url[ $site ] = $endpoints[ $site ];
		}

		return $url;
	}

	/**
	 * [get_content_image_srcs description]
	 * @return [type] [description]
	 */
	protected function get_content_image_srcs( $post_id ) {

		$content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

		libxml_use_internal_errors( true );

		$dom = new DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		$images = $dom->getElementsByTagName( 'img' );
		$source = array();

		foreach ( $images as $key => $img ) {
			$source[] = $img->getAttribute( 'src' );
		}

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
	protected function get_post_metas( $post_id ) {

		$post_title = $this->metas->get_post_title( $post_id );
		$post_description = $this->metas->get_post_description( $post_id );

		if ( 'shortlink' === $this->plugin->get_option( 'modes', 'link_mode' ) ) {
			$post_url = wp_get_shortlink( $post_id );
		} else {
			$post_url = $this->metas->get_post_url( $post_id );
		}

		return array(
			'post_title' => rawurlencode( $post_title ),
			'post_description' => rawurlencode( $post_description ),
			'post_url' => rawurlencode( $post_url ),
		);
	}

	/**
	 * Get the buttons endpoint base URLs.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $of The buttons group to retrieve.
	 * @param string $site The site slug.
	 * @return array Selected list of button the buttons endpoints or all if $of is not specified.
	 */
	protected static function get_endpoint_base( $of, $site ) {

		$endpoints['content'] = array(
			'facebook' => 'https://www.facebook.com/sharer/sharer.php',
			'twitter' => 'https://twitter.com/intent/tweet',
			'googleplus' => 'https://plus.google.com/share',
			'pinterest' => 'https://www.pinterest.com/pin/create/bookmarklet/',
			'linkedin' => 'https://www.linkedin.com/shareArticle',
			'reddit' => 'https://www.reddit.com/submit',
			'email' => 'mailto:',
		);

		$endpoints['image'] = array(
			'pinterest' => 'https://pinterest.com/pin/create/bookmarklet/',
		);

		return isset( $endpoints[ $of ][ $site ] ) ? $endpoints[ $of ][ $site ] : null;
	}

}
