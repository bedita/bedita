<!DOCTYPE html>
<html lang="{$currLang2}">
<head>
	<title>{$beurl->pageTitle()}</title>

	<meta name="robots" content="noindex,nofollow"/>
	
  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$html->webroot('img/h/apple-touch-icon.png')}">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$html->webroot('img/m/apple-touch-icon.png')}">
  <link rel="apple-touch-icon-precomposed" href="{$html->webroot('img/l/apple-touch-icon-precomposed.png')}">
  <link rel="shortcut icon" href="{$html->webroot('img/l/apple-touch-icon.png')}">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	<script>{literal}(function(){var a;if(navigator.platform==="iPad"){a=window.orientation!==90||window.orientation===-90?"{/literal}{$html->webroot('img/startup-tablet-landscape.png')}{literal}":"{/literal}{$html->webroot('img/startup-tablet-portrait.png')}{literal}"}else{a=window.devicePixelRatio===2?"{/literal}{$html->webroot('img/startup-retina.png')}{literal}":"{/literal}{$html->webroot('img/startup.png')}{literal}"}document.write('<link rel="apple-touch-startup-image" href="'+a+'"/>')})(){/literal}</script>

  <meta http-equiv="cleartype" content="on">

	{$html->css("themes/red.min.css")}
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />

	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
	<script>
		$.mobile.ajaxEnabled = false; // Disabilita chiamate ajax
		$.mobile.page.prototype.options.addBackBtn = true;
	</script>
	{*
	{$html->css("jquery.mobile-1.1.1")}
	{$html->script("jquery/jquery")}
	{$html->script("jquery/jquery.mobile-1.1.1.min")}
	*}
	
	{$beurl->addModuleScripts()}

	{* collect linked scripts around *}
	{$scripts_for_layout}
	
</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>
{$content_for_layout}
</body>
</html>