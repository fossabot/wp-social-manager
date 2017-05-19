<?php
/**
 * Public: Meta class
 *
 * @package SocialManager
 * @subpackage Public\Meta
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

use \DOMDocument;

/**
 * The class to get the site or post meta
 *
 * @since 1.0.0
 */
final class Meta {

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
	public function get_site_meta( $which = '' ) {

		if ( ! $which ) {
			return '';
		}

		return Options::get( 'meta_site', $which );
	}

	/**
	 * The method to get the website name / brand.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website name / brand
	 */
	public function get_site_name() {

		$site_name = self::get_site_meta( 'name' );

		if ( ! $site_name ) {
			$site_name = get_bloginfo( 'name' );
		}

		/**
		 * Filter the site name meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var string
		 */
		$site_name = apply_filters( 'ninecodes_social_manager_meta', $site_name, 'site_name', array() );

		return wp_kses( $site_name, array() );
	}

	/**
	 * The method to get the website title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website title
	 */
	public function get_site_title() {

		$site_title = self::get_site_meta( 'title' );

		if ( ! $site_title ) {
			$site_title = wp_get_document_title();
		}

		/**
		 * Filter the site title meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var string
		 */
		$site_title = apply_filters( 'ninecodes_social_manager_meta', $site_title, 'site_title', array() );

		return wp_kses( $site_title, array() );
	}

	/**
	 * The method to get the website description.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website description
	 */
	public function get_site_description() {

		$site_description = self::get_site_meta( 'description' );

		if ( ! $site_description ) {
			$site_description = get_bloginfo( 'description' );
		}

		if ( is_archive() ) {
			$term_description = term_description();
			$site_description = $term_description ? $term_description : $site_description;
		}

		if ( is_author() ) {
			$author = get_queried_object();
			$site_description = get_the_author_meta( 'description', (int) $author->ID );
		}

		/**
		 * Filter the site description meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var string
		 */
		$site_description = apply_filters( 'ninecodes_social_manager_meta', $site_description, 'site_description', array() );

		return wp_kses( trim( strip_shortcodes( $site_description ) ), array() );
	}

	/**
	 * The method to get the website url.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The website url
	 */
	public function get_site_url() {
		return esc_url( get_site_url() );
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
	public function get_site_image() {

		/**
		 * Filter the site image meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var array
		 */
		$image_filter = apply_filters( 'ninecodes_social_manager_meta', array(), 'site_image', array() );

		/*
		 * If the image value from the 'ninecodes_social_manager_meta' filter is there,
		 * return the image immediately and don't proceed the codes that follow.
		 */
		if ( isset( $image_filter['src'] ) && ! empty( $image_filter['src'] ) ) {

			return wp_parse_args( $image_filter, array(
				'src' => '',
				'width' => 0,
				'height' => 0,
			) );
		}

		if ( is_author() ) {

			$author = get_queried_object();
			$avatar = get_avatar_url( $author->ID, array(
				'size' => 180,
			) );

			return array(
				'src' => $avatar,
				'width' => 180,
				'height' => 180,
			);
		}

		$attachment_id = self::get_site_meta( 'image' );
		$attachment_id = $attachment_id ? $attachment_id : get_theme_mod( 'custom_logo' );

		if ( $attachment_id ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $attachment_id, 'full', true );

			return array(
				'src' => esc_url( $src ),
				'width' => absint( $width ),
				'height' => absint( $height ),
			);
		}
	}

	/**
	 * Utility method to get the post meta data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $post_id The post ID number.
	 * @param  string  $which The the meta array key.
	 * @return string|array
	 */
	public function get_post_meta( $post_id, $which ) {

		if ( ! $post_id || ! $which ) {
			return;
		}

		$post_meta = get_post_meta( $post_id, Options::slug(), true );

		/**
		 * If the post_meta is empty it means the meta has not yet
		 * been created. Let's return 'null' early.
		 */
		if ( empty( $post_meta ) ) {
			return null;
		}

		return isset( $post_meta[ $which ] ) ? $post_meta[ $which ] : null;
	}

	/**
	 * The method to get the "post" title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id The post ID.
	 * @return string The "post" title
	 */
	public function get_post_title( $post_id ) {

		$post_id = absint( $post_id );
		$post_title = '';

		// If Meta Tags is enabled check the Post meta for the title.
		if ( Helpers::is_meta_tags_enabled() ) {
			$post_title = self::get_post_meta( $post_id, 'post_title' );
		}

		// If the title is still empty get the Post title.
		if ( empty( $post_title ) ) {
			$post = get_post( $post_id );
			$post_title = $post ? apply_filters( 'the_title', $post->post_title ) : '';
		}

		/**
		 * Filter the site title meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var string
		 */
		$post_title = apply_filters( 'ninecodes_social_manager_meta', $post_title, 'post_title', array(
			'post_id' => $post_id,
		) );

		return wp_kses( $post_title, array() );
	}

