{assign_concat var="sectionNick" 1=$section.nickname 2=".tpl"}

{assign_concat var="tplfile" 1=$smarty.const.VIEWS 2="pages/" 3=$sectionNick}

{if file_exists($tplfile)}
	{include file=$tplfile}
{else}
	{include file="generic_section.tpl"}
{/if}