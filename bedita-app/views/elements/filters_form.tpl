{* 
<!--
view of search filters / included in 
elements/filters.tpl
pages/show_objects.tpl (modal)
available options:
'filters' => [
	'word' => true, 
	'tree' => true,
	'treeDescendants' => true,
	'type' => true,
	'language' => true,
	'customProp' => false
]
-->
*}

<style scoped>
	.filters td {
		border-right:1px solid gray;
		padding-right:10px;
	}
</style>

<table class="filters">
	<tr>
		
	{if !empty($filters.word)}
		<th><label>{t}word{/t}:</label></th>
		<td><input type="text" name="search" id="search" style="width:98%" value="" /></td>
	{/if}
	
	{if !empty($filters.type)}
		<th><label>{t}type{/t}:</label></th>
		<td>
			<select name="objectType" id="objectType">
				<option value="">{t}all{/t}</option>
				{foreach from=$objectTypeIds item=type_id}
					{if $type_id}
					{strip}
					<option value="{$type_id}">
						{$conf->objectTypes[$type_id].name|lower}
					</option>
					{/strip}
					{/if}
				{/foreach}
			</select>
		</td>
	{/if}

		<td rowspan="4" style="border:0px">
			<input type="button" id="searchButton" value=" {t}Find it{/t} ">
		</td>
	</tr>
	<tr>

	{if !empty($filters.tree)}
		<th><label>{t}on{/t}:</label></th>
		<td>
			<select style="width:180px" name="parent_id" id="parent_id">
			{$beTree->option($tree)}
			</select>
			{if !empty($filters.treeDescendants)}
				<input type="checkbox" name="search_descendants" /> {t}descendants{/t}
			{/if}
		</td>
	{/if}

	{if !empty($filters.language)}
		<th><label>{t}language{/t}:</label></th>
		<td>
				<select name="lang" id="lang">
				<option value="">{t}all{/t}</option>
				{foreach key=val item=label from=$conf->langOptions}
					{strip}
					<option value="{$val}">
						{$label}
					</option>
					{/strip}
				{/foreach}
			</select>
		</td>
	{/if}
	</tr>
	<tr>
	{if !empty($filters.customProp)}
		<th><label>{t}properties{/t}:</label></th>
		<td colspan="3">
			[...]
		</td>
	{/if}

	</tr>

</table>