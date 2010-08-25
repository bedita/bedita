{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$currLang}" dir="ltr">
<head>
	<title>{$beFront->title()}</title>

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	
	{$beFront->feeds()}

	{$scripts_for_layout}
</head>

<body>

{$content_for_layout}

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>