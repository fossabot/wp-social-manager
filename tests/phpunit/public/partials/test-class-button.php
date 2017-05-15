<?php
/**
 * Class Test_Button
 *
 * @package NineCodes\SocialManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialManager;

/**
 * Load Global classes;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "Button" class instance.
 *
 * @since 1.1.0
 */
class Test_Button extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = ninecodes_social_manager();
		$this->plugin->init();

		$this->button = $this->getMockForAbstractClass( __NAMESPACE__ . '\\Button', array( $this->plugin ) );
	}

	/**
	 * Teardown.
	 *
	 * @inheritdoc
	 */
	function tearDown() {

		$this->plugin = null;
		parent::tearDown();
	}

	/**
	 * Test `render_button` method.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_render_button() {
		$this->assertEquals( '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id nostrum obcaecati, temporibus, odit et fuga labore debitis animi dicta omnis facilis vitae in perspiciatis eaque unde veritatis voluptates voluptas modi.</p>', $this->button->render_button( '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id nostrum obcaecati, temporibus, odit et fuga labore debitis animi dicta omnis facilis vitae in perspiciatis eaque unde veritatis voluptates voluptas modi.</p>' ) );
	}

	/**
	 * Test `get_label` method.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_label() {

		// Bad: The site should be registered in the `Options::button_sites()`.
		$this->assertEquals( '', $this->button->get_label( 'ello', 'content' ) );

		// Bad: The context arguments should be 'image' or 'content'.
		$this->assertEquals( '', $this->button->get_label( 'facebook', 'image-content' ) );

		$this->assertEquals( 'Facebook', $this->button->get_label( 'facebook', 'content' ) ); // Facebook default label.
		$this->assertEquals( 'Twitter', $this->button->get_label( 'twitter', 'content' ) ); // Twitter default label.
		$this->assertEquals( 'Google+', $this->button->get_label( 'googleplus', 'content' ) ); // Google+ default label.
		$this->assertEquals( 'Pinterest', $this->button->get_label( 'pinterest', 'content' ) ); // Pinterest default label.
		$this->assertEquals( 'LinkedIn', $this->button->get_label( 'linkedin', 'content' ) ); // LinkedIn default label.
		$this->assertEquals( 'Reddit', $this->button->get_label( 'reddit', 'content' ) ); // Reddit default label.
		$this->assertEquals( 'Tumblr', $this->button->get_label( 'tumblr', 'content' ) ); // Tumblr default label.
		$this->assertEquals( 'Email', $this->button->get_label( 'email', 'content' ) ); // Email default label.

		$this->assertEquals( '', $this->button->get_label( 'facebook', 'image' ) ); // Facebook for Image is not yet supported.
		$this->assertEquals( 'Pinterest', $this->button->get_label( 'pinterest', 'image' ) ); // Pinterest default label.
	}

	/**
	 * Test `render_view` method.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_render_view() {

		$site = 'facebook';
		$prefix = Helpers::get_attr_prefix();

		// Actually random URL.
		$endpoint = 'https://www.facebook.com/sharer/sharer.php';

		$label = $this->button->get_label( $site, 'content' );
		$icon = $this->button->get_icons( $site );

		$buttons_view = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEquals( "<a class=\"{$prefix}-button__item site-facebook\" href=\"https://www.facebook.com/sharer/sharer.php\" target=\"_blank\" role=\"button\" rel=\"nofollow\"><svg aria-hidden=\"true\"><use xlink:href=\"#{$prefix}-icon-facebook\" /></svg></a>", $buttons_view );
	}

	/**
	 * Test `render_view` method.
	 *
	 * All return value must be empty.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_render_view_with_bad_values() {

		$site = 'facebook';
		$prefix = Helpers::get_attr_prefix();

		// Actually random URL.
		$endpoint = 'https://www.facebook.com/sharer/sharer.php';

		$label = $this->button->get_label( $site, 'content' );
		$icon = $this->button->get_icons( $site );

		/**
		 * Test with an empty "prefix".
		 *
		 * @var string
		 */
		$buttons_view2 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => '',
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view2 );

		/**
		 * Test with an empty "site".
		 *
		 * @var string
		 */
		$buttons_view3 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => '',
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view3 );

		/**
		 * Test with an empty "icon".
		 *
		 * @var string
		 */
		$buttons_view4 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => '',
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view4 );

		/**
		 * Test with an empty "label".
		 *
		 * @var string
		 */
		$buttons_view5 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => '',
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view5 );

		/**
		 * Test with an empty "endpoint".
		 *
		 * @var string
		 */
		$buttons_view6 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => '',
		) );

		$this->assertEmpty( $buttons_view6 );

		/**
		 * Test falsy value.
		 *
		 * @var string
		 */
		$buttons_view7 = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => false,
		) );

		$this->assertEmpty( $buttons_view7 );

		/**
		 * Test context.
		 *
		 * @var string
		 */
		$buttons_view8 = $this->button->render_view( 'icon', 'image-content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view8 );

		/**
		 * Test bad view type.
		 *
		 * @var string
		 */
		$buttons_view10 = $this->button->render_view( 'icon-me', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view10 );

		/**
		 * Test with empty array arguments.
		 *
		 * @var string
		 */
		$buttons_view11 = $this->button->render_view( 'icon', 'content', array() );

		$this->assertEmpty( $buttons_view11 );
	}

	/**
	 * Test `get_post_status` method.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_post_status() {

		$post_id = $this->factory()->post->create(array(
			'post_title' => 'Hello World',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		));

		$this->go_to( '?p=' . $post_id );
		setup_postdata( get_post( $post_id ) );

		$this->assertEquals( 'publish', $this->button->get_post_status() );
	}

	/**
	 * Function to test the `get_icons`.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function test_get_icons() {

		$icons = Helpers::get_social_icons();
		$this->assertEquals( $icons, $this->button->get_icons() );

		add_filter( 'ninecodes_social_manager_icons', function( $icons, $context, $args ) {

			if ( 'button' === $context ) {

				unset( $icons['facebook'] );
				unset( $icons['twitter'] );

				$icons['pinterest'] = '<img src="image/pinterest.png" />';
				$icons['linkedin'] = '<img src="image/linkedin.png" />';
			}

			return $icons;
		}, 10, 3 );

		// Button_Image should have not theses removed keys.
		$this->assertArrayNotHasKey( 'facebook', $this->button->get_icons() );
		$this->assertArrayNotHasKey( 'twitter', $this->button->get_icons() );

		$this->assertEquals( '<img src="image/pinterest.png" />', $this->button->get_icons( 'pinterest' ) );
		$this->assertEquals( '<img src="image/linkedin.png" />', $this->button->get_icons( 'linkedin' ) );
	}
}
