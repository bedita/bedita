<fieldset>
	<div id="treecontrol">
		<a href="#">{t}Close all{/t}</a>
		<a href="#">{t}Expand all{/t}</a>
	</div>
	{if isset($excludedSubTreeId)}
	{$beTree->tree("treeWhere", $tree, $excludedSubTreeId)}
	{else}
	{$beTree->tree("treeWhere", $tree)}
	{/if}
</fieldset>