<?php

namespace XCo\WPSocialManager;

abstract class Utilities {

	/**
	 * [get_social_attributes description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	final public static function get_social_properties( $name = '' ) {

		$name = sanitize_key( $name );
		$props = array(
			'facebook' => array(
				'label' => 'Facebook',
				'url'   => 'https://www.facebook.com/',
				'icon'  => self::get_social_icon( 'facebook' )
			),
			'twitter' => array(
				'label' => 'Twitter',
				'url'   => 'https://twitter.com/',
				'icon'  => self::get_social_icon( 'twitter' )
			),
			'instagram' => array(
				'label' => esc_html( 'Instagram' ),
				'url'   => esc_url( 'https://instagram.com/' ),
				'icon'  => self::get_social_icon( 'instagram' )
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'url' => esc_url( 'https://pinterest.com/' ),
				'icon' => self::get_social_icon( 'pinterest' )
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'url' => esc_url( 'https://pinterest.com/' ),
				'icon' => self::get_social_icon( 'linkedin' )
			),
			'googleplus' => array(
				'label' => 'Google+',
				'url' => 'https://plus.google.com/',
				'icon' => self::get_social_icon( 'googleplus' )
			),
			'youtube' => array(
				'label' => 'Youtube',
				'url' => 'https://www.youtube.com/user/',
				'icon' => self::get_social_icon( 'youtube' )
			),
			'dribbble' => array(
				'label' => 'Dribbble',
				'url' => 'https://dribbble.com/',
				'icon' => self::get_social_icon( 'dribbble' )
			),
			'behance' => array(
				'label' => 'Behance',
				'url' => 'https://www.behance.net/',
				'icon' => self::get_social_icon( 'behance' )
			),
			'github' => array(
				'label' => 'Github',
				'url' => 'https://github.com/',
				'icon' => self::get_social_icon( 'github' )
			),
			'codepen' => array(
				'label' => 'CodePen',
				'url' => 'https://codepen.io/',
				'icon' => self::get_social_icon( 'codepen' )
			),
		);

		if ( is_string( $name ) && ! empty( $name ) ) {
			$props = isset( $props[ $name ] ) ? $props[ $name ] : '';
		}

		return $props;
	}

	/**
	 * [get_social_icon description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	final public static function get_social_icon( $name ) {

		$facebook = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="M 350.91219,71.61471 C 340.53238,69.39414 330.69668,67.58112 321.40507,66.17562 312.11313,64.77093 302.0055,64.0457 291.08215,64 c -31.8188,0.13635 -54.79897,7.47911 -68.94053,22.02831 -14.14167,14.54991 -21.07653,35.49043 -20.80449,62.82154 l 0,39.70536 -40.24932,0 0,64.18134 40.24932,0 0,195.26345 78.32293,0 0,-195.26345 57.11046,0 5.43913,-64.18134 -62.54959,0 0,-31.00281 c -0.45338,-8.7929 0.86105,-15.68239 3.94335,-20.66856 3.08201,-4.98552 10.6514,-7.52376 22.70821,-7.61472 4.8157,0.0227 10.41343,0.38559 16.79319,1.0878 6.37942,0.70287 12.38509,1.60939 18.017,2.71955 z"/></svg>';

		$twitter = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="m 447.99983,136.90624 c -14.12738,6.26687 -29.31263,10.50047 -45.24668,12.40511 16.26238,-9.74975 28.75579,-25.18848 34.6387,-43.58592 -15.22369,9.02976 -32.08323,15.58464 -50.02948,19.11744 -14.36924,-15.312 -34.84415,-24.87744 -57.50403,-24.87744 -43.50715,0 -78.78295,35.27233 -78.78295,78.77953 0,6.17472 0.69658,12.18816 2.04,17.95392 -65.47507,-3.28512 -123.52666,-34.65025 -162.38189,-82.31425 -6.78221,11.6352 -10.6672,25.16928 -10.6672,39.60769 0,27.33312 13.90796,51.4464 35.04832,65.57376 -12.9143,-0.40896 -25.06272,-3.95328 -35.68357,-9.85344 -0.0073,0.32832 -0.0073,0.65664 -0.0073,0.99072 0,38.17018 27.15668,70.00992 63.19584,77.24968 -6.60979,1.8 -13.56978,2.76384 -20.75481,2.76384 -5.07668,0 -10.01185,-0.495 -14.82298,-1.4139 10.02528,31.29753 39.12,54.07662 73.59438,54.70962 -26.96237,21.13196 -60.9314,33.72671 -97.84268,33.72671 -6.35925,0 -12.63074,-0.37416 -18.7935,-1.10167 34.86451,22.3522 76.27584,35.39693 120.76571,35.39693 144.90857,0 224.15267,-120.04568 224.15267,-224.15425 0,-3.41569 -0.0788,-6.81216 -0.2304,-10.19137 15.3946,-11.10911 28.7501,-24.98303 39.31202,-40.78272"/></svg>';

		$instagram = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="m 265.93,64 c -41.667,1.375 -84.183,-3.251 -125.086,6.62 -40.578,9.984 -70.111,47.801 -73.974,88.801 -4.638,50.316 -2.221,100.998 -2.387,151.461 -0.13,35.781 3.377,75.54 29.696,102.571 26.992,30.526 70.203,34.851 108.429,34.06 51.153,0.126 102.552,2.098 153.519,-2.913 39.308,-5.052 74.901,-33.599 84.896,-72.546 9.743,-37.854 5.561,-77.469 6.977,-116.135 -1.503,-39.853 3.194,-80.889 -8.106,-119.676 -11.48,-38.58 -48.197,-65.647 -87.639,-69.523 -28.631,-3.127 -57.567,-2.34 -86.324,-2.72 z m -30.994,34.377 c 40.692,1.041 81.831,-2.334 122.195,4.027 27.079,4.842 48.722,27.65 52.884,54.725 5.423,39.468 2.928,79.585 3.602,119.335 -1.931,32.303 3.528,66.492 -9.587,97.085 -11.821,27.14 -42.43,38.798 -70.315,38.825 -52.965,1.631 -106.056,1.888 -159.003,-0.245 -28.757,-0.263 -59.374,-15.308 -68.969,-43.925 -10.145,-32.022 -6.293,-66.255 -7.21,-99.36 0.658,-38.302 -2.245,-77.064 4.145,-114.983 5.342,-29.297 31.986,-50.471 60.987,-53.027 23.622,-2.633 47.537,-2.147 71.272,-2.457 z m 122.951,32.38 c -19.563,-0.758 -30.439,26.323 -15.751,39.304 13.259,14.58 40.231,3.133 39.072,-16.5 0.246,-12.423 -10.968,-23.203 -23.321,-22.804 z m -100.311,26.777 c -38.532,-0.791 -76.376,22.386 -91.331,58.265 -21.747,44.612 -1.617,103.134 41.978,126.287 41.974,24.313 100.227,11.204 127.711,-28.696 29.712,-39.463 22.664,-100.415 -15.507,-131.922 -17.255,-15.073 -39.912,-23.824 -62.851,-23.934 z m -1.067,34.281 c 36.069,-0.829 67.125,33.32 63.491,69.11 -1.715,38.865 -44.436,68.767 -81.579,56.709 -36.64,-9.135 -57.429,-53.986 -40.946,-87.883 9.99,-22.905 34.017,-38.439 59.034,-37.936 z"/></svg>';

		$pinterest = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="m 265.74635,48 c -113.52746,0 -170.76887,81.39435 -170.76887,149.26985 0,41.0979 15.55966,77.66034 48.93214,91.28532 5.47228,2.23723 10.374,0.077 11.961,-5.98183 1.10402,-4.19312 3.71544,-14.77146 4.88048,-19.17689 1.60029,-5.99245 0.97928,-8.09432 -3.43676,-13.31715 -9.62296,-11.35062 -15.77198,-26.04513 -15.77198,-46.85946 0,-60.38627 45.17956,-114.44571 117.64627,-114.44571 64.16803,0 99.4221,39.20835 99.4221,91.57196 0,68.89725 -30.49037,127.04629 -75.75485,127.04629 -24.99685,0 -43.70932,-20.67368 -37.71156,-46.02881 7.18138,-30.27009 21.093,-62.93929 21.093,-84.78864 0,-19.55905 -10.49873,-35.87243 -32.22601,-35.87243 -25.55415,0 -46.08188,26.43525 -46.08188,61.84855 0,22.5553 7.62194,37.80977 7.62194,37.80977 0,0 -26.15129,110.80192 -30.73453,130.20705 -9.12933,38.64573 -1.37206,86.02004 -0.71655,90.80497 0.38482,2.83435 4.02859,3.50843 5.67929,1.36675 2.35664,-3.07583 32.79128,-40.6494 43.13874,-78.19376 2.92723,-10.63143 16.80699,-65.68075 16.80699,-65.68075 8.30131,15.83567 32.56569,29.78443 58.36932,29.78443 76.81374,0 128.92789,-70.02779 128.92789,-163.76264 0,-70.87704 -60.03331,-136.88687 -151.27617,-136.88687 z"/></svg>';

		$googleplus = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="M 192.99,128 C 120.26,128 64,189.38 64,255.68 64,320.65 116.92,384 194.84,384 c 68.53,0 118.7,-46.95 118.7,-116.37 0,-14.63 -2.13,-23.1 -2.13,-23.1 l -117.42,0 0,34.84 83.28,0 c -4.11,48.86 -44.77,69.7 -83.14,69.7 -49.09,0 -91.94,-38.63 -91.94,-92.77 0,-52.74 40.84,-93.36 92.05,-93.36 39.5,0 62.78,25.19 62.78,25.19 l 24.4,-25.27 c 0,0 -31.32,-34.86 -88.43,-34.86 z m 187.01,72 0,44 -44,0 0,24 44,0 0,44 24,0 0,-44 44,0 0,-24 -44,0 0,-44 z"/></svg>';

		$youtube = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" viewBox="0 0 512 512" style="width: 1.86em; height: 1.86em"><path d="m 216.35639,206.19127 c -0.006,35.96457 -0.0118,71.92913 -0.0177,107.8937 34.58612,-18.04494 69.17225,-36.08988 103.75837,-54.13482 -34.5802,-17.91962 -69.1606,-35.83929 -103.74067,-53.75888 z m 227.80904,126.58486 c -2.80598,21.00313 -12.62269,43.97642 -34.55509,50.79271 -24.06931,6.41846 -49.4741,4.49543 -74.1555,6.20959 -73.64987,1.88669 -147.50997,2.20493 -221.00381,-3.39054 C 92.08964,384.59914 74.08834,366.27246 70.32825,344.51496 62.59125,306.89232 64.0468,268.13834 64.16501,229.90219 66.35404,200.83324 63.9879,168.86644 81.03134,143.579 c 17.12008,-20.52063 46.08821,-18.61055 70.26973,-20.31487 82.04166,-2.85471 164.34608,-3.79916 246.24774,2.46938 22.36323,1.78688 40.3613,20.11743 44.12641,41.87319 7.60478,36.67174 6.23891,74.45084 6.22999,111.73414 -0.41429,17.85567 -1.70526,35.69235 -3.73978,53.43529 z"/></svg>';

		$icons = array(
			'facebook'   => apply_filters( 'wp_social_manager_icon', $facebook, 'facebook' ),
			'twitter'    => apply_filters( 'wp_social_manager_icon', $twitter, 'twitter' ),
			'instagram'  => apply_filters( 'wp_social_manager_icon', $instagram, 'instagram' ),
			'pinterest'  => apply_filters( 'wp_social_manager_icon', $pinterest, 'pinterest' ),
			'googleplus' => apply_filters( 'wp_social_manager_icon', $googleplus, 'googleplus' ),
			'youtube'    => apply_filters( 'wp_social_manager_icon', $youtube, 'youtube' ),
			'dribbble'   => apply_filters( 'wp_social_manager_icon', $youtube, 'dribbble' ),
			'dribbble'   => apply_filters( 'wp_social_manager_icon', $youtube, 'dribbble' )
		);

		return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
	}
}


/**
 *
 */
abstract class SettingUtilities extends Utilities {

