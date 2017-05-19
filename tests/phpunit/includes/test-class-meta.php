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
	}

	/**
	 * Test the `get_site_meta` method in the Meta class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_meta() {

		$site_meta = $this->plugin->meta->get_site_meta(); // Bad: No argument passed.
		$this->assertEmpty( $site_meta );

		$site_meta = $this->plugin->meta->get_site_meta( false ); // Bad: falsy argument passed.
		$this->assertEmpty( $site_meta );

		$this->plugin->option->update( 'meta_site', 'name', 'GoodSite' );
		$site_meta_name = $this->plugin->meta->get_site_meta( 'name' );

		$this->plugin->option->update( 'meta_site', 'title', 'Hello World - GoodSite' );
		$site_meta_title = $this->plugin->meta->get_site_meta( 'title' );

		$this->plugin->option->update( 'meta_site', 'description', 'This is the description of the good site' );
		$site_meta_desc = $this->plugin->meta->get_site_meta( 'description' );

		$this->plugin->option->update( 'meta_site', 'image', 123 );
		$site_meta_image = $this->plugin->meta->get_site_meta( 'image' );

		$this->assertEquals( 'GoodSite', $site_meta_name );
		$this->assertEquals( 'Hello World - GoodSite', $site_meta_title );
		$this->assertEquals( 'This is the description of the good site', $site_meta_desc );
		$this->assertEquals( 123, $site_meta_image );
	}

	/**
	 * Test the `get_site_name` method in the Meta class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_name() {

		$this->plugin->option->update( 'meta_site', 'name', 'GoodSite' );

		$this->assertEquals( 'GoodSite', $this->plugin->meta->get_site_name() );

		add_filter( 'ninecodes_social_manager_meta', function( $site_meta, $context ) { // Test the 'get_site_name' filter
			if ( 'site_name' === $context ) {
				return $site_meta . ' + Site Name';
			}
			return $site_meta;
		}, 10, 2 );

		$this->assertEquals( 'GoodSite + Site Name', $this->plugin->meta->get_site_name() );
	}

	/**
	 * Test the `get_site_title` method in the Meta class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_title() {

		$this->plugin->option->update( 'meta_site', 'title', 'Hello World - GoodSite' );

		$this->assertEquals( 'Hello World - GoodSite', $this->plugin->meta->get_site_title() );

		add_filter( 'ninecodes_social_manager_meta', function( $site_meta, $context ) { // Test the 'get_site_title' filter
			if ( 'site_title' === $context ) {
				return $site_meta . ' + Site Title';
			}
			return $site_meta;
		}, 10, 2 );

		$this->assertEquals( 'Hello World - GoodSite + Site Title', $this->plugin->meta->get_site_title() );
	}

	/**
	 * Test the `get_site_description` method in the Meta class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_description() {

		$this->plugin->option->update( 'meta_site', 'description', 'This is the description of the good site' );

		$this->assertEquals( 'This is the description of the good site', $this->plugin->meta->get_site_description() );

		add_filter( 'ninecodes_social_manager_meta', function( $site_meta, $context ) { // Test the 'get_site_description' filter
			if ( 'site_description' === $context ) {
				return $site_meta . ' + Site Description';
			}
			return $site_meta;
		}, 10, 2 );

		$this->assertEquals( 'This is the description of the good site + Site Description', $this->plugin->meta->get_site_description() );
	}

	/**
	 * Test the `get_site_description` method in the Meta class
	 * when it's loaded in an archive page'
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_description_in_archive() {

		$site_description = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolor, rem, voluptatum. Id sapiente suscipit recusandae, delectus quae, perspiciatis necessitatibus quis, mollitia, ipsa non eaque nostrum eos ducimus molestiae.';

		$category = $this->factory()->category->create( array(
			'slug' => 'category-2',
			'name' => 'Category 2',
			'description' => $site_description,
		) );

		$this->go_to( get_site_url() . '?cat=' . $category );

		$this->assertTrue( is_archive() ); // Make sure we are in an archive page.
		$this->assertEquals( $site_description, $this->plugin->meta->get_site_description() );
	}

	/**
	 * Test the `get_site_description` method in the Meta class
	 * when it's loaded in an author page'
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_description_in_author() {

		$site_description = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam doloremque ab, nihil quisquam, quod natus, eligendi consequuntur consectetur distinctio magni, dicta id atque sunt dolores neque voluptatum cumque rerum.';

		$user = $this->factory->user->create( array(
			'user_login' => 'john_doe',
			'user_email' => 'john@doe.com',
			'display_name' => 'John Doe',
			'description' => $site_description,
		) );

		$this->go_to( get_site_url() . '?author=' . $user );

		$this->assertTrue( is_author() ); // Make sure we are in an author page.
		$this->assertEquals( $site_description, $this->plugin->meta->get_site_description() );
	}

	/**
	 * Test the `get_site_url` method in the Meta class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function test_get_site_url() {
		$this->assertEquals( get_site_url(), $this->plugin->meta->get_site_url() );
	}
}
