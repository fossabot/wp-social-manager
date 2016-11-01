<?php
/**
 * Public: WPHead class
 *
 * @package SocialManager
 * @subpackage Public\WPHead
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to generate social meta tags within the 'head' tag of the website.
 *
 * @since 1.0.0
 */
final class WPHead {

	/**
	 * The Meta class instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Meta
	 */
	protected $metas;

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $option_slug;

	/**
	 * The current website language.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $locale;

	/**
	 * Constructor.
	 *
	 * Get the Metas instance, instantiate the setups and hooks
	 * to render the meta tags in the 'head' tag.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param Metas $metas The Metas class instance.
	 */
	function __construct( Metas $metas ) {

		$this->metas = $metas;
		$this->plugin = $metas->plugin;
		$this->option_slug = $metas->plugin->get_opts();

		$this->hooks();
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
	 *
	 * @return void
	 */
	protected function setups() {
		$this->locale = get_locale();
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

		add_action( 'wp_head', array( $this, 'site_meta_tags' ), -10 );
		add_action( 'wp_head', array( $this, 'post_meta_tags' ), -10 );
	}

	/**
	 * Print the site meta tag elements in the 'head' tag.
	 *
	 * The "site" meta tags is generted in the homepage and
	 * archive pages (e.g. Categories, Tags, and Custom Taxonomy Terms).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function site_meta_tags() {

		if ( is_single() || ! $this->metas->is_meta_enabled() ) {
			return;
		}

		$tag_args = array(
			'site_name' => $this->metas->get_site_name(),
			'site_title' => $this->metas->get_site_title(),
			'site_description' => $this->metas->get_site_description(),
			'site_url' => $this->metas->get_site_url(),
			'site_image' => $this->metas->get_site_image(),
		);

		$og = $this->site_open_graph( apply_filters( 'ninecodes_social_manager_meta_tags', $tag_args, 'site', 'open-graph' ) );
		$tc = $this->site_twitter_cards( apply_filters( 'ninecodes_social_manager_meta_tags', $tag_args, 'site', 'twitter-cards' ) );

		echo "<!-- START: Social Manager by NineCodes -->\n";
		echo wp_kses( "{$og}{$tc}", array(
			'meta' => array(
			'property' => array(),
			'content' => array(),
			'name' => array(),
			),
		) );
		echo "<!-- END: Social Manager by NineCodes -->\n";
	}

	/**
	 * Print the post meta tag elements in the 'head' tag.
	 *
	 * The "post" meta tag is generated in any a single post or page
	 * of any Post Types.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function post_meta_tags() {

		if ( ! is_single() || ! $this->metas->is_meta_enabled() ) {
			return;
		}

		$post_id = get_the_id();

		$tag_args = array(
			'site_name' => $this->metas->get_site_name(),
			'post_title' => $this->metas->get_post_title( $post_id ),
			'post_description' => $this->metas->get_post_description( $post_id ),
			'post_url' => $this->metas->get_post_url( $post_id ),
			'post_image' => $this->metas->get_post_image( $post_id ),
			'post_author' => $this->metas->get_post_author( $post_id ),
		);

		$og = $this->post_open_graph( apply_filters( 'ninecodes_social_manager_meta_tags', $tag_args, 'post', 'open-graph' ) );
		$tc = $this->post_twitter_cards( apply_filters( 'ninecodes_social_manager_meta_tags', $tag_args, 'post', 'twitter-cards' ) );

		echo "<!-- START: Social Manager by NineCodes -->\n";
		echo wp_kses( "{$og}{$tc}", array(
			'meta' => array(
			'property' => array(),
			'content' => array(),
			'name' => array(),
			),
		) );
		echo "<!-- END: Social Manager by NineCodes -->\n";
	}

	/**
	 * Create the Open Graph meta tags for the "site".
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @link http://ogp.me/
	 *
	 * @param array $args {
	 *     An array of website meta data to add in the social meta tags.
	 *
	 *     @type string $site_name 			The website name or brand.
	 *     @type string $site_title 		The website title.
	 *     @type string $site_description 	The website description.
	 *     @type string $site_url 			The website url.
	 *     @type string $site_image 		The website image / icon / logo.
	 * }
	 * @return string The Open Graph meta tags.
	 */
	public function site_open_graph( $args ) {

		$meta = '';

		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'site_title' => false,
			'site_description' => false,
			'site_url' => false,
			'site_image' => array(),
		) );

