{agent var="agent"}
<!DOCTYPE html>
<html lang="{$currLang2}">
<head>
	<title>BEdita | {$currentModule.label|default:'home'} | {$this->Html->action} | {if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{/if}</title>

	<meta name="robots" content="noindex,nofollow"/>
	
	{if $agent.iPHONE or $agent.iPAD}
		
		<meta name="viewport" content="user-scalable=yes, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
	    <meta name="apple-mobile-web-app-capable" content="yes" />
	    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
	    <link rel="apple-touch-icon" href="{$this->Html->url('/img/')}BElogo_iphone.png"/>
	    <link rel="apple-touch-startup-image" href="{$this->Html->url('/img/')}BElogo_iphone.png" />
		<link rel="stylesheet" href="{$this->Html->url('/css/')}beditaMobile.css" type="text/css" media="screen" title="main" charset="utf-8">
		
	{/if}
		
	{include file="inc/meta.tpl"}

	{$view->element('json_meta_config')}

	{$this->Html->css('bedita.css?v=01')}
	
	<!--[if lte IE 6]>
		{$this->Html->css('IE6fix')}
	<![endif]-->

	{$this->Html->script("jquery/jquery")}
	{$this->Html->script("jquery/jquery.cookie")}
	{$this->Html->script("jquery/jquery.autogrow")}
	{$this->Html->script("jquery/jquery.dimensions")}
	{$this->Html->script("jquery/jquery.tooltip.min")}
	{$this->Html->script("beditaUI")}

	{$this->Beurl->addModuleScripts()}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	{$this->Html->script("jquery/ui/jquery-ui-1.8rc3.custom")}
	{$this->Html->script("jquery/ui/jquery.ui.draggable")}

	
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