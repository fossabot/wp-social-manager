<?php
/**
 * Public: EndPoints class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Public
 */

namespace XCo\WPSocialManager;

/**
 * The class used for creating the social media endpoint URLs.
 *
 * @since 1.0.0
 */
class EndPoints extends OutputHelpers {

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_opts = '';

	/**
	 * The Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null
	 */
	protected $metas = null;

	/**
	 * Constructor.
	 *
	 * Run the WordPress Hooks, add meta tags in the 'head' tag.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args {
	 *     An array of common arguments of the plugin.
	 *
	 *     @type string $plugin_name 	The unique identifier of this plugin.
	 *     @type string $plugin_opts 	The unique identifier or prefix for database names.
	 *     @type string $version 		The plugin version number.
	 * }
	 * @param Metas $metas 				The Meta class instance.
	 */
	function __construct( array $args, Metas $metas ) {

		$this->plugin_opts = $args['plugin_opts'];
		$this->metas = $metas;

		$this->setups();
	}

	/**
	 * Setup the meta data.
	 *
	 * The setups may involve running some Classes, Functions,
	 * and sometimes WordPress Hooks that are required to render
	 * the social buttons.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function setups() {

		$this->options = (object) array(
			'profiles' => get_option( "{$this->plugin_opts}_profiles" ),
			'buttonsContent' => get_option( "{$this->plugin_opts}_buttons_content" ),
			'buttonsImage' => get_option( "{$this->plugin_opts}_buttons_image" ),
		);
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
	 * @return array         An array of sites with their label / name and button endpoint url.
	 */
	protected function get_content_endpoints( $post_id ) {

		$sites = parent::get_button_sites( 'content' );

		$content = $this->options->buttonsContent;
		$metas = $this->get_post_metas( $post_id );

		$urls = array();

		foreach ( $sites as $key => $value ) {

			if ( ! in_array( $key, $content['includes'], true ) ||
				 ! isset( $sites[ $key ]['endpoint'] ) ) {

					unset( $sites[ $key ] );
					continue;
			}

			$urls[ $key ]['label'] = $value['label'];

			switch ( $key ) {

				case 'facebook' :

					$urls[ $key ]['endpoint'] = add_query_arg(
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

					$urls[ $key ]['endpoint'] = add_query_arg( $args, $value['endpoint'] );

					break;

				case 'googleplus' :

					$urls[ $key ]['endpoint'] = add_query_arg(
						array( 'url' => $metas['post_url'] ),
						$value['endpoint']
					);

					break;

				case 'linkedin' :

					$urls[ $key ]['endpoint'] = add_query_arg(
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

					$urls[ $key ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'description' => $metas['post_title'],
							'is_video' => false,
						),
						$value['endpoint']
					);

					break;

				case 'reddit':

					$urls[ $key ]['endpoint'] = add_query_arg(
						array(
							'url' => $metas['post_url'],
							'post_title' => $metas['post_title'],
						),
						$value['endpoint']
					);

					break;

				case 'email':

					$urls[ $key ]['endpoint'] = add_query_arg(
						array(
							'subject' => $metas['post_title'],
							'body' => $metas['post_description'],
						),
						$value['endpoint']
					);

					break;

				default:

					$urls[ $key ]['endpoint'] = false;
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
	 * @param  integer $post_id  The WordPress post ID.
	 * @return array             An array of sites with their label / name and button endpoint url.
	 */
	protected function get_image_endpoints( $post_id ) {

		$sites = parent::get_button_sites( 'image' );

		$image = $this->options->buttonsImage;
		$metas = $this->get_post_metas( $post_id );

		$urls = array();

		foreach ( $sites as $key => $value ) {

			if ( ! in_array( $key, $image['includes'], true ) ||
				 ! isset( $sites[ $key ]['endpoint'] ) ) {

					unset( $sites[ $key ] );
					continue;
			}

			$urls[ $key ]['label'] = $value['label'];

			switch ( $key ) {

				case 'pinterest':

					$urls[ $key ]['endpoint'] = add_query_arg(
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

		return $urls;
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

		$post_title = $this->metas->get_post_title( $id );
		$post_description = $this->metas->get_post_description( $id );
		$post_url = $this->metas->get_post_url( $id );

		return array(
			'post_title' => rawurlencode( $post_title ),
			'post_description' => rawurlencode( $post_description ),
			'post_url' => rawurlencode( $post_url ),
		);
	}
}
