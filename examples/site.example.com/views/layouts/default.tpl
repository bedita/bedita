{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$beFront->lang()}" dir="ltr">
<head>
	<title>{$beFront->title()}</title>

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->metaAll()}
	
	<!-- RTF dublin core dataset -->
	{$beFront->metaDc()}
	<!-- end -->

	{$beFront->feeds()}
	
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