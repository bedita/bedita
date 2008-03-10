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
	{literal}
});
{/literal}
//-->
</script>

<h2 class="showHideBlockButton">{t}Where put the document into{/t}</h2>
<div class="blockForm" id="whereto" style="display: none">
<fieldset>
	{if isset($excludedSubTreeId)}
		{if empty($tree)}
			{t}No tree found{/t}
		{else}
			<div id="treecontrol">
				<a href="#">{t}Close all{/t}</a>
				<a href="#">{t}Expand all{/t}</a>
			</div>
			{$beTree->tree("treeWhere", $tree, $excludedSubTreeId)}	
		{/if}
	{else}
		{if empty($tree)}
			{t}No tree found{/t}
		{else}
			<div id="treecontrol">
				<a href="#">{t}Close all{/t}</a>
				<a href="#">{t}Expand all{/t}</a>
			</div>
			{$beTree->tree("treeWhere", $tree)}
		{/if}
	{/if}
</fieldset>
</div>