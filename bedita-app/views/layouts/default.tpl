{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | {$currentModule.label|default:'home'} | {$html->action}</title>
	{include file="inc/meta.tpl"}
	
	{$html->css('beditaNew')}
	
	<!--[if lte IE 6]>
	
		{$html->css('IE6fix')}
		
	<![endif]-->

	{$javascript->link("jquery/jquery")}
	{$javascript->link("jquery/jquery.cookie")}
	{$javascript->link("jquery/jquery.autogrow")}
	{$javascript->link("jquery/jquery.dimensions")}
	{$javascript->link("beditaUI")}

	{$beurl->addModuleScripts()}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	{$javascript->link("jquery/ui/ui.core.min")}
	{$javascript->link("jquery/ui/ui.draggable.min")}

</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>


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


{* HTML document's end *}
</body>
</html>