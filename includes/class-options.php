<?php

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

class Options {

	public static function social_profiles( $slug = '' ) {

		$slug = sanitize_key( $slug );
		$profiles = array(
			'facebook' => array(
				'label' => 'Facebook',
				'url' => 'https://www.facebook.com/',
				'description' => sprintf( esc_html__( 'Facebook profile or page (e.g. %s)', 'wp-social-manager' ), '<code>zuck</code>' ),
			),
			'twitter' => array(
				'label' => 'Twitter',
				'url' => 'https://twitter.com/',
				'description' => sprintf( esc_html__( 'Twitter profile without the %1$s (e.g. %2$s)', 'wp-social-manager' ), '<code>@</code>', '<code>jack</code>' ),
			),
			'instagram' => array(
				'label' => esc_html( 'Instagram' ),
				'url' => esc_url( 'https://instagram.com/' ),
				'description'  => sprintf( esc_html__( 'Instagram profile (e.g. %s)', 'wp-social-manager' ), '<code>victoriabeckham</code>' ),
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'url' => esc_url( 'https://pinterest.com/' ),
				'description'  => sprintf( esc_html__( 'Pinterest profile (e.g. %s)', 'wp-social-manager' ), '<code>ohjoy</code>' ),
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'url' => esc_url( 'https://www.linkedin.com/in/' ),
				'description' => sprintf( esc_html__( 'LinkedIn profile (e.g. %s)', 'wp-social-manager' ), '<code>williamhgates</code>' ),
			),
			'googleplus' => array(
				'label' => 'Google+',
				'url' => 'https://plus.google.com/',
				'description' => sprintf( esc_html__( 'Google+ profile or page. Include the %1$s sign if necessary (e.g. %2$s)', 'wp-social-manager' ), '<code>+</code>', '<code>+hishekids</code>' ),
			),
			'youtube' => array(
				'label' => 'Youtube',
				'url' => 'https://www.youtube.com/user/',
				'description' => sprintf( esc_html__( 'Youtube channel (e.g. %s)', 'wp-social-manager' ), '<code>BuzzFeedVideo</code>' ),
			),
			'reddit' => array(
				'label' => 'Reddit',
				'url' => 'https://www.reddit.com/user/',
				'description' => sprintf( esc_html__( 'Reddit profile (e.g. %s)', 'wp-social-manager' ), '<code>Unidan</code>' ),
			),
			'dribbble' => array(
				'label' => 'Dribbble',
				'url' => 'https://dribbble.com/',
				'description' => sprintf( esc_html__( 'Dribbble portfolio (e.g. %s)', 'wp-social-manager' ), '<code>simplebits</code>' ),
			),
			'behance' => array(
				'label' => 'Behance',
				'url' => 'https://www.behance.net/',
				'description' => sprintf( esc_html__( 'Behance portfolio (e.g. %s)', 'wp-social-manager' ), '<code>amocci</code>' ),
			),
			'github' => array(
				'label' => 'Github',
				'url' => 'https://github.com/',
				'description' => sprintf( esc_html__( 'Github repository (e.g. %s)', 'wp-social-manager' ), '<code>tfirdaus</code>' ),
			),
			'codepen' => array(
				'label' => 'CodePen',
				'url' => 'https://codepen.io/',
				'description' => sprintf( esc_html__( 'CodePen pens (e.g. %s)', 'wp-social-manager' ), '<code>stacy</code>' ),
			),
		);

		if ( is_string( $slug ) && ! empty( $slug ) ) {
			$profiles = isset( $profiles[ $slug ] ) ? $profiles[ $slug ] : '';
		}

		return $profiles;
	}

	/**
	 * Get the registered Post Types.
	 *
	 * This function excludes a couple of irrelevant Post Types
	 * for this plugin such as the 'revision', 'nav_menu_log', etc.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array  List of filtered Post Types.
	 */
	public static function post_types() {

		$post_types = array();

		$types = get_post_types( array(
			'public' => true,
		) );

		foreach ( $types as $key => $value ) {
			switch ( $key ) {
				case 'attachment':
				case 'revision':
				case 'nav_menu_item':
				case 'deprecated_log':
					unset( $types[ $key ] );
					break;
			}
		}

		foreach ( $types as $key => $value ) {
			$post_types[ $key ] = get_post_type_object( $key )->label;
		}

		return $post_types;
	}

	/**
	 * Get names and keys of the social button view / appearance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function button_views() {

		$types = array(
			'icon'      => esc_html__( 'Icon Only', 'wp-social-manager' ),
			'text'      => esc_html__( 'Text Only', 'wp-social-manager' ),
			'icon-text' => esc_html__( 'Icon and Text', 'wp-social-manager' ),
		);

		return $types;
	}

	/**
	 * Get names and keys of the social buttons placements in the content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function button_placements() {

		$locations = array(
			'before' => esc_html__( 'Before the content', 'wp-social-manager' ),
			'after'  => esc_html__( 'After the content', 'wp-social-manager' ),
		);

		return $locations;
	}

	/**
	 * Get names and keys of the social media to include in the social buttons lineup.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $for The buttons group to retrieve.
	 * @return array       Selected list of buttons or all if not specified.
	 */
	public static function button_sites( $for = '' ) {

		$sites['content'] = array(
			'facebook' => 'Facebook',
			'twitter' => 'Twitter',
			'googleplus' => 'Google+',
			'pinterest' => 'Pinterest',
			'linkedin' => 'LinkedIn',
			'reddit' => 'Reddit',
			'email' => 'Email',
		);

		$sites['image'] = array(
			'pinterest' => 'Pinterest',
		);

		return isset( $sites[ $for ] ) ? $sites[ $for ] : $sites;
	}

	/**
	 * Get list of button modes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array An array of button modes; the labels and the keys
	 */
	public static function buttons_modes() {

		return array(
			'html' => 'HTML (HyperText Markup Language)',
			'json' => 'JSON (JavaScript Object Notation)',
		);
	}

	/**
	 * Get options of button modes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array An array of button modes; the labels and the keys
	 */
	public static function link_modes() {

		return array(
			'permalink' => 'Permalink',
			'shortlink' => 'Shortlink',
		);
	}
}
