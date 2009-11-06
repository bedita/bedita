{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$beFront->title()}</title>

	<link rel="icon" href="{$session->webroot}favicon.ico" type="image/gif" />
	<link rel="shortcut icon" href="{$session->webroot}favicon.gif" type="image/gif" />
	{$beFront->metaAll()}
	
	<!-- RTF dublin core dataset -->
	{$beFront->metaDc()}
	<!-- end -->

	{foreach from=$feedNames item=feed}
	<link rel="alternate" type="application/rss+xml" title="{$feed.title}" href="{$html->url('/rss')}/{$feed.nickname}" />
	{/foreach}
	
	{$html->css('beditaBase')}
	{$html->css('thickbox.BEfrontend')}
	
	{$javascript->link("jquery")}
	{$javascript->link("jquery.pngFix.pack")}
	{$javascript->link("bedita")}
	{$javascript->link("jquery.thickbox.BEfrontend")}
	{$scripts_for_layout}
	
</head>


<body>

{$content_for_layout}

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>