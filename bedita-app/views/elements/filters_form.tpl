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
	'categories' => true
]
-->
*}

<form id="formFilter" action="{$filters.url|default:$beurl->getUrl(['page', 'dim', 'dir', 'order'])}" method="post">

	<input type="hidden" name="cleanFilter" value=""/>

	<table class="filters">
		<tr>
			
		{if !empty($filters.word)}
			<td>
				<input type="text" placeholder="{t}search word{/t}" name="filter[query]" id="search" style="width:255px" value="{$view->SessionFilter->read('query')}"/>&nbsp;
				<input type="checkbox"
					{if $view->SessionFilter->check('substring') || !$view->SessionFilter->check()}
						checked="checked"
					{/if} 
					id="modalsubstring" name="filter[substring]" /> {t}substring{/t}
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

			<td rowspan="4" style="border:0px">
				<input type="submit" id="searchButton" value=" {t}Find it{/t} ">
				<input type="button" id="cleanFilters" value=" {t}Clean{/t} "
			</td>
		</tr>
		<tr>

		{if !empty($filters.tree)}
			<td>
				<label>{t}on position{/t}:</label>
				<select style="margin-left:10px; width:270px" name="filter[parent_id]" id="parent_id">
				{$beTree->option($tree, $view->SessionFilter->read('parent_id'))}
				</select>
				{if !empty($filters.treeDescendants)}
					<input type="checkbox" name="filter[descendants]"
						{if $view->SessionFilter->check('descendants')}checked="checked"{/if} /> {t}descendants{/t}
				{/if}
			</td>
		{/if}

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
		</tr>

		<tr>
		{if !empty($filters.categories)}
			<td>
			<label>{t}categories{/t}:</label>
			<select style="margin-left:10px; width:270px" name="filter[category]">
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
		{/if}

		{if !empty($filters.customProp)}
			<th><label>{t}properties{/t}:</label></th>
			<td colspan="3">
				[...]
			</td>
		{/if}

		</tr>

	</table>

</form>