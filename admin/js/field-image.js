/*eslint no-unused-vars: ["error", { "vars": "local", "varsIgnorePattern": "^image" }]*/
jQuery(function($) {

	'use strict';

	var wp = window.wp || {},
		MediaUpload = {
			View: {}
		},
		ImageUpload,
		imageUpload;

	MediaUpload.View = Backbone.View.extend({
		el: '#ninecodes-social-manager-settings'
	});

	/**
	 * Backbone View to handle Media Uploader UI
	 * in the setting page.
	 */
	ImageUpload = MediaUpload.View.extend({

		events: {
			'click .add-media-img': 'selectMedia',
			'click .change-media-img': 'selectMedia',
			'click .remove-media-img': 'removeMedia'
		},

		initialize: function() {

			this.wpMediaUploader = null;
			this.wpMedia();
		},

		selectMedia: function(button) {

			this.controls(button);

			this.wpMediaWindow();
		},

		removeMedia: function(button) {

			this.controls(button);

			this.$input.val('');
			this.$inputImg.html('');
			this.controlState('');
		},

		controls: function(button) {

			var inputId = $(button.target).data('input');

			this.$input = $(inputId);

			this.$inputWrap = $(inputId + '-img-wrap');
			this.$inputImg = $(inputId + '-img-elem');

			this.$controlAdd = $(inputId + '-img-add');
			this.$controlChange = $(inputId + '-img-change');
			this.$controlRemove = $(inputId + '-img-remove');
		},

		controlState: function(imgId, imgUrl) {

			var state = imgId === parseInt(this.$input.val(), 10) && '' !== imgUrl;

			this.$inputWrap.toggleClass('is-set', state);

			this.$controlAdd.toggleClass('hide-if-js', state);
			this.$controlChange.toggleClass('hide-if-js', !state);
			this.$controlRemove.toggleClass('hide-if-js', !state);

			this.$inputImg.html(function() {

				var imgEl = '';

				if (imgUrl) {
					imgEl = document.createElement('img');
					imgEl.src = imgUrl;
				}
				return imgEl;
			});
		},

		wpMedia: function() {
			this.wpMediaUploader = wp.media.frames.file_frame = wp.media({
				title: 'Site Meta Image',
				button: {
					text: 'Set as Site Image'
				},
				multiple: false
			});
		},

		wpMediaWindow: function() {

			this.wpMediaUploader.open();

			this.wpMediaUploader.on('select', function() {
				this.wpMediaSelect();
			}.bind(this));
		},

		wpMediaSelect: function() {

			var attach = this.wpMediaUploader.state().get('selection').first().toJSON(),
				attachId = attach.id,
				attachURL = attach.url;

			this.$input.val(attachId);
			this.controlState(attachId, attachURL);
		}
	});

	imageUpload = new ImageUpload();
});
