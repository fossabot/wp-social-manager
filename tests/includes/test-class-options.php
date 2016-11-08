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
 * Test the Plugin class instance.
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

			$this->assertStringStartsWith( 'http', $arr['url'], 'The' . $key . ' URL must starts with the HTTP protocol' );
		}
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

		$this->assertArrayHasKey( 'facebook', $content );
		$this->assertArrayHasKey( 'twitter', $content );
		$this->assertArrayHasKey( 'googleplus', $content );
		$this->assertArrayHasKey( 'pinterest', $content );
		$this->assertArrayHasKey( 'linkedin', $content );
		$this->assertArrayHasKey( 'reddit', $content );
		$this->assertArrayHasKey( 'email', $content );

		$image = Options::button_sites( 'image' ); // Button image keys.

		$this->assertArrayHasKey( 'pinterest', $image );
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
