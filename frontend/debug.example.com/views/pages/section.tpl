{assign_concat var="objectNick" 0=$section.currentContent.nickname|default:'' 1=".tpl"}
{assign_concat var="objtplfile" 0=$smarty.const.VIEWS 1="pages/" 2=$objectNick}

{assign_concat var="sectionNick" 0=$section.nickname 1=".tpl"}
{assign_concat var="sectiontplfile" 0=$smarty.const.VIEWS 1="pages/" 2=$sectionNick}

{assign_concat var="objectType" 0=$section.currentContent.object_type|default:''|lower 1=".tpl"}
{assign_concat var="typetplfile" 0=$smarty.const.VIEWS 1="pages/" 2=$objectType}

{if file_exists($objtplfile)}

	{include file=$objtplfile}

{elseif file_exists($sectiontplfile)}

	{include file=$sectiontplfile}

{elseif file_exists($typetplfile)}

	{include file=$typetplfile}

{else}

	{include file="generic_section.tpl"}

{/if}
