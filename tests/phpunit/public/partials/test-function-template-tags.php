<?php
/**
 * Class Test_Function_Template_Tags
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
 * The class to test the "Test_Function_Template_Tags" class instance.
 *
 * @since 1.1.0
 */
class Test_Function_Template_Tags extends WP_UnitTestCase {

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
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();

		$this->user_id = $this->factory->user->create( array(
			'role' => 'author',
			'display_name' => 'Foo',
			'user_nicename' => 'bar',
			'user_login' => 'test_author',
			'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
		) );
	}

	/**
	 * Function to test the 'get_the_author_social_profile'.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile() {

		update_option( $this->plugin->options['profile'], array(
			'facebook' => 'foo',
			'twitter' => 'foo',
			'instagram' => 'foo',
			'googleplus' => '+foo',
		) );

		$site_profiles = get_the_site_social_profile();

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
		$this->assertContains( 'https://www.facebook.com/foo', $href );
		$this->assertContains( 'https://twitter.com/foo', $href );
		$this->assertContains( 'https://instagram.com/foo', $href );
		$this->assertContains( 'https://plus.google.com/+foo', $href );

		// Check the HTML markup. (Default: Icon).
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-facebook\" href=\"https://www.facebook.com/foo\" target=\"_blank\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-facebook\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-twitter\" href=\"https://twitter.com/foo\" target=\"_blank\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-twitter\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-instagram\" href=\"https://instagram.com/foo\" target=\"_blank\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-instagram\"/></svg></a>", $anchor_html );
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-googleplus\" href=\"https://plus.google.com/+foo\" target=\"_blank\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-googleplus\"/></svg></a>", $anchor_html );

		delete_option( $this->plugin->options['profile'] );
	}

	/**
	 * Function to test the 'get_the_author_social_profile' set with 'text' view.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile_view_text() {

		update_option( $this->plugin->options['profile'], array(
			'facebook' => 'foo',
			'twitter' => 'foo',
			'instagram' => 'foo',
			'googleplus' => '+foo',
		) );

		$site_profiles = get_the_site_social_profile( array(
			'view' => 'text',
		) );

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

		delete_option( $this->plugin->options['profile'] );
	}

	/**
	 * Function to test the 'get_the_author_social_profile' set with 'icon_text' view.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_site_social_profile_view_text_icon() {

		update_option( $this->plugin->options['profile'], array(
			'facebook' => 'yo',
			'twitter' => 'yeap',
		) );

		$site_profiles = get_the_site_social_profile( array(
			'view' => 'icon_text',
		) );

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
		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-facebook\" href=\"https://www.facebook.com/yo\" target=\"_blank\"><span class=\"{$prefix}-profiles__item-icon\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-facebook\"/></svg></span><span class=\"{$prefix}-profiles__item-text\">Facebook</span></a>", $anchor_html );

		$this->assertContains( "<a class=\"{$prefix}-profiles__item item-twitter\" href=\"https://twitter.com/yeap\" target=\"_blank\"><span class=\"{$prefix}-profiles__item-icon\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-twitter\"/></svg></span><span class=\"{$prefix}-profiles__item-text\">Twitter</span></a>", $anchor_html );

		delete_option( $this->plugin->options['profile'] );
	}

	/**
	 * Function to test the 'get_the_author_social_profile'.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_the_author_social_profile() {

		$prefix = Helpers::get_attr_prefix();
		$user_id = $this->factory()->user->create( array(
			'display_name' => 'Foo',
			'role' => 'administrator',
		) );

		update_user_meta( $user_id, $this->plugin->option_slug, array(
			'facebook' => 'zuck',
		) );

		$output = "<div class=\"{$prefix}-profile-author\"><a class=\"{$prefix}-profile-author__item item-facebook\" href=\"https://www.facebook.com/zuck\" target=\"_blank\" rel=\"nofollow\" title=\"Follow Foo on Facebook\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-facebook\" /></svg></a></div>";

		$this->assertContains( $output, (string) get_the_author_social_profile( $user_id ) );
	}
}
