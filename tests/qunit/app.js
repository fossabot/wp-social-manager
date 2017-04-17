/* eslint-env qunit */
/**
 * Test app.js
 */
QUnit.module('public/js/app.js', function() {

	'use strict';

	var nineCodesSocialManager = window.nineCodesSocialManager;

	QUnit.test( 'nineCodesSocialManager', function( assert ) {

     	assert.ok( ! _.isUndefined( nineCodesSocialManager ), '"nineCodesSocialManager" is defined' );
		assert.equal( typeof nineCodesSocialManager, 'object', '"nineCodesSocialManager" is an Object' );
	});

	QUnit.test( 'nineCodesSocialManager.app', function( assert ) {

		assert.equal( typeof nineCodesSocialManager.app, 'object', '"nineCodesSocialManager.app" is an Object' );
		assert.ok( _.isString( nineCodesSocialManager.app.route ), '"nineCodesSocialManager.app.route" is a string' );
		assert.ok( _.isFunction( nineCodesSocialManager.app.tmpl ), '"nineCodesSocialManager.app.tmpl()" is a function' );
		assert.ok( _.isFunction( nineCodesSocialManager.app.tmplString ), '"nineCodesSocialManager.app.tmplString()" is a function' );

		var templateContent = nineCodesSocialManager.app.tmpl('buttons-content');
		var templateString = nineCodesSocialManager.app.tmplString('<span class="{{ data.prefix }}-buttons {{ data.prefix }}-buttons--img {{ data.prefix }}-buttons--{{ data.id }}"></span>');

		assert.ok( _.isFunction( templateContent ), '"nineCodesSocialManager.app.tmpl()" return a function' );
		assert.ok( _.isFunction( templateString ), '"nineCodesSocialManager.app.tmplString()" return a function' );

		assert.equal( templateString({
			prefix: "social-manager",
			id: "123"
		}), '<span class="social-manager-buttons social-manager-buttons--img social-manager-buttons--123"></span>', '"nineCodesSocialManager.app.tmplString()" properly compiles the template with the given data.' );
	});
});
