<?php
/**
 * The file that defines the Options class
 *
 * @package SocialManager
 * @subpackage Options
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * Register and manage the plugin options
 *
 * The Options class may be used in the admin area such as in the settings to register
 * options and validate the options before being saved into the database, or in
 * the public view of the site to validate the output before being rendered.
 *
 * @since 1.0.0
 */
final class Options {

	/**
	 * The unique ID that prefixes the database name added by the plugin
	 *
	 * @since 2.0.0
	 * @var string
	 */
	const SLUG = 'ncsocman';

	/**
	 * Get the option slug
	 *
	 * @since 2.0.0
	 * @access public
	 * @return string
	 */
	public static function slug() {
		return self::SLUG;
	}

	/**
	 * Get the option name
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $name The name of the option.
	 * @return array|string
	 */
	public static function name( $name = '' ) {
		$slug = self::SLUG;
		return "{$slug}_{$name}";
	}

	/**
	 * Get the list of options stored in the database
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $name The option name.
	 * @param string $key  The array key to retrieve from the option.
	 * @return mixed The option value or null if option is not available.
	 */
	public static function get( $name = '', $key = '' ) {

		if ( empty( $name ) ) {
			return null;
		}

		$option_name = self::name( $name );
		$option = get_option( $option_name, null );

		if ( $name && $key ) {
			return isset( $option[ $key ] ) ? $option[ $key ] : null;
		}

		return $option ? $option : null;
	}

	/**
	 * Get the list of options stored in the database
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $name The option name.
	 * @param string $key The array key to update from the option.
	 * @param string $value The value to add in the option.
	 * @return void
	 */
	public static function update( $name = '', $key = '', $value = '' ) {

		if ( ! $name || ! $key || ! $value ) {
			return;
		}

		$option_name = self::name( $name );

		$option_value = (array) get_option( $option_name, array() );
		$option_value = array_merge( $option_value, array(
			$key => $value,
		) );

		update_option( $option_name, $option_value );
	}

	/**
	 * Get options available of the given name
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $method Method name to the option list registered.
	 * @param array  $args List arguments to pass to the method.
	 * @return mixed
	 */
	public static function get_list( $method = '', array $args = array() ) {

		if ( is_callable( array( __CLASS__, "list_{$method}" ) ) ) {
			return call_user_func_array( array( __CLASS__, "list_{$method}" ), $args );
		}

		return array();
	}

