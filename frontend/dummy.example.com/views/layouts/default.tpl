{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$publication.public_name|default:$publication.title}{if !empty($section)} | {$section.title}{/if}</title>
	<link rel="icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />

	{$html->charset('utf-8')}
</head>

<body>

{$content_for_layout}

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>