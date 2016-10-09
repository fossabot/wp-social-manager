(function( $, _, Backbone ) {

	'use strict';

	if ( _.isUndefined( wpSocialManager ) ) {
		return;
	}

	var api = wpSocialManager;

	if ( _.isUndefined( api.id ) ) {
		return;
	}

	_.templateSettings = {
		interpolate: /\{\{(.+?)\}\}/g
	};

	var $tmplContent = $( '#tmpl-buttons-content' );
	var $tmplImage = $( '#tmpl-buttons-image' );

	/**
	 * [Buttons description]
	 * @type {[type]}
	 */
	var	Buttons = Backbone.Model.extend( {
				urlRoot : ( api.root + api.namespace ) + '/buttons'
			} );

	if ( 0 !== $tmplContent.length ) {

		var	ButtonsContent = Backbone.View.extend( {
				model: new Buttons,
				template: _.template( $tmplContent.html() ),
				initialize: function() {

					this.model.bind( 'change', this.render, this );
					this.model.fetch( {
						data : {
							id : api.id
						}
					} );
				},
				render: function() {

					var resp = this.model.toJSON();

					$( '#' + api.attrPrefix + '-buttons-' + resp.id )
						.append( this.template( resp.content ) );

					return this;
				}
		} );

		new ButtonsContent();
	}

	if ( 0 !== $tmplImage.length ) {

		var	ButtonsImage = Backbone.View.extend( {
			model: new Buttons,
			template: _.template( $tmplImage.html() ),
			initialize: function() {

				if ( 0 === this.$el.length ) {
					return;
				}

				this.model.bind( 'change', this.render, this );
				this.model.fetch( {
					data : {
						id : api.id
					}
				} );
			},
			render: function() {

				var self = this;
				var resp = this.model.toJSON();
				var $images = $( '.' + api.attrPrefix + '-buttons--post-' + resp.id  );

				$images.each( function() {

					var $elem = $( this );
					var $elemSrc = $elem.find( 'img' ).attr( 'src' );

					if ( $elemSrc ) {

						// Add image cover to Pinterest image sharing.
						// @todo improve this function.
						resp.image.pinterest.endpoint = resp.image.pinterest.endpoint + '&media=' + $elemSrc;

						$elem.append( self.template( resp.image ) );
					}
				} );

				return this;
			}
		} );

		new ButtonsImage();
	}

})( jQuery, window._, window.Backbone, undefined );
