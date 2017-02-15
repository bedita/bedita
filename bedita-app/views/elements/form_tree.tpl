<script type="text/javascript">
<!--
{if $object.fixed}
$(document).ready(function(){
		$("#whereto input[type=checkbox]").prop("disabled", true);
});
{/if}
//-->
</script>

{$relcount = $parents|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}{if empty($tpl_title)}Position{else}{$tpl_title}{/if}{/t} {if $relcount > 0}&nbsp;<span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="whereto">

	{if $object.fixed}{t}The content is fixed: it's not possible to change the position in the tree{/t}{/if}
	
	{if !empty($readonlyTreePaths)}
		{foreach $readonlyTreePaths as $pathData}
			{* available fields: ids, idsPath, titles, titlesPath *}
			<div><input type="checkbox" checked="checked" disabled="disabled" />{$pathData.titlesPath}</div>
		{/foreach}
	{/if}
	{if empty($tree)}
		{t}No tree found{/t}
	{else}

		<div class="publishingtree">
		
			{assign_associative var="params" checkbox=true}
			{$view->element('tree', $params)}
		
		</div>
		
	{/if}
	
</fieldset>

