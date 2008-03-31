{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$('.lang_flags').enableDisableTabs();
	$('#main_lang').mainLang();
	{/literal}
	{foreach key=val item=label from=$conf->langOptions name=langfe}
	{if $val!=$object_lang && empty($object.LangText.title[$val])}
		{literal}$('#properties_langs_container > ul').tabs("disable",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
	{elseif $val==$object_lang}
		{literal}$('#properties_langs_container > ul').tabs("select",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
	{/if}
	{/foreach}
	{if !(isset($publication)) || $publication}
	$('#start').attachDatepicker();
	$('#end').attachDatepicker();
	{/if}
	{literal}
});
{/literal}
</script>

<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>

	<div id="properties_langs_choice">
		<span class="label">{t}Main language{/t}:</span>
		<span class="field">
			<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			</select>
		</span>
		
		<span class="label" style="margin-left: 16px;">{t}Versions{/t}:</span>
		<span class="field">{foreach key=val item=label from=$conf->langOptions name=langfe}
				<input type="checkbox" name="data[lang_version]" class="lang_flags" title="{$smarty.foreach.langfe.index}" id="flag_{$val}"
					{if $val==$object_lang || !empty($object.LangText.title[$val])} checked="checked"{/if}
					{if $val==$object_lang} disabled="disabled"{/if}/>
				<img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}" style="vertical-align: middle;" />&nbsp;
			{/foreach}
		</span>
	</div>

	<br />

	<div id="properties_langs_container" class="tabsContainer">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#property_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="property_lang_{$val}">
		<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/></h3>
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}Title{/t}:</td>
			<td class="field">
				<input {if $val==$object_lang}class="{literal}{required:true,minLength:1}{/literal}" title="{t}Title is required{/t}"{/if}
					type="text" name="data[LangText][{$val}][title]"
					value="{$object.LangText.title[$val]|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
		</div>
		{/foreach}
	</div>


	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Status{/t}:</td>
		<td class="field">{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}</td>
		<td class="status">&nbsp;</td>
	</tr>

	{if !(isset($publication)) || $publication}
	<tr>
		<td class="label">{t}Publication Schedule{/t} {t}Start{/t}:</td>
		<td class="field"><input type="text" class="{literal}{{/literal}checkDate:'{$conf->dateFormatValidation}'{literal}}{/literal}" title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format{/t}" name="data[start]" id="start" value="{if !empty($object.start)}{$object.start|date_format:$conf->date_format}{/if}"/></td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Publication Schedule{/t} {t}End{/t}:</td>
		<td class="field">
		{strip}
		<input type="text" class="{literal}{{/literal}
							checkDate:'{$conf->dateFormatValidation}', 
							dateGreaterThen: new Array('{$conf->dateFormatValidation}','start'){literal}}{/literal}" 
							title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format and greater than the previous date{/t}" 
							name="data[end]" id="end" value="{if !empty($object.end)}{$object.end|date_format:$conf->date_format}{/if}"/>
		{/strip}</td>
		<td class="status">&nbsp;</td>
	</tr>
	{/if}

	{if isset($comments)}
	<tr>
		<td class="label">{t}Comments{/t}:</td>
		<td class="field"><input type="radio"/>{t}No{/t} <input type="radio"/>{t}Yes{/t}</td>
		<td class="status">&nbsp;</td>
	</tr>
	{/if}

	{if (isset($doctype) && !empty($doctype))}
	<tr>
		<td class="label">{t}Choose document type{/t}:</td>
		<td class="field">
			{html_radios name="data[object_type_id]" options=$conf->docTypeOptions selected=$object.object_type_id|default:'22' separator="&nbsp;"}</td>
		<td class="status">&nbsp;</td>
	</tr>
	{/if}

	<tr>
		<td class="label">{t}Univocal Nickname{/t}:</td>
		<td class="field">
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/></td>
		<td class="status">&nbsp;</td>
	</tr>
	</table>

	{*<div style="background: #F00 url('img/calendar.gif') repeat-x; width: 600px; height: 1px; display: inline;"><span>&nbsp;</span></div>*}

	{if ($object)}
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Created{/t}:</td>
		<td class="field">{$object.created|date_format:$conf->date_format} [{t}Author{/t}: {$object.UserCreated.userid|default:""}]</td>
	</tr>
	<tr>
		<td class="label">{t}Last modified{/t}:</td>
		<td class="field">{$object.modified|date_format:$conf->date_format} [{t}Author{/t}: {$object.UserModified.userid|default:""}]</td>
	</tr>
	<tr><td class="label">{t}IP{/t}:</td>
		<td class="field">{$object.ip_created}</td>
	</tr>
	</table>
	{/if}
</fieldset>
</div>