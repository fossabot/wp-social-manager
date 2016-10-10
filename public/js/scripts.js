(function( $, _, Backbone ) {

	'use strict';

	if ( _.isUndefined( wpSocialManager ) ) {
		return;
	}

	var api = wpSocialManager;

	if ( _.isUndefined( api.id ) ) {
		return;
	}

	var SocialButton = {
			Collection: {},
			Model: {},
			View: {}
		};

	/**
	 * [templateSettings description]
	 * @type {Object}
	 */
	_.templateSettings = {
		interpolate: /\{\{(.+?)\}\}/g
	};

	/**
	 * [Buttons description]
	 * @type {[type]}
	 */
	SocialButton.Model = Backbone.Model.extend( {
			urlRoot: ( api.root + api.namespace ) + '/buttons',
		} );

	/**
	 * [Buttons description]
	 * @type {[type]}
	 */
	SocialButton.View = Backbone.View.extend( {

		el: $( 'body' ),

		/**
		 * [events description]
		 * @type {Object}
		 */
		events : {
			'click a[role=button]' : 'buttonDialog'
		},

		/**
		 * [initialize description]
		 * @return {[type]} [description]
		 */
		initialize: function() {

			var $template = $( this.template );

			if ( 0 === $template.length ) {
				console.info( 'Template ' + this.template + ' is not available.' );
				return;
			}

			var $templateHTML = $template.html().trim();

			if ( '' === $templateHTML ) {
				console.info( 'Template HTML of ' + this.template + ' is empty.' );
				return;
			}

			_.bindAll( this, 'render' );

			this.template = _.template( $templateHTML );
			this.listenTo( this.model, 'change:id', this.render );
		},

		/**
		 * [buttonDialog description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		buttonDialog: function( event ) {

			var target = event.currentTarget;
			var social = target.getAttribute( 'data-social' );

			if ( social === "1"  ) {

				event.preventDefault();

				var source = target.getAttribute( 'href' );

				if ( ! source || '' !== source ) {
					this.windowPopup( source );
				}
			}
		},

		/**
		 * [windowPopup description]
		 * @param  {[type]} url [description]
		 * @return {[type]}     [description]
		 */
		windowPopup: function( url ) {

			var wind = window;
			var docu = document;

			var screenLeft = wind.screenLeft !== undefined ? wind.screenLeft : screen.left;
			var screenTop = wind.screenTop !== undefined ? wind.screenTop : screen.top;
			var screenWidth = wind.innerWidth ? wind.innerWidth : docu.documentElement.clientWidth ? docu.documentElement.clientWidth : screen.width;
			var screenHeight = wind.innerHeight ? wind.innerHeight : docu.documentElement.clientHeight ? docu.documentElement.clientHeight : screen.height;

			var width = 600;
			var height = 430;

			var left = ( ( screenWidth / 2 ) - ( width / 2 ) ) + screenLeft;
			var top = ( ( screenHeight / 2 ) - ( height / 2 ) ) + screenTop;

			var newWindow = wind.open( url, "", "scrollbars=no,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left );

			if ( newWindow ) {
				newWindow.focus();
			}
		}
	} );

	/**
	 * [Content description]
	 * @type {[type]}
	 */
	SocialButton.View.Content = SocialButton.View.extend( {

		template: '#tmpl-buttons-content',

		/**
		 * [render description]
		 * @return {[type]} [description]
		 */
		render: function() {

			var response = this.model.toJSON();

			$( '#' + api.attrPrefix + '-buttons-' + response.id )
				.append( this.template( response.content ) );

			return this;
		}
	} );

	SocialButton.View.Images = SocialButton.View.extend( {

		template: '#tmpl-buttons-image',

		render: function() {

			var self = this;
			var response = this.model.toJSON();
			var responseImage = response.image;

			/**
			 * Select Images in the respective content.
			 * @type {Object}
			 */
			var $images = $( '.' + api.attrPrefix + '-buttons--' + response.id );

				$images.each( function() {

					var $target = $( this );
					var imgSource = $target.find( 'img' ).attr( 'src' );

					if ( imgSource ) {

						// Add image cover to Pinterest image sharing.
						// @todo rewrite this function.
						responseImage.pinterest.endpoint = responseImage.pinterest.endpoint + '&media=' + imgSource;

						$target.append( self.template( responseImage ) );
					}
				} );

			return this;
		}
	} );

	/**
	 * [button description]
	 * @type {SocialButton}
	 */
	var socialButton = new SocialButton.Model();

		socialButton.fetch( {
			data : {
				id : api.id
			}
		} );

	/**
	 * [buttonContent description]
	 * @type {SocialButton}
	 */
	var buttonContent = new SocialButton.View.Content( {
				model: socialButton
			} );

	/**
	 * [buttonImage description]
	 * @type {SocialButton}
	 */
	var buttonImage = new SocialButton.View.Images( {
				model: socialButton
			} );

})( jQuery, window._, window.Backbone, undefined );
