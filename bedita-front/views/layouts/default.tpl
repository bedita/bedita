{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title> </title>
	<link rel="icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$session->webroot}favicon.ico" type="image/x-icon" />

	<meta name="author" content="" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
{foreach from=$feedNames item=feed}
	<link rel="alternate" type="application/rss+xml" title="{$feed.title}" href="{$session->webroot}rss/{$feed.nickname}" />
{/foreach}

{$content_for_layout}

<div id="footerPage">
</div>

{$cakeDebug}
</body>
</html>