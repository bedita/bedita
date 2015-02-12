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

<form id="formFilter" action="{$filters.url|default:$beurl->getUrl(['page', 'dim', 'dir', 'order'])}" method="post">
	{$beForm->csrf()}
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
					id="modalsubstring" name="filter[substring]" /> <label>{t}substring{/t}</label>
			</td>
		</tr>
		{/if}
		{if !empty($filters.language)}
		<tr>
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
		</tr>
		{/if}
		{if !empty($filters.type)}
		<tr>
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
		</tr>
		{/if}
		{if !empty($filters.tree)}
		<tr>
			<th><label>{t}on position{/t}:</label></th>
			<td>
				<select name="filter[parent_id]" id="parent_id" class="areaSectionAssociation">
					<option value="">{t}None{/t}</option>
					{$beTree->option($tree, $view->SessionFilter->read('parent_id'))}
				</select>
				{if !empty($filters.treeDescendants)}
					&nbsp;<input type="checkbox" name="filter[descendants]"
						{if $view->SessionFilter->check('descendants')}checked="checked"{/if} /> <label>{t}descendants{/t}</label>
				{/if}
			</td>
		</tr>
		{/if}

		{if !empty($filters.relations)}
		<tr>
			<th><label>{t}relations{/t}:</label></th>
			<td>
				{$availableRelations = $availableRelations|default:[]}
				<select name="filter[relation]" id="relation">
					<option value="">{t}all{/t}</option>
					{foreach $availableRelations as $relName => $relLabel}
						{strip}
						<option value="{$relName}" {if $view->SessionFilter->read('relation') == $relName}selected="selected"{/if}>
							{t}{$relLabel}{/t}
						</option>
						{/strip}
					{/foreach}
				</select>

				{if !empty($filters.tree)}
					&nbsp;
					<input type="checkbox" name="filter[tree_related_object]"
						{if $view->SessionFilter->check('tree_related_object')}checked="checked"{/if} />
						<label>{t}with items located on above position{/t}</label>
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
							{$catLabel|escape}
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
				<select name="filter[custom_property]">
					<option value="">{t}all{/t}</option>
					{foreach $properties as $prop}
						{strip}
						<option value="{$prop.id}" {if $view->SessionFilter->read('custom_property') == $prop.id}selected="selected"{/if}>
							{$prop.name}
							{if is_array($filters.customProp) && !empty($filters.customProp.showObjectTypes)}
								&nbsp;({$conf->objectTypes[$prop.object_type_id].name})
							{/if}
						</option>
						{/strip}
					{/foreach}
				</select>
			</td>
		</tr>
		{/if}

		
		<tr>
			<th></th>
			<td colspan="10">
				<input type="submit" id="searchButton" style="width:150px" value=" {t}find it{/t} ">
				<input type="button" id="cleanFilters" value=" {t}reset filters{/t} ">
			</td>
		</tr>
	</table>

</form>