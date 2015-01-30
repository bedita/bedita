{agent var="agent"}
<!DOCTYPE html>
<html lang="it">
<head>
	<title>BEdita</title>


	{$html->css('bedita.css?v=01')}
	
	<!--[if lte IE 6]>
		{$html->css('IE6fix')}
	<![endif]-->

	{$html->script("libs/jquery/jquery-2.1.0.min")}
	{$html->script("libs/jquery/plugins/jquery.cookie")}
	{$html->script("libs/jquery/plugins/jquery.autosize.min")}
	{$html->script("libs/jquery/plugins/jquery.dimensions.min")}
	{$html->script("beditaUI")}

	{$beurl->addModuleScripts()}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	{$html->script("libs/jquery/ui/jquery-ui.min")}
	{$html->script("libs/jquery/ui/jquery.ui.draggable.min")}

	
</head>
<body{if !empty($bodyClass)} class="{$bodyClass}"{/if}>

{$view->element('messages')}

{$view->element('modulesmenu')}

<div class="primacolonna">
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>	
</div>

<div id="messagesDiv" style="margin-top:110px">
	<div class="message error">
		{$content_for_layout}	
	</div>
</div>


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

