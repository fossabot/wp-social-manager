<button
	type="button"
	class="button button-large widefat"
	id="ncscoman-meta-preview-toggle">
	<span class="dashicons dashicons-visibility"></span> {{ data.l10n.toggle }}
</button>

<div id="ncsocman-meta-preview-wrap" class="ncsocman-meta-preview">

	<!-- Facebook -->
	<div id="ncsocman-meta-preview-fb" class="ncsocman-meta-preview__site">
		<# if ( data.meta.image.src ) { #>
		<div class="ncsocman-meta-preview__image">
			<img src="{{ data.meta.image.src }}" alt="">
		</div>
		<# } #>
		<div class="ncsocman-meta-preview__summary">
			<div class="ncsocman-meta-preview__title">{{ data.meta.document_title }}</div>
			<div class="ncsocman-meta-preview__description">{{ data.meta.description }}</div>
			<div class="ncsocman-meta-preview__by-line">
				<span class="ncsocman-meta-preview__site-name">{{ data.meta.site_name }}</span><span class="phs">|</span><span class="ncsocman-meta-preview__author-name">{{ data.meta.author }}</span>
			</div>
		</div>
	</div>

	<!-- Twitter -->
	<div id="ncsocman-meta-preview-tw" class="ncsocman-meta-preview__site"></div>
</div>
