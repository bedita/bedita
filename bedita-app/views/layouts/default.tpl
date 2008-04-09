{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>B.Edita::{$title_for_layout}</title>
	{include file="../layout_parts/meta.tpl"}

	{$javascript->link("jquery/jquery")}
	{$javascript->link("jquery/jquery.cookie")}
	{$javascript->link("common")}
	{$html->css('bedita')}
	{$html->css('menu')}
	{$html->css('form')}
	{$html->css('message')}

	{if $moduleName|default:""}
		<link rel="stylesheet" type="text/css" href="{$html->webroot}css/module.{$moduleName}.css" />
		<script type="text/javascript" src="{$html->webroot}js/module.{$moduleName}.js"></script>
	{/if}
	{if ($agent.IE)}{$html->css('ie')}{/if}
	
	{include file="../layout_parts/inline_js.tpl"}
	{* collect linked scripts around *}
	<!-- -1- -->{$scripts_for_layout}<!-- -2- -->


{*
** Page Specific Content
** contains </head> tag, closed inside each module's view
*}
{$content_for_layout}




{*
** Page Footer
*}
<div id="footerPage">
	{*<a href="http://www.cakephp.org/" target="_blank">
	<img src="{$html->webroot}img/cake.power.png" alt="CakePHP Rapid Development Framework" border="0"/>
	</a>*}
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