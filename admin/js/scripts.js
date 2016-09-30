(function( $, wp ) {

	'use strict';


	/**
	 * [initialize description]
	 */
	var ControlView = Backbone.View.extend({

		events : {
			'click .toggle-switch-control' : 'toggleControls',
		},

		/**
		 * [initialize description]
		 * @return {[type]} [description]
		 */
		initialize : function() {

			this.$control = this.$el.find( '.toggle-switch-control' );
			this.$target = this.$el.find( this.$control.data( 'toggle-target' ) );

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


	/**
	 * [initialize description]
	 */
	var MediaUploader = Backbone.View.extend({

		events : {
			'click .button-add-media' : 'selectMedia',
			'click .button-change-media' : 'selectMedia',
			'click .button-remove-media' : 'removeMedia'
		},

		/**
		 * [initialize description]
		 * @return {[type]} [description]
		 */
		initialize : function() {

			this.wpMediaUploader;

			this.wpMedia();
		},

		/**
		 * [selectMedia description]
		 * @param  {[type]} button [description]
		 * @return {[type]}        [description]
		 */
		selectMedia : function( button ) {

			this.controls( button );

			this.wpMediaWindow();
		},

		/**
		 * [removeMedia description]
		 * @param  {[type]} button [description]
		 * @return {[type]}        [description]
		 */
		removeMedia : function( button ) {

			this.controls( button );

			this.$input.val( '' );
			this.$inputImg.html( '' );
			this.controlState( '' );
		},

		/**
		 * [controls description]
		 * @param  {[type]} button [description]
		 * @return {[type]}        [description]
		 */
		controls : function( button ) {

			var inputId = $( button.target ).data( 'input' );

			this.$input = $( inputId );

			this.$inputWrap = $( inputId + '-wrap' );
			this.$inputImg = $( inputId + '-img' );

			this.$controlAdd = $( inputId + '-add' );
			this.$controlChange = $( inputId + '-change' );
			this.$controlRemove = $( inputId + '-remove' );
		},

		/**
		 * [controlState description]
		 * @param  {[type]} imgUrl [description]
		 * @return {[type]}        [description]
		 */
		controlState : function( imgUrl ) {

			var state = ( imgUrl === this.$input.val() && '' !== imgUrl );

			this.$inputWrap.toggleClass( 'is-set', state );

			this.$controlAdd.toggleClass( 'hide-if-js', state );
			this.$controlChange.toggleClass( 'hide-if-js', !state );
			this.$controlRemove.toggleClass( 'hide-if-js', !state );

			this.$inputImg.html( function() {

				var imgEl = '';

				if ( imgUrl ) {
					imgEl = document.createElement( 'img' );
					imgEl.src = imgUrl;
				}
				return imgEl;
			} );
		},

		/**
		 * [wpMedia description]
		 * @return {[type]} [description]
		 */
		wpMedia : function() {
			this.wpMediaUploader = wp.media.frames.file_frame = wp.media({
				title: 'Set as image',
				button: {
					text: 'Set as image'
				},
				multiple: false
			} );
		},

		/**
		 * [wpMediaWindow description]
		 * @return {[type]} [description]
		 */
		wpMediaWindow : function() {

			this.wpMediaUploader.open();

			this.wpMediaUploader.on( 'select', function() {
				this.wpMediaSelect();
			}.bind( this ) );
		},

		/**
		 * [wpMediaSelect description]
		 * @return {[type]} [description]
		 */
		wpMediaSelect : function() {

			var attach = this.wpMediaUploader.state().get('selection').first().toJSON();
			var attachURL = attach[ 'url' ];

			this.$input.val( attachURL );
			this.controlState( attachURL );
		}
	});


	// Instantiation;
	var args = {
		el: $( '#wp-social-manager-screen' )
	};

	new MediaUploader( args );
	new ControlView( args );

})( jQuery, window.wp );
