<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<span style="font-weight:bold;">{t}status{/t}</span>:
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
	<hr/>
	{if (isset($doctype) && !empty($doctype))}
	<span style="font-weight:bold;">{t}Choose document type{/t}:</span>
	{html_radios name="data[object_type_id]" options=$conf->docTypeOptions selected=$object.object_type_id|default:'22' separator="&nbsp;"}
	<hr/>
	{/if}
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Language{/t}:</td>
		<td>
			<select name="data[lang]">
			{html_options options=$conf->langOptions selected=$object.lang|default:$conf->defaultLang}
			</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="Title_TR_{$object.lang|default:$conf->defaultLang}">
		<td class="label">{t}Title{/t}:</td>
		<td class="field">
			<input class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Title is required (at least %1 alphanumerical char){/t}" id="titleInput"  type="text" 
				name="data[title]" value="{$object.title|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		<td class="status">
		{* commentato temporaneamente: bug da fissare
		{if ($object)}<input class="cmdField" id="cmdTranslateTitle" type="button" value="lang ..."/>{/if}
		*}
		</td>
	</tr>
	{if (isset($object.LangText.title))}
	{foreach name=i from=$object.LangText.title key=lang item=text}
	<tr>
		<td class="label">&#160;</td>
		<td class="field">
			<input type='hidden' value='title' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<input type="text" name="data[LangText][{$smarty.foreach.i.iteration}][txt]" value="{$text|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
		<td>
			<select name="data[LangText][{$smarty.foreach.i.iteration}][lang]">
			{html_options options=$conf->langOptions selected=$lang}
			</select>
			&nbsp;&nbsp;
			<input type="button" name="delete" value=" x " onclick="$('../..', this).remove() ;"/>
		</td>
	</tr>
	{/foreach}
	{/if}
	<tr>
		<td class="label">{t}Nickname{/t}:</td>
		<td class="field">
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
	</tr>
	
	</table>
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
	<tr>
		<td class="label">{t}IP{/t}:</td><td>{$object.ip_created}</td>
	</tr>
	</table>
	{/if}
</fieldset>
</div>