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
	'categories' => true or array('label' => 'myLabel'),
	'mediaType' => false,
	'tags' => false,
	'status' => true or array() of labels
]
-->
*}

{$statusLabels = [
	'on' => 'on',
	'draft' => 'draft',
	'off' => 'off'
	]
}

<script>
	function uncheckOther(checkbox){
		$("#statusfilter").find("input").each(function(){
			if($(this).val() != checkbox){
				$(this).uncheck();
			}else{
				$(this).check();
			}
		});
		//$("#searchButton").click();
	}
</script>

<form id="formFilter" action="{$filters.url|default:$beurl->getUrl(['page', 'dim', 'dir', 'order'])|escape:"html"}" 
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
						<option value="{$type_id}" {if $view->SessionFilter->read('object_type_id') == $type_id}selected="selected"{/if}>
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
				{if !empty($filters.categories.label)}
					<label>{t}{$filters.categories.label}{/t}:</label>
				{else}
					<label>{t}categories{/t}:</label>
				{/if}
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
	
		{if !empty($filters.tags) && !empty($listTags)}
			<div class="cell categories">
				<label>{t}tags{/t}:</label>
				<select name="filter[tag]">
					<option value="">{t}all{/t}</option>
					{foreach $listTags as $tag}
						{strip}
						<option value="{$tag.id}" {if $view->SessionFilter->read('tag') == $tag.id}selected="selected"{/if}>
							{$tag.label|escape}
						</option>
						{/strip}
					{/foreach}
				</select>
			</div>
		{/if}

		{if !empty($filters.status)}
			{if $filters.status|is_array && $filters.status|count gt 0}
				{$statusLabels = $filters.status}
			{/if}
			<div class="cell" id="statusfilter">
				{if $view->SessionFilter->check('status') || !$view->SessionFilter->check()}
					{$status = $view->SessionFilter->read('status')}
				{/if}
				<label>{t}status{/t}:</label>

					{foreach item='label' key='key' from=$statusLabels}
						<fieldset style="display:inline; border-left:1px solid gray;
							padding:5px 10px 5px 10px">

							<input type="checkbox" class="filterTicket" value="{$key}" id="status_{$key}" name="filter[status][{$key}]"
							{if !empty($status) && !empty($status[{$key}])}
								checked="checked"
							{/if}
						    />
							<a href="javascript: uncheckOther('{$key}');">{t}{$label}{/t}</a>

							</fieldset>
					{/foreach}
				</fieldset>
			</div>
		{/if}

		<div class="formbuttons">
			<input type="submit" id="searchButton" value=" {t}find it{/t} ">
			<input type="button" id="cleanFilters" value=" {t}reset filters{/t} ">
		</div>

	</div>
{/strip}
</form>