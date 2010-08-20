{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$beFront->title()}</title>
	{$beFront->metaAll()}
	{$beFront->metaDc()}
	<link rel="icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />
	
	{$beFront->feeds()}

	{$scripts_for_layout}
</head>

<body>

{$content_for_layout}

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>