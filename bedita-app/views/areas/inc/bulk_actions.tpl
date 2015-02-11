
<div class="tab"><h2>{t}Bulk actions on{/t}&nbsp;<span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div class="ignore">
	
	
		<label for="selectAll" style="padding-right:20px; float:left;">
			<input type="checkbox" class="selectAll" id="selectAll" /> {t}(un)select all{/t}
		
		</label>
		

{t}change status to{/t}: 	<select style="width:75px" id="newStatus" name="newStatus">
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " class="opButton"/>
	<hr />
	
	{if !empty($tree)}
		{assign var='named_arr' value=$view->params.named}
		
		{if empty($sectionSel.id)}
			{t}copy{/t}
		
		{elseif $type=="section"}
		
			{t}move{/t}
			
		{else}
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> {t}copy{/t} </option>
				<option value="move"> {t}move{/t} </option>
			</select>
		{/if}
		&nbsp;{t}to{/t}:  &nbsp;

		<select id="areaSectionAssoc" style="width:320px" class="areaSectionAssociation" name="data[destination]">
			{$beTree->option($tree)}
		</select>

		<input type="hidden" name="data[source]" value="{$sectionSel.id|default:''}" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />
		
		{if $type!="section" && !empty($sectionSel.id)}
			{assign var='filter_section_id' value=$sectionSel.id}
			{assign var='filter_section_name' value=$pubSel.title|default:$sectionSel.title}
			<input id="removeFromAreaSection" type="button" value="{t}Remove selected from{/t} '{$filter_section_name}'" class="opButton" />
			<hr/>
		{/if}
		
	{/if}

	{if !empty($categories)}
		{t}category{/t}
		<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
		<option value="">--</option>
		{foreach from=$categories item='category' key='key'}
		{if !empty($named_arr.category) && ($key == $named_arr.category)}{assign var='filter_category_name' value=$category}{/if}
		<option value="{$key}">{$category}</option>
		{/foreach}
		</select>
		<input id="assocObjectsCategory" type="button" value="{t}Add association{/t}" class="opButton"/>
		
		{if !empty($named_arr.category)}
			<hr />
			{assign var='filter_category_id' value=$named_arr.category}
			<input id="disassocObjectsCategory" type="button" value="{t}Remove selected from category{/t} '{$filter_category_name}'" class="opButton" />
			<input id="filter_category" type="hidden" name="filter_category" value="{$filter_category_id}" />
		{/if}
	{/if}

</div>