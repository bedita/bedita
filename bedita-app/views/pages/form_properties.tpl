<script type="text/javascript">
{literal}
$(document).ready(function(){
	$('#properties_langs_container > ul').tabs();
	$('#properties_langs_container > ul > li > a').click( function() { localTriggerTabs('properties_langs_container'); } );
});
{/literal}
</script>
<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
	{if isset($comments)}<br /><span style="font-weight:bold;">{t}comments{/t}</span>:<input type="radio"/>{t}No{/t} <input type="radio"/>{t}Yes{/t}{/if}
	<br />
	{if !(isset($publication)) || $publication}
	<span style="font-weight:bold;">{t}publication{/t}. {t}start{/t}:</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}start has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" name="data[start]" id="start" value="{if !empty($object.start)}{$object.start|date_format:$conf->date_format}{/if}"/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}end has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" name="data[end]" id="end" value="{if !empty($object.end)}{$object.end|date_format:$conf->date_format}{/if}"/>
	{/if}
	<hr/>
	{if (isset($doctype) && !empty($doctype))}
	<span style="font-weight:bold;">{t}Choose document type{/t}:</span>
	{html_radios name="data[object_type_id]" options=$conf->docTypeOptions selected=$object.object_type_id|default:'22' separator="&nbsp;"}
	<hr/>
	{/if}
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Nickname{/t}:</td>
		<td class="field">
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Language{/t}:</td>
		<td class="field">
			<select name="data[lang]">
			{assign var=object_lang value=$object.lang|default:$conf->defaultLang}{html_options options=$conf->langOptions selected=$object_lang}
			</select>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	</table>
	<hr/>
	<div id="properties_langs_container">
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
				<input {if $val==$object_lang}class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Title is required (at least %1 alphanumerical char){/t}"{/if}
					type="text" name="data[LangText][{$val}][title]"
					value="{$object.LangText.title[$val]|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
		</div>
		{/foreach}
	</div>
	{if ($object)}
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Created{/t}:</td><td>{$object.created|date_format:$conf->date_format}</td>
		<td class="label">{t}From{/t}:</td><td>{$object.UserCreated.userid|default:""}</td>
	</tr>
	<tr>
		<td class="label">{t}Last modified{/t}:</td><td>{$object.modified|date_format:$conf->date_format}</td>
		<td class="label">{t}From{/t}:</td><td>{$object.UserModified.userid|default:""}</td>
	</tr>
	<tr><td class="label">{t}IP{/t}:</td><td>{$object.ip_created}</td></tr>
	</table>
	{/if}
</fieldset>
</div>