<?php
/**
 * Class TestValidation
 *
 * @package NineCodes\SocialMediaManager;
 * @subpackage Tests
 */

namespace NineCodes\SocialMediaManager;

/**
 * Load WP_UnitTestCase;
 */
use \WP_UnitTestCase;

/**
 * The class to test the "Validation" class.
 *
 * @since 1.0.0
 */
class TestValidation extends WP_UnitTestCase {

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
	public function test_setting_profiles() {

		/**
		 * Test Associative Array.
		 *
		 * @var array
		 */
		$assoc = $this->validation->setting_profiles( array(
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
		$assoc_empty = $this->validation->setting_profiles( array(
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
		$assoc_non_string = $this->validation->setting_profiles( array(
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
		$assoc_non_registered = $this->validation->setting_profiles( array(
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
		$numer = $this->validation->setting_profiles( array(
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
		$empty = $this->validation->setting_profiles( array() );
		$this->assertEmpty( $empty );

		/**
		 * Test Non-array Value (null).
		 */
		$null = $this->validation->setting_profiles( null );
		$this->assertEmpty( $null );

		/**
		 * Test Non-array Value (false).
		 */
		$false = $this->validation->setting_profiles( false );
		$this->assertEmpty( $false );

		/**
		 * Test Non-array Value (string).
		 */
		$string = $this->validation->setting_profiles( 'false' );
		$this->assertEmpty( $string );

		/**
		 * Test Non-array Value (empty string).
		 */
		$string_empty = $this->validation->setting_profiles( '' );
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
		$this->assertEmpty( $this->validation->validate_checkbox( false ) );
		$this->assertEmpty( $this->validation->validate_checkbox( null ) );
		$this->assertEmpty( $this->validation->validate_checkbox( '' ) );
	}

	/**
	 * Test multi-checkbox validation
	 *
	 * @return void
	 */
	public function test_is_array_associative() {

		$this->assertFalse( $this->validation->is_array_associative( array( 'foo', 'bar' ) ) ); // un-expected string.
		$this->assertTrue( $this->validation->is_array_associative( array( 'foo' => 'Foo', 'bar' => 'Bar' ) ) ); // expected string.
	}
}
