<!DOCTYPE html>
<html>
<head>
	{$html->charset()}
	<title>{$beFront->title()} | status: DRAFT</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	{$beFront->metaOg()}

	{$html->css('style')}

	<link rel="icon" href="{$html->webroot}favicon.png" type="image/png" />
    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <script>window.html5 || document.write('<script src="js/libs/html5shiv.js"><\/script>')</script>
    <![endif]-->

	{$beFront->feeds()}

	{$scripts_for_layout}
</head>

<body>
	<h1>Publication status: DRAFT</h1>
	{$view->element('header')}

	{$content_for_layout}

	{$view->element('footer')}

	{$beFront->stats()}
</body>
</html>