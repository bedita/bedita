{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$currLang}" dir="ltr">
<head>
	<title>{$beFront->title()}</title>

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->metaAll()}
	{$beFront->metaDc()}

	{$beFront->feeds()}

	{$html->css('twentyten')}
	{$html->css('adjustament')}
	{$html->css('colorbox')}

	{$javascript->link('jquery')}
	{$javascript->link('jquery.colorbox-min')}
	
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

{$beFront->stats()}
</body>
</html>
