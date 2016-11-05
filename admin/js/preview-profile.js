/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^_" }]*/
(function( $ ) {

	'use strict';

    /**
     * The Backbone View to preview the "Social Profile" URL.
     */
	var _socialProfiles,
		SocialProfiles = Backbone.View.extend({

        // Events the input should listen to.
			events: {
				'input': 'previewUpdate'
			},

        /**
         * Initialize the View
         * On page load, render the preview if the value is set in the input.
         *
         * @return {Void} This is executed on initialization, and does not return anything.
         */
			initialize: function() {
				this.wait = 150;
				this.previewInit();
			},

			previewInit: function() {

				var self = this;

				this.$el.each(function() {
					self.createPlaceholder( this );
					self.render( this );
				});
			},

			previewUpdate: _.throttle(function( event ) {
				this.render( event.currentTarget );
			}, this.wait ),

			render: function( target ) {

				var id, value, url = target.getAttribute( 'data-url' );

				if ( url && '' !== url ) {

					id = target.getAttribute( 'id' );
					value = this.getValue( target );

					$( '#' + id + '-preview' ).html(function() {

						var $this = $( this ),
							$siblings = $this.siblings().not( 'input' );

						$siblings.toggleClass( 'hide-if-js', '' !== value );

						return  '' !== value  ? '<code>' + url + value + '</code>' : '';
					});
				}

				return this;
			},

			createPlaceholder: function( target ) {

				var attrID = target.getAttribute( 'id' );

				return $( target ).after( '<p id=' + attrID + '-preview></p>' );
			},

			getValue: function( target ) {

				var value = target.value.replace( /\s+/g, '-' );

				target.value = value;

				return value;
			}
		});

	_socialProfiles = new SocialProfiles({
		el: '.account-profile-control'
	});

})( jQuery );
