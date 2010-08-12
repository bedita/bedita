{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>{$beFront->title()}</title>
	{$beFront->metaAll()}
	{$beFront->metaDc()}
{*
	<link rel="icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$html->webroot}favicon.ico" type="image/x-icon" />
*}

	{$html->css('twentyten')}
	
	{$scripts_for_layout}
</head>

<body class="page page-id-23 page-parent page-child parent-pageid-20 page-template page-template-default logged-in">

<div id="wrapper" class="hfeed">

	{$view->element('header')}

	<div id="main">
		{$content_for_layout}
	</div><!-- #main -->

	{$view->element('footer')}

</div><!-- #wrapper -->

{if empty($conf->staging) && !empty($publication.stats_code)}{$publication.stats_code}{/if}
</body>
</html>
