{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$publication.public_name|default:$publication.title}{if !empty($section)} | {$section.title}{/if}</title>
	<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />

	{$html->charset('utf-8')}
	<meta name="author" content="" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
{foreach from=$feedNames item=feed}
	<link rel="alternate" type="application/rss+xml" title="{$feed.title}" href="{$html->url('/rss')}/{$feed.nickname}" />
{/foreach}

{$content_for_layout}

<div id="footerPage">
</div>

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>