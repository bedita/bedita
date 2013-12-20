<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$currLang}"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="{$currLang}"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="{$currLang}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$currLang}"> <!--<![endif]-->
<head>
	{$html->charset()}
	<title>{$beFront->title()}</title>

	{$beFront->metaAll()}
	{$beFront->metaDc()}
	{$beFront->metaOg()}

	{* WebApp Specific *}
	{*
	  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
		<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />	
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		
		{$html->css('mobile.css', null, "media='all'")}
		<!-- Check device orientation -->
		{$html->css('mobile_portrait.css', null, "media='all and (orientation:portrait)'")}		
		{$html->css('mobile_landscape.css', null, "media='all and (orientation:landscape)'")}
	*}

	<link rel="icon" href="{$html->url('/')}favicon.png" type="image/png" />

	{$beFront->feeds()}
	
	{$html->css('style')}
	{$javascript->link('libs/modernizr.h5bp.custom.js')}

	{* jQuery 1.7.1 *}
	{$html->script('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')}
	<script>window.jQuery || document.write('<script src="{$html->url('/')}js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	{* Zepto 0.8 *}
	{*
	{$html->script('http://cdnjs.cloudflare.com/ajax/libs/zepto/0.8/zepto.min.js')}
	<script>window.Zepto || document.write('<script src="{$html->url('/')}js/libs/zepto-0.8.min.js"><\/script>')</script>
	*}
	{$scripts_for_layout}

</head>
<body>
	{$view->element('header')}
	
	{$content_for_layout}
	
	{$view->element('footer')}

	{$beFront->stats()}	
</body>
</html>