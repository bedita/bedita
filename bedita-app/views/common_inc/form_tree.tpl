<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$("#treeWhere").designTree({
		id_control: "treecontrol", 
		inputType: "{/literal}{$inputTreeType|default:"checkbox"}"{literal}
	});
	{/literal}
	{if $parents|default:""}
		{foreach from=$parents item="id_parent"}
			$("#s_{$id_parent}").attr("checked", "checked");
		{/foreach}
	{/if}
	{if $object.status == 'fixed'}
	$("#whereto input[@type=checkbox]").attr("disabled","disabled");
	{/if}
	{literal}
});
{/literal}
//-->
</script>

	
<div class="tab"><h2>{t}{if empty($tpl_title)}Where put the document into{else}{$tpl_title}{/if}{/t}</h2></div>

<fieldset id="whereto">
	{if $object.status == 'fixed'}{t}The content is fixed: it's not possible to change the position in the tree{/t}{/if}
	{if isset($excludedSubTreeId)}
		{if empty($tree)}
			{t}No tree found{/t}
		{else}
			<div id="treecontrol">
				<a href="#">- {t}Close all{/t}</a>
				<a href="#">+ {t}Expand all{/t}</a>
			</div>
			{$beTree->tree("treeWhere", $tree, $excludedSubTreeId)}	
		{/if}
	{else}
		{if empty($tree)}
			{t}No tree found{/t}
		{else}
			<div id="treecontrol">
				<a href="#">- {t}Close all{/t}</a>
				<a href="#">+ {t}Expand all{/t}</a>
			</div>
			{$beTree->tree("treeWhere", $tree)}
		{/if}
	{/if}
	
</fieldset>

