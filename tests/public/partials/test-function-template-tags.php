<?php
/**
 * Class TestTemplateTagFunctions
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
	public function test_get_the_author_social_profiles() {

		add_user_meta( $this->user_id, $this->option_slug, array(
			'facebook' => 'zuck',
			'twitter' => 'jack',
			'instagram' => 'jane',
			'googleplus' => '+john',
			'github' => 'tfirdaus',
		) );

		$social_profiles = get_the_author_social_profiles( $this->user_id );

		$doc = new \DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $social_profiles );

		$href = array();
		$anchors = $doc->getElementsByTagName( 'a' );

		foreach ( $anchors as $key => $anchor ) {
			$href[] = $anchor->getAttribute( 'href' );
		}

		$this->assertContains( 'https://www.facebook.com/zuck', $href );
		$this->assertContains( 'https://twitter.com/jack', $href );
		$this->assertContains( 'https://instagram.com/jane', $href );
		$this->assertContains( 'https://plus.google.com/+john', $href );
		$this->assertContains( 'https://github.com/tfirdaus', $href );
	}
}
