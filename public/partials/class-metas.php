<?php

namespace XCo\WPSocialManager;

/**
 *
 */
final class Metas extends OutputUtilities {

	/**
	 * [$meta description]
	 * @var [type]
	 */
	protected $plugin_opts;

	/**
	 * [$locale description]
	 * @var [type]
	 */
	protected $locale;

	/**
	 * [$options description]
	 * @var [type]
	 */
	protected $options;

	/**
	 * [__construct description]
	 * @param [type] $key [description]
	 */
	public function __construct( array $args ) {

		/**
		 * [$this->plugin_opts description]
		 * @var [type]
		 */
		$this->plugin_opts = $args[ 'plugin_opts' ];

		$this->hooks();
	}

	/**
	 * [action description]
	 * @return [type] [description]
	 */
	protected function hooks() {

		add_action( 'init', array( $this, 'setups' ) );

		add_action( 'wp_head', array( $this, 'site_meta_tags' ), 2 );
		add_action( 'wp_head', array( $this, 'post_meta_tags' ), 2 );
	}

	/**
	 * [setups description]
	 * @return [type] [description]
	 */
	public function setups() {

		/**
		 * [$this->options description]
		 * @var [type]
		 */
		$this->options = (object) array(
			'profiles'  => get_option( "{$this->plugin_opts}_profiles" ),
			'metasSite' => get_option( "{$this->plugin_opts}_metas_site" )
		);

		/**
		 * [$this->locale description]
		 * @var [type]
		 */
		$this->locale  = get_locale();
	}

	/**
	 * [is_meta_enabled description]
	 * @return boolean [description]
	 */
	public function is_meta_enabled() {
		return (bool) $this->get_site_meta( 'enabled' );
	}

	/**
	 * [get_site_meta description]
	 * @return [type] [description]
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
	 * [get_meta description]
	 * @param  [type] $id    [description]
	 * @param  [type] $which [description]
	 * @return [type]        [description]
	 */
	public function get_post_meta( $id, $which ) {

		if ( ! $id || ! $which ) {
			return;
		}

		$meta = get_post_meta( $id, $this->plugin_opts, true );

		return isset( $meta[ $which ] ) ? $meta[ $which ] : false;
	}

