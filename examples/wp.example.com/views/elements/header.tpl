<div id="header">
	<div id="masthead">
		
		<div id="branding" role="banner">

			<div id="site-title">
				<span>
					<a href="{$publication.public_url}" title="{$publication.public_name}" rel="home">{$publication.public_name}</a>
				</span>
			</div>
			
			<div id="site-description">Just another BEdita site</div>

			{*<img src="http://localhost/workspace/wordpress/wp-content/themes/twentyten/images/headers/path.jpg" width="940" height="198" alt="" />*}
			{if !empty($headerImage)}
				{assign_associative var="params" mode="crop" width=940 height=198}
				{assign_associative var="htmlAttr" width="940" height="198"}
				{$beEmbedMedia->object($headerImage, $params, $htmlAttr)}
			{else}
				{$html->image("path.jpg")}
			{/if}
		</div><!-- #branding -->

		<div id="access" role="navigation">
			<div class="skip-link screen-reader-text">
				<a href="#content" title="Skip to content">Skip to content</a>
			</div>

			<div class="menu">
				{$beFront->menu($sectionsTree)}
			</div>

		</div><!-- #access -->

	</div><!-- #masthead -->
</div><!-- #header -->
