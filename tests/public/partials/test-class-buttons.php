<?php
/**
 * Class TestButtons
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
 * The class to test the "Buttons" class instance.
 *
 * "Buttons" is an abstract class.
 *
 * @since 1.2.0
 */
class ButtonsTests extends Buttons {}

/**
 * The class to test the "TestButtons" class instance.
 *
 * @since 1.2.0
 */
class TestButtons extends WP_UnitTestCase {

	/**
	 * The Plugin class instance.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @var string
	 */
	protected $plugin;

	/**
	 * The Button class instance.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @var string
	 */
	protected $buttons;

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @var string
	 */
	protected $option_slug;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = new Plugin();
		$this->plugin->initialize();

		$this->option_slug = $this->plugin->get_opts();

		$this->buttons = new ButtonsTests( $this->plugin );
	}

	/**
	 * Test the Buttons class.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_buttons_methods() {

		$this->assertTrue( method_exists( $this->buttons, 'buttons_tmpl' ),  'Class does not have method \'buttons_tmpl\'' );
		$this->assertTrue( method_exists( $this->buttons, 'buttons_view_html' ),  'Class does not have method \'buttons_view_html\'' );
		$this->assertTrue( method_exists( $this->buttons, 'get_buttons_icons' ),  'Class does not have method \'get_buttons_icons\'' );
		$this->assertTrue( method_exists( $this->buttons, 'get_buttons_label' ),  'Class does not have method \'get_buttons_label\'' );
		$this->assertTrue( method_exists( $this->buttons, 'get_buttons_mode' ),  'Class does not have method \'get_buttons_mode\'' );
		$this->assertTrue( method_exists( $this->buttons, 'get_attr_prefix' ),  'Class does not have method \'get_attr_prefix\'' );
		$this->assertTrue( method_exists( $this->buttons, 'get_post_status' ),  'Class does not have method \'get_post_status\'' );
		$this->assertTrue( method_exists( $this->buttons, 'in_amp' ),  'Class does not have method \'in_amp\'' );
		$this->assertTrue( method_exists( $this->buttons, 'to_html' ),  'Class does not have method \'to_html\'' );
	}

	/**
	 * Test `buttons_view_html` method.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_buttons_view_html() {

		$site = 'facebook';
		$label = 'Facebook';
		$prefix = Helpers::get_attr_prefix();
		$endpoint = 'https://www.facebook.com/sharer/sharer.php';
		$icon = $this->buttons->get_buttons_icons( $site );

		$buttons_view = $this->buttons->buttons_view_html( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEquals( "<a class='social-manager-buttons__item item-facebook' href='https://www.facebook.com/sharer/sharer.php' target='_blank' role='button' rel='nofollow'><svg aria-hidden='true'><use xlink:href='#social-manager-icon-facebook' /></svg></a>", $buttons_view );

		/**
		 * Test with an empty "prefix".
		 *
		 * @var string
		 */
		$buttons_view2 = $this->buttons->buttons_view_html( 'icon', 'content', array(
			'attr_prefix' => '',
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		/**
		 * Test with an empty "site".
		 *
		 * @var string
		 */
		$buttons_view3 = $this->buttons->buttons_view_html( 'icon', 'content', array(
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
		$buttons_view4 = $this->buttons->buttons_view_html( 'icon', 'content', array(
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
		$buttons_view5 = $this->buttons->buttons_view_html( 'icon', 'content', array(
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
		$buttons_view6 = $this->buttons->buttons_view_html( 'icon', 'content', array(
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
		 * @var
		 */
		$buttons_view7 = $this->buttons->buttons_view_html( 'icon', 'content', array(
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
		 * @var
		 */
		$buttons_view8 = $this->buttons->buttons_view_html( 'icon', 'image-content', array(
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
		$buttons_view10 = $this->buttons->buttons_view_html( 'icon-me', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEmpty( $buttons_view10 );
	}
}
