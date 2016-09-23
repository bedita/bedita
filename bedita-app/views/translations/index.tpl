<script type="text/javascript">
var urls = {};
urls['URLBase'] = "{$html->url('index/')}" ;
urls['deleteSelected'] = "{$html->url('deleteTranslations/')}" ;
urls['changestatusSelected'] = "{$html->url('changeStatusTranslations/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var no_items_checked_msg = "{t}No items selected{/t}";
</script>

{$html->script('fragments/list_objects.js', false)}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

{assign_associative var="params" noitem="true"}
{$view->element('toolbar', $params)}

<div class="mainfull">

	<form method="post" action="{$html->url('/translations/index')}" id="formObject">
	{$beForm->csrf()}

	<input type="hidden" name="data[id]" value="{$object_translation.id.status|default:''}"/>
	<input type="hidden" name="data[master_id]" value="{$object_master.id|default:''}"/>
	<input type="hidden" name="objects_selected" id="objects_selected"/>


<div class="tab"><h2>{t}filters{/t}</h2></div>
<div>
	{t}Show translations in{/t}: &nbsp;
	<select name="data[translation_lang]">
		<option value=""></option>
	{foreach key=val item=label from=$conf->langOptions}
		<option value="{$val}"{if $langSelected==$val} selected="selected"{/if}>{$label}</option>
	{/foreach}
	</select>

	&nbsp;{t}with status{/t}: &nbsp;
	<select name="data[translation_status]">
	<option value=""></option>
	<option value="on"{if $statusSelected=='on'} selected="selected"{/if}>{t}on{/t}</option>
	<option value="off"{if $statusSelected=='off'} selected="selected"{/if}>{t}off{/t}</option>
	<option value="draft"{if $statusSelected=='draft'} selected="selected"{/if}>{t}draft{/t}</option>
	<option value="required"{if $statusSelected=='required'} selected="selected"{/if}>{t}required{/t}</option>
	</select>

	&nbsp;{t}for object type{/t}: &nbsp;
	<select name="data[translation_object_type_id]">
	<option value=""></option>
	{foreach from=$conf->objectTypes key="key" item="objectTypes"}
	{if !empty($objectTypes.model) && is_numeric($key)}
		<option value="{$objectTypes.id}" class="{$objectTypes.module_name}"{if $objectTypeIdSelected == $objectTypes.id} selected="selected"{/if}> {$objectTypes.name}</option>
	{/if}
	{/foreach}
	</select>

	&nbsp;{t}of master id{/t}:&nbsp;
	<input type="text" name="data[translation_object_id]" style="width:25px"
	value="{$objectIdSelected}"/>
	&nbsp;<input type="submit" value="{t}go{/t}"/>

{if !empty($translations)}
	<hr />
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		&nbsp;&nbsp;&nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
{/if}

	</div>
	<table class="indexlist js-header-float">
	{capture name="theader"}
	<thead>
		<tr>
			<th></th>
			<th>{$beToolbar->order('title', 'master title')}</th>
			<th>{$beToolbar->order('LangText.title', 'title')}</th>
			<th>{$beToolbar->order('object_type_id', 'type')}</th>
			<th>{$beToolbar->order('LangText.lang', 'language')}</th>
			<th>{$beToolbar->order('LangText.status', 'Status')}</th>
		</tr>
	</thead>
	{/capture}

		{$smarty.capture.theader}

		{section name="i" loop=$translations}


		{assign var="oid" value=$translations[i].LangText.object_id}
		{assign var="olang" value=$translations[i].LangText.lang}
		{assign var="ot" value=$translations[i].BEObject.object_type_id}
		{assign var="mtitle" value=$translations[i].BEObject.title}

		<tr class="obj {$translations[i].LangText.status}">
			<td class="checklist">
				<input type="checkbox" name="object_chk" class="objectCheck" title="{$translations[i].LangText.id}" />
			</td>
			<td>
				{$mtitle|escape|default:'<i>[no title]</i>'|truncate:38:true} &nbsp;
			</td>
			<td><a href="{$html->url('view/')}{$oid}/{$olang}">{$translations[i].LangText.title|escape|default:'<i>[no title]</i>'|truncate:38:true}</a></td>
			<td>
				<span class="listrecent {$conf->objectTypes[$ot].name|lower}">&nbsp;</span>
				{$conf->objectTypes[$ot].model}
			</td>
			<td>{$olang}</td>
			<td>{$translations[i].LangText.status}</td>

		</tr>
		{sectionelse}
			<tr><td colspan="100" class="noclick">{t}No items found{/t}</td></tr>
		{/section}

</table>
<br>
{assign_associative var=params objects=$translations}
{$view->element('list_objects_bulk', $params)}

</form>
</div>
