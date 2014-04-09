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
	'customProp' => false,
	'categories' => true,
	'mediaType' => false
]
-->
*}

<style scoped>
	th {
		white-space: nowrap;
	}
	.filters th {
		width: 1px;
		vertical-align: top;
		padding-top: 8px;
	}

	.filters td {
		vertical-align: top;
	}
</style>

<form id="formFilter" action="{$filters.url|default:$beurl->getUrl(['page', 'dim', 'dir', 'order'])}" method="post">

	<input type="hidden" name="cleanFilter" value=""/>

	<table class="filters"  style="width:100%">
		{if !empty($filters.word)}
		<tr>
			<th><label>{t}search word{/t}:</label></th>
			<td colspan="6">
				<input type="text" placeholder="{t}search word{/t}" name="filter[query]" id="search" style="width:255px" value="{$view->SessionFilter->read('query')}"/>&nbsp;
				<input type="checkbox"
					{if $view->SessionFilter->check('substring') || !$view->SessionFilter->check()}
						checked="checked"
					{/if} 
					id="modalsubstring" name="filter[substring]" /> {t}substring{/t}
			</td>
		</tr>
		{/if}
		<tr>
		{if !empty($filters.language)}
			<th><label>{t}language{/t}:</label></th>
			<td>
				<select name="filter[lang]" id="lang">
					<option value="">{t}all{/t}</option>
					{foreach $conf->langOptions as $val => $label}
						{strip}
						<option value="{$val}" {if $view->SessionFilter->read('lang') == $val}selected="selected"{/if}>
							{$label}
						</option>
						{/strip}
					{/foreach}
				</select>
			</td>
		{/if}
		{if !empty($filters.type)}
			<th><label>{t}type{/t}:</label></th>
			<td>
				<select name="filter[object_type_id]" id="objectType">
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
		</tr>
		{if !empty($filters.tree)}
		<tr>
			<th><label>{t}on position{/t}:</label></th>
			<td>
				<select name="filter[parent_id]" id="parent_id" class="areaSectionAssociation">
				{$beTree->option($tree, $view->SessionFilter->read('parent_id'))}
				</select>
				{if !empty($filters.treeDescendants)}
					<input type="checkbox" name="filter[descendants]"
						{if $view->SessionFilter->check('descendants')}checked="checked"{/if} /> <label>{t}descendants{/t}</label>
				{/if}
			</td>
		</tr>
		{/if}

		{if !empty($filters.mediaTypes)}
		<tr>
			<th><label>{t}media type{/t}:</label></th>
			<td>
				<select name="filter[category]">
					<option value="">{t}all{/t}</option>
					{foreach $conf->mediaTypes as $mediaType}
						{strip}
						<option value="{$mediaType}" {if $view->SessionFilter->read('category') == $mediaType}selected="selected"{/if}>
							{$mediaType}
						</option>
						{/strip}
					{/foreach}
				</select>
			</td>
		</tr>
		{/if}
		{if !empty($filters.categories)}
		<tr>
			<th><label>{t}categories{/t}:</label></th>
			<td>
				<select name="filter[category]">
					<option value="">{t}all{/t}</option>
					{foreach $categories as $catId => $catLabel}
						{strip}
						<option value="{$catId}" {if $view->SessionFilter->read('category') == $catId}selected="selected"{/if}>
							{$catLabel}
						</option>
						{/strip}
					{/foreach}
				</select>
			</td>
		</tr>
		{/if}

		{if !empty($filters.customProp)}
		<tr>
			<th><label>{t}properties{/t}:</label></th>
			<td>
				
				[...]
			</td>
		{/if}

		</tr>
		<tr>
			<td colspan="10">
				<input type="submit" id="searchButton" style="width:150px" value=" {t}find it{/t} ">
				<input type="button" id="cleanFilters" value=" {t}reset filters{/t} ">
			</td>
		</tr>
	</table>

</form>