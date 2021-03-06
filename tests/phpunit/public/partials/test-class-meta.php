<?php
/**
 * Class Test_Meta
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load Global classes;
 */
use \WP_UnitTestCase;
use \WP_REST_Server;
use \WP_REST_Request;

/**
 * The class to test the "Test_Meta" class instance.
 *
 * @since 1.1.0
 */
class Test_Meta extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.1.0
	 * @access protected
	 * @var string
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
	 * The Meta
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @var Meta
	 */
	protected $meta;


	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		_clean_term_filters();
		wp_cache_delete( 'last_changed', 'terms' );

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();

		$this->meta = new Meta( $this->plugin );
	}

	/**
	 * Function method to test the `get_post_section` function method in Meta class.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_post_section() {

		$term_id_uncategorized = get_option( 'default_category' );

		$c1 = $this->factory()->category->create( array(
			'slug' => 'c1',
			'name' => 'Category 1',
			'description' => 'Description of Category 1',
		) );

		$c2 = $this->factory()->category->create( array(
			'slug' => 'c2',
			'name' => 'Category 2',
			'description' => 'Description of Category 2',
		) );

		$c3 = $this->factory()->category->create( array(
			'slug' => 'c3',
			'name' => 'Category 3',
			'description' => 'Description of Category 3',
			'parent' => $c2,
		) );

		$c4 = $this->factory()->category->create( array(
			'slug' => 'c4',
			'name' => 'Category 4',
			'description' => 'Description of Category 4',
		) );

		$c5 = $this->factory()->category->create( array(
			'slug' => 'c5',
			'name' => 'Category 5',
			'description' => 'Description of Category 5',
			'parent' => $c4,
		) );

		$terms = get_terms( 'category', array(
			'exclude' => $term_id_uncategorized,
			'hierarchical' => true,
			'hide_empty' => false,
			'fields' => 'ids',
		) );

		// Ensure these categories are creted.
		$this->assertContains( $c1, $terms );
		$this->assertContains( $c2, $terms );
		$this->assertContains( $c3, $terms );
		$this->assertContains( $c4, $terms );
		$this->assertContains( $c5, $terms );

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'post',
		) );

		// Category parent.
		wp_set_post_terms( $post_id, array( $c1, $c2 ), 'category' );
		$this->assertEquals( 'Category 1', $this->meta->get_post_section( $post_id ) );

		// Category Child (under a parent).
		wp_set_post_terms( $post_id, array( $c5, $c3 ), 'category' );
		$this->assertEquals( 'Category 3', $this->meta->get_post_section( $post_id ) );

		// When `post_section` meta is set.
		update_post_meta( $post_id, $this->plugin->option_slug, array(
			'post_section' => "category-{$c5}",
		) );
		$this->assertEquals( 'Category 5', $this->meta->get_post_section( $post_id ) );
	}
}
