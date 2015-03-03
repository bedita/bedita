{agent var="agent"}
<!DOCTYPE html>
<html lang="{$currLang2}">
<head>
	<title>{$beurl->pageTitle()}</title>

	<meta name="robots" content="noindex,nofollow"/>
	<meta name="viewport" content="width=device-width">
	
	{include file="inc/meta.tpl"}

	{$view->element('json_meta_config')}

	{$html->css('bedita.css?v=01')}

	{$html->css('bedita-icons.css')}
	
	{$html->script("libs/jquery/jquery-2.1.0.min")}
	{$html->script("libs/jquery/plugins/jquery.cookie")}
	{$html->script("libs/jquery/plugins/jquery.autosize.min")}
	{$html->script("beditaUI")}

	<link rel="stylesheet" href="{$html->webroot}js/libs/select2/select2.css" />
	{$html->script("libs/select2/select2.min")}

	<link rel="stylesheet" href="{$html->webroot}js/libs/dropzone/css/dropzone.css" />
	{$html->script("libs/dropzone/dropzone.min")}

	{$beurl->addModuleScripts()}

	{$html->script("libs/jquery/ui/jquery-ui.min")}
	{$html->script("libs/jquery/ui/jquery.ui.draggable.min")}
	{$html->script("libs/jquery/ui/jquery.ui.tooltip.min")}
	{$html->script('libs/jquery/plugins/jquery.float_thead.min.js')}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	
</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>

{$view->element('messages')}

{$content_for_layout}
	
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

{* HTML document's end *}
</body>
</html>