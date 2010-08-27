{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$currLang}" dir="ltr">
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->feeds()}

	{$scripts_for_layout}
</head>

<body>

{$content_for_layout}

{$beFront->stats()}
</body>
</html>