	/**
	 * The method to get the "post" description.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id The post ID.
	 * @return string The "post" description
	 */
	public function get_post_description( $post_id ) {

		$post_id = absint( $post_id );
		$post_description = '';

		// If Meta Tags is enabled check the Post meta for the description.
		if ( Helpers::is_meta_tags_enabled() ) {
			$post_description = self::get_post_meta( $post_id, 'post_excerpt' );
		}

		// If the title is still empty get the Post excerpt.
		if ( empty( $post_description ) ) {

			$post = get_post( $post_id );

			if ( ! $post ) {
				return '';
			}

			if ( empty( $post->post_excerpt ) ) {
				$post_description = wp_trim_words( $post->post_content, 30, '...' );
			} else {
				$post_description = $post->post_excerpt;
			}
		}

		/**
		 * Filter the site title meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var string
		 */
		$post_description = apply_filters( 'ninecodes_social_manager_meta', $post_description, 'post_description', array(
			'post_id' => $post_id,
		) );

		return wp_kses( strip_shortcodes( $post_description ), array() );
	}

	/**
	 * The method to get the "post" image.
	 *
	 * This method will try to retrieve image from a number of sources,
	 * and return the image data based on the following priority order:
	 * 1. Post Meta Image or 'ninecodes_social_manager_meta' Filter
	 * 2. Post Featured Image
	 * 3. Post Content First Image
	 * 4. Site Meta Image
	 * 5. Site Custom Logo (Customizer)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id The post ID.
	 * @return array The image data consisting of the image source, width, and height
	 */
	public function get_post_image( $post_id ) {

		$post_id = absint( $post_id );

		/**
		 * Filter the site image meta value.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context The context; which meta value to filter.
		 * @param array  $args An array of arguments.
		 *
		 * @var array
		 */
		$image_filter = apply_filters( 'ninecodes_social_manager_meta', array(), 'post_image', array(
			'post_id' => $post_id,
		) );

		/*
		 * If the image value from the 'ninecodes_social_manager_meta' filter is there,
		 * return the image immediately and don't proceed the codes that follow.
		 */
		if ( isset( $image_filter['src'] ) && ! empty( $image_filter['src'] ) ) {

			return wp_parse_args( $image_filter, array(
				'src' => '',
				'width' => 0,
				'height' => 0,
			) );
		}

		$attachment_id = null;

		if ( Helpers::is_meta_tags_enabled() ) {
			$attachment_id = self::get_post_meta( $post_id, 'post_thumbnail' ); // Post Meta Image.
		}

		if ( ! $attachment_id ) {
			$attachment_id = get_post_thumbnail_id( $post_id ); // Post Featured Image.
		}

		if ( $attachment_id ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $attachment_id, 'full', true );

			return array(
				'src' => esc_url( $src ),
				'width' => absint( $width ),
				'height' => absint( $height ),
			);
		}

		$post = get_post( $post_id );

		if ( $post && ! empty( $post->post_content ) ) {

			$content = $post->post_content;

			$dom = new DOMDocument();
			$errors = libxml_use_internal_errors( true );

			$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

			$images = $dom->getElementsByTagName( 'img' );

			if ( 0 !== $images->length ) {

				$src = $images->item( 0 )->getAttribute( 'src' );

				if ( $src ) {

					$width = $images->item( 0 )->getAttribute( 'width' );
					$height = $images->item( 0 )->getAttribute( 'height' );

					return array(
						'src' => esc_url( $src ),
						'width' => $width && substr( $width, -1 ) === '%' ? absint( $width ) : 0,
						'height' => $height && substr( $height, -1 ) === '%' ? absint( $height ) : 0,
					);
				}
			}

			libxml_clear_errors();
			libxml_use_internal_errors( $errors );
		}

		if ( Helpers::is_meta_tags_enabled() ) {

			$site_image = self::get_site_image(); // Site Meta Image.

			if ( is_array( $site_image ) && ! empty( $site_image ) ) {
				return $site_image;
			}
		}

		$site_logo = absint( get_theme_mod( 'custom_logo' ) ); // Site Custom Logo.

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
	 * @param integer $post_id The post ID.
	 * @return string The "post" url / permalink
	 */
	public function get_post_url( $post_id ) {

		$post_url = get_permalink( absint( $post_id ) );

		return esc_url( $post_url );
	}

	/**
	 * The method to get the "post" author
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  integer $post_id The post ID.
	 * @return array {
	 *     @type string $display_name The author name.
	 *     @type string $social_profiles List of social media profiles associated with the author.
	 * }
	 */
	public function get_post_author( $post_id ) {

		$post = get_post( absint( $post_id ) );
		$author_name = $post ? get_the_author_meta( 'display_name', $post->post_author ) : '';
		$social_profiles = $post ? get_the_author_meta( Options::slug(), $post->post_author ) : array();

		return array(
			'display_name' => $author_name,
			'social_profiles' => $social_profiles,
		);
	}
}
