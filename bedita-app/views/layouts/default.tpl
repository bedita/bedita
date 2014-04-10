{agent var="agent"}
<!DOCTYPE html>
<html lang="{$currLang2}">
<head>
	<title>{$beurl->pageTitle()}</title>

	<meta name="robots" content="noindex,nofollow"/>
	
	{if $agent.iPHONE or $agent.iPAD}
		
		<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
	    <meta name="apple-mobile-web-app-capable" content="yes" />
	    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
	    <link rel="apple-touch-icon" href="{$html->url('/img/')}BElogo_iphone.png"/>
	    <link rel="apple-touch-startup-image" href="{$html->url('/img/')}BElogo_iphone.png" />
		<link rel="stylesheet" href="{$html->url('/css/')}beditaMobile.css" type="text/css" media="screen" title="main" charset="utf-8">
		
	{/if}
		
	{include file="inc/meta.tpl"}

	{$view->element('json_meta_config')}

	{$html->css('bedita.css?v=01')}
	
	<!--[if lte IE 6]>
		{$html->css('IE6fix')}
	<![endif]-->

	{$html->script("libs/jquery/jquery-2.1.0.min")}
	{$html->script("libs/jquery/plugins/jquery.cookie")}
	{$html->script("libs/jquery/plugins/jquery.autosize.min")}
	{$html->script("libs/jquery/plugins/jquery.dimensions.min")}
	{$html->script("beditaUI")}

	<link rel="stylesheet" href="{$html->webroot}js/libs/select2/select2.css" />
	{$html->script("libs/select2/select2.min")}

	<link rel="stylesheet" href="{$html->webroot}js/libs/dropzone/css/dropzone.css" />
	{$html->script("libs/dropzone/dropzone.min")}

	{$beurl->addModuleScripts()}

	{$html->script("libs/jquery/ui/jquery-ui.min")}
	{$html->script("libs/jquery/ui/jquery.ui.draggable.min")}
	{$html->script("libs/jquery/ui/jquery.ui.tooltip.min")}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	
</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>

{$view->element('messages')}

{$content_for_layout}
	
{*
** Help container
*}

{$view->element('help')}


{*
** Modal container
*}

{$view->element('modal')}


{*
** Page Footer
*}

{if empty($noFooter)}

{$view->element('footer')}

{/if}

{$view->element('sql_dump')}

{* HTML document's end *}
</body>
</html>