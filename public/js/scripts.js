(function( $, _, Backbone ) {

	'use strict';

	if ( _.isUndefined( wpSocialManager ) ) {
		return;
	}

	if ( _.isUndefined( wpSocialManager.postId ) ) {
		return;
	}

	_.templateSettings = {
		interpolate: /\{\{(.+?)\}\}/g
	};

	var root = wpSocialManager.root; // WP-JSON root.
	var namespace = wpSocialManager.namespace; // The plugin API route namespace.
	var postId = wpSocialManager.postId;

	/**
	 * [request description]
	 * @type {Object}
	 */
	var request = {};
		request.postId = parseInt( wpSocialManager.postId, 10 );

	$.ajax({
		url : root + namespace + '/buttons',
		data : request,
		dataType : 'json',
	} )
	.done( function( response ) {

		if ( _.isUndefined( response ) || ! _.isObject( response ) ) {
			return;
		}

		var prefix = wpSocialManager.attrPrefix;
		var $wrap = $( '#' + prefix + '-buttons-' + postId );

		$wrap.append( function() {

			var	template = _.template( $( '#tmpl-buttons-content' ).html() );

			return template( response );
		} );
	} );

})( jQuery, window._, window.Backbone, undefined );
