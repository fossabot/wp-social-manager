<?php
/**
 * Class TestPlugin
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

use \WP_UnitTestCase;

/**
 * The class to test the "Options" class.
 *
 * @since 1.0.0
 */
class TestOptions extends WP_UnitTestCase {

	/**
	 * Test social profiles option output.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_social_profiles() {

		/**
		 * If the social profiles is given a non-existent key name,
		 * Should return empty.
		 */
		$this->assertEmpty( Options::social_profiles( 'xyz' ) );

		foreach ( Options::social_profiles() as $key => $arr ) {
			$this->assertArrayHasKey( 'label', $arr, 'The' . $key . ' profile must has a label' );
			$this->assertArrayHasKey( 'url', $arr, 'The' . $key . ' profile must has a URL' );
			$this->assertArrayHasKey( 'description', $arr, 'The' . $key . ' profile must has a description' );

			$this->assertNotEmpty( $arr['label'], 'The' . $key . ' label must not empty' );
			$this->assertNotEmpty( $arr['url'], 'The' . $key . ' url must not empty' );
			$this->assertNotEmpty( $arr['description'], 'The' . $key . ' description must not empty' );

			$this->assertStringStartsWith( 'http', $arr['url'], 'The' . $key . ' URL must starts with the HTTP/HTTPS protocol' );
		}

		/**
		 * Test the filter hook to add a new Social Media profiles.
		 *
		 * @since 1.2.0
		 */
		add_filter( 'ninecodes_social_manager_options', function ( $value, $context ) {

			if ( 'profiles' === $context ) {

				$value['ello'] = array(
					'label' => 'Ello',
					'url' => 'https://ello.co', // No slash at the end or the URL.
					'description' => 'Set your Ello username',
				);

				// Bad example; array given with no value.
				$value['myspace'] = array();

				// Bad example; HTML element in the label and description, and URL without the HTTP protocol.
				$value['friendster'] = array(
					'label' => '<strong>Friendster</strong>',
					'url' => 'friendster.com', // No slash at the end or the URL.
					'description' => '<p>Set your <strong>Friendster</strong> username</p>',
				);
			}

			return $value;
		}, 10, 2 );

		$profiles_filtered = Options::social_profiles();

		$this->assertArrayHasKey( 'ello', $profiles_filtered );
		$this->assertEquals( 'Ello', $profiles_filtered['ello']['label'] );
		$this->assertEquals( 'https://ello.co/', $profiles_filtered['ello']['url'] );
		$this->assertEquals( 'Set your Ello username', $profiles_filtered['ello']['description'] );

		$this->assertArrayHasKey( 'myspace', $profiles_filtered );
		$this->assertEquals( 'Example', $profiles_filtered['myspace']['label'] );
		$this->assertEquals( 'http://example.com/', $profiles_filtered['myspace']['url'] );
		$this->assertEquals( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt, repudiandae.', $profiles_filtered['myspace']['description'] );

		$this->assertArrayHasKey( 'friendster', $profiles_filtered );
		$this->assertEquals( 'Friendster', $profiles_filtered['friendster']['label'] );
		$this->assertEquals( 'http://friendster.com/', $profiles_filtered['friendster']['url'] );
		$this->assertEquals( 'Set your <strong>Friendster</strong> username', $profiles_filtered['friendster']['description'] );
	}

	/**
	 * Test post types option output.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_post_types() {

		$post_types = Options::post_types();

		$this->assertArrayNotHasKey( 'attachment', $post_types );
		$this->assertArrayNotHasKey( 'revision', $post_types );
		$this->assertArrayNotHasKey( 'nav_menu_item', $post_types );
		$this->assertArrayNotHasKey( 'deprecated_log', $post_types );
	}

	/**
	 * Test button placements option output.
	 *
	 * @since 1.2.0
	 * @access public
	 * @return void
	 */
	public function test_button_placements() {

		$placements = Options::button_placements();

		$this->assertArrayHasKey( 'before', $placements );
		$this->assertArrayHasKey( 'after', $placements );

		/**
		 * Test the filter hook to add a new Social Media button placements.
		 *
		 * @since 1.2.0
		 */
		add_filter( 'ninecodes_social_manager_options', function ( $value, $context ) {

			if ( 'button_placements' === $context ) {

				$value['before'] = 'Before the content'; // Duplicate.
				$value['sebelum'] = 'Before the content'; // Duplicate value.
				$value['after'] = 'Sesudah konten'; // Duplicate key.

				$value['before'] = 'before the Content'; // Identical value.
				$value['Before'] = 'Before the content'; // Identical key.

				$value['float'] = 'Float'; // New.
				$value['withscript'] = '<span>Script</span>'; // Just bad value.
			}

			return $value;
		}, 10, 2 );

		$placements_filtered = Options::button_placements();

		$this->assertEquals( array_merge( $placements, array(
			'float' => 'Float',
			'withscript' => '&lt;span&gt;Script&lt;/span&gt;',
		) ), $placements_filtered );

		// Test a valid new addition.
		$this->assertArrayHasKey( 'float', $placements_filtered );
		$this->assertEquals( 'Float', $placements_filtered['float'] );

		// Test a bad addition.
		$this->assertArrayHasKey( 'withscript', $placements_filtered );
		$this->assertEquals( '&lt;span&gt;Script&lt;/span&gt;', $placements_filtered['withscript'] );
	}

