<?php
/**
 * Public: WPFooter class
 *
 * @package SocialManager
 * @subpackage Public\WPFooter
 */

namespace NineCodes\SocialManager;

if ( ! defined( 'WPINC' ) ) { // If this file is called directly.
	die; // Abort.
}

/**
 * The class to generate elements or tags in the wp_footer.
 *
 * @since 1.0.0
 */
final class WPFooter {

	/**
	 * The element prefix attribute.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function __construct() {

		$this->prefix = Helpers::get_attr_prefix();
		$this->hooks();
	}

	/**
	 * Run Filters and Actions required.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 - Set the 'icon_reference_svg' priority higher (-30) to render the before anything else,
	 *				  which should allow the icons to rendere ASAP without being blocked by slow JavaScript files.
	 * @access protected
	 *
	 * @return void
	 */
	protected function hooks() {

		add_action( 'wp_footer', array( $this, 'icon_reference_svg' ), -50 );
	}

	/**
	 * Function to retrieve the SGV symbol of the icons.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return array List of the svg symbol in an array.
	 */
	public function get_svg_symbols() {

		$prefix = esc_attr( $this->prefix );

		$symbol_facebook = '<symbol id="' . $prefix . '-icon-facebook" viewBox="0 0 24 24"><title>Facebook</title><path fill-rule="evenodd" d="M19 3.998v3h-2a1 1 0 0 0-1 1v2h3v3h-3v7h-3v-7h-2v-3h2v-2.5a3.5 3.5 0 0 1 3.5-3.5H19zm1-2H4c-1.105 0-1.99.895-1.99 2l-.01 16c0 1.104.895 2 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z"/></symbol>';

		$symbol_twitter = '<symbol id="' . $prefix . '-icon-twitter" viewBox="0 0 24 24"><title>Twitter</title><path fill-rule="evenodd" d="M22 5.894a8.304 8.304 0 0 1-2.357.636 4.064 4.064 0 0 0 1.804-2.235c-.792.463-1.67.8-2.605.98A4.128 4.128 0 0 0 15.847 4c-2.266 0-4.104 1.808-4.104 4.04 0 .316.037.624.107.92a11.711 11.711 0 0 1-8.458-4.22 3.972 3.972 0 0 0-.555 2.03c0 1.401.724 2.638 1.825 3.362a4.138 4.138 0 0 1-1.858-.505v.05c0 1.958 1.414 3.59 3.29 3.961a4.169 4.169 0 0 1-1.852.07c.522 1.604 2.037 2.772 3.833 2.804a8.315 8.315 0 0 1-5.096 1.73c-.331 0-.658-.02-.979-.057A11.748 11.748 0 0 0 8.29 20c7.547 0 11.674-6.155 11.674-11.493 0-.175-.004-.349-.011-.522A8.265 8.265 0 0 0 22 5.894z"/></symbol>';

		$symbol_instagram = '<symbol id="' . $prefix . '-icon-instagram" viewBox="0 0 24 24"><title>Instagram</title><path fill-rule="evenodd" d="M12 3.81c2.667 0 2.983.01 4.036.06.974.043 1.503.206 1.855.343.467.18.8.398 1.15.747.35.35.566.682.747 1.15.137.35.3.88.344 1.854.05 1.053.06 1.37.06 4.036s-.01 2.983-.06 4.036c-.043.974-.206 1.503-.343 1.855-.18.467-.398.8-.747 1.15-.35.35-.682.566-1.15.747-.35.137-.88.3-1.854.344-1.053.05-1.37.06-4.036.06s-2.983-.01-4.036-.06c-.974-.043-1.503-.206-1.855-.343-.467-.18-.8-.398-1.15-.747-.35-.35-.566-.682-.747-1.15-.137-.35-.3-.88-.344-1.854-.05-1.053-.06-1.37-.06-4.036s.01-2.983.06-4.036c.044-.974.206-1.503.343-1.855.18-.467.398-.8.747-1.15.35-.35.682-.566 1.15-.747.35-.137.88-.3 1.854-.344 1.053-.05 1.37-.06 4.036-.06m0-1.8c-2.713 0-3.053.012-4.118.06-1.064.05-1.79.22-2.425.465-.657.256-1.214.597-1.77 1.153-.555.555-.896 1.112-1.152 1.77-.246.634-.415 1.36-.464 2.424-.047 1.065-.06 1.405-.06 4.118s.012 3.053.06 4.118c.05 1.063.218 1.79.465 2.425.255.657.597 1.214 1.152 1.77.555.554 1.112.896 1.77 1.15.634.248 1.36.417 2.424.465 1.066.05 1.407.06 4.12.06s3.052-.01 4.117-.06c1.063-.05 1.79-.217 2.425-.464.657-.255 1.214-.597 1.77-1.152.554-.555.896-1.112 1.15-1.77.248-.634.417-1.36.465-2.424.05-1.065.06-1.406.06-4.118s-.01-3.053-.06-4.118c-.05-1.063-.217-1.79-.464-2.425-.255-.657-.597-1.214-1.152-1.77-.554-.554-1.11-.896-1.768-1.15-.635-.248-1.362-.417-2.425-.465-1.064-.05-1.404-.06-4.117-.06zm0 4.86C9.167 6.87 6.87 9.17 6.87 12s2.297 5.13 5.13 5.13 5.13-2.298 5.13-5.13S14.832 6.87 12 6.87zm0 8.46c-1.84 0-3.33-1.49-3.33-3.33S10.16 8.67 12 8.67s3.33 1.49 3.33 3.33-1.49 3.33-3.33 3.33zm5.332-9.86c-.662 0-1.2.536-1.2 1.198s.538 1.2 1.2 1.2c.662 0 1.2-.538 1.2-1.2s-.538-1.2-1.2-1.2z"/></symbol>';

		$symbol_pinterest = '<symbol id="' . $prefix . '-icon-pinterest" viewBox="0 0 24 24"><title>Pinterest</title><path fill-rule="evenodd" d="M12 2C6.479 2 2 6.478 2 12a10 10 0 0 0 6.355 9.314c-.087-.792-.166-2.005.035-2.87.183-.78 1.173-4.97 1.173-4.97s-.3-.6-.3-1.486c0-1.387.808-2.429 1.81-2.429.854 0 1.265.642 1.265 1.41 0 .858-.545 2.14-.827 3.33-.238.996.5 1.806 1.48 1.806 1.776 0 3.144-1.873 3.144-4.578 0-2.394-1.72-4.068-4.178-4.068-2.845 0-4.513 2.134-4.513 4.34 0 .86.329 1.78.741 2.282.083.1.094.187.072.287-.075.315-.245.995-.28 1.134-.043.183-.143.223-.334.134-1.248-.581-2.03-2.408-2.03-3.875 0-3.156 2.292-6.05 6.609-6.05 3.468 0 6.165 2.47 6.165 5.775 0 3.446-2.175 6.221-5.191 6.221-1.013 0-1.965-.527-2.292-1.15l-.625 2.378c-.225.869-.834 1.957-1.242 2.621.937.29 1.931.447 2.962.447C17.521 22.003 22 17.525 22 12.002s-4.479-10-10-10V2z"/></symbol>';

		$symbol_linkedin = '<symbol id="' . $prefix . '-icon-linkedin" viewBox="0 0 24 24"><title>LinkedIn</title><path fill-rule="evenodd" d="M19 18.998h-3v-5.3a1.5 1.5 0 0 0-3 0v5.3h-3v-9h3v1.2c.517-.838 1.585-1.4 2.5-1.4a3.5 3.5 0 0 1 3.5 3.5v5.7zM6.5 8.31a1.812 1.812 0 1 1-.003-3.624A1.812 1.812 0 0 1 6.5 8.31zM8 18.998H5v-9h3v9zm12-17H4c-1.106 0-1.99.895-1.99 2l-.01 16a2 2 0 0 0 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z"/></symbol>';

		$symbol_googleplus = '<symbol id="' . $prefix . '-icon-googleplus" viewBox="0 0 24 24"><title>Google+</title><path fill-rule="evenodd" d="M22 11h-2V9h-2v2h-2v2h2v2h2v-2h2v-2zm-13.869.143V13.2h3.504c-.175.857-1.051 2.571-3.504 2.571A3.771 3.771 0 0 1 4.365 12a3.771 3.771 0 0 1 3.766-3.771c1.227 0 2.015.514 2.453.942l1.664-1.542C11.198 6.6 9.796 6 8.131 6 4.715 6 2 8.657 2 12s2.715 6 6.131 6C11.635 18 14 15.6 14 12.171c0-.428 0-.685-.088-1.028h-5.78z"/></symbol>';

		$symbol_youtube = '<symbol id="' . $prefix . '-icon-youtube" viewBox="0 0 24 24"><title>Youtube</title><path fill-rule="evenodd" d="M21.813 7.996s-.196-1.38-.796-1.988c-.76-.798-1.615-.802-2.006-.848-2.8-.203-7.005-.203-7.005-.203h-.01s-4.202 0-7.005.203c-.392.047-1.245.05-2.007.848-.6.608-.796 1.988-.796 1.988s-.2 1.62-.2 3.24v1.52c0 1.62.2 3.24.2 3.24s.195 1.38.796 1.99c.762.797 1.762.77 2.208.855 1.603.155 6.81.202 6.81.202s4.208-.006 7.01-.21c.39-.046 1.245-.05 2.006-.847.6-.608.796-1.988.796-1.988s.2-1.62.2-3.24v-1.52c0-1.62-.2-3.24-.2-3.24zm-11.88 6.602V8.97l5.41 2.824-5.41 2.804z"/></symbol>';

		$symbol_email = '<symbol id="' . $prefix . '-icon-email" viewBox="0 0 24 24"><title>Email</title><path fill-rule="evenodd" d="M13.235 12.565c-.425.326-.99.505-1.59.505s-1.168-.18-1.593-.505L2.008 6.402v11.665c0 .59.48 1.07 1.07 1.07h17.844c.59 0 1.07-.48 1.07-1.07V5.933c0-.025-.005-.048-.006-.072l-8.75 6.705zm-1.16-.89l8.886-6.808c-.012 0-.025-.004-.038-.004H3.078c-.203 0-.39.06-.552.157l8.686 6.656c.23.176.632.177.863 0z"/></symbol>';

		$symbols = apply_filters( 'ninecodes_social_manager_svg_symbols', array(
				'facebook' => $symbol_facebook,
				'twitter' => $symbol_twitter,
				'instagram' => $symbol_instagram,
				'pinterest' => $symbol_pinterest,
				'linkedin' => $symbol_linkedin,
				'googleplus' => $symbol_googleplus,
				'youtube' => $symbol_youtube,
				'email' => $symbol_email,
			), array(
				'attr_prefix' => $prefix,
			)
		);

		return $symbols;
	}

	/**
	 * Display reference of SVG Icons.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return void
	 */
	public function icon_reference_svg() {

		/**
		 * Hook to filter all the icons.
		 *
		 * @var array
		 */
		$symbols = $this->get_svg_symbols();

		if ( 0 !== count( $symbols ) ) : ?>
<!--
	START: SVG Icon Sprites (Social Media Manager by NineCodes)
	Read: https://css-tricks.com/svg-sprites-use-better-icon-fonts/
-->
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" display="none">
<?php foreach ( $symbols as $key => $symbol ) {
	echo wp_kses( $symbol, array(
		'title' => true,
		'symbol' => array(
			'id' => true,
			'viewbox' => true,
		),
		'path' => array(
			'd' => true,
			'fill-rule' => true,
		),
	) );
} ?>
</svg>
<!-- END: SVG Icon Sprites -->
		<?php
		endif;
	}
}
