<?php
/**
 * Class TestWPFooter
 *
 * TODO: Add test for wp_kses.
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load WP_UnitTestCase;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "TestWPFooter" class instance.
 *
 * @since 1.0.4
 */
class TestWPFooter extends WP_UnitTestCase {

	/**
	 * The Endpoints class instance.
	 *
	 * @since 1.0.4
	 * @access protected
	 * @var WPFooter
	 */
	protected $wp_footer;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		parent::setUp();

		// Setup the plugin.
		$plugin = new Plugin();
		$plugin->initialize();

		$public = new ViewPublic( $plugin );
		$this->wp_footer = new WPFooter( $public );
	}

	/**
	 * Function to test Class methods availability.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( $this->wp_footer, 'icon_reference_svg' ),  'Class does not have method \'icon_reference_svg\'' );
	}

	/**
	 * Function to test hooks.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_hooks() {

		$this->assertEquals( -50, has_action( 'wp_footer', array( $this->wp_footer, 'icon_reference_svg' ) ) );
	}

	/**
	 * Function to `icon_reference_svg` method.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @return void
	 */
	public function test_icon_reference_svg() {

		$prefix = Helpers::get_attr_prefix();

		ob_start();
		$this->wp_footer->icon_reference_svg();
		$buffer = ob_get_clean();

		$doc = new \DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $buffer );
		$svgs = $doc->getElementsByTagName( 'svg' );
		$symbols = $doc->getElementsByTagName( 'symbol' );
		$paths = $doc->getElementsByTagName( 'path' );
		$titles = $doc->getElementsByTagName( 'title' );
		libxml_clear_errors();

		$this->assertContains( '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" display="none">', $buffer );

		$this->assertEquals( 1, $svgs->length );
		$this->assertEquals( 0, $svgs->item( 0 )->getAttribute( 'width' ) );
		$this->assertEquals( 0, $svgs->item( 0 )->getAttribute( 'height' ) );
		$this->assertEquals( 'none', $svgs->item( 0 )->getAttribute( 'display' ) );

		$this->assertEquals( 10, $symbols->length );
		$this->assertEquals( $symbols->length, $paths->length );
		$this->assertEquals( $symbols->length, $titles->length );

		// Make sure the ids are presist since we use theme to reference the icon.
		$this->assertEquals( $prefix . '-icon-facebook', $symbols->item( 0 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M19 3.998v3h-2a1 1 0 0 0-1 1v2h3v3h-3v7h-3v-7h-2v-3h2v-2.5a3.5 3.5 0 0 1 3.5-3.5H19zm1-2H4c-1.105 0-1.99.895-1.99 2l-.01 16c0 1.104.895 2 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z', $paths->item( 0 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-twitter', $symbols->item( 1 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M22 5.894a8.304 8.304 0 0 1-2.357.636 4.064 4.064 0 0 0 1.804-2.235c-.792.463-1.67.8-2.605.98A4.128 4.128 0 0 0 15.847 4c-2.266 0-4.104 1.808-4.104 4.04 0 .316.037.624.107.92a11.711 11.711 0 0 1-8.458-4.22 3.972 3.972 0 0 0-.555 2.03c0 1.401.724 2.638 1.825 3.362a4.138 4.138 0 0 1-1.858-.505v.05c0 1.958 1.414 3.59 3.29 3.961a4.169 4.169 0 0 1-1.852.07c.522 1.604 2.037 2.772 3.833 2.804a8.315 8.315 0 0 1-5.096 1.73c-.331 0-.658-.02-.979-.057A11.748 11.748 0 0 0 8.29 20c7.547 0 11.674-6.155 11.674-11.493 0-.175-.004-.349-.011-.522A8.265 8.265 0 0 0 22 5.894z', $paths->item( 1 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-instagram', $symbols->item( 2 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M12 3.81c2.667 0 2.983.01 4.036.06.974.043 1.503.206 1.855.343.467.18.8.398 1.15.747.35.35.566.682.747 1.15.137.35.3.88.344 1.854.05 1.053.06 1.37.06 4.036s-.01 2.983-.06 4.036c-.043.974-.206 1.503-.343 1.855-.18.467-.398.8-.747 1.15-.35.35-.682.566-1.15.747-.35.137-.88.3-1.854.344-1.053.05-1.37.06-4.036.06s-2.983-.01-4.036-.06c-.974-.043-1.503-.206-1.855-.343-.467-.18-.8-.398-1.15-.747-.35-.35-.566-.682-.747-1.15-.137-.35-.3-.88-.344-1.854-.05-1.053-.06-1.37-.06-4.036s.01-2.983.06-4.036c.044-.974.206-1.503.343-1.855.18-.467.398-.8.747-1.15.35-.35.682-.566 1.15-.747.35-.137.88-.3 1.854-.344 1.053-.05 1.37-.06 4.036-.06m0-1.8c-2.713 0-3.053.012-4.118.06-1.064.05-1.79.22-2.425.465-.657.256-1.214.597-1.77 1.153-.555.555-.896 1.112-1.152 1.77-.246.634-.415 1.36-.464 2.424-.047 1.065-.06 1.405-.06 4.118s.012 3.053.06 4.118c.05 1.063.218 1.79.465 2.425.255.657.597 1.214 1.152 1.77.555.554 1.112.896 1.77 1.15.634.248 1.36.417 2.424.465 1.066.05 1.407.06 4.12.06s3.052-.01 4.117-.06c1.063-.05 1.79-.217 2.425-.464.657-.255 1.214-.597 1.77-1.152.554-.555.896-1.112 1.15-1.77.248-.634.417-1.36.465-2.424.05-1.065.06-1.406.06-4.118s-.01-3.053-.06-4.118c-.05-1.063-.217-1.79-.464-2.425-.255-.657-.597-1.214-1.152-1.77-.554-.554-1.11-.896-1.768-1.15-.635-.248-1.362-.417-2.425-.465-1.064-.05-1.404-.06-4.117-.06zm0 4.86C9.167 6.87 6.87 9.17 6.87 12s2.297 5.13 5.13 5.13 5.13-2.298 5.13-5.13S14.832 6.87 12 6.87zm0 8.46c-1.84 0-3.33-1.49-3.33-3.33S10.16 8.67 12 8.67s3.33 1.49 3.33 3.33-1.49 3.33-3.33 3.33zm5.332-9.86c-.662 0-1.2.536-1.2 1.198s.538 1.2 1.2 1.2c.662 0 1.2-.538 1.2-1.2s-.538-1.2-1.2-1.2z', $paths->item( 2 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-pinterest', $symbols->item( 3 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M12 2C6.479 2 2 6.478 2 12a10 10 0 0 0 6.355 9.314c-.087-.792-.166-2.005.035-2.87.183-.78 1.173-4.97 1.173-4.97s-.3-.6-.3-1.486c0-1.387.808-2.429 1.81-2.429.854 0 1.265.642 1.265 1.41 0 .858-.545 2.14-.827 3.33-.238.996.5 1.806 1.48 1.806 1.776 0 3.144-1.873 3.144-4.578 0-2.394-1.72-4.068-4.178-4.068-2.845 0-4.513 2.134-4.513 4.34 0 .86.329 1.78.741 2.282.083.1.094.187.072.287-.075.315-.245.995-.28 1.134-.043.183-.143.223-.334.134-1.248-.581-2.03-2.408-2.03-3.875 0-3.156 2.292-6.05 6.609-6.05 3.468 0 6.165 2.47 6.165 5.775 0 3.446-2.175 6.221-5.191 6.221-1.013 0-1.965-.527-2.292-1.15l-.625 2.378c-.225.869-.834 1.957-1.242 2.621.937.29 1.931.447 2.962.447C17.521 22.003 22 17.525 22 12.002s-4.479-10-10-10V2z', $paths->item( 3 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-linkedin', $symbols->item( 4 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M19 18.998h-3v-5.3a1.5 1.5 0 0 0-3 0v5.3h-3v-9h3v1.2c.517-.838 1.585-1.4 2.5-1.4a3.5 3.5 0 0 1 3.5 3.5v5.7zM6.5 8.31a1.812 1.812 0 1 1-.003-3.624A1.812 1.812 0 0 1 6.5 8.31zM8 18.998H5v-9h3v9zm12-17H4c-1.106 0-1.99.895-1.99 2l-.01 16a2 2 0 0 0 2 2h16c1.103 0 2-.896 2-2v-16a2 2 0 0 0-2-2z', $paths->item( 4 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-googleplus', $symbols->item( 5 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M22 11h-2V9h-2v2h-2v2h2v2h2v-2h2v-2zm-13.869.143V13.2h3.504c-.175.857-1.051 2.571-3.504 2.571A3.771 3.771 0 0 1 4.365 12a3.771 3.771 0 0 1 3.766-3.771c1.227 0 2.015.514 2.453.942l1.664-1.542C11.198 6.6 9.796 6 8.131 6 4.715 6 2 8.657 2 12s2.715 6 6.131 6C11.635 18 14 15.6 14 12.171c0-.428 0-.685-.088-1.028h-5.78z', $paths->item( 5 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-youtube', $symbols->item( 6 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M21.813 7.996s-.196-1.38-.796-1.988c-.76-.798-1.615-.802-2.006-.848-2.8-.203-7.005-.203-7.005-.203h-.01s-4.202 0-7.005.203c-.392.047-1.245.05-2.007.848-.6.608-.796 1.988-.796 1.988s-.2 1.62-.2 3.24v1.52c0 1.62.2 3.24.2 3.24s.195 1.38.796 1.99c.762.797 1.762.77 2.208.855 1.603.155 6.81.202 6.81.202s4.208-.006 7.01-.21c.39-.046 1.245-.05 2.006-.847.6-.608.796-1.988.796-1.988s.2-1.62.2-3.24v-1.52c0-1.62-.2-3.24-.2-3.24zm-11.88 6.602V8.97l5.41 2.824-5.41 2.804z', $paths->item( 6 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-reddit', $symbols->item( 7 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M9.2 14.935c-.897 0-1.626-.73-1.626-1.625 0-.896.73-1.624 1.625-1.624s1.623.73 1.623 1.624c0 .896-.728 1.625-1.624 1.625zm11.756-1.133c.024.186.037.37.037.547 0 3.377-4.042 6.126-9.01 6.126s-9.008-2.748-9.008-6.127c0-.185.014-.377.04-.57-.636-.47-1.012-1.207-1.012-2 0-1.373 1.117-2.49 2.49-2.49.537 0 1.058.174 1.486.494 1.543-.94 3.513-1.487 5.583-1.552l1.587-4.51 3.803.91c.36-.68 1.06-1.108 1.837-1.108 1.147 0 2.08.933 2.08 2.08 0 1.146-.934 2.078-2.08 2.078-1.078 0-1.97-.827-2.07-1.88l-2.802-.67L12.82 8.25c1.923.12 3.747.663 5.187 1.544.43-.327.957-.505 1.5-.505 1.374 0 2.49 1.116 2.49 2.49.002.81-.385 1.554-1.04 2.022zm-17.76-2.02c0 .248.073.49.206.698.328-.696.842-1.352 1.51-1.927-.134-.046-.276-.07-.42-.07-.715 0-1.297.582-1.297 1.298zm16.603 2.567c0-2.72-3.507-4.935-7.817-4.935s-7.816 2.213-7.816 4.934 3.506 4.932 7.816 4.932c4.31 0 7.816-2.213 7.816-4.933zm-.737-3.79c.672.583 1.188 1.246 1.514 1.95.147-.215.227-.468.227-.73 0-.716-.582-1.298-1.297-1.298-.152 0-.302.028-.444.08zm-1.16-4.957c0 .488.396.886.885.886.49 0 .886-.398.886-.887 0-.49-.397-.886-.886-.886-.488 0-.886.398-.886.886zm-2.92 10.603c-.162 0-.313.064-.422.174-.03.03-.81.767-2.592.767-1.76 0-2.468-.735-2.47-.737-.113-.13-.277-.205-.45-.205-.142 0-.28.05-.387.143-.12.104-.194.248-.206.407-.012.16.038.314.142.434.027.03.288.326.84.607.705.36 1.558.543 2.534.543.97 0 1.833-.18 2.567-.534.568-.274.85-.56.882-.593.223-.235.216-.61-.017-.84-.116-.107-.263-.167-.42-.167zm-.02-4.52c-.895 0-1.624.73-1.624 1.624 0 .896.728 1.625 1.624 1.625.896 0 1.624-.73 1.624-1.625 0-.896-.728-1.624-1.624-1.624z', $paths->item( 7 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-tumblr', $symbols->item( 8 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M13.758 18.358c-.335-.196-.647-.536-.77-.866-.125-.33-.106-1-.106-2.16v-5.14h4.672V6.62h-4.672V1.986h-2.874c-.13 1.034-.363 1.886-.707 2.552-.344.67-.796 1.244-1.364 1.72-.564.474-1.486.842-2.278 1.098v2.837H8.4v7.024c0 .918.1 1.616.29 2.1.196.48.54.937 1.043 1.366.503.424 1.11.755 1.817.987.708.228 1.253.345 2.18.345.81 0 1.57-.084 2.268-.242.7-.163 1.48-.447 2.343-.848v-3.163c-1.01.66-2.03.987-3.05.987-.572.01-1.085-.127-1.532-.392z', $paths->item( 8 )->getAttribute( 'd' ) );

		$this->assertEquals( $prefix . '-icon-email', $symbols->item( 9 )->getAttribute( 'id' ) );
		$this->assertEquals( 'M13.235 12.565c-.425.326-.99.505-1.59.505s-1.168-.18-1.593-.505L2.008 6.402v11.665c0 .59.48 1.07 1.07 1.07h17.844c.59 0 1.07-.48 1.07-1.07V5.933c0-.025-.005-.048-.006-.072l-8.75 6.705zm-1.16-.89l8.886-6.808c-.012 0-.025-.004-.038-.004H3.078c-.203 0-.39.06-.552.157l8.686 6.656c.23.176.632.177.863 0z', $paths->item( 9 )->getAttribute( 'd' ) );
	}

	/**
	 * Function to test filter hook in `get_svg_symbols` method.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_svg_symbols_filters() {

		$symbols = $this->wp_footer->get_svg_symbols();

		$this->assertArrayHasKey( 'facebook', $symbols );
		$this->assertArrayHasKey( 'twitter', $symbols );
		$this->assertArrayHasKey( 'instagram', $symbols );
		$this->assertArrayHasKey( 'pinterest', $symbols );
		$this->assertArrayHasKey( 'linkedin', $symbols );
		$this->assertArrayHasKey( 'googleplus', $symbols );
		$this->assertArrayHasKey( 'youtube', $symbols );
		$this->assertArrayHasKey( 'reddit', $symbols );
		$this->assertArrayHasKey( 'email', $symbols );

		// Remove a few symbols.
		add_filter( 'ninecodes_social_manager_svg_symbols', function( $svg_symbols, $args ) {

			unset( $svg_symbols['facebook'] );
			unset( $svg_symbols['twitter'] );
			unset( $svg_symbols['instagram'] );

			return $svg_symbols;
		}, 10, 2 );

		$symbols = $this->wp_footer->get_svg_symbols();

		$this->assertArrayNotHasKey( 'facebook', $symbols );
		$this->assertArrayNotHasKey( 'twitter', $symbols );
		$this->assertArrayNotHasKey( 'instagram', $symbols );

		// Add a new symbol (Please ignore the svg markup).
		add_filter( 'ninecodes_social_manager_svg_symbols', function( $svg_symbols, $args ) {

			$svg_symbols['ello'] = '<g id="Capa_3"><g><circle cx="151.25" cy="151.25" r="151.25"/><path fill="none" stroke="#FFFFFF" stroke-width="17" stroke-linecap="round" stroke-linejoin="bevel" stroke-miterlimit="10" d="
			M72,171c20.766,83.064,136,81.5,158.5-1"/></g></g>';

			return $svg_symbols;
		}, 10, 2 );

		$symbols = $this->wp_footer->get_svg_symbols();

		$this->assertArrayHasKey( 'ello', $symbols );
	}
}
