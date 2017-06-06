(function( window ) {

	'use strict';

	window.ninecodes = window.ninecodes || {};
	ninecodes.socialManager = ninecodes.socialManager || {};

	/**
	 * Parse a template passed directly as a string
	 *
	 * @since 1.0.6
	 * @type {function}
	 */
	ninecodes.templateString = _.memoize(function(string) {
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

})( window );
