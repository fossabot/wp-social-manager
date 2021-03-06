<?php
/**
 * Class Test_Validation
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
 * The class to test the "Validation" class.
 *
 * @since 1.0.0
 */
class Test_Validation extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->validation = new Validation();
	}

	/**
	 * Test setting profiles validation.
	 *
	 * @return void
	 */
	public function test_setting_profile() {

		/**
		 * Test Associative Array.
		 *
		 * @var array
		 */
		$assoc = $this->validation->setting_profile( array(
			'facebook' => 'zuck',
			'twitter' => 'jack',
			'googleplus' => 'page',
		) );

		$this->assertArrayHasKey( 'facebook', $assoc );
		$this->assertEquals( 'zuck', $assoc['facebook'] );

		$this->assertArrayHasKey( 'twitter', $assoc );
		$this->assertEquals( 'jack', $assoc['twitter'] );

		$this->assertArrayHasKey( 'googleplus', $assoc );
		$this->assertEquals( 'page', $assoc['googleplus'] );

		/**
		 * Test Associative array with empty value.
		 *
		 * @var array
		 */
		$assoc_empty = $this->validation->setting_profile( array(
			'facebook' => '',
			'twitter' => '',
			'googleplus' => '',
		) );

		$this->assertEmpty( $assoc_empty['facebook'] );
		$this->assertEmpty( $assoc_empty['twitter'] );
		$this->assertEmpty( $assoc_empty['googleplus'] );

		/**
		 * Test Associative array with non-string values.
		 *
		 * @var array
		 */
		$assoc_non_string = $this->validation->setting_profile( array(
			'facebook' => true,
			'twitter' => null,
			'googleplus' => 1,
		) );

		$this->assertEmpty( $assoc_non_string['facebook'] );
		$this->assertEmpty( $assoc_non_string['twitter'] );
		$this->assertEmpty( $assoc_non_string['googleplus'] );

		/**
		 * Test Associative array with non-registered profiles.
		 *
		 * @var array
		 */
		$assoc_non_registered = $this->validation->setting_profile( array(
			'facebook' => '',
			'twitter' => '',
			'googleplus' => '',
			'friendfeed' => '',
			'myspace' => '',
			'friendster' => '',
			'digg' => '',
		) );

		$this->assertArrayNotHasKey( 'friendfeed', $assoc_non_registered );
		$this->assertArrayNotHasKey( 'myspace', $assoc_non_registered );
		$this->assertArrayNotHasKey( 'friendster', $assoc_non_registered );
		$this->assertArrayNotHasKey( 'digg', $assoc_non_registered );

		/**
		 * Test Numeric array.
		 *
		 * @var array
		 */
		$numer = $this->validation->setting_profile( array(
			'facebook',
			'twitter',
			'googleplus',
		) );

		$this->assertEmpty( $numer );

		/**
		 * Test Numeric array.
		 *
		 * @var array
		 */
		$empty = $this->validation->setting_profile( array() );
		$this->assertEmpty( $empty );

		/**
		 * Test Non-array Value (null).
		 */
		$null = $this->validation->setting_profile( null );
		$this->assertEmpty( $null );

		/**
		 * Test Non-array Value (false).
		 */
		$false = $this->validation->setting_profile( false );
		$this->assertEmpty( $false );

		/**
		 * Test Non-array Value (string).
		 */
		$string = $this->validation->setting_profile( 'false' );
		$this->assertEmpty( $string );

		/**
		 * Test Non-array Value (empty string).
		 */
		$string_empty = $this->validation->setting_profile( '' );
		$this->assertEmpty( $string_empty );
	}

	/**
	 * Test checkbox validation
	 *
	 * @return void
	 */
	public function test_validate_checkbox() {

		$this->assertEquals( 'on', $this->validation->validate_checkbox( 'on' ) ); // expected string.
		$this->assertEquals( 'on', $this->validation->validate_checkbox( 'yes' ) ); // random string.
		$this->assertEquals( 'on', $this->validation->validate_checkbox( true ) ); // bool.

		// Falsy.
		$this->assertEquals( false, $this->validation->validate_checkbox( false ) );
		$this->assertEquals( false, $this->validation->validate_checkbox( null ) );
		$this->assertEquals( false, $this->validation->validate_checkbox( '' ) );
	}

	/**
	 * Function to test button sites validation.
	 *
	 * @return void
	 */
	public function test_validate_include_sites_for_button_content() {

		$options = Options::button_sites( 'content' );
		$sites = array();

		foreach ( $options as $site => $data ) {
			$sites[ $site ] = array(
				'enable' => 'on',
				'label' => $data['label'],
			);
		}

		/**
		 * If all empty sites in the options must be enabled.
		 * The empty array assumes that there isn't data installed in the database yet.
		 */
		$this->assertEquals( $sites, $this->validation->validate_include_sites( array(), $options ) );

		/**
		 * Test a falsy value; non-array value.
		 *
		 * @var array
		 */
		$falsy = $this->validation->validate_include_sites( array(
			'facebook' => false,
		), $options );

		$this->assertArrayHasKey( 'facebook', $falsy );
		$this->assertEquals( array(
			'enable' => false,
			'label' => 'Facebook',
		), $falsy['facebook'] );

		/**
		 * Test value that is not yet registered in the options.
		 *
		 * @var array
		 */
		$non_exists = $this->validation->validate_include_sites( array(
			'ello' => array(
				'enable' => 'on',
				'label' => 'Send to Ello',
			),
		), $options );

		$this->assertArrayNotHasKey( 'ello', $non_exists );
	}
}
