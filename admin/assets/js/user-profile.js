/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^preview" }]*/
jQuery(function($) {

	'use strict';

	/**
	 * The Backbone View to preview the "Social Profile" URL.
	 */

	var ninecodes = window.ninecodes || {},
		InputProfiles = {
			View: {}
		},
		PreviewProfiles,
		previewProfiles;

	InputProfiles.View = Backbone.View.extend({
		el: '.account-profile-control'
	});

	PreviewProfiles = InputProfiles.View.extend({

		events: {
			'input': 'previewUpdate'
		},

		/**
		 * Initialize the View
		 * On page load, render the preview if the value is set in the input.
		 *
		 * @return {void}
		 */
		initialize: function() {

			this.wait = 150;
			this.previewInit();
		},

		/**
		 * Function to render preview on page load.
		 *
		 * @return {void}
		 */
		previewInit: function() {

			var self = this;

			this.$el.each(function() {
				self.createPlaceholder(this);
				self.render(this);
			});
		},

		/**
		 * Function to update the preview content,
		 * when the user type in the input.
		 *
		 * @type {void}
		 */
		previewUpdate: _.throttle(function(event) {
			this.render(event.currentTarget);
		}),

		/**
		 * Function to render the preview placeholder element.
		 *
		 * @param {object} target The JavaScript element object.
		 * @return {void}
		 */
		render: function(target) {

			var attrID = target.getAttribute('id'),
				inputValue = this.getValue(target),
				inputUrlTmpl = ninecodes.templateString(target.getAttribute('data-url'));

			if (inputUrlTmpl && _.isEmpty(inputUrlTmpl)) {

				$('#' + attrID + '-preview').html(function () {

					var $preview = $(this).siblings().not('input');

					$preview.toggleClass('hide-if-js', '' !== inputValue);

					return '' !== inputValue ? '<code>' + inputUrlTmpl({ profile: inputValue }) + '</code>' : '';
				});
			}

			return this;
		},

		/**
		 * Function to create the placeholder element.
		 *
		 * @param {object} target The JavaScript element object.
		 * @return {object} The JavaScript element object of the placeholder.
		 */
		createPlaceholder: function(target) {

			var attrID = target.getAttribute('id');

			return $(target).after('<p id=' + attrID + '-preview></p>');
		},

		/**
		 * Function to get the value of the input.
		 *
		 * @param {object} target The JavaScript element object.
		 * @return {string} The input value.
		 */
		getValue: function(target) {

			var value = target.value.replace(/\s+/g, '-'); // Replace a space with a dash.

			target.value = value;

			return value;
		}
	});

	/**
	 * Instantiation
	 *
	 * @type {PreviewProfiles}
	 */
	previewProfiles = new PreviewProfiles();
});
