{agent var="agent"}{$html->docType('xhtml-trans')}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<title>BEdita | {$title_for_layout} | {$html->action}</title>
	{include file="inc/meta.tpl"}
	
	{$html->css('print', null, "media='all'")}
	
	{assign_concat var="cssfile" 0=$smarty.const.APP 1="webroot" 2=$smarty.const.DS 3="css" 4=$smarty.const.DS 5=$printLayout 6=".css"}
	{if file_exists($cssfile)}
		{$html->css($printLayout, null, "media='all'")}
	{/if}

	{$scripts_for_layout}
</head>

<body>

{$content_for_layout}

</body>

</html>