	/**
	 * Test button views option output.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_button_views() {

		$views = Options::button_views();

		$this->assertNotEmpty( $views );

		$this->assertArrayHasKey( 'icon', $views );
		$this->assertArrayHasKey( 'text', $views );
		$this->assertArrayHasKey( 'icon-text', $views );

		/**
		 * Test the filter hook to add a new Social Media button view.
		 *
		 * @since 1.2.0
		 */
		add_filter('ninecodes_social_manager_options', function ( $value, $context ) {

			if ( 'button_views' === $context ) {
				$value['icon'] = 'Icon'; // Duplicate.
				$value['transparent'] = 'Transparent'; // New.
				$value['withscript'] = '<span>Script</span>'; // Just Bad.
			}

			return $value;
		}, 10, 2);

		$views_filtered = Options::button_views();

		// Test a duplicate.
		$this->assertArrayHasKey( 'icon', $views_filtered );
		$this->assertEquals( $views_filtered, Options::button_views() );

		// Test a valid new addition.
		$this->assertArrayHasKey( 'transparent', $views_filtered );
		$this->assertEquals( 'Transparent', $views_filtered['transparent'] );

		// Test a bad addition.
		$this->assertArrayHasKey( 'withscript', $views_filtered );
		$this->assertEquals( '&lt;span&gt;Script&lt;/span&gt;', $views_filtered['withscript'] );
	}

	/**
	 * Test button sites option output.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_button_sites() {

		$strings = array( '', 'xyz' ) ;

		foreach ( $strings as $str ) {
			$sites = Options::button_sites( $str );

			$this->assertArrayHasKey( 'content', $sites );
			$this->assertArrayHasKey( 'image', $sites );
		}

		$rand = Options::button_sites( 'rand' ); // non-existent name.

		$this->assertArrayHasKey( 'content', $rand );
		$this->assertArrayHasKey( 'image', $rand );

		$content = Options::button_sites( 'content' ); // Button content keys.

		$this->assertEquals( 7, count( $content ) );
		$this->assertArrayHasKey( 'facebook', $content );
		$this->assertArrayHasKey( 'twitter', $content );
		$this->assertArrayHasKey( 'googleplus', $content );
		$this->assertArrayHasKey( 'pinterest', $content );
		$this->assertArrayHasKey( 'linkedin', $content );
		$this->assertArrayHasKey( 'reddit', $content );
		$this->assertArrayHasKey( 'email', $content );
		$image = Options::button_sites( 'image' ); // Button image keys.

		$this->assertEquals( 1, count( $image ) );
		$this->assertArrayHasKey( 'pinterest', $image );

		/**
		 * Test the filter hook to add new Social Media buttons.
		 *
		 * @since 1.2.0
		 */
		add_filter('ninecodes_social_manager_options', function ( $value, $context ) {

			if ( 'button_sites' === $context ) {
				$value['content'] = array(
					'facebook' => 'Facebook', // Bad.
					'twitter' => array(
						'label' => 'Twitter',
						'endpoint' => 'https://twitter.com/intent/tweet',
					),
				);
				$value['image'] = array(
					'ello' => 'Ello', // Bad.
					'pinterest' => array(
						'label' => 'Pinterest',
						'endpoint' => 'https://www.pinterest.com/pin/create/bookmarklet/',
					),
				);
			}

			return $value;
		}, 10, 2);

		$content = Options::button_sites( 'content' );
		$image = Options::button_sites( 'image' );

		$this->assertArrayNotHasKey( 'facebok', $content );
		$this->assertArrayHasKey( 'twitter', $content );
		$this->assertEquals( 'Twitter', $content['twitter']['label'] );
		$this->assertEquals( 'https://twitter.com/intent/tweet', $content['twitter']['endpoint'] );

		$this->assertArrayNotHasKey( 'ello', $image );
		$this->assertArrayHasKey( 'pinterest', $image );
		$this->assertEquals( 'Pinterest', $image['pinterest']['label'] );
		$this->assertEquals( 'https://www.pinterest.com/pin/create/bookmarklet/', $image['pinterest']['endpoint'] );

		unset( $content['twitter'] );
		unset( $image['pinterest'] );

		/**
		 * Test the filter hook for bad Social Media buttons (Bad Examples).
		 *
		 * @since 1.2.0
		 */
		add_filter('ninecodes_social_manager_options', function ( $value, $context ) {

			if ( 'button_sites' === $context ) {
				$value['content'] = array(
					'twitter' => array(
						'label' => '<script>Twitter</script>',
						'endpoint' => 'https://twitter.com/intent/tweet',
					),
				);
				$value['image'] = array(
					'pinterest' => array(
						'label' => '<script>Pinterest</script>',
						'endpoint' => 'https://www.pinterest.com/pin/create/bookmarklet/',
					),
				);
			}

			return $value;
		}, 10, 2);

		$content = Options::button_sites( 'content' );
		$image = Options::button_sites( 'image' );

		$this->assertArrayHasKey( 'twitter', $content ); // Since it does not have label and endpoint.
		$this->assertEquals( '&lt;script&gt;Twitter&lt;/script&gt;', $content['twitter']['label'] ); // The output must be sanitized.

		$this->assertArrayHasKey( 'pinterest', $image );
		$this->assertEquals( '&lt;script&gt;Pinterest&lt;/script&gt;', $image['pinterest']['label'] ); // The output must be sanitized.

		unset( $content['twitter'] );
		unset( $image['pinterest'] );
	}

	/**
	 * Test button sites option output.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_buttons_modes() {

		$modes = Options::buttons_modes();

		$this->assertArrayHasKey( 'html', $modes );
		$this->assertArrayHasKey( 'json', $modes );
	}

	/**
	 * Test button link modes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function test_link_modes() {

		$modes = Options::link_modes();

		$this->assertArrayHasKey( 'permalink', $modes );
		$this->assertArrayHasKey( 'shortlink', $modes );
	}
}
