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
		_clean_term_filters();
		wp_cache_delete( 'last_changed', 'terms' );

		// Setup the plugin.
		$plugin = new Plugin();
		$plugin->initialize();

		$this->option_slug = $plugin->get_opts();

		// The Class instance to test.
		$this->wp_head = new WPHead( $plugin );
		$this->wp_head->setups();
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

		$this->assertTrue( method_exists( $this->wp_head, 'hooks' ), 'Class does not have method \'hooks\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'setups' ), 'Class does not have method \'setups\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_meta_tags' ), 'Class does not have method \'site_meta_tags\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_meta_tags' ), 'Class does not have method \'post_meta_tags\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_open_graph' ), 'Class does not have method \'site_open_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'site_twitter_cards' ), 'Class does not have method \'site_twitter_cards\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_open_graph' ), 'Class does not have method \'post_open_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_facebook_graph' ), 'Class does not have method \'post_facebook_graph\'' );
		$this->assertTrue( method_exists( $this->wp_head, 'post_twitter_cards' ), 'Class does not have method \'post_twitter_cards\'' );
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
	 * Test `site_meta_tags` function with the default values.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * TODO: Add test when for archives and image meta tags.
	 *
	 * @return void
	 */
	public function test_site_meta_tags_defaults() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
			'name' => '',
			'description' => '',
			'image' => '',
			'title' => '',
		));
		$this->go_to( get_home_url() );

		ob_start();
		$this->wp_head->site_meta_tags();
		$buffer = ob_get_clean();

		$this->assertTrue( is_home() );
		$this->assertContains( '<!-- START: Social Meta Tags (Social Manager by NineCodes) -->', $buffer );

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="website">', $buffer );
		$this->assertContains( '<meta property="og:title" content="' . wp_get_document_title() . '">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '">', $buffer );
		$this->assertContains( '<meta property="og:description" content="' . get_bloginfo( 'description' ) . '"', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_home_url() . '">', $buffer );
		$this->assertContains( '<meta property="og:locale" content="' . get_locale() . '">', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:card" content="summary">', $buffer );
		$this->assertContains( '<meta name="twitter:title" content="' . wp_get_document_title() . '">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="' . get_bloginfo( 'description' ) . '">', $buffer );
		$this->assertContains( '<meta name="twitter:url" content="' . get_home_url() . '">', $buffer );

		$this->assertContains( '<!-- END: Social Manager -->', $buffer );
	}

	/**
	 * Test `site_meta_tags` function with the custom values.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_site_meta_tags_custom() {

		$image_file = DIR_TESTDATA . '/images/canola.jpg';
		$image_id = $this->factory()->attachment->create_upload_object($image_file, null, array(
			'post_mime_type' => 'image/jpeg',
		));

		/**
		 * ============================================================
		 * The `site_meta_tags` method when the custom value is added
		 * ============================================================
		 */
		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
			'name' => 'Hello World',
			'description' => 'Lorem ipsum dolor sit amet',
			'title' => 'Hello World - Lorem ipsum dolor sit amet',
			'image' => $image_id,
		));

		ob_start();
		$this->wp_head->site_meta_tags();
		$buffer = ob_get_clean();

		$document_title = wp_get_document_title();
		$image_src = wp_get_attachment_image_src( $image_id, 'full', true );

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="website">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="Hello World">', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World - Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta property="og:description" content="Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta property="og:image" content="' . $image_src[0] . '">', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_home_url() . '">', $buffer );
		$this->assertContains( '<meta property="og:locale" content="' . get_locale() . '">', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:card" content="summary">', $buffer );
		$this->assertContains( '<meta name="twitter:title" content="Hello World - Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="Lorem ipsum dolor sit amet">', $buffer );
		$this->assertContains( '<meta name="twitter:image:src" content="' . $image_src[0] . '">', $buffer );

		// Delete the attacment after test.
		wp_delete_attachment( $image_id, true );
	}

	/**
	 * Test `site_meta_tags` function when disabled.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_site_meta_tags_disabled() {

		/**
		 * The `site_meta_tags` method when the site metas is disabled,
		 * yet we have the meta tags added.
		 */
		update_option($this->option_slug . '_metas_site', array(
			'enabled' => '',
			'name' => 'Hello World',
			'description' => 'Lorem ipsum dolor sit amet',
			'title' => 'Hello World - Lorem ipsum dolor sit amet',
			'image' => '',
		));
		$this->assertNull( $this->wp_head->site_meta_tags() );
	}

	/**
	 * Test `site_meta_tags` function run in single post.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_site_meta_tags_in_single_post() {

		// Enable the metas site with the meta values added.
		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		 	'name' => 'Hello World',
			'description' => 'Lorem ipsum dolor sit amet',
			'title' => 'Hello World - Lorem ipsum dolor sit amet',
			'image' => '',
		));

		/**
		 * Create a new post.
		 *
		 * @var integer
		 */
		$post_id = $this->factory()->post->create(array(
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
		));

		$this->go_to( '?p=' . $post_id ); // Go to that single post.
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( is_single() ); // Make sure that we are on single post.
		$this->assertNull( $this->wp_head->site_meta_tags() );
	}

	/**
	 * The `post_meta_tags` method when run in a single post with content filled.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @return void
	 */
	public function test_post_meta_tags() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		));

		$post_id = $this->factory()->post->create(array(
			'post_title' => 'Hello World #1',
			'post_content' => '(Content) Lorem ipsum dolor sit amet. <p><img src="https://example.org/upload/image.jpg" width="500" height="320"></p>',
			'post_excerpt' => '',
		));

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );
		$this->assertTrue( is_single() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="article">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '">', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World #1">', $buffer );
		$this->assertContains( '<meta property="og:description" content="(Content) Lorem ipsum dolor sit amet.">', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_permalink( $post_id ) . '">', $buffer );
		$this->assertContains( '<meta property="og:image" content="https://example.org/upload/image.jpg">', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:title" content="Hello World #1">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="(Content) Lorem ipsum dolor sit amet.">', $buffer );
		$this->assertContains( '<meta name="twitter:url" content="' . get_permalink( $post_id ) . '">', $buffer );
		$this->assertContains( '<meta name="twitter:image" content="https://example.org/upload/image.jpg">', $buffer );
	}

	/**
	 * Test `post_meta_tags` function when loaded in homepage.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_post_meta_tags_in_home() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		));
		$this->go_to( get_home_url() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		$this->assertTrue( is_home() ); // Make sure we are in homepage.
		$this->assertNull( $this->wp_head->post_meta_tags() );
	}

	/**
	 * The `post_meta_tags` method when run in a single post with
	 * content and the excerpt filled.
	 *
	 * And whent
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_post_meta_tags_with_excerpt() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		));

		$post_id = $this->factory()->post->create(array(
			'post_title' => 'Hello World #2',
			'post_content' => '(Content) Lorem ipsum dolor sit amet.',
			'post_excerpt' => '(Excerpt) Lorem ipsum dolor.',
		));

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( is_single() );

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		// Open Graph.
		$this->assertContains( '<meta property="og:type" content="article">', $buffer );
		$this->assertContains( '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '">', $buffer );
		$this->assertContains( '<meta property="og:title" content="Hello World #2">', $buffer );
		$this->assertContains( '<meta property="og:description" content="(Excerpt) Lorem ipsum dolor.">', $buffer );
		$this->assertContains( '<meta property="og:url" content="' . get_permalink( $post_id ) . '">', $buffer );

		// Twitter Cards.
		$this->assertContains( '<meta name="twitter:title" content="Hello World #2">', $buffer );
		$this->assertContains( '<meta name="twitter:description" content="(Excerpt) Lorem ipsum dolor.">', $buffer );
		$this->assertContains( '<meta name="twitter:url" content="' . get_permalink( $post_id ) . '">', $buffer );
	}

	/**
	 * The `post_meta_tags` method with post_section meta tags.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_post_meta_tags_with_post_section() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => 'on',
		));

		$cat_1 = $this->factory()->category->create(array(
			'slug' => 'cat-1',
			'name' => 'Category 1',
			'description' => 'Description of Category 1',
		));
		$cat_2 = $this->factory()->category->create(array(
			'slug' => 'cat-2',
			'name' => 'Category 2',
			'description' => 'Description of Category 2',
		));

		$post_id = $this->factory()->post->create( array(
			'post_title' => 'Post Meta Section',
		) );
		wp_set_post_terms( $post_id, array( $cat_1, $cat_2 ), 'category' );

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertTrue( is_single() ); // Ensure we are on single post.

		ob_start();
		$this->wp_head->post_meta_tags();
		$buffer = ob_get_clean();

		$this->assertContains( '<meta property="article:section" content="Category 1">', $buffer );
	}

	/**
	 * The `post_meta_tags` method when meta tags disabled.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_post_meta_tags_disabled() {

		update_option($this->option_slug . '_metas_site', array(
			'enabled' => '',
		));
		$this->assertNull( $this->wp_head->post_meta_tags() );
	}
}
