/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^checkbox" }]*/
jQuery(function($) {

	'use strict';

	var Checkbox = {
			View: {}
		},
		CheckboxControls,
		checkboxControls;

	Checkbox.View = Backbone.View.extend({
		el: $('#ninecodes-social-manager-settings').find('.field-checkbox')
	});

	CheckboxControls = Checkbox.View.extend({

		events: {
			'click': 'checkControls'
		},

		/**
		 * Initialize the View.
		 *
		 * Get the target elements attached to the trigger ($el),
		 * and set the elements visibiity on page load.
		 *
		 * @return {void}
		 */
		initialize: function() {

			this.$targetToggle = $(this.$el.data('selector-toggle'));
			this.checkControls();
		},

		/**
		 * The function that toggles the target elements
		 * following the trigger value.
		 *
		 * @return {void}
		 */
		checkControls: function() {

			if ( 0 !== this.$targetToggle.length ) {
				this.$targetToggle.toggleClass('hide-if-js', !this.$el.is(':checked'));
			}
		},
	});

	/**
	 * Instantiation
	 *
	 * @type {ToggleControls}
	 */
	checkboxControls = new CheckboxControls();
});
