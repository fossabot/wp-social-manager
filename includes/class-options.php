<?php
/**
 * This file defines the Options class.
 *
 * @package SocialManager
 * @subpackage Options
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The Options class that registers the plugin options.
 *
 * The Options class may be used in the admin area such as in the settings to register
 * options and validate the options before being saved into the database, or in
 * the public view of the site to validate the output before being rendered.
 *
 * @since 1.0.0
 */
final class Options {

	/**
	 * Options: Social Profiles and Pages.
	 *
	 * @since 1.0.0
	 * @since 1.1.3 - Remove 'reddit', 'dribbble', 'behance', 'github', 'codepen'.
	 * @access public
	 *
	 * @param string $slug The social media slug (e.g. `facebook`, `twitter`, etc.).
	 * @return mixed     	 Return an array if the profiles with the specified $slug
	 * 						 is present, otherwise return an empty string.
	 */
	public static function social_profiles( $slug = '' ) {

		$slug = sanitize_key( $slug );
		$profiles = array(
			'facebook' => array(
				'label' => 'Facebook',
				'url' => 'https://www.facebook.com/',
				'description' => sprintf( esc_html__( 'Facebook profile or page (e.g. %s)', 'ninecodes-social-manager' ), '<code>zuck</code>' ),
			),
			'twitter' => array(
				'label' => 'Twitter',
				'url' => 'https://twitter.com/',
				'description' => sprintf( esc_html__( 'Twitter profile without the %1$s (e.g. %2$s)', 'ninecodes-social-manager' ), '<code>@</code>', '<code>jack</code>' ),
			),
			'instagram' => array(
				'label' => esc_html( 'Instagram' ),
				'url' => esc_url( 'https://instagram.com/' ),
				'description'  => sprintf( esc_html__( 'Instagram profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>victoriabeckham</code>' ),
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'url' => esc_url( 'https://pinterest.com/' ),
				'description'  => sprintf( esc_html__( 'Pinterest profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>ohjoy</code>' ),
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'url' => esc_url( 'https://www.linkedin.com/in/' ),
				'description' => sprintf( esc_html__( 'LinkedIn profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>williamhgates</code>' ),
			),
			'googleplus' => array(
				'label' => 'Google+',
				'url' => 'https://plus.google.com/',
				'description' => sprintf( esc_html__( 'Google+ profile or page. Include the %1$s sign if necessary (e.g. %2$s)', 'ninecodes-social-manager' ), '<code>+</code>', '<code>+hishekids</code>' ),
			),
			'youtube' => array(
				'label' => 'Youtube',
				'url' => 'https://www.youtube.com/user/',
				'description' => sprintf( esc_html__( 'Youtube channel (e.g. %s)', 'ninecodes-social-manager' ), '<code>BuzzFeedVideo</code>' ),
			),
			'reddit' => array(
				'label' => 'Reddit',
				'url' => 'https://www.reddit.com/user/',
				'description' => sprintf( esc_html__( 'Reddit profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>Unidan</code>' ),
			),
		);

		/**
		 * Filter the profiles options
		 *
		 * This filter allows developer to add or remove Social Media profiles options and the input fields.
		 *
		 * @since 1.1.3
		 *
		 * @param string $context Option context; which option to filter.
		 *
		 * @var array
		 */
		$profiles = apply_filters( 'ninecodes_social_manager_options', $profiles, 'profiles' );

		/**
		 * Sanitize the Profiles
		 *
		 * Ensure it has the required label, url, and description.
		 *
		 * @since 1.1.3
		 *
		 * @var array
		 */
		$profiles = array_map( function( $profile ) {

			// Ensure the `$profile` input has required keys.
			$profile = wp_parse_args( $profile, array(
				'label' => esc_html_x( 'Example', 'Dummy text label for a social media profile, in case it is not supplied.', 'ninecodes-social-manager' ),
				'url' => 'http://example.com/',
				'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt, repudiandae.',
			) );

			$profile['label'] = sanitize_text_field( $profile['label'] );
			$profile['url'] = trailingslashit( esc_url( $profile['url'] ) );
			$profile['description'] = wp_kses( $profile['description'], array(
				'code' => true,
				'strong' => true,
			) );

			return $profile;

		}, array_unique( $profiles, SORT_REGULAR ) );

		if ( is_string( $slug ) && ! empty( $slug ) ) {
			return isset( $profiles[ $slug ] ) ? $profiles[ $slug ] : '';
		} else {
			return $profiles;
		}
	}

	/**
	 * Options: Post Types.
	 *
	 * This function excludes a couple of irrelevant Post Types
	 * for this plugin such as the 'revision', 'nav_menu_log', etc.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array List of filtered Post Types.
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
	 * Options: Button View.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function button_views() {

		$views = array(
			'icon' => esc_html__( 'Icon Only', 'ninecodes-social-manager' ),
			'text' => esc_html__( 'Text Only', 'ninecodes-social-manager' ),
			'icon-text' => esc_html__( 'Icon and Text', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the buttons views / styles options.
		 *
		 * This filter allows developer to add new view / style options
		 * to render the social media buttons.
		 *
		 * @since 1.1.3
		 *
		 * @param string $context Option context; which option to filter.
		 *
		 * @var array
		 */
		$views_extra = (array) apply_filters( 'ninecodes_social_manager_options', array(), 'button_views' );

		if ( ! empty( $views_extra ) ) {

			foreach ( $views_extra as $key => $value ) {
				if ( in_array( $key, array_keys( $views ), true ) ) {
					unset( $views_extra[ $key ] );
				}
			}

			$views = array_unique( array_merge( $views, $views_extra ) );
		}

		return array_map( 'esc_html', $views );
	}

	/**
	 * Options: Button Placements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function button_placements() {

		$placements = array(
			'before' => esc_html__( 'Before the content', 'ninecodes-social-manager' ),
			'after'  => esc_html__( 'After the content', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the buttons views / styles options.
		 *
		 * This filter allows developer to add new placement options
		 * to render the social media buttons.
		 *
		 * @since 1.1.3
		 *
		 * @param string $context Option context; which option to filter.
		 *
		 * @var array
		 */
		$placements_extra = (array) apply_filters( 'ninecodes_social_manager_options', array(), 'button_placements' );

		if ( ! empty( $placements_extra ) ) {

			foreach ( $placements_extra as $key => $value ) {
				if ( in_array( $key, array_keys( $placements ), true ) ) {
					unset( $placements_extra[ $key ] );
				}
			}

			$placements = array_unique( array_merge( $placements, $placements_extra ) );
		}

		return array_map( 'esc_html', $placements );
	}

	/**
	 * Function method to get names and keys of the social media to include
	 * in the social buttons line-up.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $for The buttons group to retrieve.
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

		/**
		 * Filter the buttons sites.
		 *
		 * @since 1.1.3
		 *
		 * @param string $context Option context; which option to filter.
		 *
		 * @var array
		 */
		$sites_extra = (array) apply_filters( 'ninecodes_social_manager_options', array(), 'button_sites' );

		if ( ! empty( $sites_extra ) ) {

			$sites_extra = wp_parse_args( $sites_extra, array(
				'content' => array(),
				'image' => array(),
			) );

			// Remove keys beside 'content' and 'image'.
			foreach ( $sites_extra as $key => $value ) {
				if ( ! in_array( $key, array( 'content', 'image' ), true ) ) {
					unset( $sites_extra[ $key ] );
				}
			}

			if ( ! empty( $sites_extra['content'] ) ) {

				// Remove duplicate keys from the Social Media buttons content.
				foreach ( $sites_extra['content'] as $key => $value ) {
					if ( in_array( $key, array_keys( $sites['content'] ), true ) ) {
						unset( $sites_extra['content'][ $key ] );
					}
				}

				$extras = array_map( 'esc_html', $sites_extra['content'] );
				$sites['content'] = array_unique( array_merge( $sites['content'], $extras ), SORT_REGULAR );
			}

			if ( ! empty( $sites_extra['image'] ) ) {

				// Remove duplicate keys from the Social Media buttons image.
				foreach ( $sites_extra['image'] as $key => $value ) {
					if ( in_array( $key, array_keys( $sites['image'] ), true ) ) {
						unset( $sites_extra['image'][ $key ] );
					}
				}

				$extras = array_map( 'esc_html', $sites_extra['image'] );
				$sites['image'] = array_unique( array_merge( $sites['image'], $extras ), SORT_REGULAR );
			}
		}

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