	/**
	 * [site_meta_tags description]
	 * @return [type] [description]
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
			'site_image' => $this->site_image()
		);

		$og = $this->site_open_graph( $tag_args );
		$tc = $this->site_twitter_card( $tag_args );

		echo "\n<!-- START: WP-Social-Manager [ https://wordpress.org/plugins/wp-social-manager ] -->\n";
			echo "{$og}{$tc}";
		echo "<!-- END: WP-Social-Manager -->\n\n";
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

		$tag_args = array(
			'site_name' => $this->site_name(),
			'post_title' => $this->post_title( $post_id ),
			'post_description' => $this->post_description( $post_id ),
			'post_url' => $this->post_url( $post_id ),
			'post_image' => $this->post_image( $post_id )
		);

		$fb = $this->post_facebook_graph( $post_id );
		$og = $this->post_open_graph( $tag_args );
		$tc = $this->post_twitter_card( $tag_args );

		echo "\n<!-- START: WP-Social-Manager [ https://wordpress.org/plugins/wp-social-manager ] -->\n";
			echo "{$og}{$fb}{$tc}";
		echo "<!-- END: WP-Social-Manager -->\n\n";
	}

	/**
	 * [site_open_graph description]
	 * @return [type] [description]
	 */
	public function site_open_graph( $args ) {

		$meta = '';

		/**
		 * [$args description]
		 * @var [type]
		 */
		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'site_title' => false,
			'site_description' => false,
			'site_url' => false,
			'site_image' => array()
		) );

		$meta .= $args[ 'site_name' ] ? sprintf( "<meta property='og:site_name' content='%s' />\n", $args[ 'site_name' ] ) : '';
		$meta .= $args[ 'site_title' ] ? sprintf( "<meta property='og:title' content='%s' />\n", $args[ 'site_title' ] ) : '';
		$meta .= $args[ 'site_description' ] ? sprintf( "<meta property='og:description' content='%s' />\n", $args[ 'site_description' ] ) : '';
		$meta .= $args[ 'site_url' ] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args[ 'site_url' ] ) ) : '';

		if ( ! empty( $args[ 'site_image' ] ) ) {

			$source = $args[ 'site_image' ][ 'src' ];
			$width  = $args[ 'site_image' ][ 'width' ];
			$height = $args[ 'site_image' ][ 'height' ];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} else if ( $source ) {
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
	 * [site_twitter_card description]
	 * @return [type] [description]
	 */
	public function site_twitter_card( $args ) {

		$meta = '';

		/**
		 * [$args description]
		 * @var [type]
		 */
		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'site_title' => false,
			'site_description' => false,
			'site_url' => false,
			'site_image' => array()
		) );

		$meta .= $args[ 'site_title' ] ? sprintf( "<meta name='twitter:title' content='%s' />\n", $args[ 'site_title' ] ) : '';
		$meta .= $args[ 'site_description' ] ? sprintf( "<meta name='twitter:description' content='%s' />\n", $args[ 'site_description' ] ) : '';
		$meta .= $args[ 'site_url' ] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args[ 'site_url' ] ) ) : '';

		if ( ! empty( $meta ) ) {

			$profile = $this->options->profiles[ 'twitter' ];
			$site = $profile ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $profile ) ) : '';
			$type = "<meta name='twitter:card' content='summary' />\n";
			$meta = $site . $type . $meta;
		}

		if ( ! empty( $args[ 'site_image' ] ) ) {

			$source = $args[ 'site_image' ][ 'src' ];
			$width  = $args[ 'site_image' ][ 'width' ];
			$height = $args[ 'site_image' ][ 'height' ];

			if ( $source && $width && $height ) {

				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", esc_attr( $height ) );
			} else if ( $source ) {

				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		return $meta;
	}

	/**
	 * [open_graph description]
	 * @param  [type] $title [description]
	 * @return [type]        [description]
	 */
	protected function post_open_graph( $args ) {

		$meta = '';

		/**
		 * [$args description]
		 * @var [type]
		 */
		$args = wp_parse_args( $args, array(
			'site_name' => false,
			'post_title' => false,
			'post_description' => false,
			'post_url' => false,
			'post_image' => array()
		) );

		$meta .= $args[ 'post_title' ] ? sprintf( "<meta property='og:title' content='%s' />\n", $args[ 'post_title' ] ) : '';
		$meta .= $args[ 'post_description' ] ? sprintf( "<meta property='og:description' content='%s' />\n", $args[ 'post_description' ] ) : '';
		$meta .= $args[ 'post_url' ] ? sprintf( "<meta property='og:url' content='%s' />\n", esc_url( $args[ 'post_url' ] ) ) : '';

		if ( ! empty( $args[ 'post_image' ] ) ) {

			$source = $args[ 'post_image' ][ 'src' ];
			$width  = $args[ 'post_image' ][ 'width' ];
			$height = $args[ 'post_image' ][ 'height' ];

			if ( $source && $width && $height ) {
				$meta .= sprintf( "<meta property='og:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta property='og:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta property='og:image:height' content='%s' />\n", esc_attr( $height ) );
			} else if ( $source ) {
				$meta .= sprintf( "<meta name='og:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$type = "<meta property='og:type' content='article' />\n";
			$locale = sprintf( "<meta property='og:locale' content='%s' />\n", esc_attr( $this->locale ) );

			$meta = $type . $locale . $meta;
		}

		$site = $args[ 'site_name' ] ? sprintf( "<meta property='og:site_name' content='%s' />\n", $args[ 'site_name' ] ) : '';

		return $site . $meta;
	}

	/**
	 * [post_facebook_graph description]
	 * @return [type] [description]
	 */
	protected function post_facebook_graph( $id ) {

		$meta = '';

		if ( ! $id ) {
			return $meta;
		}

		$post = get_post( $id );

		$property = self::get_social_properties( 'facebook' );
		$profile = $this->options->profiles[ 'facebook' ];
		$publisher = isset( $property[ 'url' ] ) && $profile ? "{$property['url']}{$profile}" : '';
		$category = get_the_category( $id );

		$meta .= $publisher ? sprintf( "<meta property='article:publisher' content='%s' />\n", $publisher ) : '';
		$meta .= $category[0]->name ? sprintf( "<meta property='article:section' content='%s' />\n", $category[0]->name ) : '';

		return $meta;
	}

	/**
	 * [twitter_card description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	protected function post_twitter_card( $args ) {

		$meta = '';
		$args = wp_parse_args( $args, array(
			'post_title' => false,
			'post_description' => false,
			'post_url' => false,
			'post_image' => array()
		) );

		$meta .= $args[ 'post_title' ] ? sprintf( "<meta name='twitter:title' content='%s' />\n", $args[ 'post_title' ] ) : '';
		$meta .= $args[ 'post_description' ] ? sprintf( "<meta name='twitter:description' content='%s' />\n", $args[ 'post_description' ] ) : '';
		$meta .= $args[ 'post_url' ] ? sprintf( "<meta name='twitter:url' content='%s' />\n", esc_url( $args[ 'post_url' ] ) ) : '';

		if ( ! empty( $args[ 'post_image' ] ) ) {

			$source = $args[ 'post_image' ][ 'src' ];
			$width  = $args[ 'post_image' ][ 'width' ];
			$height = $args[ 'post_image' ][ 'height' ];

			if ( $source && $width && $height ) {

				$meta .= sprintf( "<meta name='twitter:image:src' content='%s' />\n", esc_attr( $source ) );
				$meta .= sprintf( "<meta name='twitter:image:width' content='%s' />\n", esc_attr( $width ) );
				$meta .= sprintf( "<meta name='twitter:image:height' content='%s' />\n", esc_attr( $height ) );
			} else if ( $source ) {

				$meta .= sprintf( "<meta name='twitter:image' content='%s' />\n", esc_attr( $source ) );
			}
		}

		if ( ! empty( $meta ) ) {

			$profile = $this->options->profiles[ 'twitter' ];
			$site = $profile ? sprintf( "<meta name='twitter:site' content='@%s' />\n", esc_attr( $profile ) ) : '';
			$type = "<meta name='twitter:card' content='summary' />\n";
			$meta = $site . $type . $meta;
		}

		return $meta;
	}

	/**
	 * [site_title description]
	 * @return [type] [description]
	 */
	public function site_name() {

		$name = $this->get_site_meta( 'name' );
		$name = $name ? $name : get_bloginfo( 'name' );

		return wp_kses( $name, array() );
	}

	/**
	 * [site_title description]
	 * @return [type] [description]
	 */
	public function site_title() {
		return wp_kses( wp_get_document_title(), array() );
	}

	/**
	 * [site_description description]
	 * @return [type] [description]
	 */
	public function site_description() {

		$description = $this->get_site_meta( 'description' );
		$description = $description ? $description : get_bloginfo( 'description' );

		return wp_kses( $description, array() );
	}

	/**
	 * [site_title description]
	 * @return [type] [description]
	 */
	public function site_url() {

		$url = get_site_url();
		return esc_url( $url );
	}

	/**
	 * [site_image description]
	 * @return [type] [description]
	 */
	public function site_image() {

		$attachment_id = $this->get_site_meta( 'image' );
		$attachment_id = $attachment_id ? $attachment_id : get_theme_mod( 'custom_logo' );

		if ( ! $attachment_id ) {
			return false;
		}

		list( $src, $width, $height ) = wp_get_attachment_image_src( $attachment_id, 'full', true );

		return array(
			'src' => esc_url( $src ),
			'width' => (int) $width,
			'height' => (int) $height
		);
	}

	/**
	 * [get_post description]
	 * @return [type] [description]
	 */
	public function post_title( $id ) {

		if ( $this->is_meta_enabled() ) {
			$title = $this->get_post_meta( $id, 'post_title' );
		}

		if ( ! $title || empty( $title ) ) {
			$post = get_post( $id );
			$title = $post->post_title;
		}

		return wp_kses( $title, array() );
	}

	/**
	 * [get_description description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function post_description( $id ) {

		if ( $this->is_meta_enabled() ) {
			$description = $this->get_post_meta( $id, 'post_excerpt' );
		}

		if ( ! $description || empty( $description ) ) {

			$post = get_post( $id );
			$description = $this->post_excerpt;

			if ( empty( $post->post_excerpt ) ) {
				$description = wp_trim_words( $post->post_content, 30, '...' );
			}
		}

		return wp_kses( $description, array() );
	}

	/**
	 * [get_thumbnail description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function post_image( $id ) {

		// 1. Post Meta Image
		// 2. Post Featured Image
		// 3. Post Content First Image
		// 4. Site Meta Image
		// 5. Site Custom Logo (Customizer)

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
				'height' => absint( $height )
			);
		}

		$post = get_post( $id );
		$dom = new \DOMDocument();

    	$dom->loadHTML( $post->post_content );
    	$images = $dom->getElementsByTagName( 'img' );

    	if ( 0 !== $images->length ) {

			$image = getimagesize( $images->item(0)->getAttribute( 'src' ) );

			if ( $image ) {

				list( $width, $height ) = $image;

				return array(
					'src' => esc_url( $matches[1][0] ),
					'width' => absint( $width ),
					'height' => absint( $height )
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
				'height' => absint( $height )
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
}