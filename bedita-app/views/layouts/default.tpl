{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | {$title_for_layout}</title>
	{include file="inc/meta.tpl"}
	
	{$html->css('beditaNew')}
	
	{$javascript->link("jquery/jquery")}
	{$javascript->link("jquery/jquery.cookie")}
	{$javascript->link("jquery/jquery.autogrow")}
	{$javascript->link("beditaUI")
	{$javascript->link("jquery/jquery.dimensions")}


	{if $moduleName|default:""}
		{assign_concat var="cssfile" 0=$smarty.const.APP 1="webroot/css/module." 2=$moduleName 3=".css"}
		{assign_concat var="jsfile" 0=$smarty.const.APP 1="webroot/js/module." 2=$moduleName 3=".js"}
		
		{if file_exists($cssfile)}<link rel="stylesheet" type="text/css" href="{$html->webroot}css/module.{$moduleName}.css" />{/if}
		{if file_exists($jsfile)}<script type="text/javascript" src="{$html->webroot}js/module.{$moduleName}.js"></script>{/if}
	{/if}

	{* collect linked scripts around *}
	{$scripts_for_layout}

	{$javascript->link("jquery/ui/ui.core.min")}
	{$javascript->link("jquery/ui/ui.draggable.min")}



{*
** Page Specific Content
** contains </head> tag, closed inside each module's view
*}



{$content_for_layout}



	


{*
** Modal container
*}

<div id="modaloverlay"></div>
<div id="modal">
	<div id="modalheader"><span class="caption"></span><a class="close">{t}close{/t}</a></div>
	<div id="modalmain"></div>
</div>


{*
** Page Footer
*}

{if empty($noFooter)}
<div id="footerPage">

{include file="../pages/user_module_perms.tpl"}

	<div id="handlerChangeAlert"></div>

</div>
{/if}


{* CakePHP Debug - start *}
{if $conf->debug && $cakeDebug}
<p style="color: red;"><br />Cake Debug Follows:</p>
<hr style="border-top: 1px dashed red; height: 1px; width: 715px;" align="left" />
<pre style="font-family: "Courier New", Courier, monospace; font-size: 10px;">{$cakeDebug}</pre>
{/if}
{* CakePHP Debug - end *}



{* HTML document's end *}
</body>
</html>