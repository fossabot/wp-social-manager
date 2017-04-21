<?php
/**
 * Class Test_Buttons
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
 * The class to test the "Buttons" class instance.
 *
 * @since 1.2.0
 */
class Test_Button extends WP_UnitTestCase {

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
		$this->plugin->init();

		$this->button = new Buttons_Content( $this->plugin );
	}

	/**
	 * Test the Buttons class.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_buttons_methods() {

		$this->assertTrue( method_exists( $this->button, 'render_tmpl' ), 'Class does not have method \'render_tmpl\'' );
		$this->assertTrue( method_exists( $this->button, 'render_view' ), 'Class does not have method \'render_view\'' );
		$this->assertTrue( method_exists( $this->button, 'get_icons' ), 'Class does not have method \'get_icons\'' );
		$this->assertTrue( method_exists( $this->button, 'get_label' ), 'Class does not have method \'get_label\'' );
		$this->assertTrue( method_exists( $this->button, 'get_mode' ), 'Class does not have method \'get_mode\'' );
		$this->assertTrue( method_exists( $this->button, 'get_attr_prefix' ), 'Class does not have method \'get_attr_prefix\'' );
		$this->assertTrue( method_exists( $this->button, 'get_post_status' ), 'Class does not have method \'get_post_status\'' );
		$this->assertTrue( method_exists( $this->button, 'in_amp' ), 'Class does not have method \'in_amp\'' );
		$this->assertTrue( method_exists( $this->button, 'to_html' ), 'Class does not have method \'to_html\'' );
	}

	/**
	 * Test `buttons_view` method.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function test_buttons_view() {

		$site = 'facebook';
		$label = 'Facebook';
		$prefix = Helpers::get_attr_prefix();
		$endpoint = 'https://www.facebook.com/sharer/sharer.php';
		$icon = $this->button->get_icons( $site );

		$buttons_view = $this->button->render_view( 'icon', 'content', array(
			'attr_prefix' => $prefix,
			'site' => $site,
			'icon' => $icon,
			'label' => $label,
			'endpoint' => $endpoint,
		) );

		$this->assertEquals( "<a class='social-manager-buttons__item item-default item-facebook' href='https://www.facebook.com/sharer/sharer.php' target='_blank' role='button' rel='nofollow'><svg aria-hidden='true'><use xlink:href='#social-manager-icon-facebook' /></svg></a>", $buttons_view );

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
		 * @var
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
		 * @var
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
	}
}