	/**
	 * [get_social_profiles description]
	 * @return [type] [description]
	 */
	final protected static function get_social_profiles() {

		$properties  = self::get_social_properties();
		$description = array(
			'facebook'   => sprintf( esc_html__( 'Facebook profile or page (e.g. %s)', 'wp-social-manager' ), '<code>zuck</code>' ),
			'twitter'    => sprintf( esc_html__( 'Twitter profile without the %1$s (e.g. %2$s)', 'wp-social-manager' ), '<code>@</code>', '<code>jack</code>' ),
			'instagram'  => sprintf( esc_html__( 'Instagram profile (e.g. %s)', 'wp-social-manager' ), '<code>victoriabeckham</code>' ),
			'pinterest'  => sprintf( esc_html__( 'Pinterest profile (e.g. %s)', 'wp-social-manager' ), '<code>ohjoy</code>' ),
			'linkedin'   => sprintf( esc_html__( 'LinkedIn profile (e.g. %s)', 'wp-social-manager' ), '<code>williamhgates</code>' ),
			'googleplus' => sprintf( esc_html__( 'Google+ profile or page. Include the %1$s sign if necessary (e.g. %2$s)', 'wp-social-manager' ), '<code>+</code>', '<code>+hishekids</code>' ),
			'youtube'    => sprintf( esc_html__( 'Youtube channel (e.g. %s)', 'wp-social-manager' ), '<code>BuzzFeedVideo</code>' ),
			'dribbble'   => sprintf( esc_html__( 'Dribbble portfolio (e.g. %s)', 'wp-social-manager' ), '<code>simplebits</code>' ),
			'behance'    => sprintf( esc_html__( 'Behance portfolio (e.g. %s)', 'wp-social-manager' ), '<code>amocci</code>' ),
			'github'     => sprintf( esc_html__( 'Github repository (e.g. %s)', 'wp-social-manager' ), '<code>tfirdaus</code>' ) ,
			'codepen'    => sprintf( esc_html__( 'CodePen pens (e.g. %s)', 'wp-social-manager' ), '<code>stacy</code>' )
		);

		foreach ( $properties as $key => $value ) {
			$properties[ $key ][ 'description' ] = $description[ $key ];
		}

		return $properties;
	}

