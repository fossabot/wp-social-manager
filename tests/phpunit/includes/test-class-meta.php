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
	}
}
