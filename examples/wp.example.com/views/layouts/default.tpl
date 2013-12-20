{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$beFront->lang()}" dir="ltr">
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	{$beFront->metaOg()}

	{$beFront->feeds()}

	{$html->css('twentyten')}
	{$html->css('adjustament')}
	{$html->css('colorbox')}

	{$html->script('jquery')}
	{$html->script('jquery.colorbox-min')}
	
	{$scripts_for_layout}
</head>

<body class="page page-id-23 page-parent page-child parent-pageid-20 page-template page-template-default logged-in">

{$beFront->stagingToolbar()}

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
