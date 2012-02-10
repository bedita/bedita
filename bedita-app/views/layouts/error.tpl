{agent var="agent"}
<!DOCTYPE html>
<html lang="it">
<head>
	<title>BEdita</title>


	{$html->css('bedita.css?v=01')}
	
	<!--[if lte IE 6]>
		{$html->css('IE6fix')}
	<![endif]-->

	{$html->script("jquery/jquery")}
	{$html->script("jquery/jquery.cookie")}
	{$html->script("jquery/jquery.autogrow")}
	{$html->script("jquery/jquery.dimensions")}
	{$html->script("beditaUI")}

	{$beurl->addModuleScripts()}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	{$html->script("jquery/ui/jquery-ui-1.8rc3.custom")}
	{$html->script("jquery/ui/jquery.ui.draggable")}

	
</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>

{$view->element('modulesmenu')}

<div class="primacolonna">
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>	
</div>

<div id="messagesDiv" style="margin-top:140px">
	<div class="message error">
		{$content_for_layout}	
	</div>
</div>

	
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

