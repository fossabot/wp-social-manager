;(function( $, _, Backbone ) {

    'use strict';

    /**
     * The Backbone View to preview the "Social Profile" URL.
     */
    var SocialProfiles = Backbone.View.extend({

        // Events the input should listen to.
        events: {
            'input': 'previewUpdate'
        },

        /**
         * Initialize the View
         * On page load, render the preview if the value is set in the input.
         */
        initialize: function() {
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
        }, 150 ),

        render: function( target ) {

            var id, value, url = target.getAttribute( 'data-url' );

            if ( url && '' !== url ) {

                id = target.getAttribute( 'id' );
                value = this.getValue( target );

                $( '#' + id + '-preview' ).html(function() {

                    var $this = $( this );

                    var $siblings = $this.siblings().not( 'input' );
                    $siblings.toggleClass( 'hide-if-js', '' !== value );

                    return ( '' !== value ) ? '<code>' + url + value + '</code>' : '';
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

    new SocialProfiles({
        el: '.account-profile-control'
    });

})( jQuery, window._, window.Backbone, undefined );
