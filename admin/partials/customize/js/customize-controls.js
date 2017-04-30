jQuery(function( $ ) {

	'use strict';

	console.log( $( '.customize-control-ncsocman-radio-image .buttonset' ) );

	/**
	 * Control: Button Style.
	 *
	 * @see control-radio-image.php
	 */
	$( '.customize-control-ncsocman-radio-image .buttonset' ).buttonset(); // Use buttonset() for radio images.
	$( '.customize-control-ncsocman-radio-image input' ).change( function() { // Handles setting the new value in the customizer.

		var setting = $( this ).attr( 'data-customize-setting-link' ), // Get the name of the setting.
			image = $( this ).val(); // Get the value of the currently-checked radio input.

		wp.customize( setting, function( obj ) { // Set the new value.
			obj.set( image );
		} );
	});
});
