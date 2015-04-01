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

<form id="formFilter" action="{$filters.url|default:$beurl->getUrl(['page', 'dim', 'dir', 'order'])}" 
	method="post">
	{$beForm->csrf()}
	<input type="hidden" name="cleanFilter" value=""/>
{strip}
	<div class="filters" style="width:100%">

		{if !empty($filters.word)}
			<div class="cell word">
				<label>{t}search word{/t}:</label>
				<input type="text" placeholder="{t}search word{/t}" name="filter[query]" id="search" value="{$view->SessionFilter->read('query')}"/>&nbsp;
				<input type="checkbox" 
					{if $view->SessionFilter->check('substring') || !$view->SessionFilter->check()}
						checked="checked"
					{/if} 
					id="modalsubstring" name="filter[substring]" /> <span>{t}substring{/t}</span>
			</div>
		{/if}

		{if !empty($filters.language)}
			<div class="cell lang">
				<label>{t}language{/t}:</label>
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
			</div>
		{/if}

		{if !empty($filters.tree)}
			<div class="cell position">
				<label>{t}on position{/t}:</label>
					<select style="" name="filter[parent_id]" id="parent_id" class="areaSectionAssociation">
						<option value="">{t}None{/t}</option>
						{$beTree->option($tree, $view->SessionFilter->read('parent_id'))}
					</select>
					{if !empty($filters.treeDescendants)}
						&nbsp;<input type="checkbox" name="filter[descendants]"
							{if $view->SessionFilter->check('descendants')}checked="checked"{/if} />
							<span>{t}descendants{/t}</span>
					{/if}
			</div>
		{/if}

		{if !empty($filters.type)}
			<div class="cell type">
				<label>{t}type{/t}:</label>
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
			</div>
		{/if}

		{if !empty($filters.relations)}
			<div class="cell relations">
				<label>{t}relations{/t}:</label>
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
						<span>{t}with items located on above position{/t}</span>
				{/if}
			</div>
		{/if}

		{if !empty($filters.mediaTypes)}
			<div class="cell mediatype">
				<label>{t}media type{/t}:</label>
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
			</div>
		{/if}

		{if !empty($filters.categories)}
			<div class="cell categories">
				<label>{t}categories{/t}:</label>
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
			</div>
		{/if}

		{if !empty($filters.customProp)}
			<div class="cell customprop">
				<label>{t}properties{/t}:</label>
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
			</div>
		{/if}
		
		<div class="formbuttons">
			<input type="submit" id="searchButton" value=" {t}find it{/t} ">
			<input type="button" id="cleanFilters" value=" {t}reset filters{/t} ">
		</div>

	</div>
{/strip}
</form>