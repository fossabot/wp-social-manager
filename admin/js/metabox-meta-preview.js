/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^metaPreview" }]*/
jQuery(function ($) {

	'use strict';

	var Metabox = {
			View: {},
			Model: {},
		},
		MetaPreview = {
			View: {},
		},
		metaPreview;

	/**
	 * Metabox View
	 *
	 * TODO: Move this View to its own file and let the object accessible in the 'window' object.
	 *
	 * @type {Object}
	 */
	Metabox.View = Backbone.View.extend({
		el: '#butterbean-ui-ncsocman',

		/**
		 * Function to add and parse template in the script element.
		 *
		 * @since 1.0.6
		 * @type {Function}
		 */
		tmplString: _.memoize(function (id) {
			var compiled,
				options = {
					evaluate: /<#([\s\S]+?)#>/g,
					interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
					escape: /\{\{([^\}]+?)\}\}(?!\})/g,
					variable: 'data'
				};

			return function (data) {
				compiled = compiled || _.template($('#tmpl-' + id).html(), null, options);
				return compiled(data);
			};
		})
	});

	Metabox.Model = Backbone.Model.extend({
		defaults: {
			"post": window.nineCodesSocialManager.post
		}
	});

	MetaPreview = Metabox.View.extend({
		events: {
			'click #button-display-meta-preview': 'togglePreview',
			'input #butterbean-control-post_title input': 'postTitleUpdate',
		},
		initialize: function () {
			this.$sectionMeta = this.$el.find('#butterbean-ncsocman-section-meta');
			this.defaults = this.model.get( 'post' );
		},
		togglePreview: function( event ) {
			console.log( event );
		},
		postTitleUpdate: _.throttle(function (event) {
			console.log( event.currentTarget.value );
		}),
		render: function () {
			this.metaPreview = this.tmplString('butterbean-control-meta-preview');
			this.$sectionMeta.prepend(this.metaPreview( this.defaults ));
		}
	});

	metaPreview = new MetaPreview({
		model: new Metabox.Model()
	});
	metaPreview.render();
});
