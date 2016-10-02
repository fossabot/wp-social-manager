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

		$facebook = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3.998v3h-2a1 1 0 0 0-1 1v2h3v3h-3v7h-3v-7h-2v-3h2v-2.5a3.5 3.5 0 0 1 3.5-3.5H19zm1-2H4c-1.105 0-1.99.895-1.99 2l-.01 16c0 1.104.895 2 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z"/></svg>';

		$twitter = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M22 5.894a8.304 8.304 0 0 1-2.357.636 4.064 4.064 0 0 0 1.804-2.235c-.792.463-1.67.8-2.605.98A4.128 4.128 0 0 0 15.847 4c-2.266 0-4.104 1.808-4.104 4.04 0 .316.037.624.107.92a11.711 11.711 0 0 1-8.458-4.22 3.972 3.972 0 0 0-.555 2.03c0 1.401.724 2.638 1.825 3.362a4.138 4.138 0 0 1-1.858-.505v.05c0 1.958 1.414 3.59 3.29 3.961a4.169 4.169 0 0 1-1.852.07c.522 1.604 2.037 2.772 3.833 2.804a8.315 8.315 0 0 1-5.096 1.73c-.331 0-.658-.02-.979-.057A11.748 11.748 0 0 0 8.29 20c7.547 0 11.674-6.155 11.674-11.493 0-.175-.004-.349-.011-.522A8.265 8.265 0 0 0 22 5.894z"/></svg>';

		$instagram = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 3.81c2.667 0 2.983.01 4.036.06.974.043 1.503.206 1.855.343.467.18.8.398 1.15.747.35.35.566.682.747 1.15.137.35.3.88.344 1.854.05 1.053.06 1.37.06 4.036s-.01 2.983-.06 4.036c-.043.974-.206 1.503-.343 1.855-.18.467-.398.8-.747 1.15-.35.35-.682.566-1.15.747-.35.137-.88.3-1.854.344-1.053.05-1.37.06-4.036.06s-2.983-.01-4.036-.06c-.974-.043-1.503-.206-1.855-.343-.467-.18-.8-.398-1.15-.747-.35-.35-.566-.682-.747-1.15-.137-.35-.3-.88-.344-1.854-.05-1.053-.06-1.37-.06-4.036s.01-2.983.06-4.036c.044-.974.206-1.503.343-1.855.18-.467.398-.8.747-1.15.35-.35.682-.566 1.15-.747.35-.137.88-.3 1.854-.344 1.053-.05 1.37-.06 4.036-.06m0-1.8c-2.713 0-3.053.012-4.118.06-1.064.05-1.79.22-2.425.465-.657.256-1.214.597-1.77 1.153-.555.555-.896 1.112-1.152 1.77-.246.634-.415 1.36-.464 2.424-.047 1.065-.06 1.405-.06 4.118s.012 3.053.06 4.118c.05 1.063.218 1.79.465 2.425.255.657.597 1.214 1.152 1.77.555.554 1.112.896 1.77 1.15.634.248 1.36.417 2.424.465 1.066.05 1.407.06 4.12.06s3.052-.01 4.117-.06c1.063-.05 1.79-.217 2.425-.464.657-.255 1.214-.597 1.77-1.152.554-.555.896-1.112 1.15-1.77.248-.634.417-1.36.465-2.424.05-1.065.06-1.406.06-4.118s-.01-3.053-.06-4.118c-.05-1.063-.217-1.79-.464-2.425-.255-.657-.597-1.214-1.152-1.77-.554-.554-1.11-.896-1.768-1.15-.635-.248-1.362-.417-2.425-.465-1.064-.05-1.404-.06-4.117-.06zm0 4.86C9.167 6.87 6.87 9.17 6.87 12s2.297 5.13 5.13 5.13 5.13-2.298 5.13-5.13S14.832 6.87 12 6.87zm0 8.46c-1.84 0-3.33-1.49-3.33-3.33S10.16 8.67 12 8.67s3.33 1.49 3.33 3.33-1.49 3.33-3.33 3.33zm5.332-9.86c-.662 0-1.2.536-1.2 1.198s.538 1.2 1.2 1.2c.662 0 1.2-.538 1.2-1.2s-.538-1.2-1.2-1.2z"/></svg>';

		$pinterest = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.479 2 2 6.478 2 12a10 10 0 0 0 6.355 9.314c-.087-.792-.166-2.005.035-2.87.183-.78 1.173-4.97 1.173-4.97s-.3-.6-.3-1.486c0-1.387.808-2.429 1.81-2.429.854 0 1.265.642 1.265 1.41 0 .858-.545 2.14-.827 3.33-.238.996.5 1.806 1.48 1.806 1.776 0 3.144-1.873 3.144-4.578 0-2.394-1.72-4.068-4.178-4.068-2.845 0-4.513 2.134-4.513 4.34 0 .86.329 1.78.741 2.282.083.1.094.187.072.287-.075.315-.245.995-.28 1.134-.043.183-.143.223-.334.134-1.248-.581-2.03-2.408-2.03-3.875 0-3.156 2.292-6.05 6.609-6.05 3.468 0 6.165 2.47 6.165 5.775 0 3.446-2.175 6.221-5.191 6.221-1.013 0-1.965-.527-2.292-1.15l-.625 2.378c-.225.869-.834 1.957-1.242 2.621.937.29 1.931.447 2.962.447C17.521 22.003 22 17.525 22 12.002s-4.479-10-10-10V2z"/></svg>';

		$linkedin = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 18.998h-3v-5.3a1.5 1.5 0 0 0-3 0v5.3h-3v-9h3v1.2c.517-.838 1.585-1.4 2.5-1.4a3.5 3.5 0 0 1 3.5 3.5v5.7zM6.5 8.31a1.812 1.812 0 1 1-.003-3.624A1.812 1.812 0 0 1 6.5 8.31zM8 18.998H5v-9h3v9zm12-17H4c-1.106 0-1.99.895-1.99 2l-.01 16a2 2 0 0 0 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z"/></svg>';

		$googleplus = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M22 11h-2V9h-2v2h-2v2h2v2h2v-2h2v-2zm-13.869.143V13.2h3.504c-.175.857-1.051 2.571-3.504 2.571A3.771 3.771 0 0 1 4.365 12a3.771 3.771 0 0 1 3.766-3.771c1.227 0 2.015.514 2.453.942l1.664-1.542C11.198 6.6 9.796 6 8.131 6 4.715 6 2 8.657 2 12s2.715 6 6.131 6C11.635 18 14 15.6 14 12.171c0-.428 0-.685-.088-1.028h-5.78z"/></svg>';

		$youtube = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.813 7.996s-.196-1.38-.796-1.988c-.76-.798-1.615-.802-2.006-.848-2.8-.203-7.005-.203-7.005-.203h-.01s-4.202 0-7.005.203c-.392.047-1.245.05-2.007.848-.6.608-.796 1.988-.796 1.988s-.2 1.62-.2 3.24v1.52c0 1.62.2 3.24.2 3.24s.195 1.38.796 1.99c.762.797 1.762.77 2.208.855 1.603.155 6.81.202 6.81.202s4.208-.006 7.01-.21c.39-.046 1.245-.05 2.006-.847.6-.608.796-1.988.796-1.988s.2-1.62.2-3.24v-1.52c0-1.62-.2-3.24-.2-3.24zm-11.88 6.602V8.97l5.41 2.824-5.41 2.804z"/></svg>';

		$dribbble = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 22C6.487 22 2 17.512 2 12 2 6.487 6.487 2 12 2c5.512 0 10 4.487 10 10 0 5.512-4.488 10-10 10zm8.434-8.631c-.293-.093-2.644-.794-5.322-.365 1.118 3.07 1.573 5.57 1.66 6.09a8.57 8.57 0 0 0 3.663-5.725h-.001zm-5.097 6.506c-.127-.75-.624-3.36-1.825-6.475l-.055.018c-4.817 1.678-6.545 5.02-6.7 5.332A8.485 8.485 0 0 0 12 20.555a8.506 8.506 0 0 0 3.338-.679v-.001zm-9.683-2.15c.194-.333 2.537-4.213 6.944-5.638.112-.037.224-.07.337-.1-.216-.487-.45-.972-.694-1.45-4.266 1.275-8.403 1.222-8.778 1.213l-.004.26c0 2.194.831 4.197 2.195 5.713v.002zm-2.016-7.463c.383.007 3.902.022 7.897-1.04a54.666 54.666 0 0 0-3.166-4.94 8.576 8.576 0 0 0-4.73 5.982l-.001-.002zM10 3.71a45.577 45.577 0 0 1 3.185 5c3.037-1.138 4.325-2.866 4.478-3.085A8.496 8.496 0 0 0 12 3.47c-.688 0-1.359.083-2 .237v.003zm8.613 2.902C18.43 6.856 17 8.69 13.843 9.98a25.723 25.723 0 0 1 .75 1.678c2.842-.358 5.666.215 5.947.274a8.493 8.493 0 0 0-1.929-5.32h.002z"/></svg>';

		$behance = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.038 7.552H15.04v-1.24h4.998v1.24zm-8.323 5.09c.322.498.484 1.105.484 1.817 0 .735-.183 1.396-.552 1.98-.235.385-.527.71-.878.974-.395.304-.863.512-1.4.623-.54.11-1.123.168-1.752.168h-5.59V5.795h5.993c1.51.026 2.582.463 3.215 1.323.38.527.567 1.16.567 1.895 0 .76-.19 1.366-.573 1.827-.214.26-.53.494-.946.706.632.23 1.11.594 1.43 1.095zM4.89 10.687h2.627c.54 0 .976-.103 1.312-.308.335-.205.502-.57.502-1.09 0-.578-.222-.96-.666-1.146-.383-.127-.872-.193-1.466-.193H4.89v2.738zm4.694 3.594c0-.645-.263-1.09-.79-1.33-.293-.135-.708-.204-1.24-.21H4.89v3.308h2.623c.54 0 .956-.07 1.257-.218.542-.27.814-.786.814-1.55zm12.308-2.02c.06.407.088.996.077 1.766h-6.472c.036.893.344 1.518.93 1.875.352.224.78.334 1.28.334.528 0 .958-.133 1.29-.408.18-.146.34-.352.477-.61h2.372c-.062.527-.348 1.062-.86 1.605-.796.864-1.91 1.298-3.344 1.298-1.183 0-2.226-.365-3.13-1.094-.903-.732-1.356-1.917-1.356-3.56 0-1.543.407-2.723 1.223-3.544.82-.823 1.876-1.233 3.18-1.233.772 0 1.47.138 2.09.416.62.277 1.13.714 1.534 1.315.364.53.598 1.14.708 1.838zm-2.335.233c-.043-.618-.25-1.085-.62-1.405-.37-.32-.83-.48-1.378-.48-.598 0-1.06.17-1.388.51-.33.336-.535.795-.62 1.375h4.005z"/></svg>';

		$github = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.9 16.653c.4-.103.8-.103 1.199-.206 1.198-.31 2.197-.93 2.696-2.067.6-1.24.699-2.48.4-3.823-.1-.62-.4-1.033-.8-1.55-.1-.103-.1-.206-.1-.31.2-.826.2-1.549-.1-2.376 0-.103-.1-.206-.299-.206-.5 0-.899.206-1.298.413-.4.207-.7.413-.999.62-.1.103-.2.103-.3.103a9.039 9.039 0 0 0-4.693 0c-.1 0-.2 0-.3-.103-.698-.413-1.298-.827-2.096-.93-.5-.103-.5-.103-.6.413-.2.724-.2 1.447 0 2.17v.207c-.898 1.033-1.098 2.376-.898 3.616.1.413.1.723.2 1.136.499 1.447 1.497 2.273 2.995 2.687.4.103.8.206 1.298.31-.3.31-.499.826-.599 1.24 0 .103-.1.103-.1.103-.998.413-2.097.31-2.796-.827-.3-.516-.699-.93-1.398-1.033h-.5c-.199 0-.199.207-.099.31l.2.207c.499.31.898.826 1.098 1.446.4.93 1.099 1.343 2.097 1.447.4 0 .9 0 1.398-.104v1.963c0 .31-.3.517-.699.414a13.25 13.25 0 0 1-2.396-1.24c-2.996-2.17-4.594-5.166-4.394-8.989.2-4.753 3.595-8.575 8.089-9.505C15.298 1.156 20.29 4.462 21.69 9.73c1.298 5.166-1.598 10.538-6.392 12.192-.499.206-.799 0-.799-.62v-2.48c.1-.827 0-1.55-.599-2.17z"/></svg>';

		$codepen = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.874 8.773L21.86 8.7l-.014-.04c-.007-.022-.013-.043-.022-.063-.006-.014-.012-.028-.02-.04-.01-.02-.018-.04-.028-.058l-.025-.04c-.01-.018-.022-.034-.035-.05l-.03-.038c-.012-.016-.027-.03-.04-.046l-.035-.033c-.015-.014-.032-.028-.048-.04-.012-.01-.025-.02-.04-.03-.004-.004-.008-.008-.013-.01l-9.085-6.057c-.287-.19-.66-.19-.947 0L2.392 8.21l-.014.01-.04.03-.047.04-.033.034-.043.047-.03.038-.035.05-.025.04-.03.057-.018.042-.022.062c-.005.013-.01.027-.013.04l-.015.072-.007.037c-.005.036-.008.073-.008.11v6.057c0 .037.003.075.008.11l.007.038.015.07.013.043.022.063c.006.015.012.03.02.043.008.02.017.038.028.057l.024.04c.01.017.022.034.035.05l.03.038.042.047.034.033c.017.014.033.028.05.04l.038.03.014.01 9.084 6.055c.143.096.308.144.473.144s.33-.048.473-.144l9.084-6.056.014-.01.04-.03c.017-.012.033-.025.048-.04.012-.01.023-.02.034-.032l.042-.047.03-.037.035-.05.024-.04.03-.057.018-.042.022-.062c.005-.014.01-.028.013-.042.006-.023.01-.047.015-.07l.007-.038c.004-.037.007-.074.007-.11V8.92c0-.037-.003-.074-.008-.11l-.006-.037zM11.95 13.97l-3.02-2.02 3.02-2.02 3.02 2.02-3.02 2.02zm-.854-5.524l-3.703 2.477-2.99-2 6.693-4.46v3.983zm-5.24 3.504L3.72 13.38v-2.86l2.137 1.43zm1.537 1.027l3.703 2.476v3.984l-6.692-4.46 2.99-2zm5.41 2.477l3.703-2.476 2.99 2-6.693 4.46v-3.984zm5.24-3.504l2.137-1.43v2.86l-2.137-1.43zm-1.536-1.028l-3.703-2.476V4.462l6.692 4.46-2.99 2z"/></svg>';

		$icons = array(
			'facebook'   => apply_filters( 'wp_social_manager_icon', $facebook, 'facebook' ),
			'twitter'    => apply_filters( 'wp_social_manager_icon', $twitter, 'twitter' ),
			'instagram'  => apply_filters( 'wp_social_manager_icon', $instagram, 'instagram' ),
			'pinterest'  => apply_filters( 'wp_social_manager_icon', $pinterest, 'pinterest' ),
			'linkedin'   => apply_filters( 'wp_social_manager_icon', $linkedin, 'linkedin' ),
			'googleplus' => apply_filters( 'wp_social_manager_icon', $googleplus, 'googleplus' ),
			'youtube'    => apply_filters( 'wp_social_manager_icon', $youtube, 'youtube' ),
			'dribbble'   => apply_filters( 'wp_social_manager_icon', $dribbble, 'dribbble' ),
			'behance'    => apply_filters( 'wp_social_manager_icon', $behance, 'behance' ),
			'github'     => apply_filters( 'wp_social_manager_icon', $github, 'github' ),
			'codepen'    => apply_filters( 'wp_social_manager_icon', $codepen, 'codepen' )
		);

		return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
	}
}


class SettingUtilities extends Utilities {

	/**
	 * [get_social_profiles description]
	 * @return [type] [description]
	 */
	final static function get_social_profiles() {

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
	final static function get_post_types() {

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
	final static function get_button_views() {

		$types = array(
			'icon' => esc_html__( 'Icon Only', 'wp-sharing-manager' ),
			'text' => esc_html__( 'Text Only', 'wp-sharing-manager' ),
			'icon-text' => esc_html__( 'Icon and Text', 'wp-sharing-manager' )
		);

		return $types;
	}

	/**
	 * [get_buttons_location description]
	 * @return [type] [description]
	 */
	final static function get_button_locations() {

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
	final static function get_button_sites( $for = 'content' ) {

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