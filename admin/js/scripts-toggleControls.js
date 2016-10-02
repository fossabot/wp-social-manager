;(function( $, wp, _, Backbone ) {

	'use strict';

	/**
	 * [initialize description]
	 */
	var ControlsView = Backbone.View.extend({

		events : {
			'click .toggle-control' : 'toggleControls',
		},

		/**
		 * [initialize description]
		 * @return {[type]} [description]
		 */
		initialize : function() {

			this.$control = this.$el.find( '.toggle-control' );
			this.$target = this.$el.find( this.$control.data( 'toggle' ) );

			this.toggleControls();
		},

		/**
		 * [toggleControls description]
		 * @return {[type]} [description]
		 */
		toggleControls : function() {

			var $control = this.$control;
			var $target = this.$target;

			$target.toggleClass( 'hide-if-js', ! $control.is( ':checked' ) );
		}
	});

	// Instantiation;
	new ControlsView( {
		el: $( '#wp-social-manager-wrap' )
	} );

})( jQuery, window.wp, window._, window.Backbone, undefined );