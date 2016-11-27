/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^social" }]*/
(function( $ ) {

	'use strict';

	var api,
		SocialButtons,
		socialButtonsContent,
		socialButtonsImage,
		$template,
		$templateHTML;

	if ( _.isUndefined( nineCodesSocialManager ) ||
		 _.isUndefined( nineCodesSocialManager.id ) ) {
		return;
	}

	_.templateSettings = {
		interpolate: /\{\{(.+?)\}\}/g
	};

	api = nineCodesSocialManager;
	api.route = api.root + api.namespace;
	api.sync = function(method, model, options) {

		_.extend(options, {
			url: api.route + (_.isFunction(model.url) ? model.url() : model.url)
		});

		return Backbone.sync(method, model, options);
	};

	SocialButtons = {
		Collection: {},
		Model: {},
		View: {}
	};

	SocialButtons.Model = Backbone.Model.extend({
		sync: api.sync,
		url: '/social-manager/buttons/' + api.id,
		defaults: {
			id: null,
			content: {},
			images: []
		}
	});

	SocialButtons.View = Backbone.View.extend({

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

			var target = event.currentTarget,
				source = target.getAttribute( 'href' );

			if ( ! source || '' !== source ) {
				this.windowPopup( source );
				return;
			}

			return;
		},

		windowPopup: function( url ) {

			var wind = window,
				docu = document,
				screenLeft = undefined !== wind.screenLeft ? wind.screenLeft : screen.left,
				screenTop = undefined !== wind.screenTop ? wind.screenTop : screen.top,
				screenWidth = wind.innerWidth ? wind.innerWidth : docu.documentElement.clientWidth ? docu.documentElement.clientWidth : screen.width,
				screenHeight = wind.innerHeight ? wind.innerHeight : docu.documentElement.clientHeight ? docu.documentElement.clientHeight : screen.height,

				width = 560,
				height = 430,
				divide = 2,

				left = screenWidth / divide  -  width / divide   + screenLeft,
				top = screenHeight / divide  -  height / divide   + screenTop,

				newWindow = wind.open( url, '', 'scrollbars=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left );

			if ( newWindow ) {
				newWindow.focus();
			}
		}
	});

	SocialButtons.View.Content = SocialButtons.View.extend({

		template: '#tmpl-buttons-content',

		events: {
			'click [data-social-buttons="content"] a': 'buttonDialog'
		},

		render: function( model ) {

			var resp = model.toJSON();

			$( '#' + api.attrPrefix + '-buttons-' + resp.id )
				.append( this.template({
					data: resp.content
				}) );

			return this;
		}
	});

	SocialButtons.View.Images = SocialButtons.View.extend({

		template: '#tmpl-buttons-image',

		events: {
			'click [data-social-buttons="image"] a': 'buttonDialog'
		},

		render: function( model ) {

			var self = this,
				resp = model.toJSON(),
				$images = $( '.' + api.attrPrefix + '-buttons--' + resp.id );

			$images.each( function( index ) {
				$( this ).append( self.template({
					data: resp.images[index]
				}) );
			});

			return this;
		}
	});

	SocialButtons.Model = new SocialButtons.Model();
	SocialButtons.Model.fetch();

	socialButtonsContent = new SocialButtons.View.Content({
		model: SocialButtons.Model
	});

	socialButtonsImage = new SocialButtons.View.Images({
		model: SocialButtons.Model
	});

})( jQuery );
