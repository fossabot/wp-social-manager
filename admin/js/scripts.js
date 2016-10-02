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

			this.$control = this.$el.find( '[data-js-enabled]' );
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

	/**
	 * [initialize description;
	 */
	var ProfilesPreview = Backbone.View.extend( {
			events : {
				'keyup .account-profile-control' : 'previewUpdate'
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
			}, 100 ),

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


	// Instantiation;
	var args = {
		el: $( '#wp-social-manager-wrap' )
	};

	new MediaUploader( args );
	new ControlsView( args );
	new ProfilesPreview( args );

})( jQuery, window.wp, window._, window.Backbone, undefined );