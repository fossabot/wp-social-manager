;(function( $, Backbone ) {
    'use strict';

    /**
     * The Backbone View to toggle controls visibiity.
     */
    var ControlsView = Backbone.View.extend({

        // The events the toggle trigger should listen to.
        events: {
            'click': 'toggleControls'
        },

        /**
         * Initialize the View.
         * Get the target elements attached to the trigger ($el),
         * and set the elements visibiity on page load.
         */
        initialize: function() {

            this.$target = $( this.$el.data( 'toggle' ) );

            this.toggleControls();
        },

        /**
         * The function that toggles the target elements
         * following the trigger value.
         */
        toggleControls: function() {

            this.$target.toggleClass( 'hide-if-js', ! this.$el.is( ':checked' ) );
        }
    });

    // Instantiation;
    new ControlsView({
        el: $( '#wp-social-manager-wrap' ).find( '.toggle-control' ) // The trigger element.
    });

})( jQuery, window.Backbone, undefined );
