(function( $, undefined ) {

	'use strict';

	var api, target, source, SocialButton;
	var $template, $templateHTML;

	if ( 'undefined' === typeof nineCodesSocialManager ) {
		return;
	}

	api = nineCodesSocialManager;

	if ( _.isUndefined( api.id ) ) {
		return;
	}

	SocialButton = {
		Collection: {},
		Model: {},
		View: {}
	};

	_.templateSettings = {
		interpolate: /\{\{(.+?)\}\}/g
	};

	SocialButton.Model = Backbone.Model.extend({
		urlRoot: ( api.root + api.namespace ) + '/buttons',
		defaults: {
			id: null
		}
	});

	SocialButton.View = Backbone.View.extend({

		el: 'body',

		initialize: function() {

			$template = $( this.template );

			if ( 0 === $template.length ) {
				console.info( 'Template ' + this.template + ' is not available.' );
				return;
			}

			$templateHTML = $template.html().trim();

			if ( '' === $templateHTML ) {
				console.info( 'Template HTML of ' + this.template + ' is empty.' );
				return;
			}

			this.template = _.template( $templateHTML );
			this.listenTo( this.model, 'change:id', this.render );
		},

		buttonDialog: function( event ) {

			event.preventDefault();
			event.stopImmediatePropagation();

			target = event.currentTarget;
			source = target.getAttribute( 'href' );

			if ( ! source || '' !== source ) {
				this.windowPopup( source );
				return;
			}

			return;
		},

		windowPopup: function( url ) {

			var wind = window;
			var docu = document;

			var screenLeft = undefined !== wind.screenLeft ? wind.screenLeft : screen.left;
			var screenTop = undefined !== wind.screenTop ? wind.screenTop : screen.top;
			var screenWidth = wind.innerWidth ? wind.innerWidth : docu.documentElement.clientWidth ? docu.documentElement.clientWidth : screen.width;
			var screenHeight = wind.innerHeight ? wind.innerHeight : docu.documentElement.clientHeight ? docu.documentElement.clientHeight : screen.height;

			var width = 560;
			var height = 430;

			var left = ( ( screenWidth / 2 ) - ( width / 2 ) ) + screenLeft;
			var top = ( ( screenHeight / 2 ) - ( height / 2 ) ) + screenTop;

			var newWindow = wind.open( url, '', 'scrollbars=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left );

			if ( newWindow ) {
				newWindow.focus();
			}
		}
	});

	SocialButton.View.Content = SocialButton.View.extend({

		template: '#tmpl-buttons-content',

		events: {
			'click [data-social-buttons="content"] a': 'buttonDialog'
		},

		render: function( model, resp, req ) {

			var response = model.toJSON();

			$( '#' + api.attrPrefix + '-buttons-' + req.data.id )
				.append( this.template({
					data: response.content
				}) );

			return this;
		}
	});

	SocialButton.View.Images = SocialButton.View.extend({

		template: '#tmpl-buttons-image',

		events: {
			'click [data-social-buttons="image"] a': 'buttonDialog'
		},

		render: function( model, resp, req ) {

			var self = this;
			var response = model.toJSON();
			var $images = $( '.' + api.attrPrefix + '-buttons--' + req.data.id );

			$images.each( function( index ) {
				$( this ).append( self.template({
					data: response.images[index]
				}) );
			});

			return this;
		}
	});

	SocialButton.Model = new SocialButton.Model();
	SocialButton.Model.fetch({
		data: {
			id: api.id
		}
	});

	new SocialButton.View.Content({
		model: SocialButton.Model
	});

	new SocialButton.View.Images({
		model: SocialButton.Model
	});

})( jQuery );
