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