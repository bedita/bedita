{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$beFront->lang()}" dir="ltr">
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	{$beFront->metaOg()}

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->feeds()}

	{$scripts_for_layout}
</head>

<body>
{$view->element('header')}

{$content_for_layout}

{$view->element('footer')}

{$beFront->stats()}
</body>
</html>