/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^social" }]*/
(function(window, $) {

	'use strict';

	var tmplButtonContent,
		tmplButtonImage,
		socialButtonsContent,
		socialButtonsImage,
		socialButtonsModel;

	if (_.isUndefined(window.nineCodesSocialManagerAPI) ||
		_.isUndefined(window.nineCodesSocialManagerAPI.id)) {
		return;
	}

	tmplButtonContent = document.getElementById( 'tmpl-buttons-content' );
	tmplButtonImage = document.getElementById( 'tmpl-buttons-image' );

	if ( ! tmplButtonContent && ! tmplButtonImage ) {
		return;
	}

	window.nineCodesSocialManager = window.nineCodesSocialManagerAPI || {};

	nineCodesSocialManager.app = nineCodesSocialManager.app || {};
	nineCodesSocialManager.app.route = nineCodesSocialManager.root + nineCodesSocialManager.namespace;
	nineCodesSocialManager.app.sync = function(method, model, options) {

		_.extend(options, {
			url: nineCodesSocialManager.app.route + '/social-manager/buttons/' + (_.isFunction(model.url) ? model.url() : model.url)
		});

		return Backbone.sync(method, model, options);
	};

	nineCodesSocialManager.app.tmpl = _.memoize(function(id) {
		var compiled,
			options = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g,
				variable: 'data'
			};

		return function(data) {
			compiled = compiled || _.template($('#tmpl-' + id).html(), null, options);
			return compiled(data);
		};
	});

	nineCodesSocialManager.Buttons = nineCodesSocialManager.Buttons || {};
	nineCodesSocialManager.Buttons = {
		Collection: {},
		Model: {},
		View: {}
	};

	nineCodesSocialManager.Buttons.Model = Backbone.Model.extend({
		sync: nineCodesSocialManager.app.sync,
		defaults: {
			id: null,
			content: {},
			images: []
		}
	});

	nineCodesSocialManager.Buttons.View = Backbone.View.extend({

		el: 'body',

		initialize: function() {

			this.listenTo(this.model, 'change:id', this.render);
		},

		buttonDialog: function(event) {

			event.preventDefault();
			event.stopImmediatePropagation();

			var target = event.currentTarget,
				source = target.getAttribute('href');

			if (0 === source.indexOf('mailto:')) {
				window.location.href = source;
				return;
			}

			if (!source || '' !== source) {
				this.windowPopup(source);
				return;
			}

			return;
		},

		windowPopup: function(url) {

			var wind = window,
				docu = document,
				screenLeft = undefined !== wind.screenLeft ? wind.screenLeft : screen.left,
				screenTop = undefined !== wind.screenTop ? wind.screenTop : screen.top,
				screenWidth = wind.innerWidth ? wind.innerWidth : docu.documentElement.clientWidth ? docu.documentElement.clientWidth : screen.width,
				screenHeight = wind.innerHeight ? wind.innerHeight : docu.documentElement.clientHeight ? docu.documentElement.clientHeight : screen.height,

				width = 560,
				height = 430,
				divide = 2,

				left = screenWidth / divide - width / divide + screenLeft,
				top = screenHeight / divide - height / divide + screenTop,

				newWindow = wind.open(url, '', 'scrollbars=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left);

			if (newWindow) {
				newWindow.focus();
			}
		}
	});

	nineCodesSocialManager.Buttons.View.Content = nineCodesSocialManager.Buttons.View.extend({

		template: nineCodesSocialManager.app.tmpl('buttons-content'),

		events: {
			'click [data-social-buttons="content"] a': 'buttonDialog'
		},

		render: function(model) {

			var resp = model.toJSON();

			$('#' + nineCodesSocialManager.attrPrefix + '-buttons-' + resp.id)
				.append(this.template(resp.content));

			return this;
		}
	});

	nineCodesSocialManager.Buttons.View.Images = nineCodesSocialManager.Buttons.View.extend({

		template: nineCodesSocialManager.app.tmpl('buttons-image'),

		events: {
			'click [data-social-buttons="image"] a': 'buttonDialog'
		},

		render: function(model) {

			var resp = model.toJSON(),
				$images = $('.' + nineCodesSocialManager.attrPrefix + '-buttons--' + resp.id);

			$images.each(function(index, image) {
				$(image).append(this.template(resp.images));
			}.bind(this));

			return this;
		}
	});

	/**
	 * The model to interact with the Buttons API.
	 *
	 * @type {nineCodesSocialManager}
	 */
	socialButtonsModel = new nineCodesSocialManager.Buttons.Model();
	socialButtonsModel.url = nineCodesSocialManager.id;

	if ( tmplButtonContent ) {
		socialButtonsContent = new nineCodesSocialManager.Buttons.View.Content({
			model: socialButtonsModel
		});
	}

	if ( tmplButtonImage ) {
		socialButtonsImage = new nineCodesSocialManager.Buttons.View.Images({
			model: socialButtonsModel
		});
	}

	socialButtonsModel.fetch();

})(window, jQuery);