	/**
	 * [get_post_types description]
	 * @return [type] [description]
	 */
	final protected static function get_post_types() {

		$post_types = array();

		$types = get_post_types();
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
	 * [get_button_locations description]
	 * @return [type] [description]
	 */
	final protected static function get_button_views() {

		$types = array(
			'icon' => esc_html__( 'Icon Only', 'wp-sharing-manager' ),
			'text' => esc_html__( 'Text Only', 'wp-sharing-manager' ),
			'icon-text' => esc_html__( 'Icon and Text', 'wp-sharing-manager' ),
		);

		return $types;
	}

	/**
	 * [get_buttons_location description]
	 * @return [type] [description]
	 */
	final protected static function get_button_locations() {

		$locations = array(
			'before' => esc_html__( 'Before the content', 'wp-sharing-manager' ),
			'after' => esc_html__( 'After the content', 'wp-sharing-manager' )
		);

		return $locations;
	}

	/**
	 * [get_buttons_location description]
	 * @return [type] [description]
	 */
	final protected static function get_button_sites( $for = 'content' ) {

		$sites = array(
			'content' => array(
				'facebook' => 'Facebook',
				'twitter' => 'Twitter',
				'googleplus' => 'Google+',
				'pinterest' => 'Pinterest',
				'linkedin' => 'LinkedIn',
				'reddit' => 'Reddit',
				'whatsapp' => 'WhatsApp',
				'email' => 'Email'
			),
			'image' => array(
				'facebook' => 'Facebook',
				'pinterest' => 'Pinterest'
			)
		);

		return isset( $sites[ $for ] ) ? $sites[ $for ] : $sites[ 'content' ];
	}
}