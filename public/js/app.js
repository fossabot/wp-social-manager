/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^button" }]*/
(function(window, $) {

	'use strict';

	var tmplButtonContent,
		tmplButtonImage,
		buttonContent,
		buttonImage,
		buttonModel;

	if (_.isUndefined(window.nineCodesSocialManagerAPI) ||
		_.isUndefined(window.nineCodesSocialManagerAPI.id)) {
		return;
	}

	tmplButtonContent = document.getElementById('tmpl-buttons-content');
	tmplButtonImage = document.getElementById('tmpl-buttons-image');

	if (!tmplButtonContent && !tmplButtonImage) {
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

	/**
	 * Function to add and parse template in the script element.
	 *
	 * @since 1.0.6
	 * @type {Function}
	 */
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

	/**
	 * Function to parse template string.
	 *
	 * @since 1.0.6
	 * @type {Function}
	 */
	nineCodesSocialManager.app.tmplString = _.memoize(function(string) {
		var compiled,
			options = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g,
				variable: 'data'
			};

		return function(data) {
			compiled = compiled || _.template(string, null, options);
			return compiled(data);
		};
	});

	nineCodesSocialManager.Button = nineCodesSocialManager.Button || {};
	nineCodesSocialManager.Button = {
		Collection: {},
		Model: {},
		View: {}
	};

	nineCodesSocialManager.Button.Model = Backbone.Model.extend({
		sync: nineCodesSocialManager.app.sync,
		defaults: {
			id: null,
			content: {},
			images: []
		}
	});

	/**
	 * Backbone view for button.
	 *
	 * @type {Backbone}
	 */
	nineCodesSocialManager.Button.View = Backbone.View.extend({

		el: 'body',

		constructor: function() {

			/**
			 * The Underscore Templates
			 *
			 * @since 1.0.6
			 * @type {Object}
			 */
			this.template = {
				buttonsContent: nineCodesSocialManager.app.tmpl('buttons-content'),
				buttonsImage: nineCodesSocialManager.app.tmpl('buttons-image'),
				imgWrapper: nineCodesSocialManager.app.tmplString('<span class="{{ data.prefix }}-buttons {{ data.prefix }}-buttons--img {{ data.prefix }}-buttons--{{ data.id }}"></span>'),
			};

			Backbone.View.apply(this, arguments);
		},

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

	/**
	 * Social Buttons View for Content.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Change the click Event delegate element.
	 *        		- Remove 'template' (merge with the `nineCodesSocialManager.Button.View`).
	 * @type {nineCodesSocialManager}
	 */
	nineCodesSocialManager.Button.View.Content = nineCodesSocialManager.Button.View.extend({

		/**
		 * DOM Events
		 *
		 * @since 1.0.0
		 * @since 1.0.6 - Change the click Event delegate element.
		 * @type {Object}
		 */
		events: {
			'click [data-social-manager="button-content"] a': 'buttonDialog'
		},

		/**
		 * The function method to render the Buttons Image.
		 *
		 * @param {Object} model nineCodesSocialManager.Button.Model
		 * @return {Object} nineCodesSocialManager.Button.View.Content
		 */
		render: function(model) {

			var resp = model.toJSON(),
				endpoints = resp.content.endpoints,
				$content = $('#' + nineCodesSocialManager.attrPrefix + '-buttons-' + resp.id);

			try {

				/**
				 * Filter endpoint with falsy value.
				 *
				 * @type {Object}
				 */
				resp.content.endpoints = _.omit(endpoints, function(value) {
					return ! value;
				});

				$content.append(this.template.buttonsContent(resp.content));
			} catch ( err ) {
				console.info(err.name, err.message);
			}

			return this;
		}
	});

	/**
	 * Social Buttons View for Images.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 - Remove 'template' (merge with the `nineCodesSocialManager.Button.View`).
	 * @type {nineCodesSocialManager}
	 */
	nineCodesSocialManager.Button.View.Images = nineCodesSocialManager.Button.View.extend({

		/**
		 * DOM Events
		 *
		 * @since 1.0.0
		 * @since 1.0.6 - Change the click Event delegate element.
		 * @type {Object}
		 */
		events: {
			'click [data-social-manager="button-image"] a': 'buttonDialog'
		},

		/**
		 * The function method to render the Buttons Image.
		 *
		 * @param {Object} model nineCodesSocialManager.Button.Model
		 * @return {Object} nineCodesSocialManager.Button.View.Images
		 */
		render: function(model) {

			var self = this,
				resp = model.toJSON(),
				span = this.template.imgWrapper({
					id: resp.id,
					prefix: nineCodesSocialManager.attrPrefix
				}),
				$images = $('[data-social-manager="content-image-'+ resp.id +'"]');

			$images.each(function(i, img) {

				try {
					var imageAttrs = _.reduce(img.attributes, function(attrs, attribute) {
							attrs[attribute.name] = attribute.value;
							return attrs;
						}, {}),
						imageSrcResp = resp.images[i].src;

					if (_.contains(imageAttrs, imageSrcResp)) {
						$(img).wrap(span).after(self.template.buttonsImage(resp.images[i]));
					}
				} catch ( err ) {
					console.info(err.name, err.message);
				}
			});

			return this;
		}
	});

	/**
	 * The model to interact with the Buttons API.
	 *
	 * @type {nineCodesSocialManager}
	 */
	buttonModel = new nineCodesSocialManager.Button.Model();
	buttonModel.url = nineCodesSocialManager.id;

	if (tmplButtonContent) {

		/**
		 * Instantiate the View to render Buttons Content.
		 *
		 * @type {nineCodesSocialManager}
		 */
		buttonContent = new nineCodesSocialManager.Button.View.Content({
			model: buttonModel
		});
	}

	if (tmplButtonImage) {

		/**
		 * Instantiate the View to render Buttons Image.
		 *
		 * @type {nineCodesSocialManager}
		 */
		buttonImage = new nineCodesSocialManager.Button.View.Images({
			model: buttonModel
		});
	}

	// Fetch data from the API.
	buttonModel.fetch();

})(window, jQuery);