		$meta .= $args['site_name'] ? sprintf( "<meta property='og:site_name' content='%s' />\n", esc_attr( $args['site_name'] ) ) : '';
		$meta .= $args['site_title'] ? sprintf( "<meta property='og:title' content='%s' />\n", esc_attr( $args['site_title'] ) ) : '';
		$meta .= $args['site_description'] ? sprintf( "<meta property='og:description' content='%s' />\n", esc_attr( $args['site_description'] ) ) : '';
		$meta .= $args['site_url'] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args['site_url'] ) ) : '';

		if ( ! empty( $args['site_image'] ) ) {

			$source = $args['site_image']['src'];
			$width  = $args['site_image']['width'];
			$height = $args['site_image']['height'];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:url' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {
				$meta .= sprintf( "<meta property='og:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta property='og:type' content='website' />\n";
			$locale = sprintf( "<meta property='og:locale' content='%s' />\n", esc_attr( $this->locale ) );

			$meta = $type . $locale . $meta;
		}

		return $meta;
	}

	/**
	 * Create Twitter Cards meta tags for the "site".
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @link https://dev.twitter.com/cards/overview
	 *
	 * @param array $args {
	 *     An array of website meta data to add in the social meta tags.
	 *
	 *     @type string $site_name 			The website name or brand.
	 *     @type string $site_title 		The website title.
	 *     @type string $site_description 	The website description.
	 *     @type string $site_url 			The website url.
	 *     @type string $site_image 		The website image / icon / logo.
	 * }
	 * @return string The Twitter Cards meta tags.
	 */
	public function site_twitter_cards( $args ) {

		$meta = '';

		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'site_title' => false,
			'site_description' => false,
			'site_url' => false,
			'site_image' => array(),
		) );

		$meta .= $args['site_title'] ? sprintf( "<meta name='twitter:title' content='%s' />\n", esc_attr( $args['site_title'] ) ) : '';
		$meta .= $args['site_description'] ? sprintf( "<meta name='twitter:description' content='%s' />\n", esc_attr( $args['site_description'] ) ) : '';
		$meta .= $args['site_url'] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args['site_url'] ) ) : '';

		if ( ! empty( $meta ) ) {

			$twitter = $this->plugin->get_option( 'profiles', 'twitter' );

			$site = $twitter ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $twitter ) ) : '';
			$type = "<meta name='twitter:card' content='summary' />\n";
			$meta = $site . $type . $meta;
		}

		if ( ! empty( $args['site_image'] ) ) {

			$source = $args['site_image']['src'];
			$width  = $args['site_image']['width'];
			$height = $args['site_image']['height'];

			if ( $source && $width && $height ) {

				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {

				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		return $meta;
	}

	/**
	 * Create the Open Graph meta tags for the "post".
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @link http://ogp.me/
	 *
	 * @param array $args {
	 *     An array of website meta data to add in the social meta tags.
	 *
	 *     @type string $site_name 			The website name or brand.
	 *     @type string $site_title 		The website title.
	 *     @type string $site_description 	The website description.
	 *     @type string $site_url 			The website url.
	 *     @type string $site_image 		The website image / icon / logo.
	 * }
	 * @return string The Open Graph meta tags.
	 */
	protected function post_open_graph( $args ) {

		$meta = '';

		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'post_title' => false,
			'post_description' => false,
			'post_url' => false,
			'post_image' => array(),
			'post_author' => array(),
		) );

		$meta .= $args['post_title'] ? sprintf( "<meta property='og:title' content='%s' />\n", esc_attr( $args['post_title'] ) ) : '';
		$meta .= $args['post_description'] ? sprintf( "<meta property='og:description' content='%s' />\n", esc_attr( $args['post_description'] ) ) : '';
		$meta .= $args['post_url'] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args['post_url'] ) ) : '';

		if ( ! empty( $args['post_image'] ) ) {

			$source = $args['post_image']['src'];
			$width  = $args['post_image']['width'];
			$height = $args['post_image']['height'];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:url' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {
				$meta .= sprintf( "<meta property='og:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta property='og:type' content='article' />\n";
			$locale = sprintf( "<meta property='og:locale' content='%s' />\n", esc_attr( $this->locale ) );

			$meta = $type . $locale . $meta;
		}

		$site = $args['site_name'] ? sprintf( "<meta property='og:site_name' content='%s' />\n", esc_attr( $args['site_name'] ) ) : '';

		$graph = $this->post_facebook_graph( $args );

		return $site . $meta . $graph;
	}

	/**
	 * Create Facebook Graph meta tags for the "post".
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @link http://ogp.me/
	 * @todo Shall merge these to 'post_open_graph'?
	 *
	 * @param array $args {
	 *     An array of website meta data to add in the social meta tags.
	 *
	 *     @type string $site_name 			The website name or brand.
	 *     @type string $site_title 		The website title.
	 *     @type string $site_description 	The website description.
	 *     @type string $site_url 			The website url.
	 *     @type string $site_image 		The website image / icon / logo.
	 * }
	 * @return string The Twitter Cards meta tags.
	 */
	protected function post_facebook_graph( array $args ) {

		$meta = '';

		$props = Options::social_profiles( 'facebook' );

		$url = isset( $props['url'] ) ? trailingslashit( esc_url( $props['url'] ) ) : '';
		$username = $this->plugin->get_option( 'profiles', 'facebook' );

		$meta .= ($url && $username) ? sprintf( "<meta property='article:publisher' content='%s' />\n", esc_attr( "{$url}{$username}" ) ) : '';

		$author = (array) $args['post_author'];

		if ( ! empty( $author ) ) {
			if ( isset( $author['profiles']['facebook'] ) && ! empty( $author['profiles']['facebook'] ) ) {
				$meta .= sprintf( "<meta property='article:author' content='%s' />\n", esc_attr( "{$url}{$author['profiles']['facebook']}" ) );
			} else {
				$meta .= sprintf( "<meta name='author' content='%s' />\n", esc_attr( "{$author['display_name']}" ) );
			}
		}

		return $meta;
	}

	/**
	 * Create Twitter Cards meta tags for the "post".
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @link https://dev.twitter.com/cards/overview
	 *
	 * @param array $args {
	 *     An array of website meta data to add in the social meta tags.
	 *
	 *     @type string $site_name 			The website name or brand.
	 *     @type string $site_title 		The website title.
	 *     @type string $site_description 	The website description.
	 *     @type string $site_url 			The website url.
	 *     @type string $site_image 		The website image / icon / logo.
	 * }
	 * @return string The Twitter Cards meta tags.
	 */
	protected function post_twitter_cards( $args ) {

		$meta = '';
		$args = wp_parse_args( $args, array(
			'post_title' => false,
			'post_description' => false,
			'post_url' => false,
			'post_image' => array(),
			'post_author' => array(),
		) );

		$meta .= $args['post_title'] ? sprintf( "<meta name='twitter:title' content='%s' />\n", esc_attr( $args['post_title'] ) ) : '';
		$meta .= $args['post_description'] ? sprintf( "<meta name='twitter:description' content='%s' />\n", esc_attr( $args['post_description'] ) ) : '';
		$meta .= $args['post_url'] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args['post_url'] ) ) : '';

		if ( ! empty( $args['post_image'] ) ) {

			$source = $args['post_image']['src'];
			$width  = $args['post_image']['width'];
			$height = $args['post_image']['height'];

			if ( $source && 0 !== $width && 0 !== $height ) {

				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {

				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$twitter = $this->plugin->get_option( 'profiles', 'twitter' );

			$site = $twitter ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $twitter ) ) : '';
			$type = "<meta name='twitter:card' content='summary_large_image' />\n";
			$meta = $site . $type . $meta;
		}

		$author = (array) $args['post_author'];

		if ( isset( $author['profiles']['twitter'] ) && ! empty( $author['profiles']['twitter'] ) ) {
			$meta .= sprintf( "<meta name='twitter:creator' content='@%s' />\n", esc_attr( "{$author['profiles']['twitter']}" ) );
		}

		return $meta;
	}
}
