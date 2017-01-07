<?php
/**
 * Class TestWPHead
 *
 * TODO: Add tests for the Filters Hooks.
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
 * The class to test the "TestWPHead" class instance.
 *
 * @since 1.0.6
 */
class TestWPHead extends WP_UnitTestCase {

	/**
	 * The unique identifier or prefix for database names.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var string
	 */
	protected $option_slug;

	/**
	 * The WPHead class instance.
	 *
	 * @since 1.0.6
	 * @access protected
	 * @var WPHead
	 */
	protected $wp_head;

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

		$this->option_slug = $plugin->get_opts();

		// The Class instance to test.
		$this->wp_head = new WPHead( $plugin );
		$this->wp_head->setups();
	}

	/**
	 * Function to test Class properties / attributes.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @return void
	 */
	public function test_properties() {

		$this->assertClassHasAttribute( 'plugin', WPHead::class );
		$this->assertClassHasAttribute( 'metas', WPHead::class );
		$this->assertClassHasAttribute( 'locale', WPHead::class );
	}

	/**
	 * Function to test Class methods availability.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @return void
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( $this->wp_head, 'hooks' ),  'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'setups' ),  'Class does not have method \'setups\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_meta_tags' ),  'Class does not have method \'site_meta_tags\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_meta_tags' ),  'Class does not have method \'post_meta_tags\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_open_graph' ),  'Class does not have method \'site_open_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_twitter_cards' ),  'Class does not have method \'site_twitter_cards\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_open_graph' ),  'Class does not have method \'post_open_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_facebook_graph' ),  'Class does not have method \'post_facebook_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_twitter_cards' ),  'Class does not have method \'post_twitter_cards\'' );
	}

	/**
	 * Function to test hooks.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @return void
	 */
	public function test_hooks() {

		$this->assertEquals( -10, has_action( 'wp', array( $this->wp_head, 'setups' ) ) );
		$this->assertEquals( -10, has_action( 'wp_head', array( $this->wp_head, 'site_meta_tags' ) ) );
		$this->assertEquals( -10, has_action( 'wp_head', array( $this->wp_head, 'post_meta_tags' ) ) );
	}

	/**
	 * Test `site_meta_tags` function.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * TODO: Add test when for archives and image meta tags.
	 *
	 * @return void
	 */
	public function test_site_meta_tags() {

		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => 'on',
			'name' => '',
			'description' => '',
			'image' => '',
			'title' => '',
		) );
		$this->go_to( get_home_url() );

		ob_start();
		$this->wp_head->site_meta_tags();
		$buffer = ob_get_clean();

		$this->assertTrue( is_home() );
		$this->assertContains( '<!-- START: Social Meta Tags (Social Manager by NineCodes) -->', $buffer );

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="website">', $buffer );
		$this->assertContains( '<meta property="og:title" content="' . wp_get_document_title() . '">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"', $buffer );
		$this->assertContains( '<meta property="og:description" content="' . get_bloginfo( 'description' ) . '"', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_home_url() . '"', $buffer );
		$this->assertContains( '<meta property="og:locale" content="' . get_locale() . '"', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:card" content="summary">', $buffer );
		$this->assertContains( '<meta name="twitter:title" content="' . wp_get_document_title() . '">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="' . get_bloginfo( 'description' ) . '">', $buffer );
		$this->assertContains( '<meta name="twitter:url" content="' . get_home_url() . '">', $buffer );

		$this->assertContains( '<!-- END: Social Manager -->', $buffer );

		/**
		 * ============================================================
		 * The `site_meta_tags` method when the custom value is added
		 * ============================================================
		 */

		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => 'on',
			'name' => 'Hello World',
			'description' => 'Lorem ipsum dolor sit amet',
			'title' => 'Hello World - Lorem ipsum dolor sit amet',
			'image' => '',
		) );

		ob_start();
			$this->wp_head->site_meta_tags();
			$buffer = ob_get_clean();

		// Open Graph.
		$this->assertContains( '<meta property="og:site_name" content="Hello World"', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World - Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta property="og:description" content="Lorem ipsum dolor sit amet"', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:title" content="Hello World - Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="Lorem ipsum dolor sit amet">', $buffer );

		/**
		 * ============================================================
		 * The `site_meta_tags` method when the site metas is disabled.
		 * ============================================================
		 */

		 update_option( $this->option_slug . '_metas_site', array(
			 'enabled' => '',
			 'name' => '',
			 'description' => '',
			 'image' => '',
			 'title' => '',
		 ) );
		 $this->assertNull( $this->wp_head->site_meta_tags() );

		 /**
		  * ============================================================
		  * The `site_meta_tags` method when run in a single post.
		  * ============================================================
		  */

		 $post_id = $this->factory()->post->create( array(
			 'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
		 ) );

		 $this->go_to( '?p=' . $post_id );
		 setup_postdata( get_post( $post_id ) );

		 $this->assertTrue( is_single() );
		 $this->assertNull( $this->wp_head->site_meta_tags() );
	}

	/**
	 * Test `post_meta_tags` function.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * TODO: Add assertContains()
	 *
	 * @return void
	 */
	public function test_post_meta_tags() {

		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		) );
		$this->go_to( get_home_url() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		$this->assertTrue( is_home() );
		$this->assertNull( $this->wp_head->post_meta_tags() );

		/**
		 * ============================================================
		 * The `post_meta_tags` method when run in a single post with
		 * content filled.
		 * ============================================================
		 */
		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		) );

		$post_id = $this->factory()->post->create( array(
			'post_title' => 'Hello World #1',
			'post_content' => '(Content) Lorem ipsum dolor sit amet.',
			'post_excerpt' => '',
		) );

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( is_single() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="article">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World #1">', $buffer );
		$this->assertContains( '<meta property="og:description" content="(Content) Lorem ipsum dolor sit amet."', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_permalink( $post_id ) . '"', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:title" content="Hello World #1">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="(Content) Lorem ipsum dolor sit amet.">', $buffer );

		/**
		 * ============================================================
		 * The `post_meta_tags` method when run in a single post with
		 * content and the excerpt filled.
		 * ============================================================
		 */
		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		) );

		$post_id = $this->factory()->post->create( array(
			'post_title' => 'Hello World #2',
			'post_content' => '(Content) Lorem ipsum dolor sit amet.',
			'post_excerpt' => '(Excerpt) Lorem ipsum dolor.',
		) );

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( is_single() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="article">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World #2">', $buffer );
		$this->assertContains( '<meta property="og:description" content="(Excerpt) Lorem ipsum dolor."', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_permalink( $post_id ) . '"', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:title" content="Hello World #2">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="(Excerpt) Lorem ipsum dolor.">', $buffer );
		$this->assertContains( '<meta name="twitter:url" content="' . get_permalink( $post_id ) . '">', $buffer );

		/**
		 * ============================================================
		 * The `post_meta_tags` method when the site metas is disabled.
		 * ============================================================
		 */

		update_option( $this->option_slug . '_metas_site', array(
			'enabled' => '',
		) );

		$this->assertNull( $this->wp_head->post_meta_tags() );
	}
}