	/**
	 * Options: Social Profiles.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 - Remove dribbble, behance, github, codepen.
	 * @access protected
	 *
	 * @param string $slug The social media slug (e.g. facebook, twitter, etc.).
	 * @return mixed Return an array if the profiles with the specified "$slug" is present, otherwise return an empty string.
	 */
	protected static function list_social_profiles( $slug = '' ) {

		$slug = sanitize_key( $slug );
		$profiles = array(
			'facebook' => array(
				'label' => 'Facebook',
				'url' => 'https://www.facebook.com/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Facebook profile or page (e.g. %s)', 'ninecodes-social-manager' ), '<code>zuck</code>' ),
			),
			'twitter' => array(
				'label' => 'Twitter',
				'url' => 'https://twitter.com/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Twitter profile without the %1$s (e.g. %2$s)', 'ninecodes-social-manager' ), '<code>@</code>', '<code>jack</code>' ),
			),
			'instagram' => array(
				'label' => 'Instagram',
				'url' => 'https://instagram.com/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description'  => sprintf( __( 'Instagram profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>victoriabeckham</code>' ),
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'url' => 'https://pinterest.com/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description'  => sprintf( __( 'Pinterest profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>ohjoy</code>' ),
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'url' => 'https://www.linkedin.com/in/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'LinkedIn profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>williamhgates</code>' ),
			),
			'googleplus' => array(
				'label' => 'Google+',
				'url' => 'https://plus.google.com/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Google+ profile or page. Include the %1$s sign if necessary (e.g. %2$s)', 'ninecodes-social-manager' ), '<code>+</code>', '<code>+hishekids</code>' ),
			),
			'youtube' => array(
				'label' => 'Youtube',
				'url' => 'https://www.youtube.com/user/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Youtube channel (e.g. %s)', 'ninecodes-social-manager' ), '<code>BuzzFeedVideo</code>' ),
			),
			'reddit' => array(
				'label' => 'Reddit',
				'url' => 'https://www.reddit.com/user/{{ data.profile }}',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Reddit profile (e.g. %s)', 'ninecodes-social-manager' ), '<code>Unidan</code>' ),
			),
			'tumblr' => array(
				'label' => 'Tumblr',
				'url' => 'https://{{ data.profile }}.tumblr.com/',
				// translators: %s is an example of a username input.
				'description' => sprintf( __( 'Tumblr blog (e.g. %s)', 'ninecodes-social-manager' ), '<code>crowloop</code>' ),
			),
		);

		/**
		 * Filter the profiles options
		 *
		 * This filter allows developer to add or remove Social Media profiles options and the input fields.
		 *
		 * @since 1.2.0
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
		 * @since 1.2.0
		 *
		 * @var array
		 */
		$profiles = array_map( function( $profile ) {

			// Ensure the `$profile` input has required keys.
			$profile = wp_parse_args( $profile, array(
				'label' => esc_html_x( 'Example', 'Dummy text label for a social media profile, in case it is not supplied.', 'ninecodes-social-manager' ),
				'url' => 'http://example.com/{{ data.profile }}',
				'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt, repudiandae.',
			) );

			$profile['label'] = sanitize_text_field( $profile['label'] );
			$profile['url'] = sanitize_profile_url( $profile['url'] );
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
	 * @access protected
	 *
	 * @return array List of filtered Post Types.
	 */
	protected static function list_post_types() {

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
	 * @access protected
	 *
	 * @return array
	 */
	protected static function list_button_views() {

		$views = array(
			'icon' => __( 'Icon Only', 'ninecodes-social-manager' ),
			'text' => __( 'Text Only', 'ninecodes-social-manager' ),
			'icon_text' => __( 'Icon and Text', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the buttons views / styles options.
		 *
		 * This filter allows developer to add new view / style options
		 * to render the social media buttons.
		 *
		 * @since 1.2.0
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
	 * @access protected
	 *
	 * @return array
	 */
	protected static function list_button_placements() {

		$placements = array(
			'before' => __( 'Before the content', 'ninecodes-social-manager' ),
			'after'  => __( 'After the content', 'ninecodes-social-manager' ),
		);

		/**
		 * Filter the buttons views / styles options.
		 *
		 * This filter allows developer to add new placement options
		 * to render the social media buttons.
		 *
		 * @since 1.2.0
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
	 * @access protected
	 *
	 * @param string $for The buttons group to retrieve.
	 * @return array       Selected list of buttons or all if not specified.
	 */
	protected static function list_button_sites( $for = '' ) {

		$button_sites['content'] = array(
			'facebook' => array(
				'name' => 'Facebook',
				'label' => 'Facebook',
				'endpoint' => 'https://www.facebook.com/sharer/sharer.php',
			),
			'twitter' => array(
				'name' => 'Twitter',
				'label' => 'Twitter',
				'endpoint' => 'https://twitter.com/intent/tweet',
			),
			'googleplus' => array(
				'name' => 'Google+',
				'label' => 'Google+',
				'endpoint' => 'https://plus.google.com/share',
			),
			'pinterest' => array(
				'name' => 'Pinterest',
				'label' => 'Pinterest',
				'endpoint' => 'https://www.pinterest.com/pin/create/bookmarklet/',
			),
			'linkedin' => array(
				'name' => 'LinkedIn',
				'label' => 'LinkedIn',
				'endpoint' => 'https://www.linkedin.com/shareArticle',
			),
			'reddit' => array(
				'name' => 'Reddit',
				'label' => 'Reddit',
				'endpoint' => 'https://www.reddit.com/submit',
			),
			'tumblr' => array(
				'name' => 'Tumblr',
				'label' => 'Tumblr',
				'endpoint' => 'http://www.tumblr.com/share/link',
			),
			'email' => array(
				'name' => 'Email',
				'label' => 'Email',
				'endpoint' => 'mailto:',
			),
		);

		$button_sites['image'] = array(
			'pinterest' => array(
				'name' => 'Pinterest',
				'label' => 'Pinterest',
				'endpoint' => 'https://www.pinterest.com/pin/create/bookmarklet/',
			),
		);

		/**
		 * Filter the buttons sites.
		 *
		 * @since 1.2.0
		 *
		 * @param string $context Option context; which option to filter.
		 *
		 * @var array
		 */
		$button_sites = (array) apply_filters( 'ninecodes_social_manager_options', $button_sites, 'button_sites' );

		foreach ( $button_sites as $key => $buttons ) {

			if ( ! is_array( $buttons ) || ! in_array( $key, array( 'content', 'image' ), true ) ) {
				unset( $button_sites[ $key ] );
				continue;
			}

			foreach ( $buttons as $site => $button ) {

				if ( ! is_array( $button ) ) {
					unset( $button_sites[ $key ][ $site ] );
					continue;
				}

				$is_set = ! isset( $button['name'] ) || ! isset( $button['label'] ) || ! isset( $button['endpoint'] );
				$is_empty = empty( $button['name'] ) || empty( $button['label'] ) || empty( $button['endpoint'] );

				if ( $is_set || $is_empty ) {
					unset( $button_sites[ $key ][ $site ] );
					continue;
				}

				$button_sites[ $key ][ $site ] = array(
					'name' => esc_html( $button['name'] ),
					'label' => esc_html( $button['label'] ),
					'endpoint' => esc_url( $button['endpoint'] ),
				);
			}
		}

		return isset( $button_sites[ $for ] ) ? $button_sites[ $for ] : $button_sites;
	}

	/**
	 * Get style options of button content.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param string $for Which button style to retrieve.
	 * @return array
	 */
	protected static function list_button_styles( $for = '' ) {

		$button_styles = array(
			'content' => array(
				'mono' => __( 'Mono', 'ninecodes-social-manager' ),
				'color' => __( 'Color', 'ninecodes-social-manager' ),
				'circle' => __( 'Circle', 'ninecodes-social-manager' ),
				'rounded' => __( 'Rounded', 'ninecodes-social-manager' ),
				'square' => __( 'Square', 'ninecodes-social-manager' ),
				'skeumorphic' => __( 'Skeumorphic', 'ninecodes-social-manager' ),
			),
			'image' => array(
				'rounded' => __( 'Rounded', 'ninecodes-social-manager' ),
				'circle' => __( 'Circle', 'ninecodes-social-manager' ),
				'square' => __( 'Square', 'ninecodes-social-manager' ),
			),
			'widget_social_profile' => array(
				'mono' => __( 'Mono', 'ninecodes-social-manager' ),
				'color' => __( 'Color', 'ninecodes-social-manager' ),
				'circle' => __( 'Circle', 'ninecodes-social-manager' ),
				'rounded' => __( 'Rounded', 'ninecodes-social-manager' ),
				'square' => __( 'Square', 'ninecodes-social-manager' ),
			),
		);

		$button_styles = (array) apply_filters( 'ninecodes_social_manager_options', $button_styles, 'button_styles' );

		foreach ( $button_styles as $key => $styles ) {
			$button_styles[ $key ] = array_map( 'esc_html', $button_styles[ $key ] );
		}

		return isset( $button_styles[ $for ] ) ? $button_styles[ $for ] : $button_styles;
	}

	/**
	 * Get list of button modes.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array An array of button modes; the labels and the keys
	 */
	protected static function list_button_modes() {

		return array(
			'html' => 'HTML (HyperText Markup Language)',
			'json' => 'JSON (JavaScript Object Notation)',
		);
	}

	/**
	 * Get options of button modes.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array An array of button modes; the labels and the keys
	 */
	protected static function list_link_modes() {

		return array(
			'permalink' => 'Permalink',
			'shortlink' => 'Shortlink',
		);
	}
}
