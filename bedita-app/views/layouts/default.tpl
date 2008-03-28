{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>B.Edita::{$title_for_layout}</title>
	{$html->charset()}
	{$html->meta('icon')}
	<meta http-equiv="Content-Style-Type" content="text/css" />

	{* DA FARE - include file="../dove?/meta.tpl" *}
	<meta name="author" content="ChannelWeb srl - Chialab srl" />
	<meta name="description" content="Descrizione" lang="it" />
	<meta name="keywords" content="Keys" />

	{$javascript->link("jquery")}
	{$javascript->link("jquery.cookie")}
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
	
	{* correctly handle PNG transparency in IE 5.5/6 - added by xho - remove this comment in future *}
	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="js/pngfix_ielt7.js"></script>
	<![endif]-->
	

	{* DA SPOSTARE IN UN ALTRO POSTO QUESTA SCHIFEZZA QUA SOTTO *}
	{literal}
	<style type="text/css">
	TABLE.indexList TR.rowList:hover {background-color:{/literal}{if empty($moduleColor)}#FF6600{else}{$moduleColor}{/if}{literal};}
	</style>
	{/literal}



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