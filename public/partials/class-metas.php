<?php
/**
 * Public: Meta class
 *
 * @author Thoriq Firdaus <tfirdau@outlook.com>
 *
 * @package WPSocialManager
 * @subpackage Public\Metas
 */

namespace XCo\WPSocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * Meta data class.
 *
 * This class to retrieve the content meta data and generate
 * social meta tags, such as Open Graph and Twitter Cards,
 * in the head tag.
 *
 * @since 1.0.0
 */
final class Metas extends OutputUtilities {

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $plugin_opts;

	/**
	 * The current website language.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $locale;

	/**
	 * The options required to render the meta data.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $options;

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
	 */
	public function __construct( array $args ) {

		$this->plugin_opts = $args['plugin_opts'];

		$this->hooks();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'setups' ) );

		add_action( 'wp_head', array( $this, 'site_meta_tags' ), -10 );
		add_action( 'wp_head', array( $this, 'post_meta_tags' ), -10 );
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
			'profiles'  => get_option( "{$this->plugin_opts}_profiles" ),
			'metasSite' => get_option( "{$this->plugin_opts}_metas_site" ),
		);

		$this->locale  = get_locale();
	}

	/**
	 * Utility method to check if the meta option is enabled.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean True if meta is enabled, false if not.
	 */
	public function is_meta_enabled() {
		return (bool) $this->get_site_meta( 'enabled' );
	}

	/**
	 * Utility method to get the site meta data.
	 *
	 * This data is used for the homepage and sometimes
	 * acts as the default value of if specific meta data
	 * is not available for particular page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $which Meta array key.
	 * @return mixed
	 */
	public function get_site_meta( $which ) {

		if ( ! $which ) {
			return;
		}

		if ( isset( $this->options->metasSite[ $which ] ) ) {
			return $this->options->metasSite[ $which ];
		}
	}

	/**
	 * Utility method to get the post meta data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $id    The post ID number.
	 * @param  string  $which The the meta array key.
	 * @return string|array
	 */
	public function get_post_meta( $id, $which ) {

		if ( ! $id || ! $which ) {
			return;
		}

		$meta = get_post_meta( $id, $this->plugin_opts, true );

		return isset( $meta[ $which ] ) ? $meta[ $which ] : false;
	}

	/**
	 * Print the site meta tag elements in the 'head' tag.
	 *
	 * The "site" meta tags is generted in the homepage and
	 * archive pages (e.g. Categories, Tags, and Custom Taxonomy Terms).
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function site_meta_tags() {

		if ( is_single() || ! $this->is_meta_enabled() ) {
			return;
		}

		$tag_args = array(
			'site_name' => $this->site_name(),
			'site_title' => $this->site_title(),
			'site_description' => $this->site_description(),
			'site_url' => $this->site_url(),
			'site_image' => $this->site_image(),
		);

		$og = $this->site_open_graph( $tag_args );
		$tc = $this->site_twitter_card( $tag_args );

		echo "<!-- START: WP-Social-Manager -->\n";
		echo "{$og}{$tc}";
		echo "<!-- END: WP-Social-Manager -->\n";
	}

	/**
	 * Print the post meta tag elements in the 'head' tag.
	 *
	 * The "post" meta tag is generated in any a single post or page
	 * of any Post Types.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function post_meta_tags() {

		if ( ! is_single() || ! $this->is_meta_enabled() ) {
			return;
		}

		$post_id = get_the_id();

		$tag_args = array(
			'site_name' => $this->site_name(),
			'post_title' => $this->post_title( $post_id ),
			'post_description' => $this->post_description( $post_id ),
			'post_url' => $this->post_url( $post_id ),
			'post_image' => $this->post_image( $post_id ),
			'post_author' => $this->post_author( $post_id ),
		);

		$og = $this->post_open_graph( $tag_args );
		$tc = $this->post_twitter_card( $tag_args );

		echo "<!-- START: WP-Social-Manager -->\n";
		echo "{$og}{$tc}";
		echo "<!-- END: WP-Social-Manager -->\n";
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

		$meta .= $args['site_name'] ? sprintf( "<meta property='og:site_name' content='%s' />\n", $args['site_name'] ) : '';
		$meta .= $args['site_title'] ? sprintf( "<meta property='og:title' content='%s' />\n", $args['site_title'] ) : '';
		$meta .= $args['site_description'] ? sprintf( "<meta property='og:description' content='%s' />\n", $args['site_description'] ) : '';
		$meta .= $args['site_url'] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args['site_url'] ) ) : '';

		if ( ! empty( $args['site_image'] ) ) {

			$source = $args['site_image']['src'];
			$width  = $args['site_image']['width'];
			$height = $args['site_image']['height'];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {
				$meta .= sprintf( "<meta name='og:image' content='%s' />\n", esc_attr( $source ) );
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
	public function site_twitter_card( $args ) {

		$meta = '';

		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'site_title' => false,
			'site_description' => false,
			'site_url' => false,
			'site_image' => array(),
		) );

		$meta .= $args['site_title'] ? sprintf( "<meta name='twitter:title' content='%s' />\n", $args['site_title'] ) : '';
		$meta .= $args['site_description'] ? sprintf( "<meta name='twitter:description' content='%s' />\n", $args['site_description'] ) : '';
		$meta .= $args['site_url'] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args['site_url'] ) ) : '';

		if ( ! empty( $meta ) ) {

			$profile = $this->options->profiles['twitter'];
			$site = $profile ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $profile ) ) : '';
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

		$meta .= $args['post_title'] ? sprintf( "<meta property='og:title' content='%s' />\n", $args['post_title'] ) : '';
		$meta .= $args['post_description'] ? sprintf( "<meta property='og:description' content='%s' />\n", $args['post_description'] ) : '';
		$meta .= $args['post_url'] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args['post_url'] ) ) : '';

		if ( ! empty( $args['post_image'] ) ) {

			$source = $args['post_image']['src'];
			$width  = $args['post_image']['width'];
			$height = $args['post_image']['height'];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {
				$meta .= sprintf( "<meta name='og:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta property='og:type' content='article' />\n";
			$locale = sprintf( "<meta property='og:locale' content='%s' />\n", esc_attr( $this->locale ) );

			$meta = $type . $locale . $meta;
		}

		$site = $args['site_name'] ? sprintf( "<meta property='og:site_name' content='%s' />\n", $args['site_name'] ) : '';

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

		$props = self::get_social_properties( 'facebook' );
		$profile = $this->options->profiles['facebook'];

		$url = isset( $props['url'] ) ? trailingslashit( $props['url'] ) : '';

		$publisher = ! empty( $url ) && $profile ? "{$url}{$profile}" : '';
		$meta .= $publisher ? sprintf( "<meta property='article:publisher' content='%s' />\n", $publisher ) : '';

		$author = (array) $args['post_author'];

		if ( ! empty( $author ) ) {
			if ( isset( $author['profiles']['facebook'] ) && ! empty( $author['profiles']['facebook'] ) ) {
				$meta .= sprintf( "<meta property='article:author' content='%s' />\n", "{$url}{$author['profiles']['facebook']}" );
			} else {
				$meta .= sprintf( "<meta name='author' content='%s' />\n", "{$author['display_name']}" );
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
	protected function post_twitter_card( $args ) {

		$meta = '';
		$args = wp_parse_args( $args, array(
			'post_title' => false,
			'post_description' => false,
			'post_url' => false,
			'post_image' => array(),
			'post_author' => array(),
		) );

		$meta .= $args['post_title'] ? sprintf( "<meta name='twitter:title' content='%s' />\n", $args['post_title'] ) : '';
		$meta .= $args['post_description'] ? sprintf( "<meta name='twitter:description' content='%s' />\n", $args['post_description'] ) : '';
		$meta .= $args['post_url'] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args['post_url'] ) ) : '';

		if ( ! empty( $args['post_image'] ) ) {

			$source = $args['post_image']['src'];
			$width  = $args['post_image']['width'];
			$height = $args['post_image']['height'];

			if ( $source && $width && $height ) {

				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", esc_attr( $height ) );
			} elseif ( $source ) {

				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$profile = $this->options->profiles['twitter'];
			$site = $profile ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $profile ) ) : '';
			$type = "<meta name='twitter:card' content='summary_large_image' />\n";
			$meta = $site . $type . $meta;
		}

		$author = (array) $args['post_author'];

		if ( isset( $author['profiles']['twitter'] ) ) {
			if ( ! empty( $author['profiles']['twitter'] ) ) {
				$meta .= sprintf( "<meta name='twitter:creator' content='@%s' />\n", "{$author['profiles']['twitter']}" );
			}
		}

		return $meta;
	}

	/**
	 * The method to get the website name / brand.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website name / brand
	 */
	public function site_name() {

		$name = $this->get_site_meta( 'name' );
		$name = $name ? $name : get_bloginfo( 'name' );

		return wp_kses( $name, array() );
	}

	/**
	 * The method to get the website title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website title
	 */
	public function site_title() {
		return wp_kses( wp_get_document_title(), array() );
	}

	/**
	 * The method to get the website description.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website description
	 */
	public function site_description() {

		$description = $this->get_site_meta( 'description' );
		$description = $description ? $description : get_bloginfo( 'description' );

		if ( is_archive() ) {
			$term_description = term_description();
			$description = $term_description ? $term_description : $description;
		}

		if ( is_author() ) {
			$author = get_queried_object();
			$description = get_the_author_meta( 'description', (int) $author->ID );
		}

		return wp_kses( trim( $description ), array() );
	}

	/**
	 * The method to get the website url.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website url
	 */
	public function site_url() {

		$url = get_site_url();
		return esc_url( $url );
	}

	/**
	 * The method to get the website image.
	 *
	 * This image should represent the website.
	 * It generally could be a favicon, or a logo.
	 *
	 * In the author archive, the image retrieved will be the author
	 * Gravatar image profile.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array An array of image data (src, width, and height)
	 */
	public function site_image() {

		$attachment_id = $this->get_site_meta( 'image' );
		$attachment_id = $attachment_id ? $attachment_id : get_theme_mod( 'custom_logo' );

		if ( is_author() ) {

			$author = get_queried_object();
			$avatar = get_avatar_url( $author->ID, array( 'size' => 180 ) );
			return array(
					'src' => $avatar,
					'width' => 180,
					'height' => 180,
				);
		}

		if ( $attachment_id ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $attachment_id, 'full', true );

			return array(
				'src' => esc_url( $src ),
				'width' => (int) $width,
				'height' => (int) $height,
			);
		}
	}

	/**
	 * The method to get the "post" title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $id The post ID.
	 * @return string The "post" title
	 */
	public function post_title( $id ) {

		if ( $this->is_meta_enabled() ) {
			$title = $this->get_post_meta( $id, 'post_title' );
		}

		if ( ! $title || empty( $title ) ) {
			$post = get_post( $id );
			$title = apply_filters( 'the_title', $post->post_title );
		}

		return wp_kses( $title, array() );
	}

	/**
	 * The method to get the "post" description.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $id The post ID.
	 * @return string The "post" description
	 */
	public function post_description( $id ) {

		if ( $this->is_meta_enabled() ) {
			$description = $this->get_post_meta( $id, 'post_excerpt' );
		}

		if ( ! $description || empty( $description ) ) {

			$post = get_post( $id );
			$description = $post->post_excerpt;

			if ( empty( $post->post_excerpt ) ) {
				$description = wp_trim_words( $post->post_content, 30, '...' );
			}
		}

		return wp_kses( $description, array() );
	}

	/**
	 * The method to get the "post" image.
	 *
	 * This method will try to retrieve image from a number of sources,
	 * and return the image data based on the following priority order:
	 * 1. Post Meta Image
	 * 2. Post Featured Image
	 * 3. Post Content First Image
	 * 4. Site Meta Image
	 * 5. Site Custom Logo (Customizer)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $id The post ID.
	 * @return array      The image data consisting of the image source, width, and height
	 */
	public function post_image( $id ) {

		$attachment_id = null;

		if ( $this->is_meta_enabled() ) {
			$attachment_id = $this->get_post_meta( $id, 'post_thumbnail' ); // Post Meta Image.
		}

		if ( ! $attachment_id ) {
			$attachment_id = get_post_thumbnail_id( $id ); // Post Featured Image.
		}

		if ( $attachment_id ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $attachment_id, 'full', true );

			return array(
				'src' => esc_url( $src ),
				'width' => absint( $width ),
				'height' => absint( $height ),
			);
		}

		$post = get_post( $id );

		libxml_use_internal_errors( true );

		$dom = new \DOMDocument();
		$dom->loadHTML( mb_convert_encoding( $post->post_content, 'HTML-ENTITIES', 'UTF-8' ) );
		$images = $dom->getElementsByTagName( 'img' );

		if ( 0 !== $images->length ) {

			$src = $images->item( 0 )->getAttribute( 'src' );
			$image = getimagesize( $images->item( 0 )->getAttribute( 'src' ) );

			if ( $image ) {

				list( $width, $height ) = $image;

				return array(
					'src' => esc_url( $src ),
					'width' => absint( $width ),
					'height' => absint( $height ),
				);
			}
		}

		if ( $this->is_meta_enabled() ) {

			$site_image = $this->site_image(); // Site Meta Image.

			if ( is_array( $site_image ) && ! empty( $site_image ) ) {
				return $site_image;
			}
		}

		$site_logo = (int) get_theme_mod( 'custom_logo' ); // Site Custom Logo.

		if ( $site_logo ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $site_logo, 'full', true );

			return array(
				'src' => esc_url( $src ),
				'width' => absint( $width ),
				'height' => absint( $height ),
			);
		}
	}

	/**
	 * The method to get the "post" url / permalink.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id The post ID.
	 * @return string The "post" url / permalink
	 */
	public function post_url( $id ) {
		return esc_url( get_permalink( $id ) );
	}

	/**
	 * The method to get the "post" author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $id The post ID.
	 * @return array {
	 *     @type string $display_name 	The author name.
	 *     @type string $profiles 		An array of social media profiles associated
	 *           						with the author.
	 * }
	 */
	public function post_author( $id ) {

		$post = get_post( $id );
		$name = get_the_author_meta( 'display_name', $post->post_author );
		$profiles = get_the_author_meta( $this->plugin_opts, $post->post_author );

		return array(
			'display_name' => $name,
			'profiles' => $profiles,
		);
	}
}
