{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | {$title_for_layout}</title>
	{include file="inc/meta.tpl"}
	
	{$html->css('beditaNew')}
	
	
	{$javascript->link("jquery/jquery")}
	{$javascript->link("jquery/jquery.cookie")}
	{*$javascript->link("jquery.changealert")*}
	{$javascript->link("common")}
	{$javascript->link("beditaUI")}

	{* collect linked scripts around *}
	{$scripts_for_layout}




{*
** Page Specific Content
** contains </head> tag, closed inside each module's view
*}



{$content_for_layout}







{*
** Page Footer
*}
<div id="footerPage">

{include file="../pages/user_module_perms.tpl"}

	<div id="handlerChangeAlert"></div>

</div>



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