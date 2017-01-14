<?php
/**
 * Public: Template Tags Functions
 *
 * @package SocialManager
 * @subpackage Template Tags
 */

use \NineCodes\SocialManager;

if ( ! function_exists( 'get_the_author_social_profiles' ) ) {

	/**
	 * Function to retrieve the author social media profile links.
	 *
	 * @since 1.1.0
	 *
	 * @param integer $user_id The Author ID.
	 * @return string Formatted HTML of the author social profile links.
	 */
	function get_the_author_social_profiles( $user_id = null ) {

		$return = '';

		if ( ! $user_id ) {
			global $authordata;
			$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
		}

		$author_profiles = get_the_author_meta( 'ncsocman', $user_id );

		if ( is_array( $author_profiles ) && ! empty( $author_profiles ) ) :

			$author_name = get_the_author_meta( 'display_name', $user_id );

			$profiles = SocialManager\Options::social_profiles();
			$icons = SocialManager\Helpers::get_social_icons();
			$prefix = SocialManager\Helpers::get_attr_prefix();

			$return = "<div class=\"{$prefix}-profiles-author\">";
			foreach ( $author_profiles as $site => $username ) :

				if ( empty( $username ) ) {
					continue;
				}

				$url = esc_url( trailingslashit( $profiles[ $site ]['url'] ) . $username );

				/* translators: %1$s - The author name. %2$s - The social media label. */
				$title = sprintf( esc_html__( 'Follow %1$s on %2$s', 'ninecodes-social-manager' ), $author_name, $profiles[ $site ]['label'] );

				$return .= "<a class=\"{$prefix}-profiles-author__item item-{$site}\" href=\"{$url}\" target=\"_blank\" rel=\"nofollow\" title=\"{$title}\">{$icons[ $site ]}</a>";
		 	endforeach;
			$return .= '</div>';
		endif;

		return $return;
	}
}

if ( ! function_exists( 'the_author_social_profiles' ) ) {

	/**
	 * Function to print the author social media profile links.
	 *
	 * @since 1.1.0
	 *
	 * @param integer $user_id The Author ID.
	 * @return void
	 */
	function the_author_social_profiles( $user_id = null ) {

		$return = get_the_author_social_profiles();

		echo wp_kses( $return, array(
			'div' => array(
				'class' => true,
				'id' => true,
			),
			'a' => array(
				'class' => true,
				'id' => true,
				'href' => true,
				'target' => true,
				'rel' => true,
				'title' => true,
			),
			'svg' => array(
				'xmlns' => true,
				'viewbox' => true,
			),
			'title' => true,
			'symbol' => array(
				'id' => true,
				'viewbox' => true,
			),
			'path' => array(
				'd' => true,
				'fill-rule' => true,
			),
			'use' => array(
				'xlink:href' => true,
			),
		) );
	}
}
