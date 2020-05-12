{strip}
{if !empty($objects)}
<div style="white-space:nowrap; padding: 0 4px">
	<input type="checkbox" class="selectAll" {if !empty($context)}data-context="{$context}" {/if}id="selectAll{if !empty($context)}-{$context}{/if}"/>
	&nbsp;<label for="selectAll{if !empty($context)}-{$context}{/if}">{t}(un)select all{/t}</label>
	&nbsp;&nbsp;&nbsp;
	{if !isset($bulk_tags) || $bulk_tags==false}
		{$beToolbar->show('compact', ['noitem' => $noitem|default:false, 'itemName' => $itemName|default:null])}
	{/if}
</div>

<br />

<div class="tab">
	<h2>{t}Operations on{/t} <span class="selecteditems evidence"{if !empty($context)} data-context="{$context}"{/if}></span> {t}selected records{/t}</h2>
</div>
<div>
	{if !isset($bulk_status) || $bulk_status==true}
	{t}change status to:{/t}&nbsp;
	<select id="newStatus" name="newStatus" placeholder='{t}select a status{/t}' data-placeholder='{t}select a status{/t}'>
		<option></option>
		{html_options options=$conf->statusOptions}
	</select>
	&nbsp;
	<input id="changestatusSelected" type="button" value=" ok " />
	{/if}
	{if isset($bulk_checklinks) && $bulk_checklinks==true}
	<hr />
	{t}check urls{/t}: <input id="checkSelected" type="button" value="{t}check selected links{/t}" class="opButton"/>
	{/if}

	{if isset($bulk_tree) && $bulk_tree==true && !empty($tree)}
		<hr/>
		{assign var='named_arr' value=$view->params.named}
		{if empty($sectionSel.id)}
			{t}copy{/t}
		{else}
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> {t}copy{/t} </option>
				<option value="move"> {t}move{/t} </option>
			</select>
		{/if}
		&nbsp;{t}to{/t}:&nbsp;

		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			<option value="">{t}None{/t}</option>
			{$beTree->option($tree)}
		</select>

		<input type="hidden" name="data[source]" value="{$sectionSel.id|default:''}" />
		&nbsp;<input id="assocObjects" type="button" value=" ok " />
		
		{if !empty($sectionSel.id)}
			<hr />
			{assign var='filter_section_id' value=$sectionSel.id}
			{assign var='filter_section_name' value=$pubSel.title|default:$sectionSel.title}
			<input id="removeFromAreaSection" type="button" value="{t}Remove selected from{/t} '{$filter_section_name}'" class="opButton" />
		{/if}
	{/if}

	{if isset($bulk_categories) && $bulk_categories==true && !empty($categories)}
		<hr />
		{t}category{/t}:
		&nbsp;<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]" placeholder='{t}select a category{/t}' data-placeholder='{t}select a category{/t}'>
		<option></option>
		{foreach from=$categories item='category' key='key'}
		{if !empty($named_arr.category) && ($key == $named_arr.category)}{assign var='filter_category_name' value=$category|escape}{/if}
		<option value="{$key}">{$category|escape}</option>
		{/foreach}
		</select>
		&nbsp;<input id="assocObjectsCategory" type="button" value="{t}Add association{/t}" class="opButton"/>
		{if !empty($named_arr.category)}
			<hr />
			{assign var='filter_category_id' value=$named_arr.category}
			<input id="disassocObjectsCategory" type="button" value="{t}Remove selected from category{/t} '{$filter_category_name}'" class="opButton" />
			&nbsp;<input id="filter_category" type="hidden" name="filter_category" value="{$filter_category_id}" />
		{/if}
	{/if}

	{if isset($bulk_language) && $bulk_language==true}
		<hr />
		{t}main language{/t}:&nbsp;
		<select id="newLang" name="data[lang]" placeholder='{t}select a language{/t}' data-placeholder='{t}select a language{/t}'>
			<option></option>
			{html_options options=$conf->langOptions}
			{html_options options=$conf->langsIso}
		</select>
		&nbsp;
		<input id="changeLanguageSelected" type="button" value=" ok " />
	{/if}

	{if isset($bulk_rights) && $bulk_rights==true}
		<hr />
		&copy; {t}rights{/t}:&nbsp;
		<input id="newRights" name="data[rights]" type="text" />
		&nbsp;
		<input id="changeRightsSelected" type="button" value=" ok " />
	{/if}

	{if isset($bulk_permission) && $bulk_permission}
		<hr />
		<h2>{t}Permissions{/t}</h2>
		&nbsp;
		{$view->element('form_permissions', ['removeTab' => true])}
		&nbsp;
		<input id="assignPermissionSelected" type="button" value=" {t}save permission{/t} " />
	{/if}

	<hr />

	{if !isset($bulk_hide_delete) || $bulk_hide_delete==false}
		<input id="deleteSelected" type="button" value="{t}Delete selected items{/t}"/>
	{/if}
	
	{if isset($bulk_tags) && $bulk_tags==true}
	<hr />
	<textarea name="addtaglist" id="addtaglist"></textarea>
	<p style="margin-top:5px">
	<input id="addmultipletag" type="button" value="{t}add more tags{/t}"/> 
	&nbsp;{t}Add comma separated words{/t}
	</p>
	{/if}

	{if isset($bulk_export_links) && $bulk_export_links==true}
	<hr />
	{bedev}
	{t}Export to{/t}:
	&nbsp;<select name="export">
		<option>Delicious(XBEL)</option>
		<option>Excel</option>
	</select>
	{/bedev}
	{/if}
</div>
{/if}
{/strip}
