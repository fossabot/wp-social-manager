<?php

namespace XCo\WPSocialManager;

final class Metas {

	/**
	 * [$meta description]
	 * @var [type]
	 */
	protected $meta_key;

	/**
	 * [__construct description]
	 */
	public function __construct( $meta_key ) {

		$this->meta_key = $meta_key;
		$this->locale  = get_locale();

		$this->actions();
	}

	/**
	 * [action description]
	 * @return [type] [description]
	 */
	final protected function actions() {

		add_action( 'wp_head', array( $this, 'site_meta_tags' ), 5 );
		add_action( 'wp_head', array( $this, 'post_meta_tags' ), 5 );
	}

	/**
	 * [is_meta_enabled description]
	 * @return boolean [description]
	 */
	final public function is_meta_enabled() {

		return (bool) $this->get_site_meta( 'metaEnable' );
	}

	/**
	 * [site_meta_tags description]
	 * @return [type] [description]
	 */
	public function site_meta_tags() {

		if ( is_single() || ! $this->is_meta_enabled() ) {
			return;
		}

		$site_title = $this->site_title();
		$site_description = $this->site_description();
	}

	/**
	 * [meta_tags description]
	 * @return [type] [description]
	 */
	public function post_meta_tags() {

		if ( ! is_single() || ! $this->is_meta_enabled() ) {
			return;
		}

		$post_id = get_the_id();

		$post_title = $this->post_title( $post_id );
		$post_description = $this->post_description( $post_id );
		$post_url = $this->post_url( $post_id );
		$post_media = $this->post_media( $post_id );

		$tag_args = array(
			'title' => $post_title,
			'description' => $post_description,
			'url' => $post_url,
			'media' => $post_media
		);

		$og = $this->post_open_graph( $tag_args );
		$tc = $this->post_twitter_card( $tag_args );

		echo "\n<!-- START: WP-Social-Manager [ https://wordpress.org/plugins/wp-social-manager ] -->\n";
			echo "{$og}{$tc}";
		echo "<!-- END: WP-Social-Manager -->\n\n";
	}

	/**
	 * [site_open_graph description]
	 * @return [type] [description]
	 */
	public function site_open_graph() {

	}

	/**
	 * [site_twitter_card description]
	 * @return [type] [description]
	 */
	public function site_twitter_card() {

	}

	/**
	 * [open_graph description]
	 * @param  [type] $title [description]
	 * @return [type]        [description]
	 */
	public function post_open_graph( $args ) {

		$meta = '';
		$args = wp_parse_args( $args, array(
			'title' => false,
			'description' => false,
			'url' => false,
			'media' => array()
		) );

		$meta .= $args[ 'title' ] ? sprintf( "<meta property='og:title' content='%s' />\n", $args[ 'title' ] ) : '';
		$meta .= $args[ 'description' ] ? sprintf( "<meta property='og:description' content='%s' />\n", $args[ 'description' ] ) : '';
		$meta .= $args[ 'url' ] ? sprintf( "<meta property='og:url' content='%s' />\n", $args[ 'url' ] ) : '';

		if ( ! empty( $args[ 'media' ] ) ) {

			$source = $args[ 'media' ][ 'src' ];
			$width  = $args[ 'media' ][ 'width' ];
			$height = $args[ 'media' ][ 'height' ];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:src' content='%s' />\n", $source );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", $width );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", $height );
			} else if ( $source ) {
				$meta .= sprintf( "<meta name='og:image' content='%s' />\n", $source );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta property='og:type' content='article' />\n";
			$locale = sprintf( "<meta property='og:locale' content='%s' />\n", $this->locale );

			$meta = $type . $locale . $meta;
		}

		return $meta;
	}

