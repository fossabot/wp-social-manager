;(function( $, wp, _, Backbone ) {

	'use strict';

	var SocialProfiles = {
			View: {}
		};

	/**
	 * [initialize description;
	 */
	SocialProfiles.View = Backbone.View.extend( {

		events : {
			'input' : 'previewUpdate'
		},

		/**
		 * [initialize description]
		 * @return {[type]} [description]
		 */
		initialize : function() {
			this.previewInit();
		},

		/**
		 * [loadPreview description]
		 * @return {[type]} [description]
		 */
		previewInit : function() {

			var self = this;

			this.$el.each( function() {
				self.createPlaceholder( this );
				self.render( this );
			} );
		},

		/**
		 * [loadPreview description]
		 * @return {[type]} [description]
		 */
		previewUpdate : _.throttle( function( event ) {
			this.render( event.currentTarget );
		}, 150 ),

		/**
		 * [getPreview description]
		 * @param  {[type]} elem [description]
		 * @return {[type]}      [description]
		 */
		render : function( target ) {

			var url  = target.getAttribute( 'data-url' );

			if ( url && '' !== url ) {

				var id = target.getAttribute( 'id' );
				var value = this.getValue( target );

				$( '#' + id + '-preview' ).html( function() {

					var $this = $( this );
					var $siblings = $this.siblings().not( 'input' );
						$siblings.toggleClass( 'hide-if-js', '' !== value );

						return ( '' !== value ) ? '<code>' + url + value + '</code>' : '';
				} );
			}
		},

		/**
		 * [createPlaceholder description]
		 * @param  {[type]} target [description]
		 * @return {[type]}        [description]
		 */
		createPlaceholder: function( target ) {

			var attrID = target.getAttribute( 'id' );

			return $( target ).after( '<p id='+ attrID +'-preview></p>' );
		},

		/**
		 * [getValue description]
		 * @param  {[type]} target [description]
		 * @return {[type]}        [description]
		 */
		getValue: function( target ) {

			var value = target.value.replace( /\s+/g, '-' );

			target.value = value;

			return value;
		}
	} );

	new SocialProfiles.View( {
			el: '.account-profile-control'
		} );

})( jQuery, window.wp, window._, window.Backbone, undefined );