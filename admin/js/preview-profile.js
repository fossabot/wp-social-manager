;(function( $, wp, _, Backbone ) {

	'use strict';

	/**
	 * [initialize description;
	 */
	var ProfilesPreview = Backbone.View.extend( {

			events : {
				'input .account-profile-control' : 'previewUpdate'
			},

			/**
			 * [initialize description]
			 * @return {[type]} [description]
			 */
			initialize : function() {

				this.$controls = this.$el.find( '.account-profile-control' );
				this.previewInit();
			},

			/**
			 * [loadPreview description]
			 * @return {[type]} [description]
			 */
			previewInit : function() {

				this.$controls.each( function( index, elem ) {
					this.getPreview( elem );
				}.bind( this ) );
			},

			/**
			 * [loadPreview description]
			 * @return {[type]} [description]
			 */
			previewUpdate : _.throttle( function( event ) {
				this.getPreview( event.currentTarget );
			}, 200 ),

			/**
			 * [getPreview description]
			 * @param  {[type]} elem [description]
			 * @return {[type]}      [description]
			 */
			getPreview : function( elem ) {

				var target = this.getTarget( $( elem ) );

					target.sibling.toggleClass( 'hide-if-js', '' !== target.val );
					target.preview.toggleClass( function() {

						var $this = $( this );
						var $code = $this.find( 'code' );

						$code.text( '' !== target.val ? target.url + target.val : '' );

						return 'hide-if-js';

					}, '' === target.val );
			},

			/**
			 * [getTarget description]
			 * @param  {[type]} $elem [description]
			 * @return {[type]}       [description]
			 */
			getTarget : function( $elem ) {

				var url = $.trim( $elem.data( 'url' ) );
				var val = $.trim( $elem.val() );

				var $preview = $elem.siblings( '.account-profile-preview' );
				var $sibling = $preview.nextAll();

				return {
					'url' : url,
					'val' : val,
					'preview' : $preview,
					'sibling' : $sibling
				}
			}
	} );

	new ProfilesPreview( {
			el: $( '#wp-social-manager-wrap' )
		} );

})( jQuery, window.wp, window._, window.Backbone, undefined );