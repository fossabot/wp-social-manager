jQuery(function( $ ) {

	'use strict';

	// Use buttonset() for radio images.
	$( '.customize-control-radio-image .buttonset' ).buttonset();

	// Handles setting the new value in the customizer.
	$( '.customize-control-radio-image input:radio' ).change(
		function() {

			var setting = $( this ).attr( 'data-customize-setting-link' ), // Get the name of the setting.
				image = $( this ).val(); // Get the value of the currently-checked radio input.

			// Set the new value.
			wp.customize( setting, function( obj ) {
				obj.set( image );
			} );
		}
	);
});
