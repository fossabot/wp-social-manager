<?php
/**
 * Class TestTemplateTagFunctions
 *
 * @package NineCodes\SocialMediaManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialMediaManager;

/**
 * Load WP_UnitTestCase;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "TestTemplateTagFunctions" class instance.
 *
 * @since 1.1.0
 */
class TestTemplateTagFunctions extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var string
	 */
	protected $option_slug;

	/**
	 * A User ID
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var integer
	 */
	protected $user_id;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {

		$this->plugin = new Plugin();
		$this->plugin->initialize();

		$this->option_slug = $this->plugin->get_opts();

		$this->user_id = $this->factory->user->create( array(
			'role' => 'author',
			'display_name' => 'Foo',
			'user_nicename' => 'bar',
			'user_login' => 'test_author',
			'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
		) );
	}

	/**
	 * Function to test the 'get_the_author_social_profiles'.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile() {

		update_option( 'ncsocman_profiles', array(
			'facebook' => 'zuck',
			'twitter' => 'jack',
			'instagram' => 'jane',
			'googleplus' => '+john',
			'github' => 'tfirdaus',
		) );

		$site_profiles = get_the_site_social_profiles();

		$doc = new \DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $site_profiles );

		$href = array();
		$anchor_html = array();

		$prefix = Helpers::get_attr_prefix();
		$anchors = $doc->getElementsByTagName( 'a' );

		foreach ( $anchors as $key => $anchor ) {

			$href[] = $anchor->getAttribute( 'href' );
			$anchor_html[] = $doc->saveXML( $anchor );
		}

		// Check if the href url pointing to the correct address.
		$this->assertContains( 'https://www.facebook.com/zuck', $href );
		$this->assertContains( 'https://twitter.com/jack', $href );
		$this->assertContains( 'https://instagram.com/jane', $href );
		$this->assertContains( 'https://plus.google.com/+john', $href );
		$this->assertContains( 'https://github.com/tfirdaus', $href );

		// Check the HTML markup. (Default: Icon).
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-facebook\" href=\"https://www.facebook.com/zuck\" target=\"_blank\"><svg><use xlink:href=\"#{$prefix}-icon-facebook\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-twitter\" href=\"https://twitter.com/jack\" target=\"_blank\"><svg><use xlink:href=\"#{$prefix}-icon-twitter\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-instagram\" href=\"https://instagram.com/jane\" target=\"_blank\"><svg><use xlink:href=\"#{$prefix}-icon-instagram\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-googleplus\" href=\"https://plus.google.com/+john\" target=\"_blank\"><svg><use xlink:href=\"#{$prefix}-icon-googleplus\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-github\" href=\"https://github.com/tfirdaus\" target=\"_blank\"><svg><use xlink:href=\"#{$prefix}-icon-github\"/></svg></a>", $anchor_html );

		delete_option( 'ncsocman_profiles' );
	}

	/**
	 * Function to test the 'get_the_author_social_profiles' set with 'text' view.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile_view_text() {

		update_option( 'ncsocman_profiles', array(
			'facebook' => 'foo',
			'twitter' => 'foo',
			'instagram' => 'foo',
			'googleplus' => '+foo',
			'github' => 'foo',
		) );

		$site_profiles = get_the_site_social_profiles( array( 'view' => 'text' ) );

		$doc = new \DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $site_profiles );

		$anchor_html = array();

		$prefix = Helpers::get_attr_prefix();
		$anchors = $doc->getElementsByTagName( 'a' );

		foreach ( $anchors as $key => $anchor ) {
			$anchor_html[] = $doc->saveXML( $anchor );
		}

		// Check the HTML markup. (Text).
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-facebook\" href=\"https://www.facebook.com/foo\" target=\"_blank\">Facebook</a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-twitter\" href=\"https://twitter.com/foo\" target=\"_blank\">Twitter</a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-instagram\" href=\"https://instagram.com/foo\" target=\"_blank\">Instagram</a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-googleplus\" href=\"https://plus.google.com/+foo\" target=\"_blank\">Google+</a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-github\" href=\"https://github.com/foo\" target=\"_blank\">Github</a>", $anchor_html );

		delete_option( 'ncsocman_profiles' );
	}

	/**
	 * Function to test the 'get_the_author_social_profiles' set with 'icon-text' view.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile_view_text_icon() {

		update_option( 'ncsocman_profiles', array(
			'facebook' => 'yo',
		) );

		$site_profiles = get_the_site_social_profiles( array( 'view' => 'icon-text' ) );

		$doc = new \DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $site_profiles );

		$anchor_html = array();

		$prefix = Helpers::get_attr_prefix();
		$anchors = $doc->getElementsByTagName( 'a' );

		foreach ( $anchors as $key => $anchor ) {
			$anchor_html[] = $doc->saveXML( $anchor );
		}

		// Check the HTML markup. (Icon Text).
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-facebook\" href=\"https://www.facebook.com/yo\" target=\"_blank\"><span class=\"{$prefix}-profiles__item-icon\"><svg><use xlink:href=\"#{$prefix}-icon-facebook\"/></svg></span><span class=\"{$prefix}-profiles__item-text\">Facebook</span></a>", $anchor_html );

		delete_option( 'ncsocman_profiles' );
	}
}