	/**
	 * [twitter_card description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function post_twitter_card( $args ) {

		$meta = '';
		$args = wp_parse_args( $args, array(
			'title' => false,
			'description' => false,
			'url' => false,
			'media' => array()
		) );

		$meta .= $args[ 'title' ] ? sprintf( "<meta name='twitter:title' content='%s' />\n", $args[ 'title' ] ) : '';
		$meta .= $args[ 'description' ] ? sprintf( "<meta name='twitter:description' content='%s' />\n", $args[ 'description' ] ) : '';
		$meta .= $args[ 'url' ] ? sprintf( "<meta name='twitter:url' content='%s' />\n", $args[ 'url' ] ) : '';

		if ( ! empty( $args[ 'media' ] ) ) {

			$source = $args[ 'media' ][ 'src' ];
			$width  = $args[ 'media' ][ 'width' ];
			$height = $args[ 'media' ][ 'height' ];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", $source );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", $width );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", $height );
			} else if ( $source ) {
				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", $source );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta name='twitter:card' content='summary' />\n";
			$meta = $type . $meta;
		}

		return $meta;
	}

	/**
	 * [site_title description]
	 * @return [type] [description]
	 */
	public function site_title() {

		$title = $this->get_site_meta( 'name' );
		$title = $title ? $title : get_bloginfo( 'name' );

		return wp_kses( $title, array() );
	}

	public function site_description() {

		$description = $this->get_site_meta( 'description' );
		$description = $description ? $description : get_bloginfo( 'description' );

		return wp_kses( $description, array() );
	}

	public function site_media() {

	}

	/**
	 * [get_post description]
	 * @return [type] [description]
	 */
	public function post_title( $id ) {

		$title = $this->get_post_meta( $id, 'post_title' );
		return wp_kses( $title, array() );
	}

	/**
	 * [get_description description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function post_description( $id ) {

		$description = $this->get_post_meta( $id, 'post_excerpt' );
		return wp_kses( $description, array() );
	}

	/**
	 * [get_thumbnail description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function post_media( $id ) {

		// 1. Post Meta Image
		// 2. Post Featured Image
		// 3. Site Meta Image
		// 4. Site Custom Logo (Customizer)
		// 5. Post Content First Image

		$media_id = $this->get_post_meta( $id, 'post_thumbnail' ); // Post Meta.

		if ( ! $media_id ) {
			$media_id = get_post_thumbnail_id( $id );
		} else {
			$media_id = get_theme_mod( 'custom_logo', false );
		}

		if ( $media_id ) {

			list( $src, $width, $height ) = wp_get_attachment_image_src( $media_id, 'full', true );

			return array(
				'src' => $src,
				'width' => $width,
				'height' => $height
			);
		} else {

			$post = get_post( $id );
			$post_content = $post->post_content;
			$post_image = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );

			if ( $post_image === 0 ) {
				return array();
			}

			list( $width, $height ) = getimagesize( $matches[1][0] );

			return array(
				'src' => $matches[1][0],
				'width' => $width,
				'height' => $height
			);
		}
	}

	/**
	 * [get_url description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function post_url( $id ) {
		return esc_url( get_permalink( $id ) );
	}

	/**
	 * [get_site_meta description]
	 * @return [type] [description]
	 */
	public function get_site_meta( $which ) {

		if ( ! $which ) {
			return;
		}

		$meta = get_option( 'wp_social_manager_metas_site' );
		return isset( $meta[ $which ] ) ? $meta[ $which ] : '';
	}

	/**
	 * [get_meta description]
	 * @param  [type] $id    [description]
	 * @param  [type] $which [description]
	 * @return [type]        [description]
	 */
	public function get_post_meta( $id, $which ) {

		if ( ! $id || ! $which ) {
			return;
		}

		$post = get_post( $id );
		$meta = get_post_meta( $id, $this->meta_key, true );

		if ( ! isset( $meta[ $which ] ) || empty( $meta[ $which ] ) ) {
			$meta[ $which ] = $post->$which;
		}

		if ( ! $meta[ $which ] && 'post_excerpt' === $which ) {
			$meta[ $which ] = wp_trim_words( $post->post_content, 30, '...' );
		}

		return $meta[ $which ];
	}
}