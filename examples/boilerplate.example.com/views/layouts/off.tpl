<!DOCTYPE html>
<html class="no-js">
<head>
	{$html->charset()}
	<title>{$beFront->title()} | status: DRAFT</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	{$beFront->metaOg()}
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">

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
	<h1>Publication status: OFF</h1>
	{$view->element('header')}

	{$content_for_layout}

	{$view->element('footer')}

	<script src="js/main.js"></script>
	{$beFront->stats()}
</body>
</html>