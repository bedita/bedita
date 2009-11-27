{assign_concat var="sectionNick" 0=$section.nickname 1=".tpl"}

{assign_concat var="tplfile" 0=$smarty.const.VIEWS 1="pages/" 2=$sectionNick}

{if file_exists($tplfile)}
	{include file=$tplfile}
{else}
	{include file="generic_section.tpl"}
{/